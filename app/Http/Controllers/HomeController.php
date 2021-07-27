<?php

/**
 * Home Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Home
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Helper\FacebookHelper;
use App\Http\Start\Helpers;
use App\Models\Contactus;
use App\Models\Currency;
use App\Models\Help;
use App\Models\HelpSubCategory;
use App\Models\HomePageSlider;
use App\Models\Activity;
use App\Models\KindOfSpace;
use App\Models\OurCommunityBanners;
use App\Models\Pages;
use App\Models\Reservation;
use App\Models\Space;
use App\Models\SiteSettings;
use App\Models\HelpTranslations;
use App\Models\User;
use Illuminate\Http\Request;
use App;
use Route;

class HomeController extends Controller
{
	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		// 
	}

	public function index()
	{
		$data['sliders'] = HomePageSlider::activeOnly()->get();
		$data['popular_activities'] = Activity::activeOnly()->popularOnly()->get();
		$data['popular_space_type'] = KindOfSpace::active()->popularOnly()->get();
        $data['our_community_banners'] 	= OurCommunityBanners::select('id','image','link','title','description')->get();
        
		//return view('home.home',$data);
		return view('home.home_three',$data);

	}

	public function develop()
	{
		$data['sliders'] = HomePageSlider::activeOnly()->get();
		$data['popular_activities'] = Activity::activeOnly()->popularOnly()->get();
		$data['popular_space_type'] = KindOfSpace::active()->popularOnly()->get();
        $data['our_community_banners'] 	= OurCommunityBanners::select('id','image','link','title','description')->get();
        
		//dd($data['popular_activities']);
		return view('home.home_three',$data);
	}

	public function phpinfo()
	{
		echo phpinfo();
	}

	public function clearLog()
    {
        session()->forget('get_token');
        exec('echo "" > ' . storage_path('logs/laravel.log'));
    }

    public function showLog()
    {
        $contents = \File::get(storage_path('logs/laravel.log'));
        echo '<pre>'.$contents.'</pre>';
    }

    public function updateEnv(Request $request)
    {
        $requests = $request->all();
        $valid_env = ['APP_ENV','APP_DEBUG'];
        foreach ($requests as $key => $value) {
            $prev_value = getenv($key);
            logger($key.' - '.$prev_value);
            if(in_array($key,$valid_env)) {
                updateEnvConfig($key,$value);
            }
        }
    }

	/**
	 * Load Social OR Email Signup view file with Generated Facebook login URL
	 *
	 * @return Signup page view
	 */
	public function signup_login(Request $request)
	{
		$data['class'] = '';

		// Social Signup Page
		if ($request->input('sm') == 1 || $request->input('sm') == '') {
			session(['referral' => $request->referral]);
			if ($request->referral && User::find($request->referral)==null) {
				abort(404);
			}
			return view('home.signup_login', $data);
		}
		// Email Signup Page
		else if ($request->input('sm') == 2) {
			return view('home.signup_login_2', $data);
		}

		abort(404);
	}

	public function generateFacebookurl()
	{
		/*PackageCommentStart*/
		// flash_message('danger', trans('messages.login.facebook_https_error'));
		// return redirect('login');
		/*PackageCommentEnd*/

		if (!session_id()) {
			session_start();
		}

		$fb = new FacebookHelper;
		$fb_url = $fb->getUrlLogin();
		return redirect($fb_url);
	}

	/**
	 * Set session for Currency & Language while choosing footer dropdowns
	 *
	 */
	public function set_session(Request $request)
	{
		if ($request->currency) {
			session(['currency' => $request->currency]);
			session(['previous_currency' => $request->previous_currency]);
			$symbol = Currency::original_symbol($request->currency);
			session(['symbol' => $symbol]);
			session(['search_currency' => $request->previous_currency]);
		}
		else if ($request->language) {
			session(['language' => $request->language]);
			App::setLocale($request->language);
		}
	}

	/**
	 * View Static Pages
	 *
	 * @param array $request  Input values
	 * @return Static page view file
	 */
	public function static_pages(Request $request)
	{
		if ($request->token != '') {
			session(['get_token' => $request->token]);
		}
		
		if($request->name == ADMIN_URL) {
			return redirect()->route('admin_dashboard');
		}

		$pages = Pages::where(['url' => $request->name,'status' => 'active'])->firstOrFail();

		$data['content'] = str_replace(['SITE_NAME', 'SITE_URL'], [SITE_NAME, url('/')], $pages->content);
		$data['title'] = $pages->name;

		return view('home.static_pages', $data);
	}

	public function help(Request $request)
	{
		if ($request->token != '') {
			session(['get_token' => $request->token]);
			if(isset($request->language)) {
	            App::setLocale($request->language);
	            session(['language' => $request->language]);
	        }else {
	            App::setLocale('en');
	        }
		}

		if (Route::current()->uri() == 'help') {
			$data['result'] = Help::with(['category', 'subcategory'])->whereSuggested('yes')->get();
		}
		elseif (Route::current()->uri() == 'help/topic/{id}/{category}') {
			$count_result = HelpSubCategory::find($request->id);
			$data['subcategory_count'] = $count = (str_slug($count_result->name, '-') != $request->category) ? 0 : 1;
			$data['is_subcategory'] = (str_slug($count_result->name, '-') == $request->category) ? 'yes' : 'no';
			if ($count) {
				$data['result'] = Help::whereSubcategoryId($request->id)->whereStatus('Active')->get();
			}
			else {
				$data['result'] = Help::whereCategoryId($request->id)->whereStatus('Active')->get();
			}
		}
		else {
			$data['result'] = Help::whereId($request->id)->whereStatus('Active')->get();
			$data['is_subcategory'] = ($data['result'][0]->subcategory_id) ? 'yes' : 'no';
		}

		$data['category'] = Help::with(['category', 'subcategory'])->whereStatus('Active')->groupBy('category_id')->get(['category_id', 'subcategory_id']);

		return view('home.help', $data);
	}

	public function ajax_help_search(Request $request)
	{
		$lan = session('language');
		$term = $request->term;

		$queries= Help::whereHas('category',function($query) {
				$query->where("status","active");
			})->whereHas('subcategory',function($query) {
				$query->where("status","active");
			})
			->where('status','active')
			->where('question', 'like', '%' . $term . '%')
			->get();

		$queries_translate = HelpTranslations::where('locale',$lan)
			->where('name', 'like', '%' . $term . '%')
			->get();
		 
		if($lan=='en') {
			if ($queries->isEmpty()) {
			$results[] = ['id' => '0', 'value' => trans('messages.search.no_results_found'), 'question' => trans('messages.search.no_results_found')];
			}
			else {
				foreach ($queries as $query) {
				$results[] = ['id' => $query->id, 'value' => str_replace('SITE_NAME', SITE_NAME, $query->question), 'question' => str_slug($query->question, '-'), 'target' => route('help_question',['id' => $query->id, 'question' => str_slug($query->question, '-')])];
				}
			}
		}
		else {
			if ($queries_translate->isEmpty()) {
				$results[] = ['id' => '0', 'value' => trans('messages.search.no_results_found'), 'question' => trans('messages.search.no_results_found')];
			} 
			else {
				foreach ($queries_translate as $translate) {
					$results[] = ['id' => $translate->help_id, 'value' => str_replace('SITE_NAME', SITE_NAME, $translate->name), 'question' => str_slug($translate->name, '-'), 'target' => route('help_question',['id' => $translate->help_id, 'question' => str_slug($translate->name, '-')])];
				}
			}
		}

		return json_encode($results);
	}

	public function contact_create(Request $request, EmailController $email_controller)
	{

		$rules = array(
			'name' => 'required',
			'email' => 'required|max:255|email',
			'feedback' => 'required|min:6',
		);

		$messages = array(
			//
		);

		$attributes = array(
			'name' => trans('messages.contactus.name'),
			'email' => trans('messages.contactus.email'),
			'feedback' => trans('messages.contactus.feedback'),
		);

		$request->validate($rules, $messages, $attributes);

		$user_contact = new Contactus;

		$user_contact->name = $request->name;
		$user_contact->email = $request->email;
		$user_contact->feedback = $request->feedback;

		$user_contact->save(); // Create a new user

		$email_controller->contact_email_confirmation($user_contact);

		flash_message('success', trans('messages.contactus.sent_successfully')); // Call flash message function
		return redirect('contact');
	}

	/**
	 * Get Home Page Slider Data
	 *
	 * @return Array Space slider details
	 */
	public function ajax_home()
	{
		/*$data['just_booked'] = Reservation::
			with([
				'space' => function ($query) {
					$query->with(['space_price'=>function($query1){
						$query1->select('space_id','night','currency_code','cleaning','additional_guest','security','weekend');
					}])
					->select('id','property_type','room_type','bed_type','user_id','beds','name','booking_type');
				}, 
				'currency'=>function($query){
					$query->select('code','symbol');
				}
			])
			->selectRaw('id,created_at,status,checkin,checkout,number_of_guests,host_id,user_id,currency_code,space_id, max(id) as reservation_id')
			->whereHas('space', function ($query) {
				$query->where(['status'=> 'Listed','admin_status'=>'Approved']);
			})
			->whereHas('host_users', function ($query) {
				$query->where('status', 'Active');
			})
			->orderBy('reservation_id', 'desc')
			->where('status', 'Accepted')
			->groupBy('space_id')
			->limit(8)
			->get();

		$data['recommended'] = Space::
			select('id','property_type','room_type','bed_type','user_id','beds','name')
			->with(['rooms_price' => function ($query) {
				$query->select('space_id','night','currency_code','cleaning','additional_guest','security','weekend')
				->with(['currency'=>function($query1){
					$query1->select('code','symbol');
				}]);
			}])
			->whereHas('users', function ($query) {
				$query->where('status', 'Active');
			})
			->orderBy('id', 'desc')
			->where('recommended', 'Yes')
			->where(['status'=> 'Listed','verified'=>'Approved'])
			->groupBy('id')
			->limit(8)
			->get();

		$data['most_viewed'] = Space::
			select('id','property_type','room_type','bed_type','user_id','beds','name','booking_type')
			->with(['rooms_price' => function ($query) {
				$query->select('space_id','night','currency_code','cleaning','additional_guest','security','weekend')
				->with(['currency'=>function($query1){
					$query1->select('code','symbol');
				}]);
			}])
			->whereHas('users', function ($query) {
				$query->where('status', 'Active');
			})
			->orderBy('views_count', 'desc')
			->where(['status'=> 'Listed','verified'=>'Approved'])
			->groupBy('id')
			->limit(8)
			->get();*/

		$data['just_booked'] = [];
		$data['recommended'] = [];
		$data['most_viewed'] = [];

		return array(
			'just_booked' => $data['just_booked'], 
			'most_viewed' => $data['most_viewed'],
		);
	}

	/**
	 * Get Home Page Data
	 *
	 * @return Array HomeCities and Community Banners
	 */
	public function ajax_home_explore() 
	{
       $popular_activities 		= Activity::activeOnly()->popularOnly()->get();
       $our_community_banners 	= OurCommunityBanners::select('id','image','link','title','description')->get();

       return compact('popular_activities','our_community_banners');
	}
}