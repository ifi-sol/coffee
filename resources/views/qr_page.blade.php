@extends('main')
@section('title', "Cafe Qr's")
@section('sub_nav')
        <div class="sub-nav">
            <div class="left-area">
                <p class="title">Cafe QR with Profile</p>
            </div>
            <div class="right-area">
                @if(count($qr_count) > 0)
                    <div class="btn-group"><a id="re_generate" class="btn btn-secondary generate_qr" href="javascript:void(0)">Regenerate QR</a></div>
                @endif
            </div>
        </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card card-basic card-profile">
                <div class="card-header">
                    <h2>Cafe Profile</h2>
                </div>
                <div class="card-image round"><img src="@if(count($cafe_admin) > 0 && $cafe_admin->cafe_photo1 !='') {{url('images').'/'.$cafe_admin->cafe_photo1}} @else {{url('img/coffee.svg')}} @endif" alt=""></div>
                <div class="card-block">
                    <h4 class="card-title">{{$cafe_admin->first_name.' '.$cafe_admin->last_name}}</h4>
                    <p> <span class="icon"><img src="{{url('img/location.svg')}}" alt=""></span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_street_address}}@endif</p>
                    <ul class="profile-info list-group list-group-flush">
                        <li class="list-group-item"><span>Cafe Name</span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_name}}@endif</li>
                        <li class="list-group-item"><span>Cafe Email</span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_email}}@endif</li>
                        <li class="list-group-item"><span>Cafe Phone</span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_phone}}@endif</li>
                        <li class="list-group-item"><span>Admin Email</span>@if(count($cafe_admin) > 0){{$cafe_admin->email}}@endif</li>
                        <li class="list-group-item"><span>Admin Phone</span>@if(count($cafe_admin) > 0){{$cafe_admin->phone}}@endif</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-basic qr-card">
                <div class="spinner-container hide">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
                <div class="card-header">
                    <h2>QR Code</h2>
                </div>
                <div class="qr-section">
                    @if(count($qr_count) == 0)
                    <div class="empty-area">
                        <div class="icon"><img src="{{url('img/coffee-cup.svg')}}" alt=""></div>
                        <div class="content">
                            <h4>Hey There!</h4>
                            <p>No QR Generated, Have a cup of Coffee instead!</p><a class="btn btn-primary generate_qr" href="javascript:void(0)">Generate QR </a>
                        </div>
                    </div>
                    @else
                        <img src="{{url('images/qr_codes').'/'.$qr_image->pdf_name.'.png'}}" alt=""><br>
                        <a class="btn btn-primary download_q" gid="{{$qr_image->cafe_qr_id}}" href="{{url('/cafe/generate_pdf?status=').base64_encode($qr_image->cafe_qr_id)}}" target="_blank">Download as PDF </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
@endsection
@push('js')
<script type="text/javascript">
    $(document).on('click','.generate_qr',function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".spinner-container").removeClass('hide');
        $.ajax({
            type     : "POST",
            url      : '{{url("/cafe/generate_qr")}}',
            data     : {
                'status': 'add',
                '_token'  : $("#token").val(),
            },
            success  : function(data) {
                setTimeout(function(){
                    $(".spinner-container").addClass('hide');
                }, 1000);
                if(data.status == true){
                    $(".qr-section").empty();
                    $(".qr-section").append('<img src="{{url('images/qr_codes').'/'}}'+data.qr_image+'.png" alt=""><br><a class="btn btn-primary download_q" gid="'+data.qr_id+'" href="'+data.url+'" target="_blank">Download as PDF </a>');
                    if ($("#re_generate").hasClass("generate_qr")) {
                    }else{
                        $("#re_generate").addClass("generate_qr");
                    }
                    show_popup('QR Generated','Your QR Generated Successfully.','success');
                }else{
                    show_popup('Server Error',data.message,'error');
                }
            },
            error:function(data){
                setTimeout(function(){
                    $(".spinner-container").addClass('hide');
                }, 1000);
                show_popup('error','Oops! Some Server Error. Please try again','Server Error');
            }
        })
    });

    $(document).on('click','.download_qr',function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(".spinner-container").removeClass('hide');
        $.ajax({
            type     : "POST",
            url      : '{{url("/cafe/generate_pdf")}}',
            data     : {
                'status': $(this).attr('gid'),
                '_token'  : $("#token").val(),
            },
            success  : function(data) {
                setTimeout(function(){
                    $(".spinner-container").addClass('hide');
                }, 1000);
                if(data.status == true){
                    show_popup('QR Generated','Your QR Generated Successfully.','success');
                }else{
                    show_popup('Server Error','Oops! Some Server Error. Please try again lator.','error');
                }
            },
            error:function(data){
                setTimeout(function(){
                    $(".spinner-container").addClass('hide');
                }, 1000);
                show_popup('error','Oops! Some Server Error. Please try again','Server Error');
            }
        })
    });
</script>
@endpush