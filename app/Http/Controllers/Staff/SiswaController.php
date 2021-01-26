<?php

namespace App\Http\Controllers\Staff;

use App\Models\Fonnte;
use App\Models\Contact;
use App\Models\DataIbu;
use App\Models\DataAyah;
use App\Models\DataDiri;
use App\Models\DataWali;
use App\Models\Formulir;
use App\Models\AlamatAsal;
use Illuminate\Http\Request;
use App\Models\DataPendidikan;
use App\Models\BerkasPendaftaran;
use App\Models\DataRencanaSekolah;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SiswaController extends Controller
{
    //

    public function index()
    {
        $labels = [
            '' => '',
            'Dikirim' => 'primary',
            'Ditolak' => 'danger',
            'Diterima' => 'success',
            'Lulus' => 'success',
            'Tidak Lulus' => 'danger',
        ];
        $siswa = Formulir::where('status','<>','')->get();
        return view('staff.siswa.index', compact('siswa','labels'));
    }

    public function create()
    {
        return view('staff.siswa.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $contact = Contact::create([
                'nama_pendaftar' => $request->diri['nama_lengkap'],
                'status' => 'PAID',
                'nama_calon_siswa' => $request->diri['nama_lengkap'],
                'no_wa' => ' ',
                'email' => ' ',
                'alumni' => ' ',
                'sebutkan_nama_sekolah' => ' ',
                'domisili' => ' ',
                'alamat' => ' ',
                'tipe_pembayaran' => ' ',
                'biaya_pembayaran' => ' ',
                'tiket' => ' ',
            ]);
            $formulir = new Formulir();

            if ($nf = $formulir->create(['contact_id' => $contact->id,'status'=>'Diterima'])) {
                $tiket = $nf->id;
                if($tiket < 10)
                    $tiket = "000".$tiket;
                elseif($tiket < 100)
                    $tiket = "00".$tiket;
                elseif($tiket < 1000)
                    $tiket = "0".$tiket;
                
                $tiket = "MDTKT".$tiket;

                $nf->update(['kode_pendaftaran'=>$tiket]);
                $rencana = new DataRencanaSekolah();
                $diri = new DataDiri();
                $pendidikan = new DataPendidikan();
                $asal = new AlamatAsal();
                $ayah = new DataAyah();
                $ibu = new DataIbu();
                $wali = new DataWali();

                if (
                    $rencana->create(array_merge(['formulir_id' => $nf->id], $request->post('rencana'))) &&
                    $diri->create(array_merge(['formulir_id' => $nf->id], $request->post('diri'))) &&
                    $pendidikan->create(array_merge(['formulir_id' => $nf->id], $request->post('pendidikan'))) &&
                    $asal->create(array_merge(['formulir_id' => $nf->id], $request->post('asal'))) &&
                    $ayah->create(array_merge(['formulir_id' => $nf->id], $request->post('ayah'))) &&
                    $ibu->create(array_merge(['formulir_id' => $nf->id], $request->post('ibu'))) &&
                    $wali->create(array_merge(['formulir_id' => $nf->id], $request->post('wali')))
                ) {
                    $kk = $request->file("upload_kk")->store("berkas");
                    $akte = $request->file("upload_akte")->store("berkas");
                    $ijazah = $request->file("upload_ijazah")->store("berkas");
                    $shun = $request->file("upload_shun")->store("berkas");
                    $kartu_pemerintah = $request->file("upload_kartu_pemerintah")->store("berkas");
                    if ($kk && $akte && $ijazah && $shun  && $kartu_pemerintah) {

                        $berkas = new BerkasPendaftaran();

                        if ($berkas->create(array_merge($request->post('berkas'), [
                            'formulir_id' => $nf->id,
                            'upload_kk' => $kk,
                            'upload_akte' => $akte,
                            'upload_ijazah' => $ijazah,
                            'upload_shun' => $shun,
                            'upload_kartu_pemerintah' => $kartu_pemerintah,
                        ]))) {
                            DB::commit();
                            // $wa = new Fonnte;
                            // $wa->send_text("62".$contact->no_wa,$message);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
        }
        return redirect('/staff/siswa')->with(['success'=>'Berhasil buat pendaftaran']);
    }


    public function kelulusan()
    {
        $labels = [
            'Lulus' => 'success',
        ];
        $siswa = Formulir::where('status','Lulus')->get();
        return view('staff.siswa.index', compact('siswa','labels'));
    }

    function show($id)
    {
        $formulir = Formulir::findOrFail($id);
        return view('staff.siswa.show', compact('formulir'));
    }

    function updateFormulir($formulir, $status)
    {
        return $formulir->update([
            'status' => $status,
            'verificated_by' => auth()->user()->name,
        ]);
    }

    function approve(Formulir $formulir)
    {
        $this->updateFormulir($formulir, "Diterima");
        $wa = new Fonnte;
        $message = "Selamat, Berkas atas nama";
        $message .= "\nNama : ".$formulir->diri->nama_lengkap;
        $message .= "\nKota : ".$formulir->asal->kabupaten;
        $message .= "\nProgram : ".$formulir->rencana->program;
        $message .= "\nSpesifikasi : ".$formulir->rencana->spesifikasi;
        $message .= "\n\ntelah kami verifikasi. Silahkan download/cetak buktu-bukti PPDB melalui menu Download Berkas.";
        $message .= "\n\nRuang Ujian akan diumumkan melalui website https://ppdb.malhikdua.sch.id";
        $wa->send_text("62".$formulir->contact->no_wa,$message);
        return redirect()->route('staff.siswa.show',$formulir->id)->with(['success'=>'Berhasil Terima Pendaftar']);
    }

    function decline(Formulir $formulir)
    {
        $this->updateFormulir($formulir, "Ditolak");
        $wa = new Fonnte;
        $message = "Berkas anda kurang lengkap, mohon isi data yang diwajibkan. Klik https://ppdb.malhikdua.sch.id/login";
        $message .= "\nJika sudah klik tombol VERIFIKASI BERKAS/PENDAFTARAN untuk diperiksa petugas";
        $wa->send_text("62".$formulir->contact->no_wa,$message);
        return redirect()->route('staff.siswa.show',$formulir->id)->with(['success'=>'Berhasil Tolak Pendaftar']);
    }

    function lulus(Formulir $formulir)
    {
        $this->updateFormulir($formulir, "Lulus");
        $wa = new Fonnte;
        $message = "Selamat, Pendaftar atas nama";
        $message .= "\nNama : ".$formulir->diri->nama_lengkap;
        $message .= "\nKota : ".$formulir->asal->kabupaten;
        $message .= "\nProgram : ".$formulir->rencana->program;
        $message .= "\nSpesifikasi : ".$formulir->rencana->spesifikasi;
        $message .= "\n\ntelah kami nyatakan lulus";
        $wa->send_text("62".$formulir->contact->no_wa,$message);
        return redirect()->route('staff.siswa.show',$formulir->id)->with(['success'=>'Berhasil Terima Pendaftar']);
    }

    function gagal(Formulir $formulir)
    {
        $this->updateFormulir($formulir, "Tidak Lulus");
        return redirect()->route('staff.siswa.show',$formulir->id)->with(['success'=>'Berhasil Tolak Pendaftar']);
    }

    function delete($id)
    {
        Formulir::findOrFail($id)->delete();
        return redirect('/staff/siswa')->with(['success'=>'Berhasil hapus pendaftaran']);
    }
}
