@extends('admin.base')
@section('title', "Dashboard")
@section('content')
<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group pull-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li>
                        <a href="{{url('/admin/dashboard')}}">Coffee Admin</a>
                    </li>
                    <li class="active">
                        Dashboard
                    </li>
                </ol>
            </div>
            <h4 class="page-title">Coffee Cup Dashboard</h4>
        </div>
    </div>
</div>
<!-- end page title end breadcrumb -->

<div class="row">

    <div class="col-lg-3 col-md-6">
        <div class="card-box widget-box-two widget-two-custom">
            <i class="mdi mdi-account-multiple widget-two-icon"></i>
            <div class="wigdet-two-content">
                <p class="m-0 text-uppercase font-bold font-secondary text-overflow" title="Statistics">Total Customers</p>
                <h2 class=""><span><i class="mdi mdi-arrow-up"></i></span> <span data-plugin="counterup">{{$customers}}</span></h2>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-lg-3 col-md-6">
        <div class="card-box widget-box-two widget-two-custom">
            <i class="mdi mdi-account-multiple widget-two-icon"></i>
            <div class="wigdet-two-content">
                <p class="m-0 text-uppercase font-bold font-secondary text-overflow" title="Statistics">Total Cafe's</p>
                <h2 class=""><span><i class="mdi mdi-arrow-up"></i></span> <span data-plugin="counterup">{{$cafe}}</span></h2>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-lg-3 col-md-6">
        <div class="card-box widget-box-two widget-two-custom">
            <i class="mdi mdi-auto-fix widget-two-icon"></i>
            <div class="wigdet-two-content">
                <p class="m-0 text-uppercase font-bold font-secondary text-overflow" title="Statistics">Cafe Visits</p>
                <h2 class=""><span><i class="mdi mdi-arrow-up"></i></span> <span data-plugin="counterup">{{$visits}}</span></h2>
            </div>
        </div>
    </div><!-- end col -->

    <div class="col-lg-3 col-md-6">
        <div class="card-box widget-box-two widget-two-custom">
            <i class="mdi mdi-crown widget-two-icon"></i>
            <div class="wigdet-two-content">
                <p class="m-0 text-uppercase font-bold font-secondary text-overflow" title="Statistics">Free Coffee (Awarded)</p>
                <h2 class=""><span><i class="mdi mdi-arrow-up"></i></span> <span data-plugin="counterup">{{$free}}</span></h2>
            </div>
        </div>
    </div><!-- end col -->

</div>
<!-- end row -->


<div class="row">
    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="header-title m-t-0">Daily Cafe's Report</h4>
            <canvas id="doughnut" height="320" class="m-t-10"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Weekly Cafe's Overview</h4>
            <div id="line-chart" style="height: 320px;"></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-box">
            <h4 class="header-title m-t-0 m-b-30">Monthly Cafe's Report</h4>
            <div id="area-chart" style="height: 320px;"></div>
        </div>
    </div>
</div>
<!-- end row -->

@endsection
@push('js')
<script>
    /**
     * Theme: Adminox Admin Template
     * Author: Coderthemes
     * Module/App: Flot-Chart
     */

    ! function($) {
        "use strict";

        var GoogleChart = function() {
            this.$body = $("body")
        };

        //creates line graph
        GoogleChart.prototype.createLineChart = function(selector, data, axislabel, colors) {
            var options = {
                fontName: 'Open Sans',
                height: 340,
                curveType: 'function',
                fontSize: 14,
                chartArea: {
                    left: '5%',
                    width: '90%',
                    height: 300
                },
                pointSize: 4,
                tooltip: {
                    textStyle: {
                        fontName: 'Open Sans',
                        fontSize: 14
                    }
                },
                vAxis: {
                    title: axislabel,
                    titleTextStyle: {
                        fontSize: 14,
                        italic: false
                    },
                    gridlines:{
                        color: '#f5f5f5',
                        count: 10
                    },
                    minValue: 0
                },
                legend: {
                    position: 'top',
                    alignment: 'center',
                    textStyle: {
                        fontSize: 14
                    }
                },
                lineWidth: 3,
                colors: colors
            };

            var google_chart_data = google.visualization.arrayToDataTable(data);
            var line_chart = new google.visualization.LineChart(selector);
            line_chart.draw(google_chart_data, options);
            return line_chart;
        },
            //creates area graph
            GoogleChart.prototype.createAreaChart = function(selector, data, axislabel, colors) {
                var options = {
                    fontName: 'Open Sans',
                    height: 340,
                    curveType: 'function',
                    fontSize: 14,
                    chartArea: {
                        left: '5%',
                        width: '90%',
                        height: 300
                    },
                    pointSize: 4,
                    tooltip: {
                        textStyle: {
                            fontName: 'Open Sans',
                            fontSize: 14
                        }
                    },
                    vAxis: {
                        title: axislabel,
                        titleTextStyle: {
                            fontSize: 14,
                            italic: false
                        },
                        gridarea: {
                            color: '#f5f5f5',
                            count: 10
                        },
                        gridlines: {
                            color: '#f5f5f5'
                        },
                        minValue: 0
                    },
                    legend: {
                        position: 'top',
                        alignment: 'end',
                        textStyle: {
                            fontSize: 14
                        }
                    },
                    lineWidth: 2,
                    colors: colors
                };

                var google_chart_data = google.visualization.arrayToDataTable(data);
                var area_chart = new google.visualization.AreaChart(selector);
                area_chart.draw(google_chart_data, options);
                return area_chart;
            },

            //creates donut chart
            GoogleChart.prototype.createDonutChart = function(selector, data, colors) {
                var options = {
                    fontName: 'Open Sans',
                    fontSize: 13,
                    height: 300,
                    pieHole: 0.55,
                    width: 500,
                    chartArea: {
                        left: 50,
                        width: '90%',
                        height: '90%'
                    },
                    colors: colors
                };

                var google_chart_data = google.visualization.arrayToDataTable(data);
                var pie_chart = new google.visualization.PieChart(selector);
                pie_chart.draw(google_chart_data, options);
                return pie_chart;
            },
            //init
            GoogleChart.prototype.init = function () {
                var $this = this;

                //creating line chart
                var common_data = [
                    ['Year', 'Visits', 'Free Coffee'],
                    ['Week 1',  850,      120],
                    ['Week 2',  745,      200],
                    ['Week 3',  852,      180],
                    ['Week 4',  1000,      400]
                ];
                $this.createLineChart($('#line-chart')[0], common_data, 'Cafe Visits and Free Coffee', ['#297ef6', '#e52b4c']);

                var common_data1 = [
                    ['Year', 'Visits', 'Free Coffee'],
                    ['Jan',  850,      120],
                    ['Feb',  745,      200],
                    ['Mar',  852,      180],
                    ['Apr',  1000,      400],
                    ['May',  1170,      460],
                    ['Jun',  660,       1120]
                ];
                //creating area chart using same data
                $this.createAreaChart($('#area-chart')[0], common_data1, 'Cafe Visits and Free Coffee', ['#297ef6', '#e52b4c']);

                //creating pie chart
                var pie_data = [
                    ['Task', 'Hours per Day'],
                    ['Cafe Visits',     11],
                    ['Free Coffee',      2]
                ];

                //creating donut chart
                $this.createDonutChart($('#donut-chart')[0], pie_data, ['#5553ce','#297ef6', '#e52b4c', '#ffa91c', '#32c861']);


                //on window resize - redrawing all charts
                $(window).on('resize', function() {
                    $this.createLineChart($('#line-chart')[0], common_data, 'Cafe Visits and Free Coffee', ['#4bd396', '#f5707a']);
                    $this.createAreaChart($('#area-chart')[0], common_data, 'Cafe Visits and Free Coffee', ['#4bd396', '#f5707a']);
                    $this.createDonutChart($('#donut-chart')[0], pie_data, ['#188ae2', '#4bd396', '#f9c851', '#f5707a', '#6b5fb5']);
                });
            },
            //init GoogleChart
            $.GoogleChart = new GoogleChart, $.GoogleChart.Constructor = GoogleChart
    }(window.jQuery),

//initializing GoogleChart
        function($) {
            "use strict";
            //loading visualization lib - don't forget to include this
            google.load("visualization", "1", {packages:["corechart"]});
            //after finished load, calling init method
            google.setOnLoadCallback(function() {$.GoogleChart.init();});
        }(window.jQuery);


    !function($) {
        "use strict";

        var ChartJs = function() {};

        ChartJs.prototype.respChart = function(selector,type,data, options) {
            // get selector by context
            var ctx = selector.get(0).getContext("2d");
            // pointing parent container to make chart js inherit its width
            var container = $(selector).parent();

            // enable resizing matter
            $(window).resize( generateChart );

            // this function produce the responsive Chart JS
            function generateChart(){
                // make chart width fit with its container
                var ww = selector.attr('width', $(container).width() );
                switch(type){
                    case 'Line':
                        new Chart(ctx, {type: 'line', data: data, options: options});
                        break;
                    case 'Doughnut':
                        new Chart(ctx, {type: 'doughnut', data: data, options: options});
                        break;
                    case 'Pie':
                        new Chart(ctx, {type: 'pie', data: data, options: options});
                        break;
                    case 'Bar':
                        new Chart(ctx, {type: 'bar', data: data, options: options});
                        break;
                    case 'Radar':
                        new Chart(ctx, {type: 'radar', data: data, options: options});
                        break;
                    case 'PolarArea':
                        new Chart(ctx, {data: data, type: 'polarArea', options: options});
                        break;
                }
                // Initiate new chart or Redraw

            };
            // run function - render chart at first load
            generateChart();
        },

            ChartJs.prototype.init = function() {
                //donut chart
                var donutChart = {
                    labels: [
                        "Cafe Visits",
                        "Free Coffee"
                    ],
                    datasets: [
                        {
                            data: [{{$visits}}, {{$free}}],
                            backgroundColor: [
                                "#ffa91c",
                                "#32c861"
                            ],
                            hoverBackgroundColor: [
                                "#ffa91c",
                                "#32c861"
                            ],
                            hoverBorderColor: "#fff"
                        }]
                };
                this.respChart($("#doughnut"),'Doughnut',donutChart);

            },
            $.ChartJs = new ChartJs, $.ChartJs.Constructor = ChartJs

    }(window.jQuery),

//initializing
        function($) {
            "use strict";
            $.ChartJs.init()
        }(window.jQuery);

</script>
@endpush