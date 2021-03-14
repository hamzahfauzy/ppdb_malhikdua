<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>NIK</th>
            <th>Nama Lengkap</th>
            <th>Jenis Kelamin</th>
            <th>Tempat Tinggal</th>
            <th>Tanggal Lahir</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1 ?>
        @foreach($siswa as $s)
        <tr>
            <td>{{$i}}</td>
            <td>{{$s->diri->NIK}}</td>
            <td>{{$s->diri->nama_lengkap}}</td>
            <td>{{$s->diri->jenis_kelamin}}</td>
            <td>{{$s->diri->tempat_tinggal}}</td>
            <td>{{date('d-m-Y',strtotime($s->diri->tanggal_lahir))}}</td>
            <td><span class="badge badge-{{$labels[$s->status]}}">{{$s->status}}</span></td>
        </tr>
        <?php $i++ ?>
        @endforeach
    </tbody>
</table>