<?php

/**
 * Helpers
 *
 * @package     Makent Space
 * @subpackage  Helpers
 * @category    Helpers
 * @author      Trioangle Product Team
 * @version     1.0
 * @link        http://trioangle.com
 */

use App\Models\Currency;
use App\Models\Language;
use App\Models\SpacePhotos;
use App\Models\ActivityPrice;
use Illuminate\Support\Arr;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\Serializer\CompactSerializer;

/**
 * Convert String to htmlable instance
 *
 * @param  string $type      Type of the image
 * @return instance of \Illuminate\Contracts\Support\Htmlable
 */
if (!function_exists('html_string')) {

    function html_string($str)
    {
        return new HtmlString($str);
    }
}

/**
 * Check Current Route is inside given array
 *
 * @param  String route names
 * @return boolean true|false
 */
if (!function_exists('isActiveRoute')) {

    function isActiveRoute()
    {
        $routes = func_get_args();
        return in_array(request()->route()->getName(),$routes);
    }
}

/**
 * Get Site Base Url
 *
 * @return String $url Base url
 */
if (!function_exists('siteUrl')) {

    function siteUrl()
    {
        $site_settings_url = @SiteSettings::where('name', 'site_url')->first()->value;
        $url = \App::runningInConsole() ? $site_settings_url : url('/');
        return $url;
    }
}


/**
 * Get First Image
 *
 * @return String $url Base url
 */
if (!function_exists('getFirstHomeImage')) {

    function getFirstHomeImage($space_id)
    {
        $photo = SpacePhotos::where('space_id',$space_id)->where('order_id', 1)->first();
        return $photo->name;
    }
}


/**
 * Get First Image
 *
 * @return String $url Base url
 */
if (!function_exists('getPriceHourly')) {

    function getPriceHourly($space_id)
    {
        $photo = ActivityPrice::where('space_id',$space_id)->first();
        return $photo->hourly;
    }
}

/**
 * Set Flash Message function
 *
 * @param  string $class     Type of the class ['danger','success','warning']
 * @param  string $message   message to be displayed
 */
if (!function_exists('flash_message')) {

    function flash_message($class, $message)
    {
        Session::flash('alert-class', 'alert-'.$class);
        Session::flash('message', $message);
    }
}

/**
 * Currency Convert
 *
 * @param int $from   Currency Code From
 * @param int $to     Currency Code To
 * @param int $price  Price Amount
 * @return int Converted amount
 */
if (!function_exists('currency_convert')) {

    function currency_convert($from = '', $to = '', $price,$vel='')
    {   \Log::info($vel.session('currency'));
        if(session('currency')) {
            $currency_code = session('currency');
        }
        else {
            $currency_code = Currency::where('default_currency', 1)->first()->code;
        }
        \Log::info($vel.$currency_code);
        if($from == '') {
            $from = $currency_code;
        }
        if($to == '') {
            $to = $currency_code;
        }

        if($from == $to) {
            return ceil($price);
        }

        $rate = Currency::whereCode($from)->first()->rate;
        $usd_amount = $price / $rate;
        $session_rate = Currency::whereCode($to)->first()->rate;

        return ceil($usd_amount * $session_rate);
    }
}

/**
 * Get Langugage Code
 *
 * @return String $lang_code 
 */
if (!function_exists('getLangCode')) {

    function getLangCode()
    {
        $language = Language::whereValue(session('language'))->first();

        if($language) {
            $lang_code = $language->value;
        }
        else {
            $lang_code = Language::where('default_language',1)->first()->value;
        }
        return $lang_code;
    }
}

if (!function_exists('crop_image')) {
    function compress_image($source_url, $destination_url, $quality, $width = 225, $height = 225)
    {
        $info = getimagesize($source_url);
        if(!$info) {
            return false;
        }

        if($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source_url);
            $exif = @exif_read_data($source_url);
        }
        elseif($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source_url);
        }
        elseif($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source_url);
        }
        elseif($info['mime'] == 'image/webp') {
            $image = imagecreatefromwebp($source_url);
        }

        if (isset($exif) && !empty($exif['Orientation'])) {
            $imageResource = imagecreatefromjpeg($source_url);
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($imageResource, 180, 0);
                    break;
                case 6:
                    $image = imagerotate($imageResource, -90, 0);
                    break;
                case 8:
                    $image = imagerotate($imageResource, 90, 0);
                    break;
                default:
                    $image = $imageResource;
            }
        }

        imagejpeg($image, $destination_url, $quality);
        crop_image($source_url, $width, $height);
        return $destination_url;
    }
}

if (!function_exists('crop_image')) {
    function crop_image($source_url='', $crop_width=225, $crop_height=225, $destination_url = '')
    {
        ini_set('memory_limit', '-1');
        $image = Image::make($source_url);
        $image_width = $image->width();
        $image_height = $image->height();

        if($image_width < $crop_width && $crop_width < $crop_height){
            $image = $image->fit($crop_width, $image_height);
        }if($image_height < $crop_height  && $crop_width > $crop_height){
            $image = $image->fit($crop_width, $crop_height);
        }

        $primary_cropped_image = $image;

        $croped_image = $primary_cropped_image->fit($crop_width, $crop_height);

        if($destination_url == ''){
            $source_url_details = pathinfo($source_url); 
            $destination_url = @$source_url_details['dirname'].'/'.@$source_url_details['filename'].'_'.$crop_width.'x'.$crop_height.'.'.@$source_url_details['extension']; 
        }

        $croped_image->save($destination_url); 
        return $destination_url; 
    }
}

/**
 * Upload Image function
 *
 * @param  String $image     Image File
 * @param  String $target_dir   Where file to be uploaded
 * @param  String $name_prefix   Prefix of file name
 * @return Array $return_data return status,status_message and file name
 */
if (!function_exists('uploadImage')) {

    function uploadImage($image, $target_dir, $name_prefix ='', $compress_size = array())
    {
        $return_data = array('status' => 'Success','status_message' => 'Uploaded Successfully','upload_src' => 'Local');
        if(isset($image)) {
            $tmp_name = $image->getPathName();

            if(UPLOAD_DRIVER == 'cloudinary') {
                $return_data['upload_src'] = 'Cloudinary';
                $c = cloudUpload($tmp_name);
                if ($c['status'] != "error") {
                    $return_data['file_name'] = $c['message']['public_id'];
                }
                else {
                    $return_data['status'] = 'Failed';
                    $return_data['status_message'] = $c['message'];
                }
            }
            else {              
                $ext = strtolower($image->getClientOriginalExtension());
                $name = $name_prefix.time().'.'.$ext;

                $filename = dirname($_SERVER['SCRIPT_FILENAME']).$target_dir;

                if (!file_exists($filename)) {
                    mkdir(dirname($_SERVER['SCRIPT_FILENAME']).$target_dir, 0777, true);
                }

                if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'svg' || $ext == 'webp') {
                    if(!$image->move($filename, $name)) {
                        $return_data['status'] = 'Failed';
                        $return_data['status_message'] = 'Failed To Upload Image';
                    }
                    if($ext != 'gif' && $ext != 'webp' && count($compress_size) > 0) {
                        compress_image($filename."/".$name, $filename."/".$name, 80, 1440, 960);
                        compress_image($filename."/".$name, $filename."/".$name, 80, 1349, 402);
                        compress_image($filename."/".$name, $filename."/".$name, 80, 450, 250);
                    }
                }
                else {
                    $return_data['status'] = 'Failed';
                    $return_data['status_message'] =  trans('validation.mimes',['attribute' => 'Image','values'=>'Jpg,Jpeg,Png,Gif']);
                }

                $return_data['file_name'] = $name;
            }
        }
        return $return_data;
    }
}

/**
 * Upload Image to Cloudinary
 *
 * @return Array $return_data
 */
if (!function_exists('cloudUpload')) {
    function cloudUpload($file,$last_src="",$resouce_type="image")
    {
        $site_name = str_replace(".","",SITE_NAME);
        try {
            $options = [ 'folder' => $site_name.'/'];
            if($resouce_type=="video") {
                \Cloudder::uploadVideo($file, null, $options);
            }
            else {
                \Cloudder::upload($file, null, $options);
            }
            $c=\Cloudder::getResult();
            $data['status']="success";
            $data['message']=$c;
        }
        catch (\Exception $e) {
            $data['status'] = "error";
            if($e->getCode() == '400') {
                $data['message'] = trans('messages.profile.image_size_exceeds_10mb');
            }
            else {
                $data['message']= $e->getMessage();
            }
        }
        return $data;
    }
}

/**
 * Get Image function
 *
 * @param  string $name   Name of the Image
 * @param  string $path   Path of the Image
 * @param  integer $width   Width of the Image
 * @param  integer $height   Height of the Image
 */
if (!function_exists('getImage')) {

    function getImage($name, $path, $width = '', $height = '')
    {
        $url = siteUrl();

        $photo_src = explode('.',$name);
        if(count($photo_src) > 1) {
            $photo_details = pathinfo($name); 
            $image_name = @$photo_details['filename'].'.'.@$photo_details['extension'];
            $image_src = $url.$path.$image_name;
        }
        else {
            $options['secure']=TRUE;
            $options['crop']='fill';
            $image_src = \Cloudder::show($name,$options);
        }

        return $image_src;
    }
}


/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('site_settings')) {
    
    function site_settings($key) {
        $site_settings = resolve('site_settings');
        $site_setting = $site_settings->where('name',$key)->first();

        return optional($site_setting)->value ?? '';
    }
}

/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('api_credentials')) {
    
    function api_credentials($key, $site) {
        $api_credentials = resolve('api_credentials');
        $credentials = $api_credentials->where('name',$key)->where('site',$site)->first();

        return optional($credentials)->value ?? '';
    }
}

/**


/**
 * Process CURL With POST
 *
 * @param  String $url  Url
 * @param  Array $params  Url Parameters
 * @return string $data Response of URL
 */
if (!function_exists('curlPost')) {

    function curlPost($url,$params)
    {
        $curlObj = curl_init();

        curl_setopt($curlObj,CURLOPT_URL,$url);
        curl_setopt($curlObj,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curlObj,CURLOPT_HEADER, false); 
        curl_setopt($curlObj,CURLOPT_POST, count($params));
        curl_setopt($curlObj,CURLOPT_POSTFIELDS, http_build_query($params));    
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'User-Agent: curl',
        ]);
        $output = curl_exec($curlObj);

        curl_close($curlObj);
        return json_decode($output,true);
    }
}

/**

/**
 * Get a Facebook Login URL
 *
 * @return URL from Facebook API
 */
if (!function_exists('getAppleLoginUrl')) {
    function getAppleLoginUrl()
    {
        $params = [
            'response_type'     => 'code',
            'response_mode'     => 'form_post',
            'client_id'         => api_credentials('service_id','Apple'),
            'redirect_uri'      => url('apple_callback'),
            'state'             => bin2hex(random_bytes(5)),
            'scope'             => 'name email',
        ];
        $authorize_url = 'https://appleid.apple.com/auth/authorize?'.http_build_query($params);

        return $authorize_url;
    }
}

/**
 * Generate Apple Client Secret
 *
 * @return String $token
 */
if (!function_exists('getAppleClientSecret')) {
    function getAppleClientSecret()
    {
        $key_file = public_path(api_credentials('key_file','Apple'));

        $algorithmManager = new AlgorithmManager([new ES256()]);
        $jwsBuilder = new JWSBuilder($algorithmManager);
        $jws = $jwsBuilder
            ->create()
            ->withPayload(json_encode([
                'iat' => time(),
                'exp' => time() + 86400*180,
                'iss' => api_credentials('team_id','Apple'),
                'aud' => 'https://appleid.apple.com',
                'sub' => api_credentials('service_id','Apple'),
            ]))
            ->addSignature(JWKFactory::createFromKeyFile($key_file), [
                'alg' => 'ES256',
                'kid' => api_credentials('key_id','Apple')
            ])
            ->build();

        $serializer = new CompactSerializer();
        $token = $serializer->serialize($jws, 0);
        
        return $token;
    }
}

/**
 * Check if a string is a valid timezone
 *
 * @param string $timezone
 * @return bool
 */
if (!function_exists('isValidTimezone')) {
    function isValidTimezone($timezone)
    {
        return in_array($timezone, timezone_identifiers_list());
    }
}

/**
 * File Get Content by using CURL
 *
 * @param  string $url  Url
 * @return string $data Response of URL
 */
if (!function_exists('file_get_contents_curl')) {

    function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);       

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

/** 
 * create time range 
 *  
 * @param mixed $start start time 
 * @param mixed $end   end time
 * @param string $interval time intervals, 1 hour, 1 mins, 1 secs, etc.
 * @param string $format time format, e.g., 12 or 24
 */
if (!function_exists('generateTimeRange')) {
    function generateTimeRange($start, $end, $interval = '30 mins', $format = '12')
    {
        $startTime = strtotime($start); 
        $endTime   = strtotime($end);
        $returnTimeFormat = ($format == '12')?'h:i A':'H:i A';

        $current   = time();
        $addTime   = strtotime('+'.$interval, $current); 
        $diff      = $addTime - $current;

        $times = array();
        while ($startTime < $endTime) {
            $times[date('H:i:s',$startTime)] = date($returnTimeFormat, $startTime); 
            $startTime += $diff; 
        }

        $times[date('H:i:s',$startTime)] = date($returnTimeFormat, $startTime); 
        return $times;
    }
}

/** 
 * create time range 
 *
 * @return Array $day_options
 */
if (!function_exists('getDayOptions')) {
    function getDayOptions()
    {
        $day_names = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $day_options = array();
        foreach ($day_names as $day_name) {
            $key = date('w',strtotime($day_name));
            $day_options[$key] = $day_name;
        }

        return $day_options;
    }
}

/** 
 * Convert date string to timestamp 
 *
 * @return Array $day_options
 */
if (!function_exists('custom_strtotime')) {
    function custom_strtotime($date, $prev_format = '')
    {
        if($prev_format == '') {
            if(PHP_DATE_FORMAT=="d/m/Y" || PHP_DATE_FORMAT=="m-d-Y") {
                $seperator=(PHP_DATE_FORMAT=="d/m/Y")? "/" : "-";
                $explode_date=explode($seperator,$date);
                if(count($explode_date)=="1") {
                    return strtotime($date);
                }
                $original_date=$explode_date[1].$seperator.$explode_date[0].$seperator.$explode_date[2];  
                return strtotime($original_date);
            }
            return strtotime($date);
        }
        $date_time = \DateTime::createFromFormat($prev_format, $date);
        return @$date_time->format('U');
    }
}

/** 
 * Get All other times 
 *
 * @return Array $times
 */
if (!function_exists('getTimes')) {
    function getTimes($start_time, $end_time, $timezone = 'UTC')
    {
        $start  = getDateObject(strtotime($start_time));
        $end    = getDateObject(strtotime($end_time));
        
        if($end_time == '23:59:00') {
            $end->addMinute();
        }

        $times = [];
        while($start->lte($end)) {
            $times[] = $start->copy()->format('H:i:s');
            $start->addHour();
        }
        if($end_time == '23:59:00') {
            array_pop($times);
            array_push($times, $end_time);
        }
        return $times;
    }
}

/**
 * Check given input is timestamp or not
 *
 * @param String|Timestamp $timestamp
 * @return Boolean
 */
if (!function_exists('isValidTimeStamp')) {
    function isValidTimeStamp($timestamp)
    {
        try {
            new DateTime('@'.$timestamp);
        }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}

/**
 * Get Carbon Date Object from Given date or timestamp
 *
 * @param String|Timestamp $date
 * @return Object $date_obj  instance of Carbon\Carbon
 */
if (!function_exists('getDateObject')) {
    function getDateObject($date, $timezone = 'UTC')
    {
        if(isValidTimeStamp($date)) {
            $date_obj = Carbon\Carbon::createFromTimestamp($date,$timezone);
        }
        else {
            $date_obj = Carbon\Carbon::createFromTimestamp(custom_strtotime($date),$timezone);
        }
        return $date_obj;
    }
}

/**
 * Get days between two dates
 *
 * @param date $startDate  Start Date
 * @param date $endDate    End Date
 * @return array $dates    Between two dates
 */
if (!function_exists('getDays')) {
    function getDays($startDate, $endDate)
    {
        $start  = getDateObject($startDate);
        $end    = getDateObject($endDate);

        $dates = [];
        while($start->lte($end)) {
            $dates[] = $start->copy()->format('Y-m-d');
            $start->addDay();
        }
        return $dates;
    }
}

/** 
 * Remove Email and Phone Number in given String
 *
 * @return String $message
 */
if (!function_exists('removeEmailNumber')) {
    function removeEmailNumber($message)
    {
        $replacement = "[removed]";

        $dots=".*\..*\..*";

        $email_pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
        $url_pattern = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+[\.][^\.\s]+[A-Za-z0-9\?\/%&=\?\-_]+/i";
        $phone_pattern = "/\+?[0-9][0-9()\s+]{4,20}[0-9]/";

        $find = array($email_pattern, $phone_pattern);
        $replace = array($replacement, $replacement);

        $message = preg_replace($find, $replace, $message);

        if($message == $dots) {
            $message = preg_replace($url_pattern, $replacement, $message);
        }
        else {
            $message = preg_replace($find, $replace, $message);
        }

        return $message;
    }
}

/**
 * Get Minimum Amount in Given Currency Code
 *
 * @param string $currency_code
 * @return Int $amount 
 */
if (!function_exists('getMinimumAmount')) {
    function getMinimumAmount($currency_code)
    {
        return currency_convert(DEFAULT_CURRENCY, $currency_code, MINIMUM_AMOUNT);
    }
}

/**
 * Convert Given Array To Object
 * @return Object
 */
if (!function_exists('arrayToObject')) {
    function arrayToObject($arr)
    {
        $arr = Arr::wrap($arr);
        return json_decode(json_encode($arr));
    }
}

/**
 * Convert Given Float To Nearest Half Integer
 * @return Int
 */
if (!function_exists('roundHalfInteger')) {
    function roundHalfInteger($value)
    {
        return floor($value * 2) / 2;
    }
}

/**
 * Get Date for Email Subject
 * @return Int
 */
if (!function_exists('getDatesSubject')) {
    function getDatesSubject($booking_date_times)
    {
        $booking_date_times = arrayToObject($booking_date_times);

        $start_date = date(PHP_DATE_FORMAT,strtotime($booking_date_times->start_date));
        $start_time = date('h:i A',strtotime($booking_date_times->start_time));
        $end_date = date(PHP_DATE_FORMAT,strtotime($booking_date_times->end_date));
        $end_time = date('h:i A',strtotime($booking_date_times->end_time));

        if($start_date != $end_date) {
            $dates_subject = $start_date .' '.$start_time.' - '. $end_date .' '.$end_time;
        }
        else {
            $dates_subject = $start_date .' ( '.$start_time.' - '.$end_time.' )';
        }

        return $dates_subject;
    }
}

    /**
     * Generate Reservation Code
     *
     * @param date $length  Code Length
     * @param date $seed    Reservation Id
     * @return string Reservation Code
     */
if (!function_exists('getCode')) {
    function getCode($length, $seed)
    {  
        $code = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "0123456789";

        mt_srand($seed);

        for($i=0;$i<$length;$i++) {
            $code .= $codeAlphabet[mt_rand(0,strlen($codeAlphabet)-1)];
        }

        return $code;
    }
}

/**
 * Check Current Environment
 *
 * @return Boolean true or false
 */
if (!function_exists('isLiveEnv')) {
    function isLiveEnv($environments = [])
    {
        if(count($environments) > 0) {
            array_push($environments, 'live');
            return in_array(env('APP_ENV'),$environments);
        }
        return env('APP_ENV') == 'live';
    }
}

/**
 * get protected String or normal based on env
 *
 * @param {string} $str
 *
 * @return {string}
 */
if (!function_exists('protectedString')) {
    
    function protectedString($str) {
        if(isLiveEnv()) {
            return substr($str, 0, 1) . '****' . substr($str,  -4);
        }
        return $str;
    }
}

if ( ! function_exists('updateEnvConfig')) {
    function updateEnvConfig($key, $value)
    {
        $path = app()->environmentFilePath();

        $escaped = preg_quote('='.env($key), '/');
        try {
            file_put_contents($path, preg_replace(
                "/^{$key}{$escaped}/m",
               "{$key}={$value}",
               file_get_contents($path)
            ));         
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}