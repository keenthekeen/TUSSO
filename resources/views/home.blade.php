@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .sector {margin: 0.5rem 0 1rem 0;border: 1px solid #e0e0e0;border-radius: 2px;background-color: #fff;line-height: 1.5rem;padding: 10px 20px 10px 10px;}
    .fullwidth {width:99%}
    h1 .smtext {font-size:40%}
    body {background-color: #ff9800}
    nav {box-shadow:none}
@endsection

@section('content')
    <?php
            $user = Auth::user();
    ?>
    <div class="z-depth-1 card-panel" style="max-width:800px;margin: 3rem auto auto;">
        <div class="row">
            <div class="col s12 m3 center-align">
                {{-- place user profile photo here --}}
                <i class="large material-icons">perm_identity</i><br />
                <h5 class="en">{{ strtoupper($user->username) }}</h5>
            </div>
            <div class="col s12 m9">
                <h4>{{ $user->name }}</h4>
                {{ trans('messages.type') }}: {{ ($user->type == 'student') ? trans('messages.student') : trans('messages.staff') }} <i class="en">({{ $user->group }})</i>
            </div>
        </div>
    </div>
@endsection
