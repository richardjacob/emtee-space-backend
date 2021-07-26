<?php

/**
 * Wishlist Controller
 *
 * @package     Makent Space
 * @subpackage  Controller
 * @category    Wishlist
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Models\Wishlists;
use App\Models\SavedWishlists;
use App\Models\User;
use App\Mail\MailQueue;

class WishlistController extends Controller
{
    public function wishlist_list(Request $request)
    {
        $space_id = $request->id;
        if(auth()->check()) {
            $result = Wishlists::leftJoin('saved_wishlists', function($join) use($space_id) {
                $join->on('saved_wishlists.wishlist_id', '=', 'wishlists.id')->where('saved_wishlists.space_id', '=', $space_id);
            })->where('wishlists.user_id', auth()->id())->where('wishlists.name','!=','')->orderBy('wishlists.id','desc')->select(['wishlists.id as id', 'name', 'saved_wishlists.id as saved_id'])->get();

           return $result;
        }
        else {
                session(['url.intended' => url()->previous()]);

            return json_encode('redirect');
            
        }
    }

    public function create(Request $request)
    {
        $wishlist = new Wishlists;

        $wishlist->name    = $request->data;
        $wishlist->user_id = auth()->id();

        $wishlist->save();

        $where = array();
        if(isset($request->id)){
            $where['saved_wishlists.space_id'] = $request->id;
        }
        $result = Wishlists::leftJoin('saved_wishlists', function($join) use($where) {
                                $join->on('saved_wishlists.wishlist_id', '=', 'wishlists.id')->where($where);
                            })->where('wishlists.user_id', auth()->id())->orderBy('wishlists.id','desc')->where('wishlists.name','!=','')->select(['wishlists.id as id', 'name', 'saved_wishlists.id as saved_id'])->get();
        
        return json_encode($result);
    }

    public function create_new_wishlist(Request $request)
    {
        $wishlist = new Wishlists;

        $wishlist->name    = $request->name;
        $wishlist->privacy = $request->privacy;
        $wishlist->user_id = auth()->id();

        $wishlist->save();

        flash_message('success', trans('messages.wishlist.created_successfully'));
        return redirect('wishlists/my');
    }

    public function edit_wishlist(Request $request)
    {
        $wishlist = Wishlists::find($request->id);

        $wishlist->name    = $request->name;
        $wishlist->privacy = $request->privacy;

        $wishlist->save();

        flash_message('success', trans('messages.wishlist.updated_successfully'));
        return redirect('wishlists/'.$request->id);
    }

    public function delete_wishlist(Request $request)
    {
        $delete = Wishlists::whereId($request->id)->whereUserId(auth()->id());

        if($delete->count()) {
            $counts=SavedWishlists::whereWishlistId($request->id)->delete();
            $delete->delete();
            flash_message('success', trans('messages.wishlist.deleted_successfully'));
            $counts= Wishlists::whereUserId(auth()->id())->count();
            if($counts) {
                return redirect('wishlists/my');
            }
            return redirect('dashboard');
        }
        return redirect('dashboard');
    }

    public function add_note_wishlist(Request $request)
    {
        SavedWishlists::whereWishlistId($request->id)->whereUserId(auth()->id())->whereSpaceId($request->space_id)->update(['note' => $request->note]);
    }

    public function save_wishlist(Request $request)
    {
        if($request->saved_id) {
            SavedWishlists::find($request->saved_id)->delete();
            return 'null';
        }
        $save_wishlist = new SavedWishlists;

        $save_wishlist->space_id    = $request->space_id;
        $save_wishlist->wishlist_id = $request->wishlist_id;
        $save_wishlist->user_id     = auth()->id();

        $save_wishlist->save();

        return $save_wishlist->id;
    }

    public function remove_saved_wishlist(Request $request)
    {
        SavedWishlists::whereWishlistId($request->id)->whereSpaceId($request->space_id)->delete();

        return SavedWishlists::whereWishlistId($request->id)->get();
    }

    public function my_wishlists(Request $request)
    {
        if(!@$request->id || @auth()->id() == $request->id) {

            $data['result'] = Wishlists::with(['saved_wishlists' => function($query) {
                $query->with(['space']);
            },'profile_picture'])
            /*
            Commented to Show All Wishlists Details
            ->whereHas('saved_wishlists', function($query) {
                $query->whereHas('rooms', function($query) {
                    $query->where(['status'=> 'Listed','verified'=>'Approved'])
                    ->whereHas('users',function($query) {
                        $query->where('status','Active');
                    });
                })->where('list_type', 'Rooms')->orWhereHas('host_experiences', function($query) {
                    $query->where('status','Listed')->whereHas('users',function($query) {
                        $query->where('status','Active');
                    });
                 })
                ->where('list_type', 'Experiences');
            })*/
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

            $data['owner'] = 1;
            $data['user'] = auth()->user();
        }
        else {
            $data['result'] = Wishlists::with(['saved_wishlists' => function($query) {
                $query->with(['space'=>function($query) {
                    $query->where('status','Listed');
                }])
                ->where(function($query) {
                    $query->whereHas('space', function($query) {
                        $query->where('status','Listed');
                    });
                });
                }, 'profile_picture'])
                ->where('user_id', $request->id)
                ->wherePrivacy('0')
                ->orderBy('id', 'desc')
                ->get();
            $data['owner'] = 0;
            $data['user'] = User::find($request->id);
        }

        if($data['result']->count() == 0) {
            abort(404);
        }

        $data['count'] = wishlists::where('user_id',@auth()->id())->where('name','!=','')->count();

        return view('wishlists.my_wishlists', $data);
    }

    public function get_wishlists_space(Request $request)
    {
        $check = Wishlists::whereId($request->id)->whereUserId(@auth()->id())->first();
        $wishlist = Wishlists::
        with(['saved_wishlists' => function($query){
            $query->with(['space' => function($query1){
                $query1->with('space_photos','space_address','activity_price');
            }])
            ->with('users','profile_picture')
            ->whereHas('space',function($query1) {
                $query1->whereStatus('Listed')
                ->whereHas('users',function($query2) {
                    $query2->whereStatus('Active');
                });
            });
        }])
        ->where('id', $request->id);

        if($check) {
            $wishlist =$wishlist->get();
        }
        else {
            $wishlist =$wishlist->where('privacy','0')->get();
        }
        return $wishlist->tojson();
    }

    public function wishlist_details(Request $request)
    {
        $check = Wishlists::whereId($request->id)->whereUserId(@auth()->id())->first();
        $wishlist = Wishlists::with([
            'saved_wishlists' => function($query){
                $query->with([
                    'space' => function($query){
                        $query->where('status','Listed');
                    },
                    'users', 
                    'profile_picture'
                ]);
        }])->where('id', $request->id);
        if($check) 
        {
            $data['owner'] = 1;
            $wishlist =$wishlist->get();
        }
        else 
        {
            $data['owner'] = 0;
            $wishlist =$wishlist->where('privacy','0')->get();
        }
        if(!$wishlist->count()){ 
            abort('404');
        }
        $data['result']=$wishlist;
        $data['count'] = 0;
        $data['wl_id']=$request->id;
        return view('wishlists.wishlist_details', $data);
    }

    public function share_email(Request $request)
    {
        $wishlist_id = $request->id;

        // set email data
        $email_array = explode(',', $request->email);
        $to_emails = array_filter(array_map('trim', $email_array));
        $message = $request->message;

        $data['url'] = url('/').'/';
        $data['locale'] = \App::getLocale();
        $data['content'] = auth()->user()->first_name."'s Wish List Link: ".$data['url'].'wishlists/'.$wishlist_id.' <br><br>' . $message;
        $data['view_file'] = 'emails.custom_email';

        // send email to queue one by one
        foreach($to_emails as $email) {
            $user = User::where('email', $email)->get();
            $data['first_name'] = (@$user[0]->first_name) ? $user[0]->first_name : $email;
            $data['subject'] = auth()->user()->first_name . ' shared his Wish List';

            \Mail::to($email)->queue(new MailQueue($data));
        }

        flash_message('success', trans('messages.wishlist.shared_successfully'));
        return redirect('wishlists/'.$wishlist_id);
    }

    public function popular(Request $request)
    {
        $data['result'] = Space::with(['saved_wishlists' => function($query){
                $query->where('user_id', @auth()->id());
            }])->wherePopular('Yes')->whereStatus('Listed')->get();

        if(!@$request->id || @auth()->id() == $request->id) {
            $result = Wishlists::with(['saved_wishlists' => function($query){
                $query->with(['space']);
            }, 'profile_picture'])->where('user_id', @auth()->id())->orderBy('id', 'desc')->get();
        }
        else {
            $result = Wishlists::with(['saved_wishlists' => function($query){
                $query->with(['space']);
            }, 'profile_picture'])->where('user_id', $request->id)->wherePrivacy('0')->orderBy('id', 'desc')->get();
        }
        
        $data['count'] = $result->count();

        return view('wishlists.popular', $data);
    }

    public function picks(Request $request)
    {
        $data['result'] = Wishlists::
            with(['saved_wishlists' => function($query){
                $query->with(['space'])
                ->whereHas('space', function($query){
                    $query->where('status','Listed');
                });
            }, 'profile_picture'])
            ->wherePrivacy('0')
            ->wherePick('Yes')
            ->orderBy('id', 'desc')
            ->whereHas('users',function($query){
                $query->where('status','Active');
            })->get();

        if(!@$request->id || @auth()->id() == $request->id) {
            $result = Wishlists::with(['saved_wishlists' => function($query){
                $query->with(['space']);
            }, 'profile_picture'])->where('user_id', @auth()->id())->orderBy('id', 'desc')->get();
        }
        else {
            $result = Wishlists::with(['saved_wishlists' => function($query){
                $query->with(['space']);
            }, 'profile_picture'])->where('user_id', $request->id)->wherePrivacy('0')->orderBy('id', 'desc')->get();
        }
        
        $data['count'] = $result->count();
        
        return view('wishlists.picks', $data);
    }
}
