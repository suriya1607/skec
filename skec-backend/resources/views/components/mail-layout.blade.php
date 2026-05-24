<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:Arial, sans-serif;
            background:#f4f6f9;
            color:#333;
            padding:30px 15px;
        }

        .wrapper{
            max-width:640px;
            margin:auto;
            background:#fff;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 4px 20px rgba(0,0,0,0.08);
        }

        .header{
            background:linear-gradient(135deg,#1A3C6E 0%,#2E86C1 100%);
            padding:40px;
            text-align:center;
        }

        .header h1{
            color:#fff;
            font-size:28px;
        }

        .header p{
            color:rgba(255,255,255,.8);
            margin-top:8px;
            font-size:14px;
        }

        .body{
            padding:40px;
        }

        .footer{
            background:#fafafa;
            border-top:1px solid #eee;
            padding:24px;
            text-align:center;
            font-size:12px;
            color:#999;
        }

        .btn{
            display:inline-block;
            background:#1A3C6E;
            color:#fff !important;
            text-decoration:none;
            padding:14px 32px;
            border-radius:8px;
            margin-top:20px;
            font-weight:600;
        }

        .panel{
            background:#f8fafc;
            border-left:4px solid #1A3C6E;
            padding:20px;
            border-radius:6px;
            margin:25px 0;
        }

    </style>
</head>
<body>

<div class="wrapper">

    <div class="header">
        <h1>{{ $appName ?? config('app.name') }}</h1>
        <p>Your Digital Learning Hub</p>
    </div>

    <div class="body">
        {{ $slot }}
    </div>

    <div class="footer">
        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>

</div>

</body>
</html>