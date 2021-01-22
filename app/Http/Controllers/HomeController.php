<?php

namespace App\Http\Controllers;

use App\Models\AlamatAsal;
use App\Models\BerkasPendaftaran;
use App\Models\Contact;
use App\Models\DataAyah;
use App\Models\DataDiri;
use App\Models\DataIbu;
use App\Models\DataPendidikan;
use App\Models\DataRencanaSekolah;
use App\Models\DataWali;
use App\Models\Formulir;
use App\Models\User;
use Illuminate\Http\Request;
use Authy\AuthyApi;

use Dompdf\Dompdf;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $this->middleware('auth');
        return view('home');
    }

    public function siswa()
    {
        $this->middleware('auth');

        $siswa = DataDiri::get();

        $data = [
            'siswa' => $siswa
        ];
        return view('siswa', $data);
    }

    public function kartu()
    {
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml(view('kartu'));

        // $dompdf->render();

        // $dompdf->stream();

        return view('kartu');
    }

    public function pernyataan()
    {

        // $dompdf = new Dompdf();
        // $dompdf->loadHtml(view('pernyataan'));

        // $dompdf->render();

        // $dompdf->stream();

        return view('pernyataan');
    }

    public function isian()
    {

        // $dompdf = new Dompdf();
        // $dompdf->loadHtml(view('isian'));

        // $dompdf->render();

        // $dompdf->stream();

        return view('isian');
    }

    public function pembayaran()
    {
        $this->middleware('auth');
        $data = [
            'contacts' => Contact::get()
        ];
        return view('pembayaran', $data);
    }

    public function welcome(Request $request)
    {

        if ($request->isMethod('post')) {
            $authy_api = new AuthyApi(getenv('AUTHY_KEY'));

            if ($request->has('verificated')) {
                $contact = new Contact();

                $request->merge(['status' => '-']);

                unset($request['verificated']);

                if ($nc = $contact->create($request->post())) {
                    $user = new User();

                    if ($user->create([
                        'email' => $nc->no_wa,
                        'name' => $nc->nama_pendaftar,
                        'password' => encrypt($nc->tiket)
                    ])) {
                        dd($request->input());
                    }
                }
            } else {
                if (!$request->has('otp')) {
                    $user = $authy_api->registerUser($request->post('email'), $request->post('no_wa'), 62); // email, cellphone, country_code

                    if ($user->ok()) {
                        $sms = $authy_api->requestSms($user->id());

                        if ($sms->ok()) {
                            return redirect()->back()->with(['user_sms' => $user, 'sms' => $sms, 'request' => $request->input()]);
                        }
                    }
                } else {

                    $verification = $authy_api->verifyToken($request->post('user_sms'), $request->post('otp'));

                    if ($verification->ok()) {
                        return redirect()->back()->with(['verification' => $verification, 'request' => $request->input()]);
                    }
                }
            }
        }

        return view('welcome');
    }

    public function tiket(Request $request)
    {

        if ($request->isMethod('post')) {

            $contact = Contact::where('tiket', $request->post('tiket'))->first();

            if ($contact) {
                return redirect()->to('formulir')->with(['contact' => $contact]);
            }

            dd($contact);

            return redirect()->back();
        }

        return view('tiket');
    }

    public function formulir(Request $request)
    {
        if ($request->isMethod('post')) {
            $formulir = new Formulir();

            if ($nf = $formulir->create(['contact_id' => $request->post('contact_id')])) {
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
                            dd([$request->post()]);
                        }
                    }
                }
            }
        }
        return view('formulir');
    }
}
