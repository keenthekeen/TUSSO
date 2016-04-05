@extends('layouts.error')

@section('code','429')
@section('title','Too Many Requests')
@section('subtitle',"You've sent too many requests.")
@section('description','คุณส่งคำขอมามากเกินไป กรุณาหยุดรอแล้วลองใหม่')

@section('button')
<a href="" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index" style="width:80%;max-width:350px;margin-top:20px" onclick="location.reload(true)">ลองใหม่</a>
@endsection