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
                <div class="login-area">
                    <div class="logo-container">
                        <div class="logo"><img class="img-responsive" src="{{url('img/logo.png')}}" alt="cupcard logo"></div>
                    </div>
                    <div class="error_block alert alert-danger" style="display: none;"></div>
                    <div id="confirm_msg" style="display: none">
                        <div class="m-t-40 card-box" style="background-clip: padding-box; background-color: #ffffff; border: 1px solid rgba(152, 166, 173, 0.2); border-radius: 5px; margin-bottom: 20px; padding: 20px;">
                            <div class="text-center">
                                <h4 class="text-uppercase font-bold m-b-0">Forgot Password Confirmation</h4>
                            </div>
                            <div class="panel-body text-center">
                                <img src="{{url('img/mail_confirm.png')}}" alt="img" class="thumb-lg m-t-20 center-block">
                                <p class="text-muted font-13 m-t-20"> A confirmation e-mail has been sent to your e-mail inbox, Please Check Your e-mail. We sent you an e-mail with instructions to reset your password..</p>
                            </div>
                        </div>
                        <div class="seperator-area">
                            <p><span>OR</span></p><a href="{{url('/login')}}">Login Here</a>
                        </div>
                    </div>
                    <form method="post" id="forgot_password">
                        <div class="spinner-container" style="z-index: 9999;display: none">
                            <div class="spinner">
                                <div class="bounce1"></div>
                                <div class="bounce2"></div>
                                <div class="bounce3"></div>
                            </div>
                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/mail.svg')}}" alt=""></div>
                            <input class="form-control" name="email" type="email" placeholder="Cafe Admin Email">
                        </div>
                        <div id="email_error" style="color: #ff5b5b"></div>
                        <div class="submit-area">
                            <ul>
                                <li><a href="{{url('/login')}}">Already have an account?</a></li>
                                <li style="float: right">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </li>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </ul>
                        </div>
                        <div class="seperator-area">
                            <p><span>OR</span></p><a href="{{url('/signup')}}">Signup Here</a>
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
        $("#forgot_password").formValidation({
            framework: "bootstrap",
            fields: {
                email: {
                    err: "#email_error",
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Email"
                        }
                    }
                }
            }
        }).on("success.form.fv", function(e) {
            e.preventDefault(); {
                var t = $(e.target);
                t.data("formValidation")
            }
            var formData = new FormData($("#forgot_password")[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(".spinner-container").show();
            $.ajax({
                type     : "POST",
                url      : '{{url("/forgot_password")}}',
                data     : formData,
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                success  : function(data) {
                    $(".spinner-container").hide();
                    if(data.status == true){
                        $("#forgot_password").remove();
                        $("#confirm_msg").show();
                    }else{
                        $('.error_block').text(data.message).show().fadeOut(5000);
                    }
                },
                error:function(data){
                    $(".spinner-container").hide();
                    $('.error_block').text('Oops! Some Server Occurred. Please try again later').show().fadeOut(5000);
                }
            })
        });

    });
</script>
</body>
</html>