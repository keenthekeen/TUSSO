@extends('layouts.error')

@section('code','400')
@section('title','Bad Request')
@section('subtitle','It looks like there\'s something wrong with your request.')
@section('description','คำร้องขอของคุณไม่ถูกต้อง กรุณาลองใหม่')

@section('button')
<a href="" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index" style="width:80%;max-width:350px;margin-top:20px" onclick="location.reload(true)">ลองใหม่</a>
@endsection