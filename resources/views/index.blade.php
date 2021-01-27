<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PPDB</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">
    <style>
    html, body {
        height: 100%;
    }
    .payment {
        display: none;
    }
    body {
        background-color: green;
    }
    </style>
</head>

<body>
    <div class="d-flex h-100 justify-content-center align-items-center" style="margin:15px;">
        <div style="background-color: #FFF;padding:20px;max-width:500px;margin:auto;">
            <center>
                <div class="alert alert-warning">
                    Sistem sedang dalam proses migrasi. Kembali ONLINE  setelah proses selesai. Info PPDB silakan hubungi +62 81903189474
                </div>
                <h2>Selamat Datang di</h2>
                <img src="{{asset('images/faktur.png')}}" alt="" width="400px">
                <br>
                <a href="/daftar" class="btn btn-success" style="margin-bottom: 10px;">Pendaftaran Peserta Didik Baru</a>
                <a href="/check" class="btn btn-success" style="margin-bottom: 10px;">Check Status Pendaftaran</a>
            </center>
        </div>
    </div>
</body>

</html>