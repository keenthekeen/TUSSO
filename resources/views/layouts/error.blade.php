<!doctype html>
<html>
<head>
	<title>{{ trans('messages.error').' - '.trans('messages.name') }}</title>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<meta name="theme-color" content="#ff9800"/>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
	<style>
@font-face{font-family:'THSarabunNew';src:url('https://static.keendev.net/font/thsarabunnew-webfont.eot');src:url('https://static.keendev.net/font/thsarabunnew-webfont.eot?#iefix') format('embedded-opentype'),url('https://static.keendev.net/font/thsarabunnew-webfont.woff') format('woff'),url('https://static.keendev.net/font/thsarabunnew-webfont.ttf') format('truetype');font-weight:normal;font-style:normal}

.th {font-family:'THSarabunNew',Sans-Serif !important}

		body{
            <?php
if (App::isLocale('th')) {
    echo 'font-family: "THSarabunNew", Sans-Serif !important;';
    }
?>
    display:flex;min-height:100vh;flex-direction:column;background-color: #ff9800
        }
		main{flex:1 0 auto;}
		.page-footer a:hover {text-decoration: underline;}
		.brand-logo {font-size:1.5rem !important}
		.light {font-family: 'Roboto', sans-serif;font-weight: 300;}
		h1 {font-size:90px}
		h2 {font-size:30px;margin-bottom:15px}
		h3 {font-size:25px}
		main {text-align:center}
.footer-copyright a {color:lightgrey;}
.footer-copyright a:hover {text-decoration: underline}
	</style>
</head>
<body>
		<nav class="orange white-text" role="navigation">
			<div class="nav-wrapper container">
				<a id="logo-container" href="/" class="brand-logo">{{ trans('messages.name') }}</a>
				<ul class="right hide-on-med-and-down">
					<li><a href="/">{{ trans('messages.home') }}</a></li>
				</ul>
			</div>
		</nav>
	<div class="white-text orange" style="height:30px"></div>

	<main class="container">
		<div class="red darken-2 white-text" style="padding: 20px;margin-bottom:20px; padding-top:50px; padding-bottom:50px;">
			@section('content')
			<h1 class="center-align light" id="title">@yield('title')</h1>
			<h2 class="center-align light th">@yield('description')</h2>
			@show
		</div>
		@section('button')
		<a href="/" class="waves-effect waves-light btn blue darken-2 center-align" style="width:80%;max-width:350px;margin-top:20px">{{ trans('messages.backhome') }}</a>
		@show
	</main>

	<footer class="page-footer orange">
		<div class="footer-copyright">
			<div class="container">
                {{ trans('messages.organization') }} | {{ trans('messages.copyright') }}
			</div>
		</div>
	</footer>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script>
	if ($('h1')[0].scrollWidth >  $('h1').innerWidth()) {
		var fs = 85;
		while ($('h1')[0].scrollWidth >  $('h1').innerWidth()) {
			$('#title').css('font-size',fs+'px');
			fs--;
		}
		fs++;
		console.log('Title font size has been decreased to '+fs);
	}
	if ($(window).width() < 550) {
		$('#logo-container').text('{{ trans('messages.shortname') }}');
		console.log('Navbar title has been decreased');
	}
	</script>
	@yield('script')
</body>
</html>
