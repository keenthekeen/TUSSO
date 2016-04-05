@extends('layouts.error')

@section('code','503')
@section('title','Service Unavailable')
@section('subtitle',"The requested resources are not available now.")
@section('description','หน้าที่คุณร้องขอไม่สามารถเข้าถึงได้ในขณะนี้ กรุณาลองใหม่อีกครั้งภายหลัง')

@section('button')
<a href="" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index" style="width:80%;max-width:350px;margin-top:20px" onclick="location.reload(true)">ลองใหม่</a>
@endsection