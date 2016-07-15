@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .sector {margin: 0.5rem 0 1rem 0;border: 1px solid #e0e0e0;border-radius: 2px;background-color: #fff;line-height: 1.5rem;padding: 10px 20px 10px 10px;}
    .fullwidth {width:99%}
    .en {font-family: 'Roboto', Sans-Serif !important;}
@endsection

@section('content')
    <h3 class="en">Log</h3>

    <div class="sector fullwidth en">
        <?php
        if (config('app.log') === 'daily') {
            echo nl2br(str_replace('local.', '', file_get_contents(storage_path('logs/' . date('o-m-d') . '.log'))));
        } elseif (config('app.log') === 'single') {
            echo nl2br(str_replace('local.', '', file_get_contents(storage_path('logs/laravel.log'))));
        } elseif (\App\Http\Controllers\UIController::isGraylog()) {
            echo 'Using Graylog server, cannot view logs here!';
        } else {
            echo 'Not Available, please contact administrator';
        }
        ?>
    </div>
@endsection
