@extends('layouts.master')

@section('title', 'เกี่ยวกับ '.config('ats.abbname'))

@section('style')
    main {padding-top:15px;font-size:18px}
    .smll {font-size: 70%}
    h1 {line-height: 120%;text-align:center}
    h2 {font-size:1.7rem;text-align:center; font-weight:300;font-family: 'Roboto', Sans-Serif !important;}
@endsection

@section('navbar')
    @parent
    <div class="orange white-text" style="padding-top:60px;padding-bottom:40px">
        <div class="container">
            <h1>{{ config('ats.name')}}<br/>{{config('ats.organization') }}</h1>
            <h2>{{ config('ats.engname') }} ({{ config('ats.abbname') }})</h2>
        </div>
    </div>
@endsection

@section('content')
    <br/>
    <div class="row">
        <div class="col s12 m4">
            <img src="/pic/screenshot6.png" class="fullwidth materialboxed"/>
        </div>
        <div class="col s12 m8">
            <h4>แตะบัตรที่เครื่อง</h4>
            ระบบบันทึกเวลาออกแบบมาให้ทำงานกับบัตรแตะ (RFID) หรือกับบัตรบาร์โค้ด ซึ่งราคาถูก หาได้ง่าย
            หรือจะใช้บัตรที่นักเรียนมีอยู่แล้วก็ได้ เช่น บัตรรถไฟฟ้า บัตรห้องสมุด
        </div>
    </div>
    <br/>
    <div class="divider"></div><br/>
    <div class="row">
        <div class="col s12 m8">
            <h4>มากกว่าแค่ไว้แตะบัตร...</h4>
            ระบบบันทึกเวลารองรับการบันทึกการอนุญาตออกนอกโรงเรียนและการบันทึกเวลาโดยครู (โดยไม่ต้องใช้บัตร) ได้อีกด้วย
        </div>
        <div class="col s12 m4">
            <img src="/pic/screenshot1.png" class="fullwidth materialboxed"/>
        </div>
    </div>
    <br/>
    <div class="divider"></div>
    <br/>
    <div class="row">
        <div class="col s12 m4">
            <img src="/pic/screenshot3.png" class="fullwidth materialboxed"/>
        </div>
        <div class="col s12 m8">
            <h4>ทำไมมาสายล่ะ?</h4>
            ระบบจะให้นักเรียนระบุสาเหตุที่มาสายหากนักเรียนบันทึกเวลาสาย (หลังเวลาที่กำหนดไว้)
            เพื่อเป็นข้อมูลในการประเมินพฤติกรรมนักเรียน
        </div>
    </div>
    <br/>
    <div class="divider"></div><br/>
    <div class="row">
        <div class="col s12 m8">
            <h4>เครื่องมือสำหรับครู</h4>
            ครูสามารถดูข้อมูลของนักเรียน รวมถึงบันทึกเวลาแบบไม่ต้องใช้บัตรและอนุญาตให้นักเรียนออกนอกโรงเรียนก่อนเวลาได้
        </div>
        <div class="col s12 m4">
            <img src="/pic/screenshot2.png" class="fullwidth materialboxed"/>
        </div>
    </div>
    <br/>
    <div class="divider"></div>
    <br/>
    <div class="row">
        <div class="col s12 m4">
            <img src="/pic/screenshot4.png" class="fullwidth materialboxed"/>
        </div>
        <div class="col s12 m8">
            <h4>นำข้อมูลออกก็ง่าย</h4>
            ระบบสามารถแสดงข้อมูลของนักเรียนทั้งหมดในห้องเป็นตารางรวมเพื่อพิมพ์ได้
        </div>
    </div>
    <br/>
    <div class="divider"></div><br/>
    <div class="row">
        <div class="col s12 m8">
            <h4>ทำงานได้ดีไม่ว่าจะจอใหญ่หรือเล็ก</h4>
            ระบบออกแบบมาให้ทำงานได้เต็มที่ทุกฟังก์ชั่นในทุกอุปกรณ์ ทุกระบบปฏิบัติการ เนื่องจากเป็น Web Application
            <br/><br/><span style="font-size:70%">กรุณาเปิดเว็บนี้ด้วย Mozilla Firefox หรือ Google Chrome</span>
        </div>
        <div class="col s12 m4">
            <img src="/pic/screenshot5.png" class="fullwidth materialboxed"/>
        </div>
    </div>
    <br/>
    <div class="divider"></div>
    <br/>
    <div class="row">
        <div class="col s12 m4">
            <img src="/pic/screenshot7.png" class="fullwidth materialboxed"/>
        </div>
        <div class="col s12 m8">
            <h4>ทำงานบนซอฟต์แวร์ฟรี</h4>
            ระบบทำงานอยู่บนซอฟต์แวร์ Open source ทั้งหมดตั้งแต่เซิร์ฟเวอร์ถึงเครื่องอ่านบัตร ระบบปฏิบัติการถึงตัวโปรแกรม
        </div>
    </div>
    <br/>
    <div class="divider"></div><br/><br/>
    <h4>พัฒนาโดย</h4>{{-- เข้ามาเพิ่มชื่อตัวเองได้เลย --}}
    <ul class="collection">
        <?php
        foreach (config('ats.developers') as $nm) {
            echo '<li class="collection-item">'.$nm.'</li>';
        }
        ?>
    </ul>

    <br/>
@endsection

@section('script')
    @parent
    <script>
        $(function () {
            $('#logo-container').text('');
        });
    </script>
@endsection