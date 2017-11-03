@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            
            <div class="panel panel-default">
                <div class="panel-heading">同意退款</div>
                @foreach ($agree_to_refund_list as $agree)
                <div class="panel-body">
                        {{$agree['tid']}}
                    
                </div>
               @endforeach
            </div>  
                      
            <div class="panel panel-default">
                <div class="panel-heading">丢件</div>
                @foreach ($lost_list as $lost)
                <div class="panel-body">
                    {{$lost['express_list'][0]['time']}} <span style="color:red;">{{$lost['express_type']}}</span>  {{$lost['sid']}}
                </div>

               @endforeach
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">即将超时</div>
                @foreach ($timeout_list as $timeout)
                <div class="panel-body">
                        {{$timeout['tid']}}  <span style="color:red;">{{$timeout['timeout']}}</span>
                </div>
               @endforeach
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">需要寄回，不能直接退款</div>
                @foreach ($has_good_return_list as $return)
                <div class="panel-body">
                        {{$return['tid']}}  
                </div>
               @endforeach
            </div>
            
        </div>
    </div>
</div>
@endsection
