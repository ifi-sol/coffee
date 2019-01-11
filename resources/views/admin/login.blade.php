<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Coffee Cup Admin - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{url('assets/images/favicon.ico')}}">

    <!-- App css -->
    <link href="{{url('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/core.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/components.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/icons.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/pages.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/menu.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/responsive.css')}}" rel="stylesheet" type="text/css" />

    <script src="{{url('assets/js/modernizr.min.js')}}"></script>

</head>


<body class="bg-accpunt-pages">

<!-- HOME -->
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <div class="wrapper-page">

                    <div class="account-pages">
                        <div class="account-box">
                            <div class="account-logo-box">
                                <h2 class="text-uppercase text-center">
                                    <a href="{{url('/admin')}}" class="text-success">
                                        <span><img src="{{url('img/logo.png')}}" alt="" height="100"></span>
                                    </a>
                                </h2>
                                <h5 class="text-uppercase font-bold m-b-5 m-t-50">Sign In</h5>
                                <p class="m-b-0">Login to Coffee Cup Admin account</p>
                            </div>
                            <div class="account-content">
                                <div class="error_block alert alert-icon alert-danger hide alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <i class="mdi mdi-alert"></i>
                                    <span id="msg"></span>
                                </div>
                                <form class="form-horizontal" action="{{url('/admin')}}" id="login_form" method="post">

                                    <div class="form-group m-b-20">
                                        <div class="col-xs-12">
                                            <label for="emailaddress">Email address</label>
                                            <input class="form-control" name="email" type="email" id="emailaddress"  placeholder="john@deo.com">
                                        </div>
                                    </div>

                                    <div class="form-group m-b-20">
                                        <div class="col-xs-12">
                                            <label for="password">Password</label>
                                            <input class="form-control" name="password" type="password"  id="password" placeholder="Enter your password">
                                        </div>
                                    </div>

                                    <div class="form-group text-center m-t-10">
                                        <div class="col-xs-12">
                                            <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit">Sign In</button>
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                    <!-- end card-box-->


                </div>
                <!-- end wrapper -->

            </div>
        </div>
    </div>
</section>
<!-- END HOME -->


<!-- jQuery  -->
<script src="{{url('assets/js/jquery.min.js')}}"></script>
<script src="{{url('assets/js/bootstrap.min.js')}}"></script>
<script src="{{url('assets/js/waves.js')}}"></script>
<script src="{{url('assets/js/jquery.slimscroll.js')}}"></script>
<script src="{{url('assets/js/jquery.scrollTo.min.js')}}"></script>

<!-- App js -->
<script src="{{url('assets/js/jquery.core.js')}}"></script>
<script src="{{url('assets/js/jquery.app.js')}}"></script>
<script src="{{url('js/formValidation.min.js')}}"></script>
<script src="{{url('js/bootstrap_min.js')}}"></script>

</body>
</html>
<script type="text/javascript">
    $(document).ready(function(){
        $("#login_form").formValidation({
            framework: "bootstrap",
            fields: {
                email: {
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Email"
                        }
                    }
                },
                password: {
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
            $(".loader-container").addClass('visible');
            $.ajax({
                type     : "POST",
                url      : '{{url("/admin")}}',
                data     : formData,
                cache: false,
                async: false,
                contentType: false,
                processData: false,
                success  : function(data) {
                    $(".loader-container").removeClass('visible');
                    if(data.status == true){
                        window.location.href = "{{url('/admin/dashboard')}}";
                    }else{
                        $('#msg').text(data.message)
                        $('.error_block').removeClass('hide');
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