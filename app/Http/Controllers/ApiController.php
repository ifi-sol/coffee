<?php

namespace App\Http\Controllers;

use App\CafeInfo;
use App\CafeQr;
use Illuminate\Http\Request;
use DB;
use Session;
use App\User;
use Mail;
use App\Mail\CoffeeMail;
use File;
use App\CafeAdmin;
use App\CafeQrScan;

class ApiController extends Controller
{
    public function __construct()
    {

        DB::enableQueryLog();

    }

    public function login(Request $request)
    {
        $user = new User();

        if (empty($request->input('email'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Email'));
        } elseif (empty($request->input('password'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your password'));
        } elseif (empty($request->input('device_type'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Device Type'));
        }

        $check_user = $user->where(['email' => $request->input('email'), 'password' => md5($request->input('password')), 'user_type' => 'customer'])->first();
        //echo "<pre>";print_r(DB::getQueryLog());exit;
        if (empty($check_user)) {
            return response()->json(array('status' => false, 'message' => 'Invalid Email or Password'));
        } elseif ($check_user->status == 'pending') {
            return response()->json(array('status' => false, 'message' => 'Your account needs admin approval, after that you can login to your account'));
        } elseif ($check_user->status == 'terminated') {
            return response()->json(array('status' => false, 'message' => 'Your account terminated by  admin, for more detail contact admin'));
        }
        $data = array(
            'device_type' => $request->input('device_type'),
            'last_login_date' => date("Y-m-d H:i:s")
        );
        $data['device_token'] = ($request->input('device_token')) ? $request->input('device_token') : '';

        $response = $user->where('user_id', $check_user->user_id)->update($data);

        $result = $user->where('user_id', $check_user->user_id)->first();

        if ($response == true) {
            $qr_scan = new CafeQrScan();
            $free = $qr_scan->where(['buyer_id' => $result->user_id, 'free_status' => 'awarded'])->count();
            $visits = DB::table('cafe_qr_scanned')->where('buyer_id', $result->user_id)->groupby('cafe_id')->get();

            // In-App Purchase
            $check_subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id, 'status' => 'active'])->orderby('id','desc')->first();
            if(count($check_subscription) > 0){
                $annual_days = $this->get_days(date('Y-m-d H:i:s'),$check_subscription->created_at);
                if($annual_days > 365){
                    DB::table('users_subscription')->where(['user_id' => $result->user_id])->update(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }

            $days = $this->get_days(date('Y-m-d H:i:s'),$result->created_at);
            if($days > 30){
                $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $result->is_subscribed = true;
                }else{
                    if(count($subscription)){
                        $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                        $result->is_subscribed = ($free_days > 30) ? false : true;
                        $result->free_days = 30 - $free_days;
                    }else {
                        $result->is_subscribed = false;
                    }
                }
            }else{
                $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $result->is_subscribed = true;
                }else{
                    $result->free_days = 30 - $days;
                    $result->is_subscribed = true;
                }
            }

            //Check is Fresh User
            $check_fresh = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();
            $result->is_fresh_user = (count($check_fresh) ==0) ? true : false;

            unset($result->updated_at, $result->created_at, $result->last_login_date, $result->is_password_requested, $result->password);
            $promo_state = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
            $result->state = ($promo_state->value == 'yes') ? true : false;
            return response()->json(array('status' => true, 'message' => 'User Login Successfully!!', 'total_free_coffee' => $free, 'cafe_visits' => count($visits), 'response' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Some Server Error Occurred'));
        }
    }

    public function register(Request $request)
    {
        $user = new User();
        if (empty($request->input('first_name')) || is_numeric($request->input('first_name'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Valid First Name'));
        } elseif (empty($request->input('last_name')) || is_numeric($request->input('last_name'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Valid Last Name'));
        } elseif (empty($request->input('email'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Email'));
        } elseif (empty($request->input('password'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Password'));
        } elseif (strlen($request->input('password')) < 6) {
            return response()->json(array('status' => false, 'message' => 'Your password must consist of 6 characters'));
        } elseif (empty($request->input('user_type')) || is_numeric($request->input('user_type'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Valid User Type'));
        } elseif (empty($request->input('photo')) || is_numeric($request->input('photo'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Valid photo'));
        } elseif (empty($request->input('device_type'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Device Type'));
        }elseif (empty($request->input('user_signup_status'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Signup Status'));
        }

        $check_email = $user->where('email', $request->input('email'))->first();

        if ((!empty($check_email))) {
            return response()->json(array('status' => false, 'message' => 'Email is already Exists'));
        }

        $data = array(
            'first_name' => htmlspecialchars($request->input('first_name')),
            'last_name' => htmlspecialchars($request->input('last_name')),
            'email' => $request->input('email'),
            'password' => md5($request->input('password')),
            'user_type' => strtolower($request->input('user_type')),
            'device_type' => strtoupper($request->input('device_type')),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'last_login_date' => date("Y-m-d H:i:s"),
            'status' => 'active',
            'is_admin_approved' => 'yes',
            'user_signup_status' => strtolower($request->input('user_signup_status'))
        );

        if ($request->has('phone')) {
            if (is_numeric($request->input('phone')) == false) {
                return response()->json(array('status' => false, 'message' => 'Please Enter Valid Your Phone'));
            }
            $data['phone'] = $request->input('phone');
        } else {
            $data['phone'] = '';
        }
        $data['device_token'] = ($request->input('device_token')) ? $request->input('device_token') : '';
        if (!empty($request->input('photo'))) {
            define('UPLOAD_DIR', public_path() . '/images/');
            $image = base64_decode($request->input('photo'));
            $file = UPLOAD_DIR . md5(date('Y-m-d H:i:s')) . '.jpg';
            file_put_contents($file, $image);
            $data['photo'] = str_replace(public_path() . '/images/', '', $file);
        } else {
            $data['photo'] = '';
        }

        $response = $user->insertGetId($data);

        $get_user = $user->where('user_id', $response)->first();
        unset($get_user->updated_at, $get_user->created_at, $get_user->last_login_date, $get_user->is_password_requested, $get_user->password);
        if ($response == true) {

            $qr_scan = new CafeQrScan();
            $free = $qr_scan->where(['buyer_id' => $get_user->user_id, 'free_status' => 'awarded'])->count();
            $visits = DB::table('cafe_qr_scanned')->where('buyer_id', $get_user->user_id)->groupby('cafe_id')->get();

            // In-App Purchase
            $check_subscription = DB::table('users_subscription')->where(['user_id' => $get_user->user_id, 'status' => 'active'])->orderby('id','desc')->first();
            if(count($check_subscription) > 0){
                $annual_days = $this->get_days(date('Y-m-d H:i:s'),$check_subscription->created_at);
                if($annual_days > 365){
                    DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->update(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }

            $days = $this->get_days(date('Y-m-d H:i:s'),$get_user->created_at);
            if($days > 30){
                $subscription = DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $get_user->is_subscribed = true;
                }else{
                    if(count($subscription)){
                        $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                        $get_user->is_subscribed = ($free_days > 30) ? false : true;
                        $get_user->free_days = 30 - $free_days;
                    }else {
                        $get_user->is_subscribed = false;
                    }
                }
            }else{
                $subscription = DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $get_user->is_subscribed = true;
                }else{
                    $get_user->free_days = 30 - $days;
                    $get_user->is_subscribed = true;
                }
            }

            $check_fresh = DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->orderby('id','desc')->first();
            $get_user->is_fresh_user = (count($check_fresh) ==0) ? true : false;
            $promo_state = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
            $get_user->state = ($promo_state->value == 'yes') ? true : false;
            return response()->json(array('status' => true, 'message' => 'User Login Successfully!!', 'total_free_coffee' => $free, 'cafe_visits' => count($visits), 'response' => $get_user));

        } else {
            return response()->json(array('status' => false, 'message' => 'Some Server Error Occurred'));
        }

    }

    public function forgot_password(Request $request)
    {
        $user = new User();
        if (empty($request->input('email'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter your email address'));
        }

        $check = $user->where(['email' => $request->input('email'), 'status' => 'active'])->first();
        if (count($check) == 0) {
            return response()->json(array('status' => false, 'message' => "The email address '" . $request->input("email") . "' is not registered with Coffee Cup. Please Try again."));
        }
        if ($check->user_signup_status == 'facebook') {
            return response()->json(array('status' => false, 'message' => "Your were Using CupCard via Facebook. We Cannot accept your forgot password request."));
        }
        $new_password = $this->generate_password(6);
        $user->where('email', $request->input("email"))->update(['is_password_requested' => 'yes', 'password' => md5($new_password)]);
        $check->for = 'forgot_password';
        $check->subject = 'Forgot Password';
        $check->new_password = $new_password;
        Mail::to($check->email)->send(new CoffeeMail($check));
        return response()->json(array('status' => true, 'message' => 'Please Check Your Email. We sent you an email with instructions to reset your password.'));
    }

    public function change_password(Request $request){
        $user = new User();
        // Check Paramerters
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }
        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }elseif(empty($request->input('old_password'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter your current password'));
        }elseif(md5($request->input('old_password')) != $check_user->password){
            return response()->json(array('status'    => false,   'message'   => 'Your Current Password is wrong'));
        }elseif(empty($request->input('new_password'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your New Password'));
        }elseif(strlen($request->input('new_password')) < 6){
            return response()->json(array('status'    => false,   'message'   => 'Your password must consist of 6 characters'));
        }

        $response = $user->where('user_id',$request->input('user_id'))->update(array('password'=> md5($request->input('new_password'))));
        if($response == true){
            return response()->json(array('status'    => true,   'message'   => 'Password Changed Successfully!!'));
        }else{
            return response()->json(array('status'    => false,   'message'   => 'Some Server Error Occurred'));
        }
    }

    public function update_profile(Request $request){
        $user = new User();
        // Check Paramerters
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }
        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }elseif(empty($request->input('first_name')) || is_numeric($request->input('first_name'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Valid First Name'));
        }elseif(empty($request->input('last_name')) || is_numeric($request->input('last_name'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Valid Last Name'));
        }elseif(empty($request->input('device_type'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your Device Type'));
        }

        $data = array(
            'first_name'    => $request->input('first_name'),
            'last_name'    => $request->input('last_name'),
            'phone'    => $request->input('phone'),
            'device_type'    => strtoupper($request->input('device_type')),
        );

        $data['device_token'] = ($request->input('device_token')) ? $request->input('device_token') : '';

        if ($request->has('phone')) {
            if (is_numeric($request->input('phone')) == false) {
                return response()->json(array('status' => false, 'message' => 'Please Enter Valid Your Phone'));
            }
            $data['phone'] = $request->input('phone');
        } else {
            $data['phone'] = '';
        }

        $response = $user->where('user_id',$request->input('user_id'))->update($data);
        // Getting User updated response
        $result = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();

        $qr_scan = new CafeQrScan();
        $free = $qr_scan->where(['buyer_id' => $result->user_id, 'free_status' => 'awarded'])->count();
        $visits = DB::table('cafe_viewed')->where('user_id', $result->user_id)->get();

        // In-App Purchase
        $check_subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id, 'status' => 'active'])->orderby('id','desc')->first();
        if(count($check_subscription) > 0){
            $annual_days = $this->get_days(date('Y-m-d H:i:s'),$check_subscription->created_at);
            if($annual_days > 365){
                DB::table('users_subscription')->where(['user_id' => $result->user_id])->update(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }

        $days = $this->get_days(date('Y-m-d H:i:s'),$result->created_at);
        if($days > 30){
            $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $result->is_subscribed = true;
            }else{
                if(count($subscription)){
                    $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                    $result->is_subscribed = ($free_days > 30) ? false : true;
                    $result->free_days = 30 - $free_days;
                }else {
                    $result->is_subscribed = false;
                }
            }
        }else{
            $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $result->is_subscribed = true;
            }else{
                $result->free_days = 30 - $days;
                $result->is_subscribed = true;
            }
        }

        unset($result->updated_at, $result->created_at, $result->last_login_date, $result->is_password_requested, $result->password);
        return response()->json(array('status'    => true,   'message'   => 'User profile updated Successfully!!', 'total_free_coffee' => $free, 'cafe_visits' => count($visits), 'response' => $result));
    }

    public function get_user_profile(Request $request){
        $user = new User();
        // Check Paramerters
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter user id'));
        }
        $check = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $result = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();

        $qr_scan = new CafeQrScan();
        $free = $qr_scan->where(['buyer_id' => $result->user_id, 'free_status' => 'awarded'])->count();
        $visits = DB::table('cafe_viewed')->where('user_id', $result->user_id)->get();

        // In-App Purchase
        $check_subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id, 'status' => 'active'])->orderby('id','desc')->first();
        if(count($check_subscription) > 0){
            $annual_days = $this->get_days(date('Y-m-d H:i:s'),$check_subscription->created_at);
            if($annual_days > 365){
                DB::table('users_subscription')->where(['user_id' => $result->user_id])->update(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }

        $days = $this->get_days(date('Y-m-d H:i:s'),$result->created_at);
        if($days > 30){
            $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $result->is_subscribed = true;
            }else{
                if(count($subscription)){
                    $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                    $result->is_subscribed = ($free_days > 30) ? false : true;
                    $result->free_days = 30 - $free_days;
                }else {
                    $result->is_subscribed = false;
                }
            }
        }else{
            $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $result->is_subscribed = true;
            }else{
                $result->free_days = 30 - $days;
                $result->is_subscribed = true;
            }
        }

        //Check is Fresh User
        $check_fresh = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();
        $result->is_fresh_user = (count($check_fresh) ==0) ? true : false;

        unset($result->updated_at, $result->created_at, $result->last_login_date, $result->is_password_requested, $result->password);
        $promo_state = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
        $result->state = ($promo_state->value == 'yes') ? true : false;
        return response()->json(array('status'    => true,   'message'   => "profile fetched Successfully!!", 'response' => $result, 'total_free_coffee' => $free, 'cafe_visits' => count($visits), 'response' => $result));
    }

    public function update_profile_image(Request $request){
        $user = new User();
        // Check Paramerters
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }
        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }
        if(empty($request->input('device_type'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your Device Type'));
        }

        if(!empty($request->input('photo'))){
            define('UPLOAD_DIR', public_path().'/images/');
            $image = base64_decode($request->input('photo'));
            $file = UPLOAD_DIR . md5(date('Y-m-d H:i:s')).'.jpg';
            file_put_contents($file, $image);
            if (File::exists(public_path() . '/images/' . $check_user->photo)){
                unlink(public_path() . '/images/' . $check_user->photo);
            }
            $data['photo'] = str_replace(public_path().'/images/', '', $file);
        }else{
            $data['photo'] = $check_user->photo;
        }

        $data['device_token'] = ($request->input('device_token')) ? $request->input('device_token') : '';

        $response = $user->where('user_id',$request->input('user_id'))->update($data);
        // Getting User updated response
        $result = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        unset($result->updated_at, $result->created_at, $result->last_login_date, $result->is_password_requested, $result->password);
        return response()->json(array('status'    => true,   'message'   => 'User profile Image updated Successfully!!', 'response' => $result));
    }

    public function get_cafe_list(Request $request){
        $user = new User();
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }elseif(empty($request->input('latitude'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your Latitude'));
        }elseif(empty($request->input('longitude'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your Longitude'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $circle_radius = 200;
        $max_distance = 200;
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        $response = DB::select('SELECT * FROM
                    (SELECT ci.cafe_id,cafe_name,ci.description,ci.street_address AS cafe_street_address,region AS cafe_region,city As cafe_city,country As cafe_country,post_code AS cafe_post_code,ci.photo AS cafe_photo,latitude As cafe_latitude,longitude AS cafe_longitude,website AS cafe_website,ci.phone AS cafe_phone,ci.email AS cafe_email,u.user_id,first_name,last_name,u.email,user_type,u.phone,u.photo,user_signup_status,status, 111.045 * DEGREES(ACOS(COS(RADIANS('.$lat.'))
            * COS(RADIANS(latitude))
            * COS(RADIANS(longitude) - RADIANS('.$lng.'))
            + SIN(RADIANS('.$lat.'))
            * SIN(RADIANS(latitude))))
            AS distance
            FROM cafe_info ci
            JOIN cafe_admin ca ON ci.cafe_id = ca.cafe_id
            JOIN users u ON u.user_id = ca.user_id WHERE u.status = "active" AND u.is_admin_approved = "yes") AS distances
                WHERE distance < ' . $max_distance . '
                ORDER BY distance;
            ');


        if(count($response) > 0){
            foreach ($response as $key => $value){
                $timings = DB::table('cafe_timings')->select('day','start_time','end_time','is_close')->where('cafe_id',$value->cafe_id)->get();
                $timings = $this->sort_cafe_timings($timings);
                $response[$key]->schedule_available = (count($timings) > 0) ? true : false;
                $response[$key]->timings = $timings;
            }
        }

        return response()->json(array('status'    => true,   'message'   => 'Cafe List Fetched Successfully.', 'response' => $response));
    }

    public function sort_cafe_timings($check_timings){
        $timings = array();

        $days = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ];

        if(count($check_timings) > 0){
            foreach ($check_timings as $key => $value){
                $index = array_search(ucfirst($value->day), $days);
                //echo $value->day.'/'.$index;exit;
                $time = array(
                    'day' => ucfirst($value->day),
                    'start_time' => $value->start_time,
                    'end_time' => $value->end_time,
                    'is_close' => $value->is_close
                );
                array_push($timings,$time);
            }

            foreach($timings as $k=>$v) {
                $key = array_search(ucfirst($v['day']), $days);
                if($key !== FALSE) {
                    $timings[$key] = $v;
                }
            }
        }else {
            $timings = array();
        }


        return $timings;
    }

    public function get_cafe_detail(Request $request){
        $user = new User();
        $cafe = new CafeAdmin();
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }elseif(empty($request->input('cafe_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Cafe ID'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $response = DB::table('cafe_info AS ci')->select('ci.cafe_id','ci.description','cafe_name','ci.street_address AS cafe_street_address','region AS cafe_region','city As cafe_city','country As cafe_country','post_code AS cafe_post_code','ci.photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','website AS cafe_website','ci.phone AS cafe_phone','ci.email AS cafe_email','u.user_id','first_name','last_name','u.email','user_type','u.phone','u.photo','user_signup_status','status')
            ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
            ->join('users AS u','u.user_id', '=', 'ca.user_id')
            ->where('ci.cafe_id',$request->input('cafe_id'))->first();

        $timings = DB::table('cafe_timings')->select('day','start_time','end_time','is_close')->where('cafe_id',$request->input('cafe_id'))->get();
        $response->schedule_available = (count($timings) > 0) ? true : false;
        $response->timings = $timings;

        /*DB::table('cafe_viewed')->insert([
            'cafe_id'       => $request->input('cafe_id'),
            'user_id'       => $request->input('user_id'),
            'viewed_at'     => date('Y-m-d H:i:s')
        ]);*/

        return response()->json(array('status'    => true,   'message'   => 'Cafe Detail Fetched Successfully.'));

    }

    public function scan_qr(Request $request){
        $qr_scan = new CafeQrScan();
        $cafe_qr = new CafeQr();
        $user = new User();
        $cafe_info = new CafeInfo();

        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }elseif(empty($request->input('cafe_qr'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Cafe Qr ID'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $check_qr = $cafe_qr->where(array('qr_random_id'=>$request->input('cafe_qr')))->first();
        $get_cafe_user = DB::table('cafe_admin')->where('cafe_id',$check_qr->cafe_id)->first();
        $check_status = $user->where(['user_id' => $request->input('user_id'), 'is_admin_approved' => 'yes', 'status' => 'active'])->first();
        $check_cafe_status = $user->where(['user_id' => $get_cafe_user->user_id, 'is_admin_approved' => 'yes', 'status' => 'active'])->first();

        if(count($check_cafe_status) == 0){
            return response()->json(array('status'  => false,   'message'   => 'You are not allowed to Scan Cafe QR. This Cafe is blocked by Administrator'));
        }

        if(count($check_status) == 0){
            return response()->json(array('status'  => false,   'message'   => 'Your account is currently blocked, please contact CupCard to resolve this issue'));
        }

        if(empty($check_qr)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid Cafe QR'));
        }elseif($check_qr->status == 'expire'){
            return response()->json(array('status'    => false,   'message'   => 'Your Cafe Qr is Expired. Please Scan Latest Qr'));
        }

        $check_user_status = $this->check_user_status($request->input('user_id'));

        if($check_user_status->is_subscribed == true){
            if(isset($check_user_status->free_days) && $check_user_status->free_days == 0){
                return response()->json(array('status'    => false,   'message'   => 'Your trial period is over. You need to Subscribe to avail CupCard'));
            }else{
                $data = array(
                    'buyer_id'      => $request->input('user_id'),
                    'cafe_id'       => $check_qr->cafe_id,
                    'visited_at'    => date('Y-m-d H:i:s'),
                    'winning_at'    => date('Y-m-d H:i:s'),
                    'award_at'      => date('Y-m-d H:i:s'),
                );

                $count_qr = $qr_scan->where(['buyer_id' => $request->input('user_id'), 'scan_status' => 'active'])->count();
                //echo $count_qr;exit;
                if($count_qr == 9){
                    $data['free_status'] = 'awarded';
                    $data['scan_status'] = 'expired';
                    $qr_scan->where(['buyer_id' => $request->input('user_id')])->update(['scan_status' => 'expired']);
                }elseif($count_qr == 8){
                    $data['free_status'] = 'won';
                    $data['scan_status'] = 'active';
                }else{
                    $data['free_status'] = 'purchased';
                    $data['scan_status'] = 'active';
                }

                $qr_scan->insert($data);
                $free = $qr_scan->where(['buyer_id' => $request->input('user_id'), 'free_status' => 'awarded'])->count();
                $total = $qr_scan->where(['buyer_id' => $request->input('user_id'),'scan_status' => 'active'])->count();

                DB::table('cafe_viewed')->insert([
                    'cafe_id'       => $check_qr->cafe_id,
                    'user_id'       => $request->input('user_id'),
                    'viewed_at'     => date('Y-m-d H:i:s')
                ]);
                return response()->json(array('status'    => true,   'message'   => 'Cafe Qr Scanned Successfully.', 'total_free_coffee' => $free, 'total_coffee' => $total));
            }
        }else{
            return response()->json(array('status'    => false,   'message'   => 'Your Subscription is expired. You need to re-new your Subscription to avail CupCard'));
        }
    }


    public function get_coffee_card(Request $request){
        $qr_scan = new CafeQrScan();
        $user = new User();
        $cafe_info = new CafeInfo();

        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $scan_coffee = $qr_scan->where(['buyer_id' => $request->input('user_id'), 'scan_status' => 'active'])->orderBy('qr_scan_id','asc')->get();
        $response = array();
        if(count($scan_coffee) > 0){
            foreach ($scan_coffee as $coffee){
                $results = DB::table('cafe_info AS ci')->select('ci.cafe_id','ci.description','cafe_name','ci.street_address AS cafe_street_address','region AS cafe_region','city As cafe_city','country As cafe_country','post_code AS cafe_post_code','ci.photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','website AS cafe_website','ci.phone AS cafe_phone','ci.email AS cafe_email','u.user_id','first_name','last_name','u.email','user_type','u.phone','u.photo','user_signup_status','status')
                    ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
                    ->join('users AS u','u.user_id', '=', 'ca.user_id')
                    ->where('ci.cafe_id',$coffee->cafe_id)->first();

                $timings = DB::table('cafe_timings')->select('day','start_time','end_time','is_close')->where('cafe_id',$coffee->cafe_id)->get();
                $timings = $this->sort_cafe_timings($timings);
                $results->schedule_available = (count($timings) > 0) ? true : false;
                $results->timings = $timings;

                $response[] = $results;
                //$cafe_info->select('cafe_id','cafe_name','street_address AS cafe_street_address','region AS cafe_region','city As cafe_city','country As cafe_country','post_code AS cafe_post_code','photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','phone AS cafe_phone','email AS cafe_email')->where('cafe_id',$coffee->cafe_id)->first();
            }
        }

        return response()->json(array('status'    => true,   'message'   => 'Coffee Card List Fetched Successfully.', 'response' => $response));
    }

    public function coffee_awards(Request $request){
        $qr_scan = new CafeQrScan();
        $user = new User();
        $cafe_info = new CafeInfo();

        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $free = $qr_scan->where(['buyer_id' => $request->input('user_id'), 'free_status' => 'awarded'])->count();

        //Check is Fresh User
        $check_fresh = DB::table('users_subscription')->where(['user_id' => $request->input('user_id')])->orderby('id','desc')->first();
        $is_fresh_user = (count($check_fresh) ==0) ? true : false;

        $days = $this->get_days(date('Y-m-d H:i:s'),$check_user->created_at);
        $free_days = 0;
        if($days > 30){
            $subscription = DB::table('users_subscription')->where(['user_id' => $request->input('user_id')])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $is_subscribed = true;
            }else{
                if(count($subscription)){
                    $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                    $is_subscribed = ($free_days > 30) ? false : true;
                    $free_days = 30 - $free_days;
                }else {
                    $is_subscribed = false;
                }
            }
        }else{
            $subscription = DB::table('users_subscription')->where(['user_id' => $request->input('user_id')])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $is_subscribed = true;
            }else{
                $free_days = 30 - $days;
                $is_subscribed = true;
            }
        }

        $promo_state = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
        $state = ($promo_state->value == 'yes') ? true : false;
        return response()->json(array('status'    => true,   'message'   => 'Coffee Award Count Fetched Successfully.', 'total_free_coffee' => $free, 'is_fresh_user' => $is_fresh_user, 'is_subscribed' => $is_subscribed, 'free_days' => $free_days, 'state' => $state));
    }

    public function generate_password($length = 10)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public function user_subscription(Request $request){
        $user = new User();
        if(empty($request->input('user_id'))){
            return response()->json(array('status'    => false,   'message'   => 'Please Enter Your user id'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $check_status = $user->where(['user_id' => $request->input('user_id'), 'is_admin_approved' => 'yes', 'status' => 'active'])->first();
        if(count($check_status) == 0){
            return response()->json(array('status'  => false,   'message'   => 'Your account is currently blocked, please contact CupCard to resolve this issue'));
        }

        $subscription = DB::table('users_subscription')->where(['user_id' => $request->input('user_id'), 'status' => 'active'])->first();
        if(count($subscription) > 0){
            DB::table('users_subscription')->where('user_id', $request->input('user_id'))->update(['status' => 'expired']);
        }
        DB::table('users_subscription')->insert([
            'user_id'       => $request->input('user_id'),
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        return response()->json(array('status'    => true,   'message'   => 'Customer Subscribed Successfully.'));
    }

    public function get_days($date1, $date2)
    {
        $datetime1 = new \DateTime($date1);
        $datetime2 = new \DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $counter = $interval->format('%a');

        return $counter;
    }

    public function check_is_facebook_user(Request $request){
        $user = new User();
        if (empty($request->input('email'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Email'));
        } elseif (empty($request->input('user_type')) || is_numeric($request->input('user_type'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Valid User Type'));
        } elseif (empty($request->input('device_type'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Device Type'));
        }elseif (empty($request->input('user_signup_status'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your Signup Status'));
        }

        $check_email = $user->where('email', $request->input('email'))->first();

        // If Already Facebook Logged in
        if ((!empty($check_email))) {
            $result = $user->where(array('user_id'=>$check_email->user_id,'user_type'=>'customer'))->first();
            $qr_scan = new CafeQrScan();
            $free = $qr_scan->where(['buyer_id' => $check_email->user_id, 'free_status' => 'awarded'])->count();
            $visits = DB::table('cafe_viewed')->where('user_id', $result->user_id)->get();

            // In-App Purchase
            $check_subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id, 'status' => 'active'])->orderby('id','desc')->first();
            if(count($check_subscription) > 0){
                $annual_days = $this->get_days(date('Y-m-d H:i:s'),$check_subscription->created_at);
                if($annual_days > 365){
                    DB::table('users_subscription')->where(['user_id' => $result->user_id])->update(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }

            $days = $this->get_days(date('Y-m-d H:i:s'),$result->created_at);
            if($days > 30){
                $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $result->is_subscribed = true;
                }else{
                    if(count($subscription)){
                        $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                        $result->is_subscribed = ($free_days > 30) ? false : true;
                        $result->free_days = 30 - $free_days;
                    }else {
                        $result->is_subscribed = false;
                    }
                }
            }else{
                $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $result->is_subscribed = true;
                }else{
                    $result->free_days = 30 - $days;
                    $result->is_subscribed = true;
                }
            }
            $check_fresh = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();
            $result->is_fresh_user = (count($check_fresh) ==0) ? true : false;

            unset($result->updated_at, $result->created_at, $result->last_login_date, $result->is_password_requested, $result->password);
            $promo_state = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
            $result->state = ($promo_state->value == 'yes') ? true : false;
            return response()->json(array('status'    => true,   'message'   => "User Login form Facebook Successfully!!", 'response' => $result, 'total_free_coffee' => $free, 'cafe_visits' => count($visits)));

        }

        // Register new as Facebbok Login
        $data = array(
            'first_name' => ($request->has('first_name')) ? htmlspecialchars($request->input('first_name')) : '',
            'last_name' => ($request->has('last_name')) ? htmlspecialchars($request->input('last_name')) : '',
            'email' => $request->input('email'),
            'password' => '',
            'user_type' => strtolower($request->input('user_type')),
            'device_type' => strtoupper($request->input('device_type')),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'last_login_date' => date("Y-m-d H:i:s"),
            'status' => 'active',
            'is_admin_approved' => 'yes',
            'user_signup_status' => strtolower($request->input('user_signup_status'))
        );

        if ($request->has('phone')) {
            if (is_numeric($request->input('phone')) == false) {
                return response()->json(array('status' => false, 'message' => 'Please Enter Valid Your Phone'));
            }
            $data['phone'] = $request->input('phone');
        } else {
            $data['phone'] = '';
        }
        $data['device_token'] = ($request->input('device_token')) ? $request->input('device_token') : '';
        if (!empty($request->input('photo'))) {
            define('UPLOAD_DIR', public_path() . '/images/');
            $image = base64_decode($request->input('photo'));
            $file = UPLOAD_DIR . md5(date('Y-m-d H:i:s')) . '.jpg';
            file_put_contents($file, $image);
            $data['photo'] = str_replace(public_path() . '/images/', '', $file);
        } else {
            $data['photo'] = '';
        }

        $response = $user->insertGetId($data);

        $get_user = $user->where('user_id', $response)->first();
        unset($get_user->updated_at, $get_user->created_at, $get_user->last_login_date, $get_user->is_password_requested, $get_user->password);
        if ($response == true) {

            $qr_scan = new CafeQrScan();
            $free = $qr_scan->where(['buyer_id' => $get_user->user_id, 'free_status' => 'awarded'])->count();
            $visits = DB::table('cafe_qr_scanned')->where('buyer_id', $get_user->user_id)->groupby('cafe_id')->get();

            // In-App Purchase
            $check_subscription = DB::table('users_subscription')->where(['user_id' => $get_user->user_id, 'status' => 'active'])->orderby('id','desc')->first();
            if(count($check_subscription) > 0){
                $annual_days = $this->get_days(date('Y-m-d H:i:s'),$check_subscription->created_at);
                if($annual_days > 365){
                    DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->update(['status' => 'expired', 'updated_at' => date('Y-m-d H:i:s')]);
                }
            }

            $days = $this->get_days(date('Y-m-d H:i:s'),$get_user->created_at);
            if($days > 30){
                $subscription = DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $get_user->is_subscribed = true;
                }else{
                    if(count($subscription)){
                        $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                        $get_user->is_subscribed = ($free_days > 30) ? false : true;
                        $get_user->free_days = 30 - $free_days;
                    }else {
                        $get_user->is_subscribed = false;
                    }
                }
            }else{
                $subscription = DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->orderby('id','desc')->first();

                if(count($subscription) > 0 && $subscription->status == 'active'){
                    $get_user->is_subscribed = true;
                }else{
                    $get_user->free_days = 30 - $days;
                    $get_user->is_subscribed = true;
                }
            }

            $check_fresh = DB::table('users_subscription')->where(['user_id' => $get_user->user_id])->orderby('id','desc')->first();
            $get_user->is_fresh_user = (count($check_fresh) ==0) ? true : false;
            $promo_state = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
            $get_user->state = ($promo_state->value == 'yes') ? true : false;
            return response()->json(array('status' => true, 'message' => 'User Signup with facebook Successfully!!', 'total_free_coffee' => $free, 'cafe_visits' => count($visits), 'response' => $get_user));

        } else {
            return response()->json(array('status' => false, 'message' => 'Some Server Error Occurred'));
        }
    }

    public function utilize_promo(Request $request){
        $user = new User();
        if (empty($request->input('user_id'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Your User ID'));
        } elseif (empty($request->input('promo'))) {
            return response()->json(array('status' => false, 'message' => 'Please Enter Valid Promo Code'));
        }

        $check_user = $user->where(array('user_id'=>$request->input('user_id'),'user_type'=>'customer'))->first();
        if(empty($check_user)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid User ID'));
        }

        $check_status = $user->where(['user_id' => $request->input('user_id'), 'is_admin_approved' => 'yes', 'status' => 'active'])->first();
        if(count($check_status) == 0){
            return response()->json(array('status'  => false,   'message'   => 'Your account is currently blocked, please contact CupCard to resolve this issue'));
        }

        $check_promo = DB::table('promo_codes')->where(array('promo_code'=>$request->input('promo'),'status'=>'unUsed'))->first();
        if(empty($check_promo)){
            return response()->json(array('status'    => false,   'message'   => 'Invalid Promo Code'));
        }

        DB::table('promo_codes')->where(['promo_code'=>$request->input('promo')])->update(['status'=>'used', 'utilize_date' => date('Y-m-d H:i:s')]);
        DB::table('users_subscription')->insert([
            'user_id'           => $request->input('user_id'),
            'status'            => 'active',
            'promo_code_id'     => $check_promo->promo_code_id,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        return response()->json(array('status'    => true,   'message'   => 'Promo Code Utilized Successfully!'));
    }


    public function check_user_status($user_id){
        $user = new User();
        $result = $user->where(array('user_id'=>$user_id,'user_type'=>'customer'))->first();
        $days = $this->get_days(date('Y-m-d H:i:s'),$result->created_at);
        if($days > 30){
            $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $result->is_subscribed = true;
            }else{
                if(count($subscription)){
                    $free_days = $this->get_days(date('Y-m-d H:i:s'),$subscription->updated_at);
                    $result->is_subscribed = ($free_days > 30) ? false : true;
                    $result->free_days = 30 - $free_days;
                }else {
                    $result->is_subscribed = false;
                }
            }
        }else{
            $subscription = DB::table('users_subscription')->where(['user_id' => $result->user_id])->orderby('id','desc')->first();

            if(count($subscription) > 0 && $subscription->status == 'active'){
                $result->is_subscribed = true;
            }else{
                $result->free_days = 30 - $days;
                $result->is_subscribed = true;
            }
        }

        return $result;
    }


}
