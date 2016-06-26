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
        <form class="login-form" id="cform">
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">{{ trans('messages.change_pwd') }}</h4>
                </div>
            </div>
            <div class="row" style="display: none" id="errorw">
                <ul class="collection white-text">
                    <li class="collection-item red darken-1" id="errorm"></li>
                </ul>
            </div>
            <div class="row center-align waiting" style="display:none">
                <div class="preloader-wrapper big active">
                    <div class="spinner-layer spinner-blue">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>

                    <div class="spinner-layer spinner-red">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>

                    <div class="spinner-layer spinner-yellow">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>

                    <div class="spinner-layer spinner-green">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </div>

            <input id="username" type="hidden" name="username" autocomplete="username" value="{{ Auth::user()->username }}"/>
            <div class="row margin userpwd-form">
                <div class="input-field col s12">
                    <input id="opassword" type="password" name="oldpassword"
                           class="validate"/>
                    <label for="opassword">{{ trans('messages.old_pwd') }}</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="password" type="password" name="password"
                           class="validate" autocomplete="new-password"/>
                    <label for="password">{{ trans('messages.new_pwd') }}</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="validate"/>
                    <label for="password_confirmation">{{ trans('messages.confirm').' '.strtolower(trans('messages.new_pwd')) }}</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12" id="button-div">
                    <button class="btn waves-effect waves-light blue" type="submit" name="action" id="fSubmit"
                            style="width:100%">
                        {{ trans('messages.proceed') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')
    @parent

    <script>
        $(document).ready(function () {

            $('form').submit(function (event) {
                $('.userpwd-form').slideUp();
                $('.waiting').slideDown();

                if ($('#password').val() == $('#password_confirmation').val()) {
                    $.ajax({
                        url: '/password/change',
                        data: {
                            oldpassword: $('#opassword').val(),
                            password: $('#password').val(),
                            password_confirmation: $('#password_confirmation').val(),
                            _token: $('input[name="_token"]').val()
                        },
                        error: function () {
                            Materialize.toast('ไม่สามารถติดต่อเซิร์ฟเวอร์', 4000);
                            $('.userpwd-form').slideDown();
                        },
                        success: function (data) {
                            if (data == 'SUCCEED') {
                                $('.userpwd-form').html('<h4>Password changed!</h4>').slideDown();
                                $('#button-div').html('<a class="btn waves-effect waves-light red" href="/" style="width:100%" title="Index">กลับไปยังหน้าหลัก</a>');

                                if (navigator.credentials) {
                                    // @todo Not work yet
                                    var c = new PasswordCredential(event.target);
                                    navigator.credentials.store(document.getElementById('cform'));
                                }
                            } else if (data == 'PASSWORD_NOT_MATCH') {
                                Materialize.toast('รหัสผ่านเดิมผิด - Old password doesn\'t match');
                                $('.userpwd-form').slideDown();
                            } else {
                                Materialize.toast('เกิดปัญหา กรุณาติดต่อผู้ดูแลระบบ - Error Occured!', 4000);
                                $('.userpwd-form').slideDown();
                            }
                        },
                        type: 'POST'
                    });
                } else {
                    Materialize.toast('รหัสผ่านใหม่ไม่ตรงกัน', 4000);
                }
                $('.waiting').slideUp();
                event.preventDefault();
            });
        });
    </script>
@endsection