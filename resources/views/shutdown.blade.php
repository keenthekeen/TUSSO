<!doctype html>
<html>
<head>
    <title>Triamudom Student Postmaster</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="theme-color" content="#e91e63"/>
    <meta name="csrf-token" content=""/>
    <!-- link rel="manifest" href="/manifest.json"/ -->
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
            font-family: 'THSarabunNew', Sans-Serif !important
        }

        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        .brand-logo {
            font-size: 1.5rem !important
        }

        .orange {
            background-color: #e91e63 !important
        }
        .orange-text {
            color: #e91e63 !important
        }

        .footer-copyright a {
            color: lightgrey;
        }

        .footer-copyright a:hover {
            text-decoration: underline
        }

        .fullwidth {width:100%}
        main {padding-top:15px;font-size:18px}
        .smll {font-size: 70%}
        h1 {line-height: 120%;text-align:center}
        h2 {font-size:1.7rem;text-align:center; font-weight:300;font-family: 'Roboto', Sans-Serif !important;}
    </style>
</head>
<body class="orange">
<main class="orange white-text" style="padding-top:10vh;padding-bottom:10vh">
    <div class="container">
        <h1>ระบบจัดการอีเมลนักเรียน<br/>โรงเรียนเตรียมอุดมศึกษา</h1>
        <h2>Triamudom Student Postmaster</h2>
        <br /><br /><br />
        <div style="max-width:500px;font-size:1.5rem;border-radius:1vh;padding:1.5vh;margin:auto" class="white black-text center-align" id="dtext">
            ระบบปิด
        </div>
        <br /><br /><br /><br />
        <div class="center-align" style="font-size: 80%" id="dev"><?php
            echo 'พัฒนาโดย ศิวัช เตชวรนันท์ ต.อ.๗๘';
            ?></div>
    </div>
</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    $(function() {
        var t1 = false;
        setInterval(function() {
            if (t1) {
                $('#dtext').text('ระบบปิดใช้งาน');
                t1 = false;
            } else {
                $('#dtext').text('System Shutdown by Administrator');
                t1 = true;
            }
        }, 3000);
    });
</script>

</body>
</html>
