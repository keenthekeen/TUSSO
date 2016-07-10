@extends('layouts.master')

@section('style')
    h4 {text-align:center}
    h1 {text-align: center;font-size:4rem;}
    body {background-color: #ff9800}
@endsection

@section('navbar')
    @parent
    <div class="white-text orange" style="height:20px"></div>
@endsection

@section('content')
    <div class="z-depth-1 card-panel" style="max-width:800px;margin:auto">
        <form>
            {{ csrf_field() }}
            <div class="row">
                <div class="input-field col s12 center">
                    <h4 class="center login-form-text">User Search</h4>
                </div>
            </div>
            <div class="row" style="display: none" id="errorw">
                <ul class="collection white-text">
                    <li class="collection-item red darken-1" id="errorm"></li>
                </ul>
            </div>
            <div class="row margin">
                <div class="input-field col s12 m4">
                    <select name="type">
                        <option value="id">ID</option>
                        <option value="lastname">Last name</option>
                    </select>
                    <label>Search type</label>
                </div>
                <div class="input-field col s12 m8">
                    <input id="search" type="text" name="search" class="validate" autofocus />
                    <label for="search">Keyword</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12" id="button-div">
                    <button class="btn waves-effect waves-light blue" type="submit" name="action" id="fSubmit"
                            style="width:100%">
                        {{ trans('messages.proceed') }}
                    </button>
                </div>
            </div>
        </form>
        @if(Request::has('type') && Request::has('search'))
            <br />
            <?php dump(json_decode(file_get_contents(config('tusso.turs').'/student/'. ((Request::input('type') == 'id') ? 'id/'.Request::input('search').'?' : 'search/'.Request::input('type').'?value='.Request::input('search').'&').'access_token='.\App\Http\Controllers\ProviderController::issueAccessToken('sso.local.triamudom.ac.th', ['openid', 'student', 'citizenid', 'searchstudent'], '*')))); ?><br />
            @endif
        <br />
        <form method="POST" action="/admin/loginas">
            {{ csrf_field() }}
            <div class="row margin">
                <div class="input-field col s8">
                    <input id="user" type="text" name="user" class="validate"/>
                    <label for="user">Username</label>
                </div>
                <div class="input-field col s4" id="button-div">
                    <button class="btn waves-effect waves-light pink" type="submit"
                            style="width:100%">
                        Log in as
                    </button>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('script')
    @parent
    <script>
        $(document).ready(function () {
            $('select').material_select();
        });
    </script>
@endsection