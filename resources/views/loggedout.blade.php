@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .fullwidth {width:99%}
    h1 .smtext {font-size:40%}
    body {background-color: #ff9800}
    nav {box-shadow:none}
@endsection

@section('content')
    <div class="z-depth-1 card-panel center-align" style="max-width:500px;margin: 3rem auto auto;">
        <div class="row">
            <br/>
            <i class="large material-icons red-text">exit_to_app</i><br/>
        </div>
        <div class="row">
            <h4>{{ trans('messages.loggedout') }}</h4><br/>
            <a class="btn waves-effect waves-light red" style="width:100%" href="/" id="tBack">{{ trans('messages.proceed') }}</a>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script>
        $(function () {
            $('#tBack').html('{{ trans('messages.proceed') }} (<span id="tLeft">6</span>)');
            var tLeft = 5;
            setInterval(function () {
                $('#tLeft').text(tLeft);
                tLeft--;
                if (tLeft <= 0) {
                    window.location.assign("/");
                }
            }, 1000);
        });
    </script>
@endsection