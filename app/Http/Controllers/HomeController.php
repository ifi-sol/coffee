<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use App\CafeQr;
use QrCode;
use App\CafeAdmin;
use App\CafeInfo;
use App\CafeQrScan;
use DB;
use PDF;
use Bodunde\GoogleGeocoder\Geocoder;
use App\libraries\FileUploader;

class HomeController extends Controller
{
    public function __construct(Geocoder $geocoder) {
        $this->geocoder = $geocoder;
        DB::enableQueryLog();
    }

    public function index(){
        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');
        $cafe_info = DB::table('cafe_info AS ci')
            ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
            ->where('ca.user_id',$user_id)->first();
        if(count($cafe_info) == 0){
            Session::put('cafe_info', 'false');
        }
        //echo "<pre>";print_r(Session::all());exit;
        return view('index');
    }

    public function qr_page(){
        $cafe_qr = new CafeQr();
        $user = new User();
        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');
        if(!Session::has('cafe_info')){
        $data['cafe_admin'] = DB::table('cafe_info AS ci')->select('ci.cafe_id','cafe_name','ci.street_address AS cafe_street_address','ci.photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','website AS cafe_website','ci.phone AS cafe_phone','ci.email AS cafe_email','u.user_id','first_name','last_name','u.email','user_type','u.phone','u.photo','user_signup_status','status')
            ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
            ->join('users AS u','u.user_id', '=', 'ca.user_id')
            ->where('ca.user_id',$user_id)->first();
        $data['qr_count'] = $cafe_qr->where('cafe_id', $data['cafe_admin']->cafe_id)->get();
        $data['qr_image'] = $cafe_qr->where(['cafe_id' => $data['cafe_admin']->cafe_id, 'status' => 'valid'])->first();

            Session::put('Coffee_Cafe_Logged_in.picture', $data['cafe_admin']->photo);
            $appendedFiles[] = array(
                "name" => $data['cafe_admin']->photo,
                "type" => FileUploader::mime_content_type(public_path('images/') . $data['cafe_admin']->photo),
                "size" => filesize(public_path('images/') . $data['cafe_admin']->photo),
                "file" => url('images/') . '/' . $data['cafe_admin']->photo,
                "data" => array(
                    "url" => url('images/') . $data['cafe_admin']->photo
                )
            );
            $data['cafe_admin']->photo = ($data['cafe_admin']->photo != '') ? $appendedFiles : '';
            $data['cafe_admin']->cafe_photo1 = $data['cafe_admin']->cafe_photo;
            $appendedFiles1[] = array(
                "name" => $data['cafe_admin']->cafe_photo,
                "type" => FileUploader::mime_content_type(public_path('images/') . $data['cafe_admin']->cafe_photo),
                "size" => filesize(public_path('images/') . $data['cafe_admin']->cafe_photo),
                "file" => url('images/') . '/' . $data['cafe_admin']->cafe_photo,
                "data" => array(
                    "url" => url('images/') . $data['cafe_admin']->cafe_photo
                )
            );
            $data['cafe_admin']->cafe_photo = ($data['cafe_admin']->cafe_photo != '') ? $appendedFiles1 : '';
        }else{
            $data['qr_count'] = array();
            $data['qr_image'] = array();
            $data['cafe_admin'] = $user->where('user_id',$user_id)->first();
        }
        return view('qr_page',$data);
    }

    public function generate_qr(Request $request){
        $user = new User();
        $cafe_qr = new CafeQr();

        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');
        if(Session::has('cafe_info')){
            return response()->json(array('status'  => false));
        }
        $check_user = $user->where(['user_id' => $user_id, 'is_admin_approved' => 'yes', 'status' => 'active'])->first();
        if(count($check_user) == 0){
            return response()->json(array('status'  => false,   'message'   => 'You Cannot Generate QR, because your account is blocked by Admin. Thankyou'));
        }
        $cafe_admin = DB::table('cafe_info AS ci')->select('ci.cafe_id','cafe_name','ci.street_address AS cafe_street_address','ci.photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','website AS cafe_website','ci.phone AS cafe_phone','ci.email AS cafe_email','u.user_id','first_name','last_name','u.email','user_type','u.phone','u.photo','user_signup_status','status')
            ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
            ->join('users AS u','u.user_id', '=', 'ca.user_id')
            ->where('ca.user_id',$user_id)->first();

        $status = $request->input('status');

        $qr_count = $cafe_qr->where('cafe_id', $cafe_admin->cafe_id)->get();
        if(count($qr_count) > 0){
            $cafe_qr->where(['cafe_id' => $cafe_admin->cafe_id, 'status' => 'valid'])->update(['status' => 'expire', 'expire_at' => date('Y-m-d H:i:s')]);
        }

        $pdf_name = $cafe_admin->first_name.'_'.$cafe_admin->last_name.'_'.date('Y-m-d-H-i-s');
        $data = array(
            'cafe_id'               => $cafe_admin->cafe_id,
            'qr_random_id'          => $this->generate_id(8),
            'status'                => 'valid',
            'pdf_name'              => $pdf_name,
            'pdf_download_counter'  => 0,
            'last_downlaod_date'    => date('Y-m-d H:i:s'),
            'created_at'            => date('Y-m-d H:i:s'),
            'expire_at'             => date('Y-m-d H:i:s')
        );
        $response = $cafe_qr->insertGetId($data);

        $qr_image = $cafe_qr->where(['cafe_qr_id' => $response, 'status' => 'valid'])->first();
        // Generate QR Image
        $cafe_admin->cafe_qr_id = $qr_image->qr_random_id;

        QrCode::format('png');
        QrCode::margin(2);
        QrCode::size(800);
        QrCode::generate(json_encode($cafe_admin), public_path('images/qr_codes').'/'.$pdf_name.'.png');
        $url = url('/cafe/generate_pdf?status=').base64_encode($qr_image->cafe_qr_id);
        return response()->json(array('status'  => true,   'message'   => 'Qr Generated Successfully!!', 'qr_image' => $qr_image->pdf_name,'qr_id' => $qr_image->cafe_qr_id, 'url' => $url));
    }

    public function profile(Request $request){

        $cafe_admin = new CafeAdmin();
        $cafe_info = new CafeInfo();
        $user = new User();
        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');

        if($request->isMethod('post')) {

            //echo "<pre>";print_r($request->all());exit;
            $request_data = $request->all();
            //echo "<pre>";print_r($coordinates);exit;
            if(empty($request->input('lat')) || empty($request->input('lng'))){
                return response()->json(array('message'    => 'Unable to find your street address, please choose location from Map'));
            }

            $data1 = array(
                'first_name'    => htmlspecialchars_decode($request->input('first_name')),
                'last_name'     => htmlspecialchars_decode($request->input('last_name')),
                'phone'         => htmlspecialchars_decode($request->input('phone')),
                'device_type'   => 'WEB',
                'device_token'  => '',
                'updated_at'    => date('Y-m-d H:i:s')
            );
            $user_photo = json_decode($request->input('fileuploader-list-files'));
            if (count($user_photo) > 0 && strpos($user_photo[0], 'images') == false) {
            //if(count() > 0) {
                $FileUploader = new FileUploader('files', array(
                    'uploadDir' => public_path('images/'),
                    'title' => 'auto',
                ));
                $user_file = $FileUploader->upload();
                if ($user_file['isSuccess'] && count($user_file['files']) > 0) {
                    $data1['photo'] = $user_file['files'][0]['name'];
                } else {
                    $data1['photo'] = '';
                }
            }else{
                if(count($user_photo) == 0) {
                    $data1['photo'] = '';
                }
            }

            $user->where('user_id',$user_id)->update($data1);

            $data2 = array(
                'cafe_name'    => htmlspecialchars_decode($request->input('name')),
                'street_address'     => htmlspecialchars_decode($request->input('address')),
                'region'         => htmlspecialchars_decode($request->input('region')),
                'city'         => htmlspecialchars_decode($request->input('city')),
                'country'         => htmlspecialchars_decode($request->input('country')),
                'post_code'         => htmlspecialchars_decode($request->input('code')),
                'website'         => htmlspecialchars_decode($request->input('website')),
                'phone'         => htmlspecialchars_decode($request->input('phone1')),
                'email'         => htmlspecialchars_decode($request->input('email1')),
                'description'         => htmlspecialchars_decode($request->input('description')),
                'latitude'         => $request->input('lat'),
                'longitude'         => $request->input('lng'),
                'updated_at'    => date('Y-m-d H:i:s')
            );
            $user_photo1 = json_decode($request->input('fileuploader-list-files1'));
            if (count($user_photo1) > 0 && strpos($user_photo1[0], 'images') == false) {
                $FileUploader = new FileUploader('files1', array(
                    'uploadDir' => public_path('images/'),
                    'title' => 'auto',
                ));
                $user_file = $FileUploader->upload();
                if ($user_file['isSuccess'] && count($user_file['files']) > 0) {
                    $data2['photo'] = $user_file['files'][0]['name'];
                } else {
                    $data2['photo'] = '';
                }
            }else{
                if(count($user_photo1) == 0) {
                    $data2['photo'] = '';
                }
            }

            if(Session::has('cafe_info')){
                $data2['created_at'] = date('Y-m-d H:i:s');
                $response = $cafe_info->insertGetId($data2);

                $cafe_admin->insert([
                    'user_id' => $user_id,
                    'cafe_id' => $response,
                    'admin_updated' => 'yes',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $cafe_id = $response;

                // Update Cafe Timings
                $timings = $this->set_cafe_timings($request_data,$user_id,$cafe_id);
                $check_timings = DB::table('cafe_timings')->where(['cafe_id' => $cafe_id, 'user_id' => $user_id])->get();
                if(count($check_timings) > 0){
                    foreach ($timings as $time_key => $time_value){
                        DB::table('cafe_timings')->where(['day' => $time_value['day'], 'user_id' => $user_id, 'cafe_id' => $cafe_id])->update([
                            'start_time'    => $time_value['start_time'],
                            'end_time'    => $time_value['end_time'],
                            'is_close'    => $time_value['is_close']
                        ]);
                    }
                }else{
                    foreach ($timings as $time_key => $time_value){
                        DB::table('cafe_timings')->insert($time_value);
                    }
                }

            }else {
                $cafe = $cafe_admin->where('user_id',$user_id)->first();
                $cafe_info->where('cafe_id',$cafe->cafe_id)->update($data2);
                $cafe_id = $cafe->cafe_id;

                // Update Cafe Timings
                $timings = $this->set_cafe_timings($request_data,$user_id,$cafe_id);
                $check_timings = DB::table('cafe_timings')->where(['cafe_id' => $cafe_id, 'user_id' => $user_id])->get();
                //echo "<pre>";print_r($timings);exit;
                if(count($check_timings) > 0){
                    foreach ($timings as $time_key => $time_value){
                        DB::table('cafe_timings')->where(['day' => $time_value['day'], 'user_id' => $user_id, 'cafe_id' => $cafe_id])->update([
                            'start_time'    => $time_value['start_time'],
                            'end_time'    => $time_value['end_time'],
                            'is_close'    => $time_value['is_close']
                        ]);
                    }
                }else{
                    foreach ($timings as $time_key => $time_value){
                        DB::table('cafe_timings')->insert($time_value);
                    }
                }
            }

            Session::forget('cafe_info');
            Session::flash('messages','Cafe Profile Updated Successfully!!');
            return response()->json(array('status'    => true,   'message'   => 'Cafe Profile Updated'));
        }

        $data['cafe_admin'] = DB::table('cafe_info AS ci')->select('ci.cafe_id','ci.description','cafe_name','ci.street_address AS cafe_street_address','region AS cafe_region','city As cafe_city','country As cafe_country','post_code AS cafe_post_code','ci.photo AS cafe_photo','latitude As cafe_latitude','longitude AS cafe_longitude','website AS cafe_website','ci.phone AS cafe_phone','ci.email AS cafe_email','u.user_id','first_name','last_name','u.email','user_type','u.phone','u.photo','user_signup_status','status')
            ->join('cafe_admin AS ca','ca.cafe_id', '=', 'ci.cafe_id')
            ->join('users AS u','u.user_id', '=', 'ca.user_id')
            ->where('ca.user_id',$user_id)->first();
        if(count($data['cafe_admin']) > 0) {
            Session::put('Coffee_Cafe_Logged_in.picture', $data['cafe_admin']->photo);
            $appendedFiles[] = array(
                "name" => $data['cafe_admin']->photo,
                "type" => FileUploader::mime_content_type(public_path('images/') . $data['cafe_admin']->photo),
                "size" => filesize(public_path('images/') . $data['cafe_admin']->photo),
                "file" => url('images/') . '/' . $data['cafe_admin']->photo,
                "data" => array(
                    "url" => url('images/') . $data['cafe_admin']->photo
                )
            );
            $data['cafe_admin']->photo = ($data['cafe_admin']->photo != '') ? $appendedFiles : '';
            $data['cafe_admin']->cafe_photo1 = $data['cafe_admin']->cafe_photo;
            $appendedFiles1[] = array(
                "name" => $data['cafe_admin']->cafe_photo,
                "type" => FileUploader::mime_content_type(public_path('images/') . $data['cafe_admin']->cafe_photo),
                "size" => filesize(public_path('images/') . $data['cafe_admin']->cafe_photo),
                "file" => url('images/') . '/' . $data['cafe_admin']->cafe_photo,
                "data" => array(
                    "url" => url('images/') . $data['cafe_admin']->cafe_photo
                )
            );
            $data['cafe_admin']->cafe_photo = ($data['cafe_admin']->cafe_photo != '') ? $appendedFiles1 : '';
        }else{
            $data['cafe_admin'] = $user->where('user_id',$user_id)->first();
        }
        $data['check_timings'] = DB::table('cafe_timings')->where('user_id',$user_id)->get();
        $data['timings'] = $this->get_cafe_timings($user_id);
        //echo "<pre>";print_r($data['timings']);exit;
        return view('profile',$data);
    }

    public function generate_pdf(Request $request){
        $user = new User();
        $cafe_qr = new CafeQr();
        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');
        if(Session::has('cafe_info')){
            return response()->json(array('status'  => false, 'message' => 'Our record indicate that you have not yet completed your Cafe Information. Without this information, we cannot start servicing your cafe, so please complete this as soon as possible.'));
        }

        $qr_id = base64_decode($request->input('status'));

        $data['qr_image'] = $cafe_qr->where(['cafe_qr_id' => $qr_id, 'status' => 'valid'])->first();
        if(count($data['qr_image']) > 0) {
            $counter = 0;
            $counter += $data['qr_image']->pdf_download_counter+1;
            $cafe_qr->where('cafe_qr_id',$qr_id)->update(['pdf_download_counter' => $counter]);
            // Generate QR PDF
            PDF::loadView('mail.qr_pdf', $data, [], [
                'title' => 'Cafe QR Code',
            ])->stream($data['qr_image']->pdf_name . '.pdf');
            //PDF::loadView('mail.qr_pdf', $data, [])->stream($data['qr_image']->pdf_name . '.pdf');

            return response()->json(array('status' => true, 'message' => 'Qr Generated Successfully!!'));
        }else{
            return response()->json(array('status'  => false, 'message' => 'Oops! Some Server Error. Please try again lator.'));
        }
    }

    public function scanned_users(){
        $qr_scan = new CafeQrScan();
        $user = new User();
        $cafe_admin = new CafeAdmin();
        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');

        $cafe = $cafe_admin->where('user_id',$user_id)->first();
        $free_coffee = array();
        $cafe_visits = array();
        if(count($cafe) > 0) {
            $buyers = DB::table('cafe_qr_scanned')->where('cafe_id', $cafe->cafe_id)->orderBy('qr_scan_id', 'desc')->groupBy('buyer_id')->paginate(16);
            if (count($buyers) > 0) {
                foreach ($buyers as $key => $buyer) {
                    $buyer_data = $user->where('user_id', $buyer->buyer_id)->first();
                    $buyer_data->free_coffee = $qr_scan->where(['buyer_id' => $buyer->buyer_id, 'free_status' => 'awarded'])->count();
                    $buyer_data->visits = DB::table('cafe_viewed')->where(['user_id' => $buyer->buyer_id])->count();
                    $buyer_data->last_visit = DB::table('cafe_viewed')->where(['user_id' => $buyer->buyer_id, 'cafe_id' => $user_id])->orderBy('viewed_id', 'desc')->first();
                    $buyers[$key]->user = $buyer_data;
                    $free_coffee = DB::table('cafe_qr_scanned')->where(['cafe_id' => $cafe->cafe_id, 'free_status' => 'awarded'])->get();
                    $cafe_visits = DB::table('cafe_viewed')->where(['cafe_id' => $cafe->cafe_id])->get();
                }
            }
            $data['buyers'] = $buyers;
        }else {
            $data['buyers'] = array();
        }
        $data['free_coffee'] = $free_coffee;
        $data['cafe_visits'] = $cafe_visits;
        //echo "<pre>";print_r($buyers);exit;
        return view('users',$data);
    }

    public function ajax_search(Request $request){
        $qr_scan = new CafeQrScan();
        $cafe_admin = new CafeAdmin();
        $user = new User();
        $user_id = Session::get('Coffee_Cafe_Logged_in.user_id');
        $params = array_filter($request->all());
        //echo "<pre>";print_r($params);exit;
        $sorting = array('column' => 'id','value' => 'desc');

        $cafe = $cafe_admin->where('user_id',$user_id)->first();
        // Applying pagination
        $buyers =   DB::table('cafe_qr_scanned')->where('cafe_id', $cafe->cafe_id)->orderBy('qr_scan_id', 'desc')->groupBy('buyer_id')->paginate(20);
        //echo "<pre>";print_r(DB::getQueryLog());exit;
        if(count($buyers) > 0){
            foreach ($buyers as $key=>$buyer){
                if(isset($params['keyword'])) {
                    $buyer_data = $user->where('user_id', $buyer->buyer_id)
                                    ->where('first_name', 'like', '%' . $params['keyword'] . '%')
                                    ->first();
                    //echo "<pre>";print_r(DB::getQueryLog());exit;
                }else{
                    $buyer_data = $user->where('user_id',$buyer->buyer_id)->first();
                }
                //echo "<pre>";print_r(DB::getQueryLog());exit;
                if(count($buyer_data) > 0) {
                    $buyer_data->free_coffee = $qr_scan->where(['buyer_id' => $buyer->buyer_id, 'free_status' => 'awarded'])->count();
                    $buyer_data->visits = DB::table('cafe_viewed')->where(['user_id' => $buyer->buyer_id, 'cafe_id' => $user_id])->count();
                    $buyer_data->last_visit = DB::table('cafe_viewed')->where(['user_id' => $buyer->buyer_id, 'cafe_id' => $user_id])->orderBy('viewed_id', 'desc')->first();
                    $buyers[$key]->user = $buyer_data;
                }else{
                    unset($buyers[$key]);
                }
            }
        }
        $data['buyers'] = $buyers;
        return view('search_users',$data);
    }

    public function generate_id($length){
        $number = '';
        for ($i = 0; $i < $length; $i++){
            $number .= rand(1,9);
        }
        return (int)$number;
    }

    public function set_cafe_timings($data,$user_id,$cafe_id){
        //echo "<pre>";print_r($data);exit;
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

        for($i=1; $i<=7; $i++){
            $array = array(
                'day'           => $days[$i-1],
                'start_time'    => (isset($data['close'][$i])) ? '' : $this->strbefore($data[$i],' -'),
                'end_time'      => (isset($data['close'][$i])) ? '' : $this->strafter($data[$i],'- '),
                'is_close'      => (isset($data['close'][$i])) ? 'yes' : 'no',
                'user_id'       => $user_id,
                'cafe_id'       => $cafe_id
            );
            array_push($timings,$array);
        }



        return $timings;
    }

    public function get_cafe_timings($user_id){
        $check_timings = DB::table('cafe_timings')->where('user_id',$user_id)->get();
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
                    'id' => $index+1,
                    'name' => strtolower($value->day),
                    'value' => $value->start_time.' - '.$value->end_time,
                    'close' => $value->is_close
                );
                array_push($timings,$time);
            }
        }else {
            $timings = array(
                array(
                    'id' => '1',
                    'name' => 'monday',
                    'value' => '',
                    'close' => 'no'
                ), array(
                    'id' => '2',
                    'name' => 'tuesday',
                    'value' => '',
                    'close' => 'no'
                ), array(
                    'id' => '3',
                    'name' => 'wednesday',
                    'value' => '',
                    'close' => 'no'
                ), array(
                    'id' => '4',
                    'name' => 'thursday',
                    'value' => '',
                    'close' => 'no'
                ), array(
                    'id' => '5',
                    'name' => 'friday',
                    'value' => '',
                    'close' => 'no'
                ), array(
                    'id' => '6',
                    'name' => 'saturday',
                    'value' => '',
                    'close' => 'no'
                ), array(
                    'id' => '7',
                    'name' => 'sunday',
                    'value' => '',
                    'close' => 'no'
                )
            );
        }


        foreach($timings as $k=>$v) {
            $key = array_search(ucfirst($v['name']), $days);
            if($key !== FALSE) {
                $timings[$key] = $v;
            }
        }
        return $timings;
    }

    function strafter($string, $substring) {
        $pos = strpos($string, $substring);
        if ($pos === false)
            return $string;
        else
            return(substr($string, $pos+strlen($substring)));
    }

    function strbefore($string, $substring) {
        $pos = strpos($string, $substring);
        if ($pos === false)
            return $string;
        else
            return(substr($string, 0, $pos));
    }
}
