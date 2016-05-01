@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .fullwidth {width:99%}
    h1 .smtext {font-size:40%}
    body {background-color: #ff9800}
    nav {box-shadow:none}
@endsection

@section('content')
    <div class="z-depth-1 card-panel" style="max-width:800px;margin: 3rem auto auto;">
        <div class="row">
            <div class="col s12 m3 center-align">
                <br />
                <i class="large material-icons red-text">block</i><br/>
            </div>
            <div class="col s12 m9">
                <h4>{{ trans('messages.error') }}</h4>
                {{ $error }}<br /><br />
                <a class="btn waves-effect waves-light red" style="width:100%" href="/">{{ trans('messages.backhome') }}</a>
            </div>
        </div>
    </div>
@endsection