@if(count($buyers) > 0)
    @foreach($buyers as $buyer)
        <div class="col-md-3">
            <div class="card card-user">
                <div class="card-below"></div>
                <div class="avatar"><img src="@if($buyer->user->photo != '') {{url('images').'/'.$buyer->user->photo}} @else {{url('img/dummy.png')}} @endif" alt=""></div>
                <div class="card-block">
                    <h4 class="card-title">{{$buyer->user->first_name.' '.$buyer->user->last_name}}</h4>
                    <p class="card-text">Customer</p>
                </div>
                <div class="card-footer">
                    <ul>
                        <li>Total Visits
                            <Span>{{$buyer->user->visits}}</Span>
                        </li>
                    </ul>
                    <ul>
                        <li>Free Coffee<span>{{$buyer->user->free_coffee}}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="col-md-12">
        <p class="alert alert-warning">No Record Found</p>
    </div>
@endif