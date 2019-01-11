<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use DB;
use Mail;
use App\Mail\CoffeeMail;
use Session;

class AdminController extends Controller
{
    public function index(){
        $user = new User();

        $data['customers'] = $user->where(['user_type' => 'customer', 'status' => 'active'])->count();
        $data['cafe'] = $user->where(['user_type' => 'cafe_admin', 'status' => 'active'])->count();
        $data['visits'] = DB::table('cafe_viewed')->count();
        $data['free'] = DB::table('cafe_qr_scanned')->where(['free_status' => 'awarded'])->count();


        return view('admin.index',$data);
    }

    public function cafe_list(Request $request){
        $gid = $request->segment(3);
        $params = array('active','pending','blocked');
        if(!in_array($gid, $params)){
            return redirect('/admin/dashboard');
        }
        if($gid == 'active'){
            if($request->has('keyword')){
                $data['cafe_list'] = DB::table('cafe_info AS ci')->select('ci.cafe_id', 'cafe_name', 'ci.street_address AS cafe_street_address', 'ci.photo AS cafe_photo', 'latitude As cafe_latitude', 'longitude AS cafe_longitude', 'website AS cafe_website', 'ci.phone AS cafe_phone', 'ci.email AS cafe_email', 'u.user_id', 'first_name', 'last_name', 'u.email', 'user_type', 'u.phone', 'u.photo', 'user_signup_status', 'status')
                    ->join('cafe_admin AS ca', 'ca.cafe_id', '=', 'ci.cafe_id')
                    ->join('users AS u', 'u.user_id', '=', 'ca.user_id')
                    ->where(['u.status' => 'active', 'user_type' => 'cafe_admin', 'is_admin_approved' => 'yes'])
                    ->where('ci.cafe_name', 'like', "\\" . $request->input('keyword') . "%")->orderby('u.user_id', 'desc')->get();
            }else {
                $data['cafe_list'] = DB::table('cafe_info AS ci')->select('ci.cafe_id', 'cafe_name', 'ci.street_address AS cafe_street_address', 'ci.photo AS cafe_photo', 'latitude As cafe_latitude', 'longitude AS cafe_longitude', 'website AS cafe_website', 'ci.phone AS cafe_phone', 'ci.email AS cafe_email', 'u.user_id', 'first_name', 'last_name', 'u.email', 'user_type', 'u.phone', 'u.photo', 'user_signup_status', 'status')
                    ->join('cafe_admin AS ca', 'ca.cafe_id', '=', 'ci.cafe_id')
                    ->join('users AS u', 'u.user_id', '=', 'ca.user_id')
                    ->where(['u.status' => 'active', 'user_type' => 'cafe_admin', 'is_admin_approved' => 'yes'])->orderby('u.user_id', 'desc')->get();
            }
            if(count($data['cafe_list']) > 0){
                foreach ($data['cafe_list'] as $key=>$value){
                    $data['cafe_list'][$key]->free_coffee = DB::table('cafe_qr_scanned')->where(['cafe_id' => $value->cafe_id, 'free_status' => 'awarded'])->count();
                    $data['cafe_list'][$key]->visits = DB::table('cafe_viewed')->where(['cafe_id' => $value->cafe_id])->count();
                }
            }
        }elseif($gid == 'pending'){
            $data['cafe_list'] = DB::table('cafe_info AS ci')->select('ci.cafe_id', 'cafe_name', 'ci.street_address AS cafe_street_address', 'ci.photo AS cafe_photo', 'latitude As cafe_latitude', 'longitude AS cafe_longitude', 'website AS cafe_website', 'ci.phone AS cafe_phone', 'ci.email AS cafe_email', 'u.user_id', 'first_name', 'last_name', 'u.email', 'user_type', 'u.phone', 'u.photo', 'user_signup_status', 'status')
                ->join('cafe_admin AS ca', 'ca.cafe_id', '=', 'ci.cafe_id')
                ->join('users AS u', 'u.user_id', '=', 'ca.user_id')
                ->where(['u.status' => 'active', 'user_type' => 'cafe_admin', 'is_admin_approved' => 'no'])->orderby('u.user_id', 'desc')->get();
            if(count($data['cafe_list']) > 0){
                foreach ($data['cafe_list'] as $key=>$value){
                    $data['cafe_list'][$key]->free_coffee = 0;
                    $data['cafe_list'][$key]->visits = 0;
                }
            }
        }else{
            if($request->has('keyword')){
                $data['cafe_list'] = DB::table('cafe_info AS ci')->select('ci.cafe_id', 'cafe_name', 'ci.street_address AS cafe_street_address', 'ci.photo AS cafe_photo', 'latitude As cafe_latitude', 'longitude AS cafe_longitude', 'website AS cafe_website', 'ci.phone AS cafe_phone', 'ci.email AS cafe_email', 'u.user_id', 'first_name', 'last_name', 'u.email', 'user_type', 'u.phone', 'u.photo', 'user_signup_status', 'status')
                    ->join('cafe_admin AS ca', 'ca.cafe_id', '=', 'ci.cafe_id')
                    ->join('users AS u', 'u.user_id', '=', 'ca.user_id')
                    ->where(['u.status' => 'terminated', 'user_type' => 'cafe_admin'])
                    ->where('ci.cafe_name', 'like', "\\" . $request->input('keyword') . "%")->orderby('u.user_id', 'desc')->get();
            }else {
                $data['cafe_list'] = DB::table('cafe_info AS ci')->select('ci.cafe_id', 'cafe_name', 'ci.street_address AS cafe_street_address', 'ci.photo AS cafe_photo', 'latitude As cafe_latitude', 'longitude AS cafe_longitude', 'website AS cafe_website', 'ci.phone AS cafe_phone', 'ci.email AS cafe_email', 'u.user_id', 'first_name', 'last_name', 'u.email', 'user_type', 'u.phone', 'u.photo', 'user_signup_status', 'status')
                    ->join('cafe_admin AS ca', 'ca.cafe_id', '=', 'ci.cafe_id')
                    ->join('users AS u', 'u.user_id', '=', 'ca.user_id')
                    ->where(['u.status' => 'terminated', 'user_type' => 'cafe_admin'])->orderby('u.user_id', 'desc')->get();
            }
            if(count($data['cafe_list']) > 0){
                foreach ($data['cafe_list'] as $key=>$value){
                    $data['cafe_list'][$key]->free_coffee = DB::table('cafe_qr_scanned')->where(['cafe_id' => $value->cafe_id, 'free_status' => 'awarded'])->count();
                    $data['cafe_list'][$key]->visits = DB::table('cafe_viewed')->where(['cafe_id' => $value->cafe_id])->count();
                }
            }
        }


//echo "<pre>";print_r($data);exit;
        return view('admin.cafes',$data);
    }

    public function ajax_get_cafe_detail(Request $request){
        $cafe_id = $request->input('user_id');

        $cafe = DB::table('cafe_info AS ci')->select('ci.cafe_id','ci.description','cafe_name','ci.street_address AS cafe_street_address','ci.photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','website AS cafe_website','ci.phone AS cafe_phone','ci.email AS cafe_email','u.user_id','first_name','last_name','u.email','user_type','u.phone','u.photo','user_signup_status','status')
            ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
            ->join('users AS u','u.user_id', '=', 'ca.user_id')
            ->where(['ci.cafe_id' => $cafe_id])->first();

        $cafe_timings = $this->get_cafe_timings($cafe_id);


        if(count($cafe) > 0){
            $response = '';
            $response .= '<div class="p-10 task-detail">
                        <div class="media m-t-0 m-b-20">
                            <div class="media-left">
                                <a href="#"> <img class="media-object img-circle" alt="64x64" src="'.url($cafe->cafe_photo!=""?"images/".$cafe->cafe_photo : "img/coffee.svg").'" style="width: 48px; height: 48px;"> </a>
                            </div>
                            <div class="media-body">

                                <h4 class="media-heading m-b-5">'.$cafe->cafe_name.'</h4>
                                <span class="text-muted">'.$cafe->cafe_street_address.'</span>
                            </div>
                        </div>

                        <ul class="list-inline task-dates m-b-0 m-t-20">
                            <li>
                                <h5 class="font-600 m-b-5">Admin First Name</h5>
                                <p>'.$cafe->first_name.'</p>
                            </li>

                            <li>
                                <h5 class="font-600 m-b-5">Admin Last Name</h5>
                                <p>'.$cafe->last_name.'</p>
                            </li>
                            
                            <li>
                                <h5 class="font-600 m-b-5">Admin Email</h5>
                                <p>'.$cafe->email.'</p>
                            </li>
                            
                            <li>
                                <h5 class="font-600 m-b-5">Admin Phone</h5>
                                <p>'.$cafe->phone.'</p>
                            </li>
                            
                            <li>
                                <h5 class="font-600 m-b-5">Cafe Email</h5>
                                <p>'.$cafe->cafe_email.'</p>
                            </li>
                            
                            <li>
                                <h5 class="font-600 m-b-5">Cafe Phone</h5>
                                <p>'.$cafe->cafe_phone.'</p>
                            </li>
                            
                            <li>
                                <h5 class="font-600 m-b-5">Cafe Website</h5>
                                <p>'.$cafe->cafe_website.'</p>
                            </li>
                            
                            <li style="width: 100% !important;">
                                <h5 class="font-600 m-b-5">Cafe Description</h5>
                                <p>'.$cafe->description.'</p>
                            </li>
                        </ul>';
            if(count($cafe_timings) > 0) {
                $response .= '<div class="clearfix"></div>
                        <div class="media-body" style="padding-top: 25px; border-top:1px solid #ccc">
                                <h4 class="media-heading m-b-5">Cafe Timings</h4>
                        </div>
                        <ul class="list-inline task-dates m-b-0 m-t-20">';
                            foreach ($cafe_timings as $key => $value) {
                                if($value["close"] == 'no'){
                                $response .= '<li>
                                                <h5 class="font-600 m-b-5">'.ucfirst($value["name"]).'</h5>
                                                <p>' . $value["value"].'</p>
                                              </li>';
                                }else{
                                    $response .= '<li>
                                                <h5 class="font-600 m-b-5">'.ucfirst($value["name"]).'</h5>
                                                <p>Close Today</p>
                                              </li>';
                                }
                            }
                        $response .= '</ul>';
            }
                    $response .= '
                        <div class="clearfix"></div></div>';
            return response()->json(array('status'  => true,   'message'   => 'Cafe Detail Fetched Successfully', 'response' => $response));
        }else{
            return response()->json(array('status'  => false,   'message'   => 'Oops! Some Server Error. Please try again later.'));
        }
    }

    public function ajax_change_users_status(Request $request){
        $user_id = $request->input('user_id');
        $status = $request->input('status');

        $params = array('active','pending','blocked','delete','terminated');
        if(!in_array($status, $params)){
            return response()->json(array('status'  => false,   'message'   => 'Oops! Some Server Error. Please try again later.'));
        }

        $check = DB::table('users')->where('user_id', $user_id)->first();

        if(count($check) > 0){
            if($status == 'delete'){
                if($check->user_type == 'cafe_admin') {
                    $get_cafe = DB::table('cafe_admin')->where('user_id', $user_id)->first();
                    DB::table('cafe_viewed')->where('cafe_id', $get_cafe->cafe_id)->delete();
                    DB::table('cafe_qr_scanned')->where('cafe_id', $get_cafe->cafe_id)->delete();
                    DB::table('cafe_qr')->where('cafe_id', $get_cafe->cafe_id)->delete();
                    DB::table('cafe_info')->where('cafe_id', $get_cafe->cafe_id)->delete();
                    DB::table('cafe_admin')->where('user_id', $user_id)->delete();
                    DB::table('users')->where('user_id', $user_id)->delete();
                }else{
                    DB::table('users_subscription')->where('user_id', $user_id)->delete();
                    DB::table('cafe_viewed')->where('user_id', $user_id)->delete();
                    DB::table('cafe_qr_scanned')->where('buyer_id', $user_id)->delete();
                    DB::table('users')->where('user_id', $user_id)->delete();
                }
            }elseif($status == 'active'){
                DB::table('users')->where('user_id', $user_id)->update(['is_admin_approved' => 'yes', 'status' => 'active']);
                $response = DB::table('users')->where('user_id', $user_id)->first();
                $response->for = 'approve_user';
                $response->subject = 'Account Approved';

                Mail::to($response->email)->send(new CoffeeMail($response));
            }else {
                DB::table('users')->where('user_id', $user_id)->update(['status' => $status]);
            }

            return response()->json(array('status'  => true,   'message'   => 'Cafe Detail Updated Successfully'));
        }else{
            return response()->json(array('status'  => false,   'message'   => 'Oops! Some Server Error. Please try again later.'));
        }
    }


    public function customers_list(Request $request){
        $gid = $request->segment(3);
        $params = array('active','pending','blocked');
        if(!in_array($gid, $params)){
            return redirect('/admin/dashboard');
        }
        if($gid == 'active'){
            $status = 'active';
        }elseif($gid == 'pending'){
            $status = 'pending';
        }else{
            $status = 'terminated';
        }

        if($request->has('keyword')){
            $data['customers_list'] = DB::table('users')->where(['status' => $status, 'user_type' => 'customer'])->where('first_name', 'like', "\\" . $request->input('keyword') . "%")->orderby('user_id', 'desc')->get();
        }else {
            $data['customers_list'] = DB::table('users')->where(['status' => $status, 'user_type' => 'customer'])->orderby('user_id', 'desc')->get();
        }
        if(count($data['customers_list']) > 0){
            foreach ($data['customers_list'] as $key=>$value){
                $data['customers_list'][$key]->free_coffee = DB::table('cafe_qr_scanned')->where(['buyer_id' => $value->user_id, 'free_status' => 'awarded'])->count();
                $data['customers_list'][$key]->visits = DB::table('cafe_viewed')->where(['user_id' => $value->user_id])->count();
            }
        }
//echo "<pre>";print_r($data);exit;
        return view('admin.customers',$data);
    }

    public function send_email(Request $request){
        $user_id = $request->input('user_id');
        $response = DB::table('users')->where('user_id', $user_id)->first();
        $response->for = 'admin_email';
        $response->subject = $request->input('subject');
        $response->content = $request->input('msg');

        Mail::to($response->email)->send(new CoffeeMail($response));
        Session::flash('messages','Email Sent to '.$response->first_name.' '.$response->last_name.' Successfully!!');
        return response()->json(array('status'  => true,   'message'   => 'Email Sent Successfully'));
    }

    public function promo_codes(Request $request){
        if($request->isMethod('post')){
            for($i = 0; $i<20; $i++){
                $data = array(
                    'promo_code'    => $this->generate_promo(6),
                    'status'        => 'unUsed',
                    'created_at'    => date('Y-m-d H:i:s'),
                    'expiry_date'    => date('Y-m-d H:i:s', strtotime('+1 year', strtotime(date('Y-m-d H:i:s'))) ),
                    'utilize_date'    => Null,
                );
                DB::table('promo_codes')->insert($data);
            }
            return response()->json(array('status'  => true,   'message'   => 'Email Sent Successfully'));
        }

        $data['promo_codes'] = DB::table('promo_codes')->orderby('promo_code_id','desc')->get();
        $data['promo_state'] = DB::table('general_settings')->where(['method' => 'promo_state'])->first();
        return view('admin.promo', $data);
    }

    public function generate_promo($length)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public function get_cafe_timings($cafe_id){
        $check_timings = DB::table('cafe_timings')->where('cafe_id',$cafe_id)->get();
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
                $time = array(
                    'id' => $key+1,
                    'name' => strtolower($value->day),
                    'value' => $value->start_time.' - '.$value->end_time,
                    'close' => $value->is_close
                );
                array_push($timings,$time);
            }

            foreach($timings as $k=>$v) {
                $key = array_search(ucfirst($v['name']), $days);
                if($key !== FALSE) {
                    $timings[$key] = $v;
                }
            }
        }

        return $timings;
    }


    public function change_promo_state(Request $request){
        $promo = $request->input('promo');
        $response = DB::table('general_settings')->where(['method' => 'promo_state'])->update(['value' => $promo]);
        return response()->json(array('status'  => true,   'message'   => 'Email Sent Successfully'));
    }
}
