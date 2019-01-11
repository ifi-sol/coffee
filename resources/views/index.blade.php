@extends('main')
@section('title', 'Home')
@section('content')
<div class="welcome-block">
    <p>Welcome Admin</p>
    <h1>How do you want to proceed?</h1>
</div>
<div class="row">
    <div class="col-md-3 offset-md-3">
        <div class="card card-intro">
            <div class="icon"><img src="{{url('img/qr-code.svg')}}" alt=""></div>
            <div class="card-block">
                <h4 class="card-title">Generate QR</h4>
                <p class="card-text">Edit, create or view QR Code for your cafe. </p><a class="btn btn-primary" href="{{url('/cafe/qrs')}}">Proceed</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-intro">
            <div class="icon"><img src="img/visitors.svg" alt=""></div>
            <div class="card-block">
                <h4 class="card-title">View Users</h4>
                <p class="card-text">View user visit to you cafe </p><a class="btn btn-primary" href="{{url('/cafe/users')}}">Proceed</a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script type="text/javascript">
    @if (Session::has('login'))
    iziToast.show({
        theme: 'light',
        backgroundColor: '#03c205',
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
</script>
@endpush