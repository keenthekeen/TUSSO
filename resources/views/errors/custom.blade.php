@extends('layouts.error')

<?php
$allvar = ['title', 'description'];
foreach ($allvar as $av) {
    if (empty($title)) {
        $var[$av] = session()->get('error.' . $av, 'Error');
    } else {
        $var[$av] = $$av;
    }
}
$btn = '';
if (isset($button)) {
    $btn = $button;
} else {
    $btn = '<a href="" class="waves-effect waves-light btn indigo darken-3 tooltipped center-align" data-tooltip="Back to index"
       style="width:80%;max-width:350px;margin-top:20px" onclick="location.reload(true)">ลองใหม่</a>';
}
?>

@section('title',$var['title'])
@section('description',$var['description'])

@section('button')
    <?=$btn?>
@endsection