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
    <div class="z-depth-1 card-panel" style="max-width:550px;margin:auto">
        <form class="login-form" method="POST" action="/login">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">{{ trans('messages.pleaselogin') }}</h4>
                </div>
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                    <i class="mdi-social-person-outline prefix"></i>
                    <input id="username" type="text" name="username" required/>
                    <label for="username" class="center-align">{{ trans('messages.username') }}</label>
                </div>
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                    <i class="mdi-action-lock-outline prefix"></i>
                    <input id="password" type="password" name="password"/>
                    <label for="password">{{ trans('messages.password') }}</label>
                </div>
            </div>
            <div class="center-align">
                <input type="checkbox" id="rem" name="remember" value="true" />
                <label for="rem">{{ trans('messages.remember') }}</label>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light red" type="submit" name="action" style="width:100%">
                        {{ trans('messages.login') }}
                    </button>
                </div>
            </div>

        </form>
    </div>

@endsection
