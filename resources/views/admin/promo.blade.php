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
                            <div class="checkbox checkbox-custom">
                                <input id="checkbox11" type="checkbox" name="promo" value="1" @if($promo_state->value == 'yes') checked="checked" @endif>
                                <label for="checkbox11">
                                    Promo Code State
                                </label>
                            </div>
                        </li>
                        <li class="active" style="margin-top: -5px;">
                            <button class="btn btn-success btn-rounded" id="generate_promo">Generate Promo</button>
                        </li>
                    </ol>
                </div>
                <h4 class="page-title">Cafe Promo Codes</h4>
            </div>
        </div>
    </div>
    <!-- end page title end breadcrumb -->

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <table id="datatable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Sr #</th>
                        <th>Promo Code</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Utilize Date</th>
                        <th>Expiry Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($promo_codes) > 0)
                        <?php $count = 1; ?>
                        @foreach ($promo_codes as $promo)
                    <tr>
                        <td>{{$count}}</td>
                        <td>{{$promo->promo_code}}</td>
                        <td>@if($promo->status == 'used') <span class="label label-success">Used</span> @else <span class="label label-warning">Un-Used</span>@endif</td>
                        <td>{{date("d M, Y", strtotime($promo->created_at))}}</td>
                        <td>@if($promo->utilize_date == Null) Not Yet @else {{date("d M, Y", strtotime($promo->utilize_date))}} @endif</td>
                        <td>{{date("d M, Y", strtotime($promo->expiry_date))}}</td>
                    </tr>
                            <?php $count++; ?>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
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
    $('#datatable').dataTable({
        "aaSorting": [],
    });
    $(document).on('click', '#generate_promo', function (e) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".loader-container").addClass('visible');
        $.ajax({
            type     : "POST",
            url      : '{{url("/admin/promo_codes")}}',
            dataType: 'JSON',
            data     : {
                '_token'  : $("#token").val(),
            },
            success  : function(data) {
                if(data.status == true) {
                    window.location.href = '{{url("/admin/promo_codes")}}';
                }else{
                    show_popup('Server Error','Oops! Some Server Occurred. Please try again later','error');
                }
            }
        });
    });

    $(document).on('change', '#checkbox11', function (e) {
        var promo = '';
        if($(this).prop("checked") == true){
            promo = 'yes';
        }
        else if($(this).prop("checked") == false){
            promo = 'no';
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".loader-container").addClass('visible');
        $.ajax({
            type     : "POST",
            url      : '{{url("/admin/change_status/promo_codes")}}',
            dataType: 'JSON',
            data     : {
                '_token'  : $("#token").val(),
                'promo'   : promo
            },
            success  : function(data) {
                if(data.status == true) {
                    show_popup('Success','Promo Code State Updated Successfully!','success');
                }else{
                    show_popup('Server Error','Oops! Some Server Occurred. Please try again later','error');
                }
            }
        });
    });


</script>
@endpush