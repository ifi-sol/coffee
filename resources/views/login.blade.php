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
    <title>Coffee - Login</title>
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
                    <form method="post" id="login_form">
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('img/user.svg')}}" alt=""></div>
                            <input class="form-control" name="email" type="text" placeholder="Email">
                        </div>
                        <div id="email_error" style="color: #ff5b5b"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('img/lock.svg')}}" alt=""></div>
                            <input class="form-control" name="password" type="password" placeholder="Password">
                        </div>
                        <div id="password_error" style="color: #ff5b5b"></div>
                        <div class="submit-area">
                            <ul>
                                <li><a href="{{url('/forgot_password')}}">Forgot Password</a></li>
                                <li style="float: right">
                                    <button class="btn btn-primary" type="submit">Login</button>
                                </li>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </ul>
                        </div>
                    </form>
                    <div class="seperator-area">
                        <p><span>OR</span></p><a href="{{url('/signup')}}">Register Now</a>
                    </div>
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
        $("#login_form").formValidation({
            framework: "bootstrap",
            fields: {
                email: {
                    err: "#email_error",
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Email"
                        }
                    }
                },
                password: {
                    err: "#password_error",
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Password"
                        }
                    }
                }
            }
        }).on("success.form.fv", function(e) {
            e.preventDefault(); {
                var t = $(e.target);
                t.data("formValidation")
            }
            var formData = new FormData($("#login_form")[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.error_block').text('').hide();
            $(".spinner-container").removeClass('hide');
            $.ajax({
                type     : "POST",
                url      : '{{url("/login")}}',
                data     : formData,
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                success  : function(data) {
                    $('#login_form').formValidation('disableSubmitButtons', false);
                    if(data.status == true){
                        window.location.href = "{{url('/')}}";
                    }else{
                        setTimeout(function(){
                            $(".spinner-container").addClass('hide');
                        }, 1000);
                        $('.error_block').text(data.message).show().fadeOut(10000);
                    }
                }
            })
        });
    });

    @if (Session::has('active'))
    iziToast.show({
        theme: 'light',
        backgroundColor: '#00c292',
        icon: 'fa fa-smile-o',
        title: 'Welcome to Coffee Cup',
        titleColor: '#fff',
        titleSize: '16px',
        message: 'Your account Active Successfully! Now you may Logged in to Coffee Cup',
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
</script>
</body>
</html>