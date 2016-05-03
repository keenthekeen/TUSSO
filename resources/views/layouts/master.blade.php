<!doctype html>
<html>
<head>
    <title>{{ trans('messages.name') }}</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="google" content="notranslate" />
    <meta name="theme-color" content="#ff9800"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url('https://static.keendev.net/font/thsarabunnew-webfont.eot');
            src: url('https://static.keendev.net/font/thsarabunnew-webfont.eot?#iefix') format('embedded-opentype'), url('https://static.keendev.net/font/thsarabunnew-webfont.woff') format('woff'), url('https://static.keendev.net/font/thsarabunnew-webfont.ttf') format('truetype');
            font-weight: normal;
            font-style: normal
        }

        body {
        <?php
        if (App::isLocale('th')) {
            echo 'font-family: "THSarabunNew", Sans-Serif !important;';
            }
        ?>
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
            min-height: 400px;
            opacity:0;
        }
        nav, footer {
            opacity:1;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        .brand-logo {
            font-size: 1.5rem !important
        }

        .footer-copyright a {
            color: lightgrey;
        }

        .footer-copyright a:hover {
            text-decoration: underline
        }

        .fullwidth {
            width: 100%
        }

        .th {
            font-family: 'THSarabunNew', Sans-Serif !important
        }

        .en {
            font-family: 'Roboto', Sans-Serif !important
        }
        @yield('style')
    </style>
</head>
<body>
@section('navbar')
    <nav class="orange" role="navigation">
        <div class="nav-wrapper container">
            <a id="logo-container" href="/" class="brand-logo">{{ trans('messages.name') }}</a>
            <!-- ul class="right hide-on-med-and-down">
                <li<?= isset($home) ? ' class="active"' : '' ?>><a href="/">{{ trans('messages.home') }}</a></li>
            </ul>
            <ul id="nav-mobile" class="side-nav">
                <li<?= isset($home) ? ' class="active"' : '' ?>><a href="/">{{ trans('messages.home') }}</a></li>
            </ul>
            <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a> -->
        </div>
    </nav>
@show

<main class="container">
    @yield('content')
</main>

@section('footer')
    <footer class="page-footer orange">
        <div class="footer-copyright">
            <div class="container">
                {{ trans('messages.organization') }} | {{ trans('messages.copyright') }} | <a href="/switch_lang">{{ trans('messages.switchlang') }}</a>
                <?php
                    $uicontroller = new \App\Http\Controllers\UIController();
                if ($uicontroller->isLoggedIn()) echo ' | <a href="/logout">'.trans('messages.logout').'</a>';
                ?>
            </div>
        </div>
    </footer>
@show

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
    <script>
        $(function () {
            $(".button-collapse").sideNav();
            $("main").fadeTo("slow", 1);
            <?php
            if (session()->has('notify')) {
                echo 'Materialize.toast("' . session('notify') . '", 4000);';
            }
            ?>
            if ($(window).width() < 550) {
                $('#logo-container').text('{{ trans('messages.shortname') }}');
                console.log('Navbar title has been decreased');
            }
            $(window).bind('beforeunload', function(){
                $("main").fadeTo("fast", 0);
            });
        });
    </script>
@show
</body>
</html>
