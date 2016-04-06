@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .sector {margin: 0.5rem 0 1rem 0;border: 1px solid #e0e0e0;border-radius: 2px;background-color: #fff;line-height: 1.5rem;padding: 10px 20px 10px 10px;}
    .fullwidth {width:99%}
    h1 .smtext {font-size:40%}
    body {background-color: #ff9800}
    nav {box-shadow:none}
@endsection

@section('content')
    <div class="z-depth-1 card-panel" style="max-width:800px;margin: 3rem auto auto;">
        <div class="row">
            <i class="large material-icons">fingerprint</i><br/>
        </div>
        <div class="col s12 m9">
            <h4>{{$client->getName()}}</h4>
            {{ trans('messages.accessprompt') }}
            <form method="post" action="{{route('oauth.authorize.post', $params)}}">
                {{ csrf_field() }}
                <input type="hidden" name="client_id" value="{{$params['client_id']}}">
                <input type="hidden" name="redirect_uri" value="{{$params['redirect_uri']}}">
                <input type="hidden" name="response_type" value="{{$params['response_type']}}">
                <input type="hidden" name="state" value="{{$params['state']}}">
                <input type="hidden" name="scope" value="{{$params['scope']}}">
                <div class="row">
                    <button type="submit" name="deny" value="1" class="btn waves-effect waves-light red col s4"><i
                                class="material-icons left">block</i>Deny
                    </button>
                    <button type="submit" name="approve" value="1" class="btn waves-effect waves-light green col s8"><i
                                class="material-icons left">done</i>Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

