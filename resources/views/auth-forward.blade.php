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
    <div class="z-depth-1 card-panel" style="max-width:800px;margin: 3rem auto auto;">
        <div class="row">
            <i class="large material-icons">fingerprint</i><br/>
        </div>
        <div class="col s12 m9">
            <h4>{{ trans('messages.redirectingtoapp') }}</h4>
            <form method="post" action="{{ $goto }}">
                <input type="hidden" name="client_id" value="{{ $serialized }}">
                <div class="row">
                    <button type="submit" name="approve" value="1"
                            class="btn waves-effect waves-light blue col s12">{{ trans('messages.go') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script>

    </script>
@endsection