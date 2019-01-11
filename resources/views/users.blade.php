@extends('main')
@section('title', 'Scanned Users')
@section('sub_nav')
    <div class="sub-nav">
        <div class="left-area" style="margin-top:6px;">
            <form class="form-inline" id="search_form">
                <div class="input-group">
                    <div class="input-group-addon"><img src="{{url('/img/search.svg')}}" alt=""></div>
                    <input class="form-control mr-sm-2 keyword" name="keyword" type="text" placeholder="Search User">
                </div>
            </form>
        </div>
        <div class="right-area">
            <div style="color: #fff;display:inline-block; font-weight: bold;margin-top:none !important"  >Total Free Coffee</div> <span class="count btn btn-secondary" style="font-weight: 700; margin: 0 10px">{{count($free_coffee)}}</span>
            <div style="color: #fff;display:inline-block;font-weight: bold;margin-top:none !important">Total Sold Coffee</div> <span class="count btn btn-secondary" style="font-weight: 700; margin: 0 10px">{{count($cafe_visits)}}</span>
        </div>
    </div>
@endsection
@section('content')
    <section id="content">
        <div class="container-fluid cards-content">
            <div class="spinner-container hide">
                <div class="spinner">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>
                @if(count($buyers) > 0)
                    <div id="search_content" class="row">
                        @include('search_users')
                    </div>
                @else
                    <p class="alert alert-warning">No Record Found</p>
                @endif
            @if(count($buyers) > 0)
            {{ $buyers->links() }}
                @endif
        </div>
    </section>
@endsection
@push('js')
<script type="text/javascript">
    $('.count').each(function () {
        $(this).prop('Counter',0).animate({
            Counter: $(this).text()
        }, {
            duration: 4000,
            easing: 'swing',
            step: function (now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
    @if (Session::has('messages'))
    iziToast.show({
        theme: 'light',
        backgroundColor: '#00c292',
        image: '@if(Session::get('Coffee_Cafe_Logged_in.picture') !='') images/{{Session::get('Coffee_Cafe_Logged_in.picture')}} @else img/dummy.png @endif',
        imageWidth: 50,
        title: 'Welcome {{Session::get('Coffee_Cafe_Logged_in.first_name')}}',
        titleColor: '#fff',
        titleSize: '16px',
        message: 'You have Logged in to Cafe Panel',
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
    $('input.keyword').on('input',function(event){
        event.preventDefault();
        $(".spinner-container").removeClass('hide');
        $.ajax({
            type     : "GET",
            url      : '{{url("/cafe/users/ajax_get_users")}}',
            data     : {
                '_token'    : $("#token").val(),
                'keyword'      : $(this).val(),
            },
            success  : function(data) {
                $(".spinner-container").addClass('hide');
                $('#search_content').html(data);
            },

        });
    });

    $('.sorting').on('click',function(event){
        event.preventDefault();
        $(".spinner-container").removeClass('hide');
        $.ajax({
            type     : "GET",
            url      : '{{url("/cafe/users/ajax_get_users")}}',
            data     : {
                '_token'    : $("#token").val(),
                'keyword'      : $('.keyword').val(),
                'sorting'   : $(this).attr('gid')
            },
            success  : function(data) {
                $(".spinner-container").addClass('hide');
                $('#search_content').html(data);
            },

        });
    });
</script>
@endpush