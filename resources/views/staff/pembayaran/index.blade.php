@extends('layouts.staff')
@section('title','Data Pembayaran')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Data Pembayaran</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/staff">Home</a></li>
                    <li class="breadcrumb-item active">Pembayaran</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content profile">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
                <a href="{{route('staff.pembayaran.create')}}" class="btn btn-success"><i class="fas fa-plus"></i> Kirim Tiket</a>
            </div>
            <div class="card-body login-card-body">
              @if ($msg = session('success'))
                  <div class="alert alert-success" role="alert">
                      {{ $msg }}
                  </div>
              @endif
              <div class="table-responsive">
              <table class="table table-striped datatable display responsive nowrap">
                  <thead>
                  <tr>
                      <td>#</td>
                      <td>Nama Pendaftar</td>
                      <td>Nama Calon Siswa</td>
                      <td>Pembayaran</td>
                      <td>Tiket</td>
                      <td>Status</td>
                      <td>Aksi</td>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($contacts as $key => $contact)
                  <tr>
                      <td>{{++$key}}</td>
                      <td>{{$contact->nama_pendaftar}}</td>
                      <td>{{$contact->nama_calon_siswa}}</td>
                      <td>
                          {{$contact->tipe_pembayaran}} - {{$contact->payment_code}}<br>
                          Rp. <b>{{number_format($contact->biaya_pembayaran)}}</b>
                      </td>
                      <td>{{$contact->tiket}}</td>
                      <td>{{$contact->status}}</td>
                      <td>
                          <a href="{{route('staff.pembayaran.show',$contact->id)}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Detail</a>
                      </td>
                  </tr>
                  @endforeach
                  </tbody>
              </table>
            </div>
            </div>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@endsection

@section('script')

<script>
    $("table").DataTable({
        "responsive": true,
    })
</script>

@endsection