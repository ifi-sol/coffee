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
    <title>Coffee - SignUp</title>
    <!-- Style Sheets-->
    <link rel="stylesheet" href="{{url('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('css/styles.min.css')}}">
    <link rel="stylesheet" href="{{url('css/iziToast.min.css')}}">
</head>
<body>
<main>
    <div class="loader-container">
        <div class="loader"><img src="{{url('img/loader.png')}}" alt=""></div>
    </div>
    <section id="forms-area">
        <div class="row">
            <div class="col-md-6">
                <div class="coffee_slider"></div>
            </div>
            <div class="col-md-6">
                <div class="spinner-container hide">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
                <div class="login-area">
                    <div class="logo-container">
                        <div class="logo"><img class="img-responsive" src="{{url('img/logo.png')}}" alt="cupcard logo"></div>
                    </div>
                    <div class="error_block alert alert-danger" style="display: none;"></div>
                    <div id="confirm_msg" style="display: none">
                        <div class="m-t-40 card-box" style="background-clip: padding-box; background-color: #ffffff; border: 1px solid rgba(152, 166, 173, 0.2); border-radius: 5px; margin-bottom: 20px; padding: 20px;">
                            <div class="text-center">
                                <h4 class="text-uppercase font-bold m-b-0">Signup Confirmation</h4>
                            </div>
                            <div class="panel-body text-center">
                                <img src="{{url('img/mail_confirm.png')}}" alt="img" class="thumb-lg m-t-20 center-block">
                                <p class="alert alert-success"> Confirmation successful!! Thank you for completing the signup process.</p>
                                <p class="text-muted font-13 m-t-20"> A confirmation e-mail has been sent to your e-mail inbox, please click on the link provided to verify your e-mail address. Please check your junk or spam folder if your are not received email.</p>
                            </div>
                        </div>
                        <div class="seperator-area">
                            <p><span>OR</span></p><a href="{{url('/login')}}">Login Here</a>
                        </div>
                    </div>
                    <form method="post" id="register_form">
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/user.svg')}}" alt=""></div>
                            <input class="form-control" name="first_name" type="text" placeholder="Cafe Admin First Name">
                        </div>
                        <div id="name1_error" style="color: #ff5b5b"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/user.svg')}}" alt=""></div>
                            <input class="form-control" name="last_name" type="text" placeholder="Cafe Admin Last Name">
                        </div>
                        <div id="name2_error" style="color: #ff5b5b"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/mail.svg')}}" alt=""></div>
                            <input class="form-control" name="email" type="email" placeholder="Cafe Admin Email">
                        </div>
                        <div id="email_error" style="color: #ff5b5b"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/lock.svg')}}" alt=""></div>
                            <input class="form-control" name="password" type="password" placeholder="Cafe Password">
                        </div>
                        <div id="pass1_error" style="color: #ff5b5b"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/lock.svg')}}" alt=""></div>
                            <input class="form-control" name="password_confirmation" type="password" placeholder="Confirm Password">
                        </div>
                        <div id="pass2_error" style="color: #ff5b5b"></div>
                        <div class="submit-area">
                            <ul>
                                <li><a href="{{url('/forgot_password')}}">Forgot Password</a></li>
                                <li style="float: right">
                                    <button class="btn btn-primary" type="submit">Sign Up</button>
                                </li>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </ul>
                        </div>
                        <div class="seperator-area">
                            <p><span>OR</span></p><a href="{{url('/login')}}">Login Here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>
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
    $(document).ready(function(){
        $("#register_form").formValidation({
            framework: "bootstrap",
            fields: {
                first_name: {
                    err: "#name1_error",
                    validators: {
                        notEmpty: {
                            message: 'First Name is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'First Name must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'First Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'First Name Should be Alpha Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                last_name: {
                    err: "#name2_error",
                    validators: {
                        notEmpty: {
                            message: 'Last Name is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Last Name must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'Last Name Should be Alpha Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                email: {
                    err: "#email_error",
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Email address"
                        },
                        emailAddress: {
                            message: 'The value is not a valid email address'
                        }
                    }
                },
                password: {
                    err: "#pass1_error",
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Password"
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
            var formData = new FormData($("#register_form")[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(".spinner-container").removeClass('hide');
            $.ajax({
                type     : "POST",
                url      : '{{url("/signup")}}',
                data     : formData,
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                success  : function(data) {
                    setTimeout(function(){
                        $(".spinner-container").addClass('hide');
                    }, 1000);
                    if(data.status == true){
                        $("#register_form").remove();
                        $("#confirm_msg").show();
                    }else{
                        $('.error_block').text(data.message).show().fadeOut(5000);
                    }
                },
                error:function(data){
                    setTimeout(function(){
                        $(".spinner-container").addClass('hide');
                    }, 1000);
                    $('.error_block').text('Oops! Some Server Occurred. Please try again later').show().fadeOut(10000);
                }
            })
        });

    });
</script>
</body>
</html>