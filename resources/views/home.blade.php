@extends('layouts.master')

@section('style')
    h1 {font-size:3rem}
    .sector {margin: 0.5rem 0 1rem 0;border: 1px solid #e0e0e0;border-radius: 2px;background-color: #fff;line-height: 1.5rem;padding: 10px 20px 10px 10px;}
    .fullwidth {width:99%}
    .att-good {font-weight:bold;color:green}
    .att-bad {font-weight:bold;color:red}
    .att-null {font-decoration: italic}
    .att-warn {font-weight:bold;color:orange}
    .att-info {font-weight:bold;color:blue}
    h1 .smtext {font-size:40%}
@endsection

@section('content')
    <?php
            $user = Auth::user();
    ?>
    <div class="z-depth-1 card-panel" style="max-width:800px;margin:auto">
        <div class="row">
            <div class="col s12 m3 l2 center-align">
                {{-- place user profile photo here --}}
                <i class="large material-icons">perm_identity</i><br />
                <h5>{{ $user->username }}</h5>
            </div>
            <div class="col s12 m9 l10">
                <h4>{{ $user->name }}</h4>
                ประเภท: <i>{{ ($user->type == 'student') ? 'นักเรียน' : 'ครู/ลูกจ้าง' }}</i>
            </div>
        </div>
    </div>
@endsection
