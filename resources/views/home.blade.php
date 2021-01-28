@extends('layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                @if(!auth()->user()->contact->formulir)
                <center>
                    Belum ada data. silahkan klik tombol di bawah ini untuk mengisi Formulir.
                    <br>
                    <a href="{{route('formulir')}}" class="btn btn-success">Isi Formulir</a>
                </center>
                @else
                    @php $formulir=auth()->user()->contact->formulir; @endphp
                    @if($formulir->status == NULL || $formulir->status == "Ditolak")
                    <a href="{{route('formulir')}}" class="btn btn-success">Edit Formulir</a>
                    <a href="{{route('send')}}" onclick="if(confirm('Apakah anda sudah yakin akan mengirimkan formulir untuk di verifikasi ?')){return true}else{return false}" class="btn btn-primary">VERIFIKASI BERKAS / PENDAFTARAN</a>
                    <p></p>
                    @elseif($formulir->status == 'Dikirim')
                    <div class="alert alert-warning" role="alert">
                        Data formulir sedang diproses
                    </div>
                    @endif
                    @if ($msg = session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ $msg }}
                        </div>
                    @endif
                    <h4>Data Rencana Sekolah</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Program</label>
                                <p>{{$formulir->data_rencana_sekolah->program}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Spesifikasi</label>
                                <p>{{$formulir->data_rencana_sekolah->spesifikasi}}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Data Diri</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Nama Lengkap</label>
                                <p>{{$formulir->data_diri->nama_lengkap}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Alamat</label>
                                <p>{{$formulir->data_diri->alamat}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Tempat Tinggal</label>
                                <p>{{$formulir->data_diri->tempat_tinggal}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Tanggal Lahir</label>
                                <p>{{date('d-m-Y',strtotime($formulir->data_diri->tanggal_lahir))}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Jenis Kelamin</label>
                                <p>{{$formulir->data_diri->jenis_kelamin}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">NIK</label>
                                <p>{{$formulir->data_diri->NIK}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Anak Ke</label>
                                <p>{{$formulir->data_diri->anak_ke}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Jumlah Saudara</label>
                                <p>{{$formulir->data_diri->jumlah_saudara}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Status Anak</label>
                                <p>{{$formulir->data_diri->status}}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Data Pendidikan</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">NISN</label>
                                <p>{{$formulir->data_pendidikan->NISN}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Sekolah Asal</label>
                                <p>{{$formulir->data_pendidikan->sekolah_asal}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">NPSN Sekolah Asal</label>
                                <p>{{$formulir->data_pendidikan->NPSN}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Angkatan Lulus</label>
                                <p>{{$formulir->data_pendidikan->angkatan_lulus}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Alamat</label>
                                <p>{{$formulir->data_pendidikan->alamat}}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Alamat Asal</h4>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <p>
                                    {{$formulir->alamat_asal->alamat.', RT '.$formulir->alamat_asal->rt.', RW '}}
                                    {{$formulir->alamat_asal->rw.', '.$formulir->alamat_asal->desa_kelurahan.' Kec. '}}
                                    {{$formulir->alamat_asal->kecamatan.', Kab. '.$formulir->alamat_asal->kabupaten.', '.$formulir->alamat_asal->provinsi}}
                                    Kode Pos {{$formulir->alamat_asal->kode_pos}}
                                </p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Data Ayah</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Nomor KK</label>
                                <p>{{$formulir->ayah->no_kk}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Nama</label>
                                <p>{{$formulir->ayah->nama}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Status</label>
                                <p>{{$formulir->ayah->status}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">No KK Ayah</label>
                                <p>{{$formulir->ayah->no_kk}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Tanggal Lahir</label>
                                <p>{{date('d-m-Y',strtotime($formulir->ayah->tanggal_lahir))}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Keadaan</label>
                                <p>{{$formulir->ayah->keadaan}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Pekerjaan</label>
                                <p>{{$formulir->ayah->pekerjaan}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Pendidikan</label>
                                <p>{{$formulir->ayah->pendidikan}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Penghasilan</label>
                                <p>{{$formulir->ayah->penghasilan}}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Data Ibu</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Nomor KK</label>
                                <p>{{$formulir->ibu->no_kk}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Nama</label>
                                <p>{{$formulir->ibu->nama}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Status</label>
                                <p>{{date('d-m-Y',strtotime($formulir->ibu->tanggal_lahir))}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">No KK Ayah</label>
                                <p>{{$formulir->ibu->no_kk}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Tanggal Lahir</label>
                                <p>{{$formulir->ibu->tanggal_lahir}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Keadaan</label>
                                <p>{{$formulir->ibu->keadaan}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Pekerjaan</label>
                                <p>{{$formulir->ibu->pekerjaan}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Pendidikan</label>
                                <p>{{$formulir->ibu->pendidikan}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Penghasilan</label>
                                <p>{{$formulir->ibu->penghasilan}}</p>
                            </div>
                        </div>
                    </div>

                    @if($formulir->wali)
                    <hr>

                    <h4>Data Wali</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Nama Wali</label>
                                <p>{{$formulir->wali->nama}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">NIK</label>
                                <p>{{$formulir->wali->NIK}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Hubungan dengan pendaftar</label>
                                <p>{{$formulir->wali->hubungan_dengan_pendaftar}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Penghasilan</label>
                                <p>{{$formulir->wali->penghasilan}}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>
                    <h4>Berkas Pendaftaran</h4>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">KK</label>
                                <p><a href="{{Storage::url($formulir->berkas_pendaftaran->upload_kk)}}">Download</a></p>
                            </div>
                            <div class="form-group">
                                <label for="">Akte</label>
                                <p><a href="{{Storage::url($formulir->berkas_pendaftaran->upload_akte)}}">Download</a></p>
                            </div>
                            <div class="form-group">
                                <label for="">Ijazah</label>
                                <p>{{$formulir->berkas_pendaftaran->no_seri_ijazah}} - <a href="{{Storage::url($formulir->berkas_pendaftaran->upload_ijazah)}}">Download</a></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">SHUN</label>
                                <p>{{$formulir->berkas_pendaftaran->no_seri_shun}} - <a href="{{Storage::url($formulir->berkas_pendaftaran->upload_shun)}}">Download</a></p>
                            </div>
                            <div class="form-group">
                                <label for="">No Peserta UN</label>
                                <p>{{$formulir->berkas_pendaftaran->no_peserta_un}}</p>
                            </div>
                            <div class="form-group">
                                <label for="">Kartu Pemerintah</label>
                                @if($formulir->berkas_pendaftaran->kartu_pemerintah=='Ya')
                                <p><a href="{{Storage::url($formulir->berkas_pendaftaran->upload_kartu_pemerintah)}}">Download</a></p>
                                @else
                                -
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
@endsection