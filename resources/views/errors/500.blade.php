@extends('layouts.error')

@section('code','500')
@section('title','Internal Server Error')
@section('subtitle',"We've got some errors, please try again.")
@section('description','เซิร์ฟเวอร์มีปัญหา กรุณาลองใหม่อีกครั้ง')

@section('button')
<a href="" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index" style="width:80%;max-width:350px;margin-top:20px" onclick="location.reload(true)">ลองใหม่</a>
@endsection