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

<?php
$redirect = empty($redirect) ? session()->get('redirect_queue', '') : $redirect;
?>

@section('content')
    <div class="z-depth-1 card-panel" style="max-width:550px;margin:auto">
        <form class="login-form" method="POST" action="/login">
            <input type="hidden" name="_token" id="csrftoken" value="{{ csrf_token() }}"/>
            <input type="hidden" name="redirect_queue" id="iRedir" value="{{ $redirect }}"/>
                <input type="hidden" name="mac" id="iMac" value="{{ isset($mac) ? $mac : '' }}"/>
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">{{ trans('messages.pleaselogin') }}</h4>
                </div>
            </div>
            @if (count($errors) > 0)
                <ul class="collection white-text">
                    <li class="collection-item red darken-1">เกิดข้อผิดพลาดในข้อมูล
                        ({{ implode(', ', $errors->all()) }})
                    </li>
                </ul>
            @endif
            <ul class="collection white-text"
                style="margin:0;{{ session()->has('error_message') ? '' : 'display:none' }}" id="error-message">
                <li class="collection-item red darken-1">{{ session('error_message') }}</li>
            </ul>
            <div class="row margin">
                <div class="input-field col s12">
                    <i class="mdi-social-person-outline prefix"></i>
                    <input id="username" type="text" name="username"
                           class="validate {{ $errors->has('username') ? 'invalid' : '' }}" required/>
                    <label for="username">{{ trans('messages.username') }}</label>
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
                    <br/><a href="/newstudent_register">{{trans('messages.tuent_register')}}</a>
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
            setTimeout(function () {
                // Browser's password autofill may corrupt the form
                if ($('#username').val().length > 0) { // Many browsers won't allow us to get password, to prevent scraping.
                    $("#password").prop('placeholder', ' ');
                    Materialize.updateTextFields();
                }
            }, 300);
        });

        if (navigator.credentials) {
            $(function () {
                // Credential Management API (W3C draft, available in Chrome 51+, see http://w3c.github.io/webappsec-credential-management/)
                navigator.credentials.get({"password": true}).then(
                        function (credential) {
                            if (!credential) {
                                // The user either doesn’t have credentials for this site, or refused to share them. Insert some code here to show a basic
                                // login form (or, ideally, do nothing, since this API should really be progressive enhancement on top of an existing form).
                                return;
                            }
                            if (credential.type == "password") {
                                // It's not possible for JavaScript on the website to retrieve a raw password
                                var myHeaders = new Headers({
                                    "X-CSRF-TOKEN": $('#csrftoken').val(),
                                    "X-Requested-With": 'XMLHttpRequest'
                                });
                                fetch("/login?redirect_queue=" + encodeURIComponent($("#iRedir").val()) + "&mac=" + encodeURIComponent($("#iMac").val()), {
                                    credentials: credential,
                                    method: "POST",
                                    headers: myHeaders
                                })
                                        .then(function (response) {
                                            var contentType = response.headers.get("content-type");
                                            if (contentType && contentType.indexOf("application/json") !== -1) {
                                                return response.json().then(function (json) {
                                                    // process JSON
                                                    if (json.error != undefined) {
                                                        $("error-message").text(json.error).slideDown();
                                                    } else if (json.redirect != undefined) {
                                                        window.location.assign(json.redirect);
                                                    } else {
                                                        $("error-message").text("Unexpected error occured! (JSO)").slideDown();
                                                    }
                                                });
                                            } else {
                                                $("error-message").text("Unexpected error occured! (NJS)").slideDown();
                                            }
                                        });
                            } else {
                                // Federated Sign-in or etc.
                            }
                        });
            });
        }
    </script>
@endsection
