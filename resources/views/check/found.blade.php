<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PPDB - Pendaftaran Ditemukan</title>

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
    <div class="d-flex h-100 justify-content-center align-items-center">
        <div style="background-color: #FFF;padding:20px;max-width:1000px;margin:auto;">
            <center>
                <img src="{{asset('images/faktur.png')}}" alt="" width="400px">
                <h2>Check Status Pendaftaran</h2>
                
                <table class="table table-bordered">
                    <tr>
                        <td>Nama</td>
                        <td>{{$formulir->diri->nama_lengkap}}</td>
                    </tr>
                    <tr>
                        <td>Kota</td>
                        <td>{{$formulir->asal->kabupaten}}</td>
                    </tr>
                    <tr>
                        <td>Program</td>
                        <td>{{$formulir->rencana->program}}</td>
                    </tr>
                    <tr>
                        <td>Spesifikasi</td>
                        <td>{{$formulir->rencana->spesifikasi}}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span class="badge badge-{{$labels[$formulir->status]}}">{{$formulir->status}}</span></td>
                    </tr>
                </table>
                <a href="/check" class="btn btn-warning">Kembali</a>
            </center>
        </div>
    </div>
</body>

</html>