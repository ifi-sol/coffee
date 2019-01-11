<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Coffee Cup - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{url('assets/images/favicon.ico')}}">

    <!-- C3 charts css -->
    <link href="{{url('assets/plugins/morris/morris.css')}}" rel="stylesheet" type="text/css"  />
    <link href="{{url('assets/plugins/spinkit/spinkit.css')}}" rel="stylesheet" type="text/css"  />

    <!-- App css -->
    <link href="{{url('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/core.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/components.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/icons.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/pages.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/menu.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/css/responsive.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{url('css/iziToast.min.css')}}">
    @stack('css')
    <script src="{{url('assets/js/modernizr.min.js')}}"></script>

</head>


<body>


<!-- Navigation Bar-->
<header id="topnav">
    <div class="topbar-main">
        <div class="container">

            <!-- Logo container-->
            <div class="logo">
                <a href="{{url('/admin')}}" class="logo">
                    <img src="{{url('img/logo.png')}}" alt="" height="50">
                </a>

            </div>
            <!-- End Logo container-->


            <div class="menu-extras">

                <ul class="nav navbar-nav navbar-right pull-right">

                    <li class="dropdown navbar-c-items">
                        <a href="" class="dropdown-toggle waves-effect profile" data-toggle="dropdown" aria-expanded="true"><img src="{{url('assets/images/cafe.jpg')}}" alt="user-img" class="img-circle"> </a>
                        <ul class="dropdown-menu dropdown-menu-right arrow-dropdown-menu arrow-menu-right user-list notify-list">
                            <li class="text-center">
                                <h5>Burhan, Khan</h5>
                            </li>
                            <li><a href="{{url('/logout')}}"><i class="dripicons-power m-r-10"></i> Logout</a></li>
                        </ul>

                    </li>
                </ul>
                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>
            <!-- end menu-extras -->

        </div> <!-- end container -->
    </div>
    <!-- end topbar-main -->

    <div class="navbar-custom">
        <div class="container">
            <div id="navigation">
                <!-- Navigation Menu-->
                <ul class="navigation-menu">

                    <li>
                        <a href="{{url('/admin/dashboard')}}"><i class="fi-air-play"></i>Dashboard</a>
                    </li>

                    <li class="has-submenu">
                        <a href="{{url('/admin/cafe/active')}}"><i class="fi-briefcase"></i>Cafe List</a>
                        <ul class="submenu megamenu">
                            <li>
                                <ul>
                                    <li><a href="{{url('/admin/cafe/active')}}"><i class="fa fa-check-square m-r-15"></i> Active</a></li>
                                    <li><a href="{{url('/admin/cafe/pending')}}"><i class="fa fa-exclamation-triangle m-r-15"></i> Pending</a></li>
                                    <li><a href="{{url('/admin/cafe/blocked')}}"><i class="fa fa-power-off m-r-15"></i> Blocked</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="has-submenu">
                        <a href="{{url('/admin/customers/active')}}"><i class="fa fa-users"></i>Cafe Customers</a>
                        <ul class="submenu megamenu">
                            <li>
                                <ul>
                                    <li><a href="{{url('/admin/customers/active')}}"><i class="fa fa-check-square m-r-15"></i> Active</a></li>
                                    <!--<li><a href="{{url('/admin/customers/pending')}}"><i class="fa fa-exclamation-triangle m-r-15"></i> Pending</a></li>-->
                                    <li><a href="{{url('/admin/customers/blocked')}}"><i class="fa fa-power-off m-r-15"></i> Blocked</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{url('/admin/promo_codes')}}"><i class="fi-layers"></i>Promo Codes</a>
                    </li>

                </ul>
                <!-- End navigation menu -->
            </div> <!-- end #navigation -->
        </div> <!-- end container -->
    </div> <!-- end navbar-custom -->
</header>
<!-- End Navigation Bar-->


<div class="wrapper">
    <div class="container">

    @yield('content')
        <!-- Footer -->
        <footer class="footer text-right">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        2017 © IFISOL - ifisol.com
                    </div>
                </div>
            </div>
        </footer>
        <!-- End Footer -->

    </div> <!-- end container -->
</div>
<!-- end wrapper -->


<!-- jQuery  -->
<script src="{{url('assets/js/jquery.min.js')}}"></script>
<script src="{{url('assets/js/bootstrap.min.js')}}"></script>
<script src="{{url('assets/js/waves.js')}}"></script>
<script src="{{url('assets/js/jquery.slimscroll.js')}}"></script>
<script src="{{url('assets/js/jquery.scrollTo.min.js')}}"></script>

<!-- Counter js  -->
<script src="{{url('assets/plugins/waypoints/jquery.waypoints.min.js')}}"></script>
<script src="{{url('assets/plugins/counterup/jquery.counterup.min.js')}}"></script>

<!--C3 Chart-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="{{url('assets/plugins/chart.js/chart.min.js')}}"></script>

<!--Echart Chart-->


<!-- Dashboard init -->
@stack('js')
<!-- App js -->
<script src="{{url('assets/js/jquery.core.js')}}"></script>
<script src="{{url('assets/js/jquery.app.js')}}"></script>
<script src="{{url('js/iziToast.min.js')}}"></script>
<script type="text/javascript">
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
</script>

</body>
</html>