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
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <input type="hidden" name="redirect_queue"
                   value="{{ empty($redirect) ? session()->get('redirect_queue', '') : $redirect }}"/>
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">{{ trans('messages.pleaselogin') }}</h4>
                </div>
            </div>
            <div class="row">
                @if (count($errors) > 0)
                    <ul class="collection white-text">
                        <li class="collection-item red darken-1">เกิดข้อผิดพลาดในข้อมูล
                            ({{ implode(', ', $errors->all()) }})
                        </li>
                    </ul>
                @endif
                @if(session()->has('error_message'))
                    <ul class="collection white-text">
                        <li class="collection-item red darken-1">{{ session('error_message') }}</li>
                    </ul>
                @endif
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                    <i class="mdi-social-person-outline prefix"></i>
                    <input id="username" type="text" name="username"
                           class="validate{{ $errors->has('username') ? ' invalid' : '' }}" required/>
                    <label for="username" class="center-align">{{ trans('messages.username') }}</label>
                </div>
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                    <i class="mdi-action-lock-outline prefix"></i>
                    <input id="password" type="password" name="password"
                           class="validate {{ $errors->has('password') ? 'invalid' : '' }}" required/>
                    <label for="password">{{ trans('messages.password') }}</label>
                </div>
            </div>
            <div class="center-align">
                <input type="checkbox" id="rem" name="remember" value="true"/>
                <label for="rem">{{ trans('messages.remember') }}</label>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <button class="btn waves-effect waves-light red" type="submit" name="action" style="width:100%">
                        {{ trans('messages.login') }}
                    </button>
                </div>
            </div>
            <div class="center-align">
                <a class="modal-trigger" href="#modal-forget">{{trans('messages.forget_pwd')}}</a>
                @if (config('tusso.use_tuent'))
                    <br /><a href="/newstudent_register">{{trans('messages.tuent_register')}}</a>
                @endif
            </div>

        </form>
    </div>

    <div id="modal-forget" class="modal">
        <div class="modal-content">
            <h4>{{ trans('messages.forget_pwd') }}</h4>
            <p>{{ trans('messages.forget_instruction') }}</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class=" modal-action modal-close waves-effect waves-green btn-flat">Acknowledged</a>
        </div>
    </div>

@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('.modal-trigger').leanModal();
        });
    </script>
@endsection
