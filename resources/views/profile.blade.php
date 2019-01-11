@extends('main')
@section('title', "Cafe Profile")
@section('sub_nav')
        <div class="sub-nav">
            <div class="left-area">

            </div>
            <div class="right-area">
                <div class="btn-group"><a id="re_generate" class="btn btn-secondary" href="javascript:void(0)">Cafe Profile</a></div>
            </div>
        </div>
@endsection
@section('content')
    <style>
        .fileuploader-theme-thumbnails .fileuploader-thumbnails-input, .fileuploader-theme-thumbnails .fileuploader-items-list li.fileuploader-item{
            width: 100% !important;
        }
        .parent{
            width:100%;
            height:300px;
            position: relative;
            margin-bottom: 50px;
        }
        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input {
            background-color: #fff;
            padding: 0 11px 0 13px;
            width: 400px;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            text-overflow: ellipsis;
        }

        #pac-input:focus {
            border-color: #4d90fe;
            margin-left: -1px;
            padding-left: 14px; /* Regular padding-left + 1. */
            width: 401px;
        }
        .pac-container {
            font-family: Roboto;
        }

        #type-selector {
            color: #fff;
            background-color: #4d90fe;
            padding: 5px 11px 0px 11px;
        }

        #type-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }
        #target {
            width: 345px;
        }
        .calendar-table {
            display: none !important;
        }
        .drp-calendar.right .calendar-time{
            margin:4px auto 0px 30px !important;
        }
        /* The customcheck */
        .customcheck {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 22px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default checkbox */
        .customcheck input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            border-radius: 5px;
        }

        /* On mouse-over, add a grey background color */
        .customcheck:hover input ~ .checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .customcheck input:checked ~ .checkmark {
            background-color: #02cf32;
            border-radius: 5px;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .customcheck input:checked ~ .checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .customcheck .checkmark:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }
    </style>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-basic card-profile">
                <div class="card-header">
                    <h2>Cafe Profile</h2>
                </div>
                <div class="card-image round"><img src="@if(count($cafe_admin) > 0 && $cafe_admin->cafe_photo1 !='') {{url('images').'/'.$cafe_admin->cafe_photo1}} @else {{url('img/coffee.svg')}} @endif" alt=""></div>
                <div class="card-block">
                    <h4 class="card-title">@if(count($cafe_admin) > 0){{$cafe_admin->first_name.' '.$cafe_admin->last_name}}@endif</h4>
                    <p> <span class="icon"><img src="{{url('img/location.svg')}}" alt=""></span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_street_address}}@endif</p>
                    <ul class="profile-info list-group list-group-flush">
                        <li class="list-group-item"><span>Cafe Name</span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_name}}@endif</li>
                        <li class="list-group-item"><span>Cafe Email</span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_email}}@endif</li>
                        <li class="list-group-item"><span>Cafe Phone</span>@if(count($cafe_admin) > 0){{$cafe_admin->cafe_phone}}@endif</li>
                        <li class="list-group-item"><span>Admin Email</span>@if(count($cafe_admin) > 0){{$cafe_admin->email}}@endif</li>
                        <li class="list-group-item"><span>Admin Phone</span>@if(count($cafe_admin) > 0){{$cafe_admin->phone}}@endif</li>
                    </ul>
                </div>
                <div class="card-block">
                    @if(count($check_timings) > 0)
                        <h4 class="card-title timings_heading">Cafe hours</h4>
                        <ul class="profile-info list-group list-group-flush timings">
                            @foreach($timings as $key => $value)
                                <li class="list-group-item">
                                    <span>{{ucfirst($value['name'])}}</span>
                                    @if($value['close'] == 'yes')
                                        Close
                                    @else
                                        {{$value['value']}}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-basic qr-card clearfix">
                <div class="spinner-container hide">
                    <div class="spinner">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
                <div class="card-header">
                    <h2>Update Profile</h2>
                </div>
                <div class="container">
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <h2>Cafe Admin Information</h2>
                        <div class="input-group">
                            <div class="logo-uploads" style="width: 250px; margin: 0 auto;">
                                <input type="file" name="files[]" id="fileuploader" @if(count($cafe_admin) > 0 && count($cafe_admin->photo) > 0)  data-fileuploader-files="{{json_encode($cafe_admin->photo)}}" @endif>
                            </div>
                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/user.svg')}}" alt=""></div>
                            <input class="form-control" name="first_name" type="text" placeholder="Cafe Admin First Name" value="@if(count($cafe_admin) > 0){{$cafe_admin->first_name}}@endif">
                        </div>
                        <div id="name1_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/user.svg')}}" alt=""></div>
                            <input class="form-control" name="last_name" type="text" placeholder="Cafe Admin Last Name" value="@if(count($cafe_admin) > 0){{$cafe_admin->last_name}}@endif">
                        </div>
                        <div id="name2_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/mail.svg')}}" alt=""></div>
                            <input class="form-control" name="email" disabled="disabled" type="email" placeholder="Cafe Admin Email" value="@if(count($cafe_admin) > 0){{$cafe_admin->email}}@endif">
                        </div>
                        <div id="email_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/smartphone.svg')}}" alt=""></div>
                            <input class="form-control" name="phone" type="text" placeholder="Admin Phone" value="@if(count($cafe_admin) > 0){{$cafe_admin->phone}}@endif">
                        </div>
                        <div id="phone_error" style="color: #ff5b5b;text-align: left;"></div>
                        <h2>Cafe Information</h2>
                        <div class="input-group">
                            <div class="logo-uploads" style="width: 250px; margin: 0 auto;">
                                <input type="file" name="files1[]" id="fileuploader1" @if(count($cafe_admin) > 0 && count($cafe_admin->cafe_photo) > 0)  data-fileuploader-files="{{json_encode($cafe_admin->cafe_photo)}}" @endif>
                            </div>
                        </div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/user.svg')}}" alt=""></div>
                            <input class="form-control" name="name" type="text" placeholder="Cafe Name" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_name}}@endif">
                        </div>
                        <div id="name_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/mail.svg')}}" alt=""></div>
                            <input class="form-control" name="email1" type="text" placeholder="Cafe Email" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_email}}@endif">
                        </div>
                        <div id="mail_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/smartphone.svg')}}" alt=""></div>
                            <input class="form-control" name="phone1" type="text" placeholder="Cafe Phone" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_phone}}@endif">
                        </div>
                        <div id="phone1_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group form-group">
                            <div class="input-group-addon"><img src="{{url('/img/dribbble-logo.svg')}}" alt=""></div>
                            <input class="form-control" name="website" type="text" placeholder="Cafe Website" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_website}}@endif">
                        </div>
                        <div id="website_error" style="color: #ff5b5b;text-align: left;"></div>
                        <div class="input-group">
                            <textarea rows="10" class="form-control" name="description" placeholder="Cafe description" >@if(count($cafe_admin) > 0){{$cafe_admin->description}}@endif</textarea>
                        </div>
                        <div id="des_error" style="color: #ff5b5b;text-align: left;"></div>


                        <h2>Cafe Location</h2>
                        <p><span>Place marker to find your location or enter your address below</span></p>
                        <div class="parent clearfix">
                            <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                            <div id="map_canvas"></div>
                        </div>
                            <div class="clearfix"></div>
                            <div class="input-group form-group">
                                <div class="input-group-addon"><img src="{{url('/img/location.svg')}}" alt=""></div>
                                <input class="form-control" name="address" id="address" type="text" placeholder="Cafe Street Address" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_street_address}}@endif">
                            </div>
                            <div id="address_error" style="color: #ff5b5b;text-align: left;"></div>
                            <div class="input-group form-group">
                                <div class="input-group-addon"><img src="{{url('/img/location.svg')}}" alt=""></div>
                                <input class="form-control" name="region" id="region" type="text" placeholder="Cafe Region" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_region}}@endif">
                            </div>
                            <div id="region_error" style="color: #ff5b5b;text-align: left;"></div>
                            <div class="input-group form-group">
                                <div class="input-group-addon"><img src="{{url('/img/location.svg')}}" alt=""></div>
                                <input class="form-control" name="city" id="city" type="text" placeholder="Cafe City" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_city}}@endif">
                            </div>
                            <div id="city_error" style="color: #ff5b5b;text-align: left;"></div>
                            <div class="input-group form-group">
                                <div class="input-group-addon"><img src="{{url('/img/location.svg')}}" alt=""></div>
                                <input class="form-control" name="code" id="code" type="text" placeholder="Cafe Post Code" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_post_code}}@endif">
                            </div>
                            <div id="post_error" style="color: #ff5b5b;text-align: left;"></div>
                            <div class="input-group form-group">
                                <div class="input-group-addon"><img src="{{url('/img/location.svg')}}" alt=""></div>
                                <input class="form-control" name="country" id="country" type="text" placeholder="Cafe Country" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_country}}@endif">
                            </div>
                            <div id="country_error" style="color: #ff5b5b;text-align: left;"></div>
                            <input type="hidden" id="lat" name="lat" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_latitude}}@endif">
                            <input type="hidden" id="lng" name="lng" value="@if(count($cafe_admin) > 0){{$cafe_admin->cafe_longitude}}@endif">




                        <h2>Cafe Timings</h2>
                        @foreach($timings as $key => $value)
                        <div class="row">
                            <div class="col-md-9">
                                <div class="input-group form-group">
                                    <div class="input-group-addon" style="font-weight: 700">{{ucfirst($value['name'])}}</div>
                                    <input class="form-control" name="{{$value['id']}}" id="{{$value['id']}}" type="text" @if($value['close'] == 'yes') disabled="disabled" style="color: #eceeef !important;"  @else value="{{$value['value']}}" @endif placeholder="Choose time for {{ucfirst($value['name'])}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="customcheck">Close this day
                                    <input type="checkbox" name="close[{{$value['id']}}]" class="{{$value['id']}}" @if($value['close'] == 'yes') checked="checked" value="{{$value['value']}}" @endif>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        @endforeach



                        <div class="error_block alert alert-danger" style="display: none;"></div>
                        <div class="submit-area" style="margin-bottom: 20px">
                            <button class="btn btn-primary btn-block" type="submit">Update Profile</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
    <style>
        #map_canvas { margin: 0; padding: 0; height: 100%;}
    </style>
@endsection
@push('css')
<link rel="stylesheet" href="{{url('css/jquery.fileuploader.css')}}">
<link rel="stylesheet" href="{{url('css/jquery.fileuploader-theme-thumbnails.css')}}">
@endpush
@push('js')
<script src="{{url('js/jquery.fileuploader.min.js')}}"></script>
<script src="{{url('js/formValidation.min.js')}}"></script>
<script src="{{url('js/bootstrap_min.js')}}"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCj5qqI4UL0do4QcAn4guQ6Ssdpk7Csd78&v=3.exp&
sensor=false&libraries=places">
</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript">
    $(document).ready(function() {

        $(function() {
            @foreach($timings as $key => $value)
              @if($value['value'] != '')
                $("#{{$value['id']}}").daterangepicker({
                    timePicker: true,
                    locale: {
                        format: 'hh:mm A'
                    }
                });
              @else
                  $("#{{$value['id']}}").daterangepicker({
                      timePicker: true,
                      startDate: moment().startOf('hour'),
                      endDate: moment().startOf('hour').add(15, 'minutes'),
                      locale: {
                          format: 'hh:mm A'
                      }
                  });
              @endif


            $("#{{$value['id']}}").on('apply.daterangepicker', function(ev, picker) {
                if(picker.startDate.format('hh:mm A') == picker.endDate.format('hh:mm A')) {
                    $(this).val(picker.startDate.format('hh:mm A') + ' - ' + moment(picker.endDate.format('hh:mm A'), 'hh:mm A').add(15, 'minutes').format('hh:mm A'));
                }else if(picker.startDate.format('hh:mm A') < picker.endDate.format('hh:mm A')) {
                    //alert("Hello kidr? Hosh kar k nails lo.");
                }
            });

            $(".{{$value['id']}}").change(function() {
                if(this.checked) {
                    $("#{{$value['id']}}").attr('disabled','disabled').val('');
                }else{
                    var time = moment().startOf('hour').format('hh:mm A') + ' - ' + moment().startOf('hour').add(15, 'minutes').format('hh:mm A');
                    $("#{{$value['id']}}").removeAttr('disabled','disabled').val(time).css("color", "#000");;
                }
            });
            @endforeach
        });

        var map;
        var geocoder;
        var mapOptions = { center: new google.maps.LatLng(0.0, 0.0), zoom: 2,
            mapTypeId: google.maps.MapTypeId.ROADMAP };

        function initialize() {
            var myOptions = {
                center: new google.maps.LatLng(49.2835516, -123.1228422 ),
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            geocoder = new google.maps.Geocoder();
            var map = new google.maps.Map(document.getElementById("map_canvas"),
                myOptions);
            google.maps.event.addListener(map, 'click', function(event) {
                placeMarker(event.latLng);
            });

            var marker;
            function placeMarker(location) {
                if(marker){ //on vérifie si le marqueur existe
                    marker.setPosition(location); //on change sa position
                }else{
                    marker = new google.maps.Marker({ //on créé le marqueur
                        position: location,
                        map: map
                    });
                }
                document.getElementById('lat').value=location.lat();
                document.getElementById('lng').value=location.lng();
                getAddress(location);
            }

            function getAddress(latLng) {
                geocoder.geocode( {'latLng': latLng},
                    function(results, status) {
                        if(status == google.maps.GeocoderStatus.OK) {
                            if(results[0]) {
                                console.log(results[0]);
                                document.getElementById("address").value = results[0].formatted_address;
                                $("#profile_form").formValidation("revalidateField", "address", true, 'callback');
                                $.each(results[0]['address_components'], function (key, val) {

                                    $cn=val['types'];

                                    if($.inArray("country", $cn) !== -1){
                                        document.getElementById("country").value = val['long_name'];
                                        $("#profile_form").formValidation("revalidateField", "country", true, 'callback');
                                    }

                                    if($.inArray("administrative_area_level_2", $cn) !== -1){
                                        document.getElementById("city").value = val['long_name'];
                                        $("#profile_form").formValidation("revalidateField", "city", true, 'callback');
                                    }

                                    if($.inArray("administrative_area_level_1", $cn) !== -1){
                                        document.getElementById("region").value = val['long_name'];
                                        $("#profile_form").formValidation("revalidateField", "region", true, 'callback');
                                    }

                                    if($.inArray("postal_code", $cn) !== -1){
                                        document.getElementById("code").value = val['long_name'];
                                        $("#profile_form").formValidation("revalidateField", "code", true, 'callback');
                                    }
                                });



                            }
                            else {
                                document.getElementById("address").value = "No results";
                            }
                        }
                        else {
                            document.getElementById("address").value = status;
                        }
                    });
            }

            var markers = [];
            var input = /** @type {HTMLInputElement} */(
                document.getElementById('pac-input'));
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            var searchBox = new google.maps.places.SearchBox(
                /** @type {HTMLInputElement} */(input));
            google.maps.event.addListener(searchBox, 'places_changed', function() {
                var places = searchBox.getPlaces();

                for (var i = 0, marker; marker = markers[i]; i++) {
                    marker.setMap(null);
                }

// For each place, get the icon, place name, and location.
                markers = [];
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0, place; place = places[i]; i++) {
                    var image = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

// Create a marker for each place.
                    var marker = new google.maps.Marker({
                        map: map,
                        icon: image,
                        title: place.name,
                        position: place.geometry.location
                    });

                    markers.push(marker);
                    bounds.extend(place.geometry.location);
                }

                map.fitBounds(bounds);
            });
// [END region_getplaces]

// Bias the SearchBox results towards places that are within the bounds of the
// current map's viewport.
            google.maps.event.addListener(map, 'bounds_changed', function() {
                var bounds = map.getBounds();
                searchBox.setBounds(bounds);
            });
        }




        google.maps.event.addDomListener(window, 'load', initialize);





        // enable fileuploader plugin
        $('#fileuploader,#fileuploader1').fileuploader({
            limit: 1,
            extensions: ['jpg', 'jpeg', 'png', 'gif'],
            changeInput: ' ',
            theme: 'thumbnails',
            enableApi: true,
            addMore: true,
            thumbnails: {
                box: '<div class="fileuploader-items">\
                      <ul class="fileuploader-items-list">\
					      <li class="fileuploader-thumbnails-input"><div class="fileuploader-thumbnails-input-inner">+</div></li>\
                      </ul>\
                  </div>',
                item: '<li class="fileuploader-item">\
				       <div class="fileuploader-item-inner">\
                           <div class="thumbnail-holder">${image}</div>\
                           <div class="actions-holder">\
                               <a class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="remove"></i></a>\
                           </div>\
                       	   <div class="progress-holder">${progressBar}</div>\
                       </div>\
                   </li>',
                item2: '<li class="fileuploader-item">\
				       <div class="fileuploader-item-inner">\
                           <div class="thumbnail-holder">${image}</div>\
                           <div class="actions-holder">\
                               <a class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="remove"></i></a>\
                           </div>\
                       </div>\
                   </li>',
                startImageRenderer: true,
                canvasImage: false,
                _selectors: {
                    list: '.fileuploader-items-list',
                    item: '.fileuploader-item',
                    start: '.fileuploader-action-start',
                    retry: '.fileuploader-action-retry',
                    remove: '.fileuploader-action-remove'
                },
                onItemShow: function(item, listEl, parentEl, newInputEl, inputEl) {
                    var plusInput = listEl.find('.fileuploader-thumbnails-input'),
                        api = $.fileuploader.getInstance(inputEl.get(0));

                    if(api.getFiles().length >= api.getOptions().limit) {
                        plusInput.hide();
                    }

                    plusInput.insertAfter(item.html);


                    if(item.format == 'image') {
                        item.html.find('.fileuploader-item-icon').hide();
                    }
                },
                onItemRemove: function(html, listEl, parentEl, newInputEl, inputEl) {
                    var plusInput = listEl.find('.fileuploader-thumbnails-input'),
                        api = $.fileuploader.getInstance(inputEl.get(0));

                    html.children().animate({'opacity': 0}, 200, function() {
                        setTimeout(function() {
                            html.remove();

                            if(api.getFiles().length - 1 < api.getOptions().limit) {
                                plusInput.show();
                            }
                        }, 100);
                    });

                }
            },
            afterRender: function(listEl, parentEl, newInputEl, inputEl) {
                var plusInput = listEl.find('.fileuploader-thumbnails-input'),
                    api = $.fileuploader.getInstance(inputEl.get(0));

                plusInput.on('click', function() {
                    api.open();
                });
            }
        });


        $("#profile_form").formValidation({
            framework: "bootstrap",
            fields: {
                first_name: {
                    err: "#name1_error",
                    validators: {
                        notEmpty: {
                            message: 'First Name is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'First Name must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'First Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'First Name Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                last_name: {
                    err: "#name2_error",
                    validators: {
                        notEmpty: {
                            message: 'Last Name is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Last Name must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'Last Name Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                email: {
                    err: "#email_error",
                    validators: {
                        notEmpty: {
                            message: "Please Enter Your Email address"
                        },
                        emailAddress: {
                            message: 'The value is not a valid email address'
                        }
                    }
                },
                name: {
                    err: "#name_error",
                    validators: {
                        notEmpty: {
                            message: 'Cafe Name is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Cafe Name must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'Cafe Name Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                description: {
                    err: "#des_error",
                    validators: {
                        stringLength: {
                            message: 'Description content must be maximum 500 characters long',
                            max: 500
                        }
                    }
                },
                address: {
                    err: "#address_error",
                    validators: {
                        notEmpty: {
                            message: 'Street Address is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Street Address must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'Street Address Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                region: {
                    err: "#region_error",
                    validators: {
                        notEmpty: {
                            message: 'Region is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Region must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'Region Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                city: {
                    err: "#city_error",
                    validators: {
                        notEmpty: {
                            message: 'City is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'City must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'City Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                code: {
                    err: "#post_error",
                    validators: {
                        notEmpty: {
                            message: 'Post Code is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Post Code must be more than 3 and less than 100 characters long'
                        }
                    }
                },
                country: {
                    err: "#country_error",
                    validators: {
                        notEmpty: {
                            message: 'Country is required'
                        },
                        stringLength: {
                            max: 3,
                            max: 100,
                            message: 'Country must be more than 3 and less than 100 characters long'
                        },
                        callback: {
                            message: 'Last Name Should be Alpha Numeric',
                            callback: function (value, event_name, $field) {
                                // The event name doesn't contain only digit
                                if (value.match(/^[0-9]+$/) != null) {
                                    return {
                                        valid: false,
                                        message: 'Country Should not be Numeric'
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                email1: {
                    err: "#mail_error",
                    validators: {
                        emailAddress: {
                            message: 'The value is not a valid email address'
                        }
                    }
                },
                phone: {
                    err: "#phone_error",
                    validators: {
                        regexp: {
                            regexp: /^[0-9]+$/,
                            message: 'Phone can only consist of numbers'
                        }
                    }
                },
                phone1: {
                    err: "#phone1_error",
                    validators: {
                        regexp: {
                            regexp: /^[0-9]+$/,
                            message: 'Phone can only consist of numbers'
                        }
                    }
                },
            }
        }).on("success.form.fv", function(e) {
            e.preventDefault(); {
                var t = $(e.target);
                t.data("formValidation")
            }
            var formData = new FormData($("#profile_form")[0]);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(".spinner-container").removeClass('hide');
            $.ajax({
                type     : "POST",
                url      : '{{url("/cafe/profile")}}',
                data     : formData,
                cache: false,
                contentType: false,
                processData: false,
                async: true,
                success  : function(data) {
                    setTimeout(function(){
                        $(".spinner-container").addClass('hide');
                    }, 1000);
                    $('#profile_form').formValidation('disableSubmitButtons', false);
                    if(data.status == true){
                        window.location = '{{url('/cafe/profile')}}';
                    }else{
                        //show_popup('Server Error',data.message,'error')
                        $('.error_block').text(data.message).show().fadeOut(5000);
                    }
                },
                error:function(data){
                    $('#profile_form').formValidation('disableSubmitButtons', false);
                    setTimeout(function(){
                        $(".spinner-container").addClass('hide');
                    }, 1000);
                    $('.error_block').text('Oops! Some Server Occurred. Please try again later').show().fadeOut(5000);
                }
            })
        });

    });

</script>
@endpush