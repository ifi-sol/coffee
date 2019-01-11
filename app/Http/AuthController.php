<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use Mail;
use App\Mail\CoffeeMail;

class AuthController extends Controller
{
    public function __construct() {

    }

    public function admin_login(Request $request){
        $user = new User();

        if (Session::has('Coffee_Admin_Logged_in')) {
            return redirect('/admin/dashboard');
        }
        if($request->isMethod('get')){
            return view('admin.login');
        } else {
            if(empty($request->input('email'))){
                return response()->json(array('status'  => false,   'message'   => 'Please Enter your Email'));
            }elseif(empty($request->input('password'))){
                return response()->json(array('status'  => false,   'message'   => 'Please Enter your Password'));
            }
            $email = $request->input('email');
            $password = $request->input('password');
            $user = $user->where(['email' => $email, 'password' => md5($password), 'user_type' => 'admin'])->first();
            if(empty($user) && empty($vendor)){
                return response()->json(array('status'  => false,   'message'   => 'Email or Password is wrong'));
            }
            if(!empty($user)){
                if($user->status == 'pending'){
                    return response()->json(array('status'  => false,   'message'   => 'You will be allowed to login after screening, please wait,  will be updated shortly via eMail after approval. Thankyou'));
                }

                $session_data = array(
                    'Logged_in'     => true,
                    'user_id'       => $user->id,
                    'first_name'    => $user->first_name,
                    'last_name'     => $user->last_name,
                    'email'         => $user->email,
                    'picture'       => $user->photo,
                    'user_type'     => $user->user_type
                );
                Session::put('Coffee_Admin_Logged_in', $session_data);
                return response()->json(array('status'  => true,   'message'   => 'Email or Password is wrong'));
            }
        }
    }

    public function login(Request $request){
        $user = new User();

        if (Session::has('Coffee_Cafe_Logged_in')) {
            return redirect('/');
        }

        if($request->isMethod('post')){
           // echo "<pre>";print_r($request->all());exit;
            if(empty($request->input('email'))){
                return response()->json(array('status'  => false,   'message'   => 'Please Enter your Email'));
            }elseif(empty($request->input('password'))){
                return response()->json(array('status'  => false,   'message'   => 'Please Enter your Password'));
            }
            $email = $request->input('email');
            $password = $request->input('password');
            $users = $user->where(['email' => $email, 'password' => md5($password), 'user_type' => 'cafe_admin'])->first();
            if(empty($users)){
                return response()->json(array('status'  => false,   'message'   => 'Email or Password is wrong.'));
            }

            if($users->status == 'pending'){
                return response()->json(array('status'  => false,   'message'   => 'You will be allowed to login after screening, please wait,  will be updated shortly via eMail after approval. Thankyou'));
            }

            $session_data = array(
                'Logged_in'     => true,
                'user_id'       => $users->user_id,
                'first_name'    => $users->first_name,
                'last_name'     => $users->last_name,
                'email'         => $users->email,
                'picture'       => $users->photo,
                'user_type'     => $users->user_type
            );
            Session::put('Coffee_Cafe_Logged_in', $session_data);
            Session::flash('login','Logged in Successfully!!');
            return response()->json(array('status'  => true,   'message'   => 'Logged in Successfully!!'));
        }
        return view('login');
    }

    public function register(Request $request){
        if (Session::has('Coffee_Cafe_Logged_in')) {
            return redirect('/');
        }

        if($request->isMethod('post')){
            $user = new User();
            // Check Vendor Email
            $email_check = $user->where('email',$request->input('email'))->first();
            if(!empty($email_check)){
                return response()->json(array('message'    => 'The email has already been taken.'));
            }

            //echo "<pre>";print_r($request->all());exit;
            $user->first_name = htmlspecialchars($request->input('first_name'));
            $user->last_name = htmlspecialchars($request->input('last_name'));
            $user->photo = '';
            $user->email = htmlspecialchars($request->input('email'));
            $user->phone = '';
            $user->user_type = 'cafe_admin';
            $user->user_signup_status = 'normal';
            $user->is_password_requested = 'no';
            $user->password = md5($request->input('password'));
            $user->device_type = 'WEB';
            $user->device_token = '';
            $user->status = 'pending';
            $user->last_login_date = date('Y-m-d H:i:s');
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();

            $response = $user->find($user->user_id);
            $response->for = 'new_user';
            $response->subject = 'Account Created';

            Mail::to($response->email)->send(new CoffeeMail($response));
            return response()->json(array('status'    => true, 'message' => 'Your Account Successfully Created. Now you may Login to your account'));
        }

        return view('register');
    }

    public function logout(){
        Session::flush();
        \Illuminate\Support\Facades\Cache::flush();
        return redirect('/');
    }

    public function forgot_password(Request $request){
        if (Session::has('Coffee_Cafe_Logged_in')) {
            return redirect('/');
        }

        if($request->isMethod('post')){
            $user = new User();
            $check = $user->where(['email' => $request->input('email'), 'user_type' => 'cafe_admin'])->first();
            if (count($check) == 0) {
                return response()->json(array('status' => false, 'message' => "The email address '" . $request->input("email") . "' is not registered with Coffee Cup. Please Try again."));
            }elseif (count($check) > 0 && $check->status == 'pending') {
                return response()->json(array('status' => false, 'message' => "The email address '" . $request->input("email") . "' is not activated. Please Check the email."));
            }
            $new_password = $this->generate_password(6);
            $user->where('email', $request->input("email"))->update(['is_password_requested' => 'yes', 'password' => md5($new_password)]);
            $check->for = 'forgot_password';
            $check->subject = 'Forgot Password';
            $check->new_password = $new_password;
            Mail::to($check->email)->send(new CoffeeMail($check));
            return response()->json(array('status' => true, 'message' => 'Please Check Your Email. We sent you an email with instructions to reset your password.'));
        }

        return view('forgot');
    }

    public function activate(Request $request){
        if (Session::has('Coffee_Cafe_Logged_in')) {
            return redirect('/');
        }
        $user = new User();
        $user_id = base64_decode($request->segment(3));

        $users = $user->where(['user_id' => $user_id, 'status' => 'pending'])->first();

        if(count($users) > 0){
            $user->where(['user_id' => $user_id])->update(['status' => 'active']);
            Session::flash('active','Logged in Successfully!!');
            return redirect('/login');
        }else{
            return redirect('/');
        }
    }

    public function generate_password($length = 10)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public function change_password(Request $request){
        if (!Session::has('Coffee_Cafe_Logged_in')) {
            return redirect('/');
        }

        $user = new User();
        $check = $user->where(['user_id' => $request->input('user_id'), 'user_type' => 'cafe_admin'])->first();
        if (count($check) == 0) {
            return response()->json(array('status' => false, 'message' => "Invalid User"));
        }elseif ($check->password != md5($request->input('curr_password'))) {
            return response()->json(array('status' => false, 'message' => "Your Current Password is not Correct"));
        }

        $user->where('user_id', $request->input("user_id"))->update(['password' => md5($request->input('password'))]);

        Session::flash('messages','Your Password Changed Successfully!');

        return response()->json(array('status' => true, 'message' => 'Password Changed Successfully!'));
    }
}
