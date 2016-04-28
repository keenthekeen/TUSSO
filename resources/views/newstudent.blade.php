@extends('layouts.master')

@section('style')
    h4 {text-align:center}
    h1 {text-align: center;font-size:4rem;}
    body {background-color: #ff9800}
    input[type='number'] {
    -moz-appearance: textfield;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    }
@endsection

@section('navbar')
    @parent
    <div class="white-text orange" style="height:20px"></div>
@endsection

@section('content')
    <div class="z-depth-1 card-panel" style="max-width:800px;margin:auto">
        <form class="login-form" method="POST" action="/newstudent_register">
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">ลงทะเบียนใช้งานสำหรับนักเรียนใหม่</h4>
                </div>
            </div>
            สำหรับนักเรียนใหม่ที่ยังไม่มีบัญชีผู้ใช้งาน ให้นักเรียนลงทะเบียนรับรหัสผ่านชั่วคราวผ่านแบบฟอร์มนี้
            โดยนักเรียนต้องสะกดให้ถูกต้องทุกตัวอักษรตามที่ลงทะเบียนสมัครสอบคัดเลือก
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
            <div class="row margin userinfo-form">
                <div class="input-field col s12 m6">
                    <i class="mdi-social-person prefix"></i>
                    <input id="fname" type="text" name="fname"
                           class="" required/>
                    <label for="fname">ชื่อตัว</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="lname" type="text" name="lname"
                           class="validate" required/>
                    <label for="lname">นามสกุล</label>
                </div>
            </div>
            <div class="row margin userinfo-form">
                <div class="input-field col s12 m6">
                    <i class="mdi-image-looks-one prefix"></i>
                    <input id="citizenid" type="number" name="citizenid"
                           class="validate" required min="1100000000000" max="9999999999999"/>
                    <label for="citizenid">รหัสประจำตัวประชาชน</label>
                </div>
                <div class="input-field col s12 m6">
                    <select name="plan" id="plan">
                        <option value="" disabled selected>เลือกแผนการเรียน</option>
                        <option value="5">วิทย์-คณิต</option>
                        <option value="4">ภาษา-คณิต</option>
                        <option value="1">ภาษา-ฝรั่งเศส</option>
                        <option value="2">ภาษา-เยอรมัน</option>
                        <option value="3">ภาษา-ญี่ปุ่น</option>
                        <option value="7">ภาษา-สเปน</option>
                        <option value="8">ภาษา-จีน</option>
                    </select>
                    <label>แผนการเรียน</label>
                </div>
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

            <div class="row margin userinfo-show" style="display: none">
                <div class="col s12" style="padding-left:2rem;font-size:1.3rem">
                    <b>ชื่อ</b> <span id="iName">สมชาย ใจดี</span><br/>
                    <b>รหัสประจำตัวประชาชน</b> <span id="iCitizenId">1111111111119</span>
                </div>
            </div>

            <div class="row margin userpwd-form" style="display: none">
                <div class="input-field col s12 m6">
                    <i class="mdi-action-lock-outline prefix"></i>
                    <input id="password" type="password" name="password"
                           class="validate {{ $errors->has('password') ? 'invalid' : '' }}"/>
                    <label for="password">ตั้งรหัสผ่าน</label>
                </div>
                <div class="input-field col s12 m6">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="validate {{ $errors->has('password_confirmation') ? 'invalid' : '' }}"/>
                    <label for="password_confirmation">ยืนยันรหัสผ่าน</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12" id="button-div">
                    <button class="btn waves-effect waves-light blue" type="submit" name="action" id="fSubmit"
                            style="width:100%">
                        ตรวจสอบข้อมูล
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
            $('#fname, #lname, #citizenid, #plan, #password, #password_confirmation').prop('disabled', false).removeClass('invalid');
            $('select').material_select();

            var step1 = false;
            $('form').submit(function (event) {
                if (step1) {
                    //Step2
                    $('.userinfo-form, .userpwd-form').slideUp();
                    $('.waiting').slideDown();

                    $.ajax({
                        url: '/newstudent_register',
                        data: {
                            fname: $('#fname').val(),
                            lname: $('#lname').val(),
                            citizenid: $('#citizenid').val(),
                            plan: $('#plan').val(),
                            password: $('#password').val(),
                            password_confirmation: $('#password_confirmation').val(),
                            _token: $('input[name="_token"]').val()
                        },
                        error: function () {
                            Materialize.toast('ไม่สามารถติดต่อเซิร์ฟเวอร์', 4000);
                            $('.userinfo-form, .userpwd-form').slideDown();
                            $('.waiting').slideUp();
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == 'SUCCEED') {
                                $('#fname, #lname, #citizenid, #plan, #password, #password_confirmation').prop('disabled', true).removeClass('invalid');
                                $('select').material_select();
                                $('.userpwd-form').html('<h4>ลงทะเบียนเรียบร้อย ชื่อผู้ใช้ของนักเรียนคือ n' + $('#citizenid').val() + '</h4>').slideDown();
                                $('#button-div').html('<a class="btn waves-effect waves-light red" href="/" style="width:100%">ออก</button>');
                                $('.waiting').slideUp();
                                step1 = true;
                            } else if (data.status == 'USER_EXISTS') {
                                $('.userpwd-form').html('<h4>เคยลงทะเบียนแล้ว ชื่อผู้ใช้ของนักเรียนคือ n' + $('#citizenid').val() + '</h4>').slideDown();
                                $('#button-div').html('<a class="btn waves-effect waves-light red" href="/" style="width:100%">ออก</button>');
                                $('.userinfo-form').slideDown();
                                $('.waiting').slideUp();
                            } else if (data.status == 'INVALID_INFO') {
                                Materialize.toast('ชื่อหรือแผนการเรียนไม่ถูกต้อง', 4000);
                                $('#fname, #lname, #plan').addClass('invalid');
                                $('.userinfo-form, .userpwd-form').slideDown();
                                $('.waiting').slideUp();
                            } else if (data.status == 'MALFORMED_REQUEST') {
                                Materialize.toast('ข้อมูลไม่ถูกรูปแบบ', 4000);
                                $('#password, #password_confirmation').addClass('invalid');
                                $('.userinfo-form, .userpwd-form').slideDown();
                                $('.waiting').slideUp();
                            } else {
                                Materialize.toast('เกิดปัญหา กรุณาติดต่อผู้ดูแลระบบ', 4000);
                                $('#citizenid').addClass('invalid');
                                $('.userinfo-form, .userpwd-form').slideDown();
                                $('.waiting').slideUp();
                            }
                        },
                        type: 'POST'
                    });
                } else {
                    //Step1
                    $('.userinfo-form').slideUp();
                    $('.waiting').slideDown();

                    $.ajax({
                        url: '/newstudent_register',
                        data: {
                            fname: $('#fname').val(),
                            lname: $('#lname').val(),
                            citizenid: $('#citizenid').val(),
                            plan: $('#plan').val(),
                            _token: $('input[name="_token"]').val()
                        },
                        error: function () {
                            Materialize.toast('ไม่สามารถติดต่อเซิร์ฟเวอร์ได้ หรือ นักเรียนลองหลายครั้งเกินไป ให้รอแล้วลองใหม่', 4000);
                            $('.userinfo-form').slideDown();
                            $('.waiting').slideUp();
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == 'GOOD') {
                                $('#fname, #lname, #citizenid, #plan').prop('disabled', true);
                                $('#fname, #lname, #plan').removeClass('invalid');
                                $('select').material_select();
                                $('#fSubmit').removeClass('blue').addClass('indigo').text('ลงทะเบียน');
                                $('.userinfo-form').slideDown();
                                $('.waiting').slideUp();
                                $('.userpwd-form').slideDown();
                                step1 = true;
                            } else if (data.status == 'INVALID_INFO') {
                                Materialize.toast('ชื่อหรือแผนการเรียนไม่ถูกต้อง', 4000);
                                $('#fname, #lname, #plan').addClass('invalid');
                                $('.userinfo-form').slideDown();
                                $('.waiting').slideUp();
                            } else if (data.status == 'MALFORMED_REQUEST') {
                                Materialize.toast('ข้อมูลไม่ถูกรูปแบบ', 4000);
                                $('#fname, #lname, #plan, #citizenid').addClass('invalid');
                                $('.userinfo-form').slideDown();
                                $('.waiting').slideUp();
                            } else if (data.status == 'USER_EXISTS') {
                                $('.userpwd-form').html('<h4>เคยลงทะเบียนแล้ว ชื่อผู้ใช้ของนักเรียนคือ n' + $('#citizenid').val() + '</h4>').slideDown();
                                $('#button-div').html('<a class="btn waves-effect waves-light red" href="/" style="width:100%">ออก</button>');
                                $('.waiting').slideUp();
                            } else {
                                Materialize.toast('ไม่พบผู้เข้าสอบ', 4000);
                                $('#citizenid').addClass('invalid');
                                $('.userinfo-form').slideDown();
                                $('.waiting').slideUp();
                            }
                        },
                        type: 'POST'
                    });
                }
                event.preventDefault();
            });
        });
    </script>
@endsection