<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon & Stuff-->
    <link rel="shortcut icon" href="{{url('img/fav.png')}}">
    <link rel="apple-touch-icon" href="{{url('img/fav.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{url('img/fav.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{url('img/fav.png')}}">
    <!-- Title-->
    <title>Coffee - @yield('title')</title>
    <!-- Style Sheets-->
    <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('css/styles.min.css')}}">
    <link rel="stylesheet" href="{{url('css/iziToast.min.css')}}">
    @stack('css')
</head>
<body>
<main>
    <header>
        <nav class="navbar navbar-toggleable-md navbar-light">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button><a class="navbar-brand" href="{{url('/')}}">
                <img src="{{url('/img/mail_logo.png')}}" style="width: 80px; height: 20px">
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item @if(Request::segment(2) == '') active @endif"><a class="nav-link" href="{{url('/')}}">Home <span class="sr-only">(current)</span></a></li>
                    <li class="nav-item @if(Request::segment(2) == 'qrs') active @endif"><a class="nav-link" href="{{url('/cafe/qrs')}}">Cafe QR / Profile</a></li>
                    <li class="nav-item @if(Request::segment(2) == 'users') active @endif"><a class="nav-link" href="{{url('/cafe/users')}}">Visited Users</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="dropdown"><a class="dropdown-toggle" id="dropdownMenuLink" href="javascript:void(0)" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="@if(Session::get('Coffee_Cafe_Logged_in.picture') !='') {{url('images').'/'.Session::get('Coffee_Cafe_Logged_in.picture')}} @else {{url('img/dummy.png')}} @endif" alt="">{{Session::get('Coffee_Cafe_Logged_in.first_name').' '.Session::get('Coffee_Cafe_Logged_in.last_name')}}</a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="{{url('/cafe/profile')}}">Edit Profile</a>
                            <a class="dropdown-item" href="javascript:void(0)" style="color: #969594" data-toggle="modal" data-target="#passwordModal">Change Password</a>
                            <a class="dropdown-item" href="{{url('/logout')}}">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        @yield('sub_nav')
    </header>
    <section id="content">
        <div class="continer text-center">
            @yield('content')
        </div>
    </section>
</main>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="vertical-align: middle;">
        <div class="modal-content">
            <div class="modal-body">
                <div class="m-t-40 card-box" style="background-clip: padding-box; background-color: #ffffff; border: 1px solid rgba(152, 166, 173, 0.2); border-radius: 5px; padding: 20px;">
                    <div class="panel-body text-center">
                        <img src="{{url('img/warning.svg')}}" alt="img" class="thumb-lg m-t-20 center-block" style="width: 100px; margin-bottom: 20px;">
                        <p class="alert alert-warning"> Profile In-Complete Warning!</p>
                        <p class="text-muted font-13 m-t-20"> Our record indicate that you have not yet completed your <strong>Cafe Information</strong>. Without this information, we cannot start servicing your cafe, so please complete this as soon as possible.</p>
                    </div>
                </div>
                <a class="btn btn-primary btn-block" style="margin-top: 20px" href="{{url('/cafe/profile')}}">Complete Profile</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="vertical-align: middle;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="color: #000">Change Password</h5>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></a>
            </div>
            <div class="modal-body">
                <div class="spinner-container hide">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
                <form method="post" id="password_form">
                    <div class="input-group form-group">
                        <div class="input-group-addon"><img src="{{url('/img/lock.svg')}}" alt=""></div>
                        <input class="form-control" name="curr_password" type="password" placeholder="Current Password">
                    </div>
                    <div id="pass_curr" style="color: #ff5b5b"></div>
                    <div class="input-group form-group">
                        <div class="input-group-addon"><img src="{{url('/img/lock.svg')}}" alt=""></div>
                        <input class="form-control" name="password" type="password" placeholder="New Password">
                    </div>
                    <div id="pass1_error" style="color: #ff5b5b"></div>
                    <div class="input-group form-group">
                        <div class="input-group-addon"><img src="{{url('/img/lock.svg')}}" alt=""></div>
                        <input class="form-control" name="password_confirmation" type="password" placeholder="Confirm Password">
                    </div>
                    <div id="pass2_error" style="color: #ff5b5b"></div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input name="user_id" value="{{Session::get('Coffee_Cafe_Logged_in.user_id')}}" type="hidden">
                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px" href="{{url('/cafe/profile')}}">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer-->
<footer><small>&copy; 2017 CupCard All rights reserved! Designed & Developed by:<a href="http://www.ifisol.com" target="_blank">IFISOL </a></small></footer>
<!-- Scripts-->
<script src="{{url('js/jquery.min.js')}}"></script>
<script src="{{url('js/bootstrap.min.js')}}"></script>
<script src="{{url('js/vegas.min.js')}}"></script>
<script src="{{url('js/jquery.scrollbar.min.js')}}"></script>
<script src="{{url('js/slim.jquery.min.js')}}"></script>
<script src="{{url('js/iziToast.min.js')}}"></script>
<script src="{{url('js/scripts.js')}}"></script>
<script src="{{url('js/formValidation.min.js')}}"></script>
<script src="{{url('js/bootstrap_min.js')}}"></script>
<script type="text/javascript">
    @if(Session::has('cafe_info') && Request::segment(1) != '' && Request::segment(2) != 'profile')
        $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    @endif

    @if (Session::has('messages'))
    iziToast.show({
        theme: 'light',
        backgroundColor: '#03c205',
        icon: 'fa fa-smile-o',
        title: 'Success!',
        titleColor: '#fff',
        titleSize: '16px',
        message: '{{Session::get('messages')}}',
        messageColor: '#fff',
        messageSize: '12px',
        layout: 2,
        animateInside: true,
        transitionIn: 'fadeInUp',
        position: 'topRight',
        progressBarColor: 'rgb(0, 255, 184)',
        animateInside: true,
    });
    @endif

    function show_popup(title,msg,cls){
        if(cls == 'success') {
            iziToast.show({
                theme: 'light',
                backgroundColor: '#03c205',
                icon: 'fa fa-smile-o',
                iconColor: '#fff',
                title: title,
                titleColor: '#fff',
                titleSize: '16px',
                message: msg,
                messageColor: '#fff',
                messageSize: '12px',
                layout: 2,
                animateInside: true,
                transitionIn: 'fadeInUp',
                position: 'topRight',
                progressBarColor: 'rgb(0, 255, 184)',
                animateInside: true,
            });
        }else{
            iziToast.show({
                theme: 'light',
                backgroundColor: '#fb0004',
                icon: 'fa fa-frown-o',
                iconColor: '#fff',
                title: title,
                titleColor: '#fff',
                titleSize: '16px',
                message: msg,
                messageColor: '#fff',
                messageSize: '12px',
                layout: 2,
                animateInside: true,
                transitionIn: 'fadeInUp',
                position: 'topRight',
                progressBarColor: 'rgb(0, 255, 184)',
                animateInside: true,
            });
        }
    }


    $("#password_form").formValidation({
        framework: "bootstrap",
        fields: {
            curr_password: {
                err: "#pass_curr",
                validators: {
                    notEmpty: {
                        message: "Please Enter Your Current Password"
                    }
                }
            },
            password: {
                err: "#pass1_error",
                validators: {
                    notEmpty: {
                        message: "Please Enter Your New Password"
                    }
                }
            },
            password_confirmation: {
                err: "#pass2_error",
                validators: {
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    }
                }
            },
        }
    }).on("success.form.fv", function(e) {
        e.preventDefault(); {
            var t = $(e.target);
            t.data("formValidation")
        }
        var formData = new FormData($("#password_form")[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".spinner-container").removeClass('hide');
        $.ajax({
            type     : "POST",
            url      : '{{url("/cafe/change_password")}}',
            data     : formData,
            cache: false,
            async: false,
            contentType: false,
            processData: false,
            success  : function(data) {
                if(data.status == true){
                    location.reload();
                }else{
                    setTimeout(function(){
                        $(".spinner-container").addClass('hide');
                    }, 1000);
                    show_popup('Server Error',data.message,'error');
                    $('#password_form').formValidation('disableSubmitButtons', false);
                }
            },
            error:function(data){
                setTimeout(function(){
                    $(".spinner-container").addClass('hide');
                }, 1000);
                show_popup('Server Error','Oops! Some Server Occurred. Please try again later','error');
                $('#password_form').formValidation('disableSubmitButtons', false);
            }
        })
    });
</script>
@stack('js')
</body>
</html>