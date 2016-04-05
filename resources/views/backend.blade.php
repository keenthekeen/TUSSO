@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .sector {margin: 0.5rem 0 1rem 0;border: 1px solid #e0e0e0;border-radius: 2px;background-color: #fff;line-height: 1.5rem;padding: 10px 20px 10px 10px;}
    .fullwidth {width:99%}
    .att-good {font-weight:bold;color:green}
    .att-bad {font-weight:bold;color:red}
    .att-warn {font-weight:bold;color:orange}
    .att-null {font-decoration: italic}
    .att-info {font-weight:bold;color:blue}
    h1 .smtext {font-size:40%}
    #cont {display:none}
    #rmt {display:none}
@endsection

@section('content')
    <h1>แผงควบคุมของผู้ดูแล</h1>

    <div class="sector fullwidth">
        <h4> ข้อมูลการมาโรงเรียนรายบุคคล</h4>
        <div class="input-field">
            <input id="sid" type="text" min="3" class="validate" onkeyup="ckfv()"/>
            <label for="sid">รหัสประจำตัวนักเรียน/บุคลากร</label>
        </div>
        <div class="row">
            <div class="col s12 l3">
                <a class="waves-effect waves-light btn pink fullwidth fvbtn disabled" onclick="mants()" id="manbtn">บันทึกเวลา</a>
            </div>
            <div class="col s12 l6">
                <a class="waves-effect waves-light btn blue fullwidth" onclick="viewsi()">ดูข้อมูล</a>
            </div>
            <div class="col s12 l3">
                <a class="waves-effect waves-light btn amber fullwidth fvbtn disabled" onclick="prexit()" id="prbtn">อนุญาตให้ออก</a>
            </div>
        </div>
        <div class="sector fullwidth" id="cont">
        </div>
    </div>
    <script>
        var checkif;
        var firstview;
        function ckfv() {
            if (firstview == $('#sid').val()) {
                $('.fvbtn').removeClass('disabled');
                $('#cont').slideDown();
            } else {
                $('.fvbtn').addClass('disabled')
                $('#cont').slideUp();
            }
            return true;
        }
        function viewsi() {
            clearInterval(checkif);
            $('#cont').html('');
            if ($('#sid').val().length < 3) {
                $('#cont').html('กรุณาระบุรหัสประจำตัวนักเรียน/บุคลากร');
                return false;
            }

            $('#cont').slideDown();
            $('#cont').load('/backend/user?user=' + $('#sid').val(), function () {
                $('.tooltipped').tooltip({delay: 50});
            });
        }
        function mants() {
            if ($('#sid').val().length < 3) {
                $('#cont').html('กรุณาระบุรหัสประจำตัวนักเรียน/บุคลากร');
            } else if (firstview != $('#sid').val()) {
                $('#cont').html('กรุณาตรวจสอบข้อมูลนักเรียนให้ถูกต้องก่อน');
            } else if (confirm('แน่ใจหรือไม่ที่จะบันทึกเวลาให้กับ ' + $('#sid').val())) {
                $("#manbtn").addClass('disabled');
                $('#cont').slideDown();
                $('#cont').html('<h4>จำลองเครื่องอ่านบัตร</h4><iframe src="/client/stamp?user=' + $('#sid').val() + '" height="550px" width="100%" id="sti"></iframe>');
                checkif = setInterval(function () {
                    console.log('CheckIf() Interval passed');
                    if (document.getElementById("sti").contentWindow.location.href == '<?=config('app.url')?>/client') {
                        $('#cont').html('<h3>บันทึกเวลาด้วยตนเองเรียบร้อย</h3>');
                        setTimeout(function () {
                            $('#cont').slideUp();
                            $("#manbtn").removeClass('disabled');
                        }, 1000);
                        clearInterval(checkif);
                    }
                }, 3000);
            } else {
                return false;
            }
        }
        function prexit() {
            if ($('#sid').val().length < 3) {
                $('#cont').html('กรุณาระบุรหัสประจำตัวนักเรียน/บุคลากร');
            } else if (firstview != $('#sid').val()) {
                $('#cont').html('กรุณาตรวจสอบข้อมูลนักเรียนให้ถูกต้องก่อน');
            } else {
                $('#cont').html('<h4><i class="small material-icons">check_circle</i> อนุญาตให้นักเรียนออกจากโรงเรียนก่อนเวลา</h4> <form method="POST" action="/backend/preexit">{{ csrf_field() }}<input type="hidden" name="user" value="' + $('#sid').val() +
                        '" /><div class="input-field"> <input id="preason" type="text" name="reason" class="validate"/> <label for="preason">เหตุผลในการขอออกนอกโรงเรียน (ต้องกรอก)</label> </div> กรุณากรอกเหตุผลในการขอออก หากเว้นว่างไว้จะเป็นการเพิกถอนการอนุญาต<br />สามารถอนุญาตได้วันต่อวันเท่านั้น<br /><br /><button class="btn waves-effect waves-light fullwidth orange" type="submit" name="action">อนุญาต/เพิกถอนการอนุญาต <i class="material-icons right">send</i> </button> </form>'
                );
                $('#cont').slideDown();
                return true;
            }
        }

    </script>


    <!-- div class="divider"></div -->
    <div class="sector fullwidth">
        <h4> ข้อมูลการมาโรงเรียนรายห้อง</h4>
        <form method="GET" action="/backend/dept" target="_blank">
            <div class="input-field">
                <input id="rm" type="text" min="3" class="validate" name="dept" required />
                <label for="rm">ห้อง/ฝ่ายงาน</label>
            </div>
            <button class="btn waves-effect waves-light fullwidth orange" type="submit" name="action">
                แสดงข้อมูลในแท็บใหม่
            </button>
        </form>
    </div>
    <div class="center-align fullwidth">
    <a href="/backend/log">ดูบันทึกการแก้ไขข้อมูล (Log)</a>
    </div>

@endsection
