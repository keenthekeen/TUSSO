@extends('layouts.master')

@section('style')
    h4 {text-align:center}
    h1 {text-align: center;font-size:4rem;}
    body {background-color: #ff9800}
@endsection

@section('navbar')
    @parent
    <div class="white-text orange" style="height:20px"></div>
@endsection

@section('content')
    <div class="z-depth-1 card-panel" style="max-width:800px;margin:auto">
        <form method="POST" action="/admin/loginas">
            {{ csrf_field() }}
            <div class="row margin">
                <div class="input-field col s8">
                    <input id="user" type="text" name="user" class="validate"/>
                    <label for="user">Username</label>
                </div>
                <div class="input-field col s4" id="button-div">
                    <button class="btn waves-effect waves-light pink" type="submit"
                            style="width:100%">
                        Log in as
                    </button>
                </div>
            </div>
        </form>
        <br/>
        @if (!\App\Http\Controllers\UIController::isGraylog())
            <a class="btn waves-effect waves-light cyan" href="/log">View logs</a>
        @endif
    </div>

@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('select').material_select();
        });
    </script>
@endsection