@extends('admin.base')
@section('title', "Cafe's Admin")
@section('content')
    <style>
        .ribbon-box .ribbon-two{
            transform: rotate(90deg);
            right: -5px !important;
            left: unset !important;
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
                            Cafe List
                        </li>
                        <li class="active">
                            {{ucfirst(Request::segment(3))}}
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">Cafe List - {{ucfirst(Request::segment(3))}}</h4>
            </div>
        </div>
    </div>
    <!-- end page title end breadcrumb -->
    @if(Request::segment(3) == 'active')
    <div class="btn-group m-b-10 text-center">
        <a href="{{url('admin/cafe').'/'.Request::segment(3)}}" class="btn waves-effect @if(!Request::has('keyword')) btn-inverse waves-light @else btn-default @endif">All</a>
        @foreach(range('A', 'Z') as $char)
            <a href="{{url('admin/cafe').'/'.Request::segment(3).'?keyword='.strtolower($char)}}" class="btn waves-effect @if(Request::has('keyword') && Request::input('keyword') == strtolower($char)) btn-inverse waves-light @else btn-default @endif">{{$char}}</a>
        @endforeach
    </div>
    @if(count($cafe_list) > 0)
    @foreach ($cafe_list->chunk(4) as $chunk)
        <div class="row">
            @foreach ($chunk as $cafe)
                <div class="col-md-6 col-lg-3" gid="{{$cafe->user_id}}">
                <div class="company-card card-box ribbon-box">
                    <a href="javascript:void(0)" gid="{{$cafe->user_id}}" @if(Request::segment(3) == 'active') status="terminated" @else status="active" @endif class="change_users ribbon-two @if(Request::segment(3) == 'active') ribbon-two-danger @elseif(Request::segment(3) == 'pending') ribbon-two-success @else ribbon-two-info @endif" ><span>@if(Request::segment(3) == 'active') Block @else Approve @endif this</span></a>
                    <!--<div class="dropdown pull-right">
                        <a href="javascript:void(0)" gid="{{$cafe->user_id}}" class="dropdown-toggle card-drop label @if(Request::segment(3) == 'active') label-danger @elseif(Request::segment(3) == 'pending') label-success @else label-info @endif change_users" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-power-off m-r-5"></i>@if(Request::segment(3) == 'active') Block @else Approve @endif this
                        </a>
                    </div>-->
                    <img src="@if($cafe->cafe_photo !='') {{url('images').'/'.$cafe->cafe_photo}} @else {{url('img/coffee.svg')}} @endif" alt="logo" gid="{{$cafe->cafe_id}}" class="company-logo waves-effect waves-light view_detail">
                    <div class="company-detail">
                        <h4 class="m-b-5 view_detail" style="cursor: pointer" gid="{{$cafe->cafe_id}}">@if(strlen($cafe->cafe_name) > 15){{substr($cafe->cafe_name,0,15),' ...'}}@else{{$cafe->cafe_name}}@endif</h4>
                        <p>@if(strlen($cafe->cafe_street_address) > 15){{substr($cafe->cafe_street_address, 0, 15).' ...'}}@else{{$cafe->cafe_street_address}}@endif</p>
                    </div>

                    <hr/>

                    <h5 class="text-muted font-normal"><span class="pull-right @if($cafe->visits > 0) text-success @else text-danger @endif"><i class="mdi @if($cafe->visits > 0) mdi-arrow-up @else mdi-arrow-down @endif"></i> {{$cafe->visits}}</span> Customer Visits</h5>

                    <div class="text-center m-t-20">
                        <h5 class="font-normal text-muted">You have Awarded</h5>
                        <h3 class="m-b-30"><i class="mdi @if($cafe->free_coffee > 0) mdi-arrow-up-bold-hexagon-outline text-success @else mdi-arrow-down-bold-hexagon-outline text-danger @endif"></i> {{$cafe->free_coffee}} <small>free Coffees</small></h3>
                    </div>

                    <div id="company-1" class="text-center"></div>

                </div>
            </div>
            @endforeach
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

    @else
    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Cafe Name</th>
                        <th>Admin Name</th>
                        <th>Cafe Phone</th>
                        <th>Cafe Email</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($cafe_list) > 0)
                        @foreach ($cafe_list as $cafe)
                    <tr>
                        <td><a class="view_detail" style="cursor: pointer" gid="{{$cafe->cafe_id}}">{{$cafe->cafe_name}}</td>
                        <td>{{$cafe->first_name.' '.$cafe->last_name}}</td>
                        <td>{{$cafe->cafe_phone}}</td>
                        <td>{{$cafe->cafe_email}}</td>
                        <td>
                            <a href="javascript:void(0)" gid="{{$cafe->user_id}}" @if(Request::segment(3) == 'active') status="terminated" @else status="active" @endif class="change_users"><span class="label label-table label-success">Activate</span></a>
                            @if(Request::segment(3) == 'blocked')
                                <a href="javascript:void(0)" gid="{{$cafe->user_id}}" status="delete" class="change_users"><span class="label label-table label-danger">Delete</span></a>
                            @endif
                            @if(Request::segment(3) != 'active')
                                <a href="javascript:void(0)" gid="{{$cafe->user_id}}" class="send_mail"><span class="label label-table label-info">Send Email</span></a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
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

    <div id="email_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="full-width-modalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <div class="modal-body p-t-0" id="modal_body">
                    <form method="post" id="email_form">
                    <div class="p-10 task-detail">
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" class="form-control" name="subject" placeholder="Enter Email Subject">
                        </div>

                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control" id="summernote" name="msg"></textarea>
                        </div>
                        <input type="hidden" name="user_id" id="user_id">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group">
                            <button class="btn btn-block btn-success btn-lg" type="submit">Send Email</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

@endsection
@push('css')
<link href="{{url('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
@if(Request::segment(3) != 'active')
    <link href="{{url('assets/plugins/datatables/jquery.dataTables.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{url('assets/plugins/summernote/summernote.css')}}" rel="stylesheet" type="text/css" />
@endif
@endpush
@push('js')
<script src="{{url('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
@if(Request::segment(3) != 'active')
    <script src="{{url('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{url('assets/plugins/datatables/dataTables.bootstrap.js')}}"></script>
    <script src="{{url('assets/plugins/summernote/summernote.min.js')}}"></script>
    <script src="{{url('js/formValidation.min.js')}}"></script>
    <script src="{{url('js/bootstrap_min.js')}}"></script>
@endif
<script type="text/javascript">
    @if(Request::segment(3) != 'active')
    $('#datatable').dataTable({
        "aaSorting": [],
    });

    $('#summernote').summernote({
        height: 150,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    }).on('summernote.change', function(customEvent, contents, $editable) {
        // Revalidate the content when its value is changed by Summernote
        $('#email_form').formValidation('revalidateField', 'msg');
    });

    @endif
    $(document).on('click', '.view_detail', function (e) {
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
                                window.location.href = "{{url('/admin/cafe').'/'.Request::segment(3)}}";
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


    $(document).on('click', '.send_mail', function () {
        var gid = $(this).attr( "gid" );
        $('#email_form #user_id').val(gid);
        $("#email_modal").modal('show');
    });

    $("#email_form").formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        fields: {
            subject: {
                validators: {
                    notEmpty: {
                        message: 'Email Subject is required'
                    },
                    stringLength: {
                        min: 3,
                        max: 50,
                        message: 'Email Subject must be more than 3 and less than 50 characters long'
                    }
                }
            },
            msg: {
                validators: {
                    callback: {
                        message: 'Email Message is required and cannot be empty',
                        callback: function(value, validator, $field) {
                            var code = $('[name="msg"]').summernote('code');
                            // <p><br></p> is code generated by Summernote for empty content
                            return (code !== '' && code !== '<p><br></p>');
                        }
                    }
                }
            }
        }
    }).on("success.form.fv", function(e) {
        e.preventDefault(); {
            var t = $(e.target);
            t.data("formValidation")
        }
        var formData = new FormData($("#email_form")[0]);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".spinner-container").removeClass('hide');
        $.ajax({
            type     : "POST",
            url      : '{{url("/admin/send_mail_to_user")}}',
            data     : formData,
            cache: false,
            async: false,
            contentType: false,
            processData: false,
            success  : function(data) {
                if(data.status == true){
                    location.reload();
                }else{
                    show_popup('Server Error','Oops! Some Server Occurred. Please try again later','error');
                }
            },
            error:function(data){
                show_popup('Server Error','Oops! Some Server Occurred. Please try again later','error');
            }
        })
    });
</script>
@endpush