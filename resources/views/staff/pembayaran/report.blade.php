<table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
        <td>#</td>
        <td class="all">Nama Pendaftar</td>
        <td>Nama Calon Siswa</td>
        <td>Pembayaran</td>
        <td>Tiket</td>
        <td>Status</td>
        <td>Pendaftaran</td>
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
            Rp. <b>{{is_numeric($contact->biaya_pembayaran) ? number_format($contact->biaya_pembayaran) : 0}}</b>
        </td>
        <td>{{$contact->tiket}}</td>
        <td>{{$contact->status}}</td>
        <td>{{$contact->payment_gateway?'Online':'Offline'}}</td>
    </tr>
    @endforeach
    </tbody>
</table>