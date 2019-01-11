@extends('admin.base')
@section('title', "Cafe's Admin")
@section('content')
    <style>
        .thumb-xl img{
            width:100%;
            height:100%;
        }
    </style>
    <div class="sk-double-bounce hide">
        <div class="sk-child sk-double-bounce1"></div>
        <div class="sk-child sk-double-bounce2"></div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group pull-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li>
                            <a href="{{url('/admin/dashboard')}}">Coffee Admin</a>
                        </li>
                        <li class="active">
                            Customers List
                        </li>
                        <li class="active">
                            {{ucfirst(Request::segment(3))}}
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">Customers List - {{ucfirst(Request::segment(3))}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title end breadcrumb -->
    <div class="btn-group m-b-10 text-center">
        <a href="{{url('admin/customers').'/'.Request::segment(3)}}" class="btn waves-effect @if(!Request::has('keyword')) btn-inverse waves-light @else btn-default @endif">All</a>
        @foreach(range('A', 'Z') as $char)
            <a href="{{url('admin/customers').'/'.Request::segment(3).'?keyword='.strtolower($char)}}" class="btn waves-effect @if(Request::has('keyword') && Request::input('keyword') == strtolower($char)) btn-inverse waves-light @else btn-default @endif">{{$char}}</a>
        @endforeach
    </div>
    <div class="clearfix"></div>
    @if(count($customers_list) > 0)
    @foreach ($customers_list as $user)
        <div class="col-md-4" gid="{{$user->user_id}}">
            <div class="text-center card-box">
                @if(Request::segment(3) == 'blocked')
                <div class="dropdown pull-left">
                    <a href="javascript:void(0)" gid="{{$user->user_id}}" status="delete" class="dropdown-toggle card-drop label label-danger change_users" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-power-off m-r-5"></i>Delete this
                    </a>
                </div>
                @endif
                <div class="dropdown pull-right">
                    <a href="javascript:void(0)" gid="{{$user->user_id}}" @if(Request::segment(3) == 'active') status="terminated" @else status="active" @endif class="dropdown-toggle card-drop label @if(Request::segment(3) == 'active') label-danger @elseif(Request::segment(3) == 'pending') label-success @else label-info @endif change_users" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-power-off m-r-5"></i>@if(Request::segment(3) == 'active') Block @else Approve @endif this
                    </a>
                </div>
                <div class="clearfix"></div>
                <div class="member-card">
                    <div class="thumb-xl member-thumb m-b-10 center-block">
                        <img src="@if($user->photo !='') {{url('images').'/'.$user->photo}} @else {{url('img/dummy.png')}} @endif" class="img-circle img-thumbnail"  alt="profile-image">
                        <i class="mdi member-star @if(Request::segment(3) == 'active') mdi-star-circle text-success @elseif(Request::segment(3) == 'pending') mdi-alert-circle text-warning @else mdi-close-circle text-danger @endif" title="verified user"></i>
                    </div>

                    <div class="">
                        <h4 class="m-b-5">{{$user->first_name.', '.$user->last_name}}</h4>
                        <p class="text-muted">{{$user->phone}} <span> | </span> <span> <a href="mailto:{{$user->email}}" class="text-pink">{{$user->email}}</a> </span></p>
                    </div>

                    <div class="m-t-20">
                        <div class="row">
                            <div class="col-xs-4 pull-left">
                                <div class="m-t-20 m-b-10">
                                    <h4 class="m-b-5">{{$user->free_coffee}}</h4>
                                    <p class="m-b-0 text-muted">Free Coffees</p>
                                </div>
                            </div>

                            <div class="col-xs-4 pull-right">
                                <div class="m-t-20">
                                    <h4 class="m-b-5">{{$user->visits}}</h4>
                                    <p class="m-b-0 text-muted">Total Visits</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    @endforeach
    @else
        <div class="row">
            <div class="col-sm-12 text-center">
                <div class="wrapper-page">
                    <div class="account-pages" style="display: inline !important;">
                        <div class="account-box">
                            <div class="account-content">
                                <h1 class="text-error" style="text-shadow: none !important;"><span class="fa fa-frown-o"></span></h1>
                                <h2 class="text-uppercase text-danger m-t-30">No Result Found</h2>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    @endif
    <!-- end row -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <div id="task-detail-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="full-width-modalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <div class="modal-body p-t-0" id="modal_body">

                    <div class="p-10 task-detail">
                        <div class="media m-t-0 m-b-20">
                            <div class="media-left">
                                <a href="#"> <img class="media-object img-circle" alt="64x64" src="assets/images/users/avatar-2.jpg" style="width: 48px; height: 48px;"> </a>
                            </div>
                            <div class="media-body">

                                <h4 class="media-heading m-b-5">Michael Zenaty</h4>
                                <span class="label label-danger">Urgent</span>
                            </div>
                        </div>

                        <ul class="list-inline task-dates m-b-0 m-t-20">
                            <li>
                                <h5 class="font-600 m-b-5">Start Date</h5>
                                <p> 22 March 2017 <small class="text-muted">1:00 PM</small></p>
                            </li>

                            <li>
                                <h5 class="font-600 m-b-5">Due Date</h5>
                                <p> 17 April 2017 <small class="text-muted">12:00 PM</small></p>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

@endsection
@push('css')
<link href="{{url('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@endpush
@push('js')
<script src="{{url('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>

<script type="text/javascript">

    $(document).on('click', '.view_detail', function () {
        var gid = $(this).attr( "gid" );
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".loader-container").addClass('visible');
        $.ajax({
            type     : "POST",
            url      : '{{url("/admin/get_cafe_detail")}}',
            dataType: 'JSON',
            data     : {
                'user_id': gid,
                '_token'  : $("#token").val(),
            },
            success  : function(data) {
                $(".loader-container").removeClass('visible');
                if(data.status == true) {
                    $("#modal_body").empty();
                    $("#modal_body").append(data.response);
                    $("#task-detail-modal").modal('show');
                }
            }
        });
    });

    $(document).on('click', '.change_users', function () {
        var gid = $(this).attr( "gid" );
        var status = $(this).attr( "status" );
        swal({
            title: 'Are you Sure',
            confirmButtonText: 'Yes, Do it!',
            text: "You won't be able to revert this!",
            showLoaderOnConfirm: true,
            showCloseButton: true,
            preConfirm: function () {
                return new Promise(function (resolve) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type     : "POST",
                        url      : '{{url("/admin/update_user_status")}}',
                        dataType: 'JSON',
                        data     : {
                            'user_id': gid,
                            'status': status,
                            '_token'  : $("#token").val(),
                        },
                        success  : function(data) {
                            $(".loader-container").removeClass('visible');
                            if(data.status == true) {
                                window.location.href = "{{url('/admin').'/'.Request::segment(2).'/'.Request::segment(3)}}";
                            }else{
                                swal(
                                    'Oops!',
                                    data.message,
                                    'error'
                                )
                            }
                        }
                    });

                })
            }
        });


    });
</script>
@endpush