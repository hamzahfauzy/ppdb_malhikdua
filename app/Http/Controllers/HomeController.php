<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Authy\AuthyApi;
use Dompdf\Options;
use App\Models\User;
use App\Models\Duitku;
use App\Models\Fonnte;
use App\Models\Tripay;
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

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('index');
    }

    public function home()
    {
        return view('home');
    }

    public function paymentChannel()
    {
        $tripay = new Tripay(getenv('TRIPAY_PRIVATE_KEY'), getenv('TRIPAY_API_KEY'));
        // return $tripay->curlAPI($tripay->URL_channelPp,'','GET');
        return $tripay->curlAPI($tripay->URL_channelMp,'','GET');
        // return view('payment-gateway');
    }

    public function paymentGateway()
    {
        $channels = $this->paymentChannel();
        return view('payment-gateway',[
            'channels' => $channels
        ]);
    }

    public function siswa()
    {

        $siswa = DataDiri::get();

        $data = [
            'siswa' => $siswa
        ];
        return view('siswa', $data);
    }

    public function kartu()
    {
        $path = 'images/kop.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $kop = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $formulir = auth()->user()->contact->formulir;
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('download.kartu',compact('kop','formulir')));

        $dompdf->render();

        $dompdf->stream('Kartu Ujian Masuk.pdf',['Attachment'=>false]);

        // return view('kartu');
    }

    public function pernyataan()
    {

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('download.pernyataan',['formulir'=>auth()->user()->contact->formulir]));

        $dompdf->render();

        $dompdf->stream('Surat Pernyataan',['Attachment'=>false]);

        // return view('pernyataan');
    }

    public function isian()
    {
        $formulir = auth()->user()->contact->formulir;
        $age = (new Carbon($formulir->diri->tanggal_lahir))->age;
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('download.isian',compact('formulir','age')));

        $dompdf->render();


        $dompdf->stream('formulir.pdf',['Attachment'=>false]);
    }

    public function faktur()
    {
        $path = 'images/faktur.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $faktur = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $formulir = auth()->user()->contact->formulir;
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('download.faktur',compact('formulir','faktur')));

        $dompdf->render();


        $dompdf->stream('formulir.pdf',['Attachment'=>false]);
    }

    public function pembayaran()
    {
        $data = [
            'contacts' => Contact::get()
        ];
        return view('pembayaran', $data);
    }

    public function welcome(Request $request)
    {
        $duitku = [
            'VC' => 'Credit Card (Visa / Master)',
            'BK' => 'BCA KlikPay',
            'M1' => 'Mandiri Virtual Account',
            'BT' => 'Permata Bank Virtual Account',
            'B1' => 'CIMB Niaga Virtual Account',
            'A1' => 'ATM Bersama',
            'I1' => 'BNI Virtual Account',
            'VA' => 'Maybank Virtual Account',
            'FT' => 'Ritel',
            'OV' => 'OVO',
        ];
        if ($request->isMethod('post')) {

            if($request->has('reset'))
            {
                session()->forget('user_sms');
                session()->forget('sms');
                session()->forget('request');
                return redirect()->back();
            }
            // check email and phone exists
            $check_contact = Contact::where('email',$request->email)->orwhere('no_wa',$request->no_wa)->exists();
            $check_user = Contact::where('email',$request->no_wa)->exists();
            if($check_contact || $check_user)
                return redirect()->back()->with(['contact_exists' => 'Email atau No. Whatsapp sudah terdaftar']);
            
            $authy_api = new AuthyApi(getenv('AUTHY_KEY'));

            if ($request->has('verificated')) {
                unset($request['verificated']);
                unset($request['otp']);
                unset($request['user_sms']);
                
                $contact = new Contact();
                $request->merge(['status'=>'']);
                $amount = $request->domisili == 'Warga Benda' || $request->alumni == 'Ya' ? 110000 : 135000;
                // bayar dulu
                if($request->payment_gateway == 'tripay')
                {
                    $privateKey = getenv('TRIPAY_PRIVATE_KEY');
                    $merchantCode = getenv('TRIPAY_MERCHANT_CODE');
                    $merchantRef = getenv('TRIPAY_MERCHANT_REF');
                    
                    $signature = hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey);
                    $data = [
                        'method'            => $request->tipe_pembayaran,
                        'merchant_ref'      => $merchantRef,
                        'amount'            => $amount,
                        'customer_name'     => $request->nama_pendaftar,
                        'customer_email'    => $request->email,
                        'customer_phone'    => $request->no_wa,
                        'order_items'       => [
                            [
                                'sku'       => 'PPDB',
                                'name'      => 'PPDB Malhikdua',
                                'price'     => $amount,
                                'quantity'  => 1
                            ]
                        ],
                        'signature'         => hash_hmac('sha256', $merchantCode.$merchantRef.$amount, $privateKey)
                    ];

                    $tripay = new Tripay($privateKey, getenv('TRIPAY_API_KEY'));
                    $response = $tripay->curlAPI($tripay->URL_transMp,$data,'POST');
                    if($response['success'] == false)
                        return redirect()->back();
                    $response_data = $response['data'];

                    $request->merge([
                        'status' => $response_data['status'],
                        'tiket' => '',
                        'payment_gateway' => $request->payment_gateway,
                        'payment_reference' => $response_data['reference'],
                        'payment_code' => $response_data['pay_code'],
                        'checkout_url' => $response_data['checkout_url'],
                        'expired_time' => $response_data['expired_time'],
                    ]);
                }
                else
                {
                    $duitku = new Duitku;
                    $result = $duitku->pay($amount, $request->tipe_pembayaran, [
                        'name' => $request->nama_pendaftar,
                        'email' => $request->email,
                        'phone' => $request->no_wa
                    ]);

                    // return $result;

                    $request->merge([
                        'status' => $result['statusMessage'],
                        'tiket' => '',
                        'payment_gateway' => $duitku[$request->payment_gateway],
                        'payment_reference' => $result['reference'],
                        'payment_code' => $result['vaNumber'],
                        'checkout_url' => $result['paymentUrl'],
                        'expired_time' => '',
                    ]);

                }

                if ($nc = $contact->create($request->post())) {
                    $user = new User();

                    if ($user->create([
                        'email' => $nc->no_wa,
                        'name' => $nc->nama_pendaftar,
                        'password' => bcrypt($nc->no_wa)
                    ])) {
                        $contact = $nc;
                        $message = "Halo, $contact->nama_pendaftar ($contact->alamat), \nBerikut ini adalah data registrasi anda:";
                        $message .= "\nID Transaksi : $contact->payment_code";
                        $message .= "\nTotal : Rp. ".number_format($contact->biaya_pembayaran);
                        $message .= "\nSilahkan melakukan pembayaran melalui $contact->tipe_pembayaran untuk mendapatkan tiket pengisian formulir PPDB.";
                        $message .= "\nTerima Kasih";
                        $message .= "\n\nCek Status PPDB anda di ".route('check');

                        $wa = new Fonnte;
                        $wa->send_text("62".$contact->no_wa,$message);

                        // if($request->payment_gateway == 'tripay')
                        return redirect()->to($nc->checkout_url);
                    }
                }
            } else {
                if (!$request->has('otp')) {
                    try {
                        //code...
                        $user = $authy_api->registerUser($request->post('email'), $request->post('no_wa'), 62); // email, cellphone, country_code
    
                        if ($user->ok()) {
                            $sms = $authy_api->requestSms($user->id());
    
                            if ($sms->ok()) {
                                session([
                                    'user_sms' => $user,
                                    'sms' => $sms,
                                    'request' => $request->input()
                                ]);
                                return redirect()->back();
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                        return view('not-connected');
                    }
                } else {
                    try {
                        $verification = $authy_api->verifyToken($request->post('user_sms'), $request->post('otp'));
    
                        if ($verification->ok()) {
                            session()->forget('request');

                            session([
                                'verification' => $verification,
                                'request' => $request->input()
                            ]);
                            return redirect()->back();
                        }
                        //code...
                    } catch (\Throwable $th) {
                        //throw $th;
                        return view('not-connected');
                    }
                }
            }
        }

        $tripay = $this->paymentChannel();

        return view('welcome',compact('duitku','tripay'));
    }

    public function tiket(Request $request)
    {
        if ($request->isMethod('post')) {
            $contact = Contact::where('tiket', $request->post('tiket'))->first();
            if ($contact) {
                return redirect()->to('formulir')->with(['contact' => $contact]);
            }
            return redirect()->back();
        }

        return view('tiket');
    }

    public function formulir(Request $request)
    {
        $contact = auth()->user()->contact;
        if($contact->formulir && in_array($contact->formulir->status,['Dikirim','Diterima']))
            return redirect('/home');
        if ($request->isMethod('post')) {
            if($contact->formulir)
            {
                DB::beginTransaction();
                try {
                    $formulir = $contact->formulir;
    
                    $rencana = $formulir->data_rencana_sekolah;
                    $diri = $formulir->data_diri;
                    $pendidikan = $formulir->data_pendidikan;
                    $asal = $formulir->alamat_asal;
                    $ayah = $formulir->ayah;
                    $ibu = $formulir->ibu;
                    if($formulir->wali)
                        $wali = $formulir->wali;
                    else
                        $wali = (new DataWali)->create(array_merge(['formulir_id' => $formulir->id], $request->post('wali')));
                    if (
                        $rencana->update($request->post('rencana')) &&
                        $diri->update($request->post('diri')) &&
                        $pendidikan->update($request->post('pendidikan')) &&
                        $asal->update($request->post('asal')) &&
                        $ayah->update($request->post('ayah')) &&
                        $ibu->update($request->post('ibu')) &&
                        $wali->update($request->post('wali'))
                    ) {
                        $berkas = $formulir->berkas_pendaftaran;
                        $berkas->update($request->post('berkas'));

                        if($request->file('upload_kk'))
                        {
                            $kk = $request->file("upload_kk")->store("berkas");
                            $berkas->update(['upload_kk' => $kk]);
                        }
                        if($request->file('upload_akte'))
                        {
                            $kk = $request->file("upload_akte")->store("berkas");
                            $berkas->update(['upload_akte' => $kk]);
                        }
                        if($request->file('upload_ijazah'))
                        {
                            $kk = $request->file("upload_ijazah")->store("berkas");
                            $berkas->update(['upload_ijazah' => $kk]);
                        }
                        if($request->file('upload_shun'))
                        {
                            $kk = $request->file("upload_shun")->store("berkas");
                            $berkas->update(['upload_shun' => $kk]);
                        }
                        if($request->file('upload_kartu_pemerintah'))
                        {
                            $kk = $request->file("upload_kartu_pemerintah")->store("berkas");
                            $berkas->update(['upload_kartu_pemerintah' => $kk]);
                        }
                        DB::commit();
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    $e;
                }
            }
            else
            {
                DB::beginTransaction();
                try {
                    $formulir = new Formulir();
    
                    if ($nf = $formulir->create(['contact_id' => $contact->id])) {
                        $tiket = $nf->id;
                        if($tiket < 10)
                            $tiket = "000".$tiket;
                        elseif($tiket < 100)
                            $tiket = "00".$tiket;
                        elseif($tiket < 1000)
                            $tiket = "0".$tiket;
                        
                        $tiket = "MDTKT".$tiket;

                        $nf->update(['kode_formulir'=>$tiket]);

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
                    // throw($e);
                    // something went wrong
                }
            }
            return redirect('/home')->with(['success'=>'Berhasil edit formulir']);
        }
        if($contact->formulir)
        {
            $formulir = $contact->formulir()->with(['alamat_asal','berkas_pendaftaran','ayah','ibu','wali','data_diri','data_pendidikan','data_rencana_sekolah'])->first();
            $labels = [
                'diri' => [
                    'nama_lengkap',
                    'alamat',
                    'tempat_tinggal',
                    'tanggal_lahir',
                    'jenis_kelamin',
                    'NIK',
                    'anak_ke',
                    'jumlah_saudara',
                    'status'
                ],
                'asal' => [
                    'alamat',
                    'rt',
                    'rw',
                    'desa_kelurahan',
                    'kecamatan',
                    'kabupaten',
                    'provinsi',
                    'kode_pos'
                ],
                'ayah' => [
                    'no_kk',
                    'nama',
                    'no_kk_ayah',
                    'status',
                    'tanggal_lahir',
                    'keadaan',
                    'pekerjaan',
                    'pendidikan',
                    'penghasilan',
                ],
                'ibu' => [
                    'no_kk',
                    'nama',
                    'no_kk_ibu',
                    'status',
                    'tanggal_lahir',
                    'keadaan',
                    'pekerjaan',
                    'pendidikan',
                    'penghasilan',
                ],
                'wali' => [
                    'nama',
                    'NIK',
                    'hubungan_dengan_pendaftar',
                    'tanggal_lahir',
                    'pekerjaan',
                    'pendidikan',
                ],
                'pendidikan' => [
                    'NISN',
                    'sekolah_asal',
                    'NPSN',
                    'angkatan_lulus',
                    'alamat'
                ],
                'rencana' => [
                    'program','spesifikasi'
                ],
                'berkas' => [
                    'no_seri_shun',
                    'no_seri_ijazah',
                    'no_peserta_un',
                    'kartu_pemerintah',
                ]
            ];
            return view('edit-formulir',compact('formulir','labels'));
        }
        else
            return view('formulir');
    }

    function send()
    {
        $formulir = auth()->user()->contact->formulir;
        $formulir->update([
            'status' => 'Dikirim'
        ]);
        $wa = new Fonnte;
        $message = "Selamat, Pendaftar atas nama";
        $message .= "\nNama : ".$formulir->diri->nama_lengkap;
        $message .= "\nKota : ".$formulir->asal->kabupaten;
        $message .= "\nProgram : ".$formulir->rencana->program;
        $message .= "\nSpesifikasi : ".$formulir->rencana->spesifikasi;
        $message .= "\n\nFormulir anda berhasil dikirim";
        $message .= "\n\nSilahkan check status pendaftaran pada ".route('check')." dengan kode formulir ".$formulir->kode_formulir;
        $wa->send_text("62".$formulir->contact->no_wa,$message);
        return redirect('/home')->with(['success'=>'Formulir sudah di kirim']);
    }

    function berkas()
    {
        return view('berkas');
    }

    function download($row)
    {
        if($row == 'Surat Pernyataan')
            return $this->pernyataan();
        if($row == 'Kartu Ujian Masuk')
            return $this->kartu();
        if($row == 'Biodata')
            return $this->isian();
        if($row == 'Faktur Pembayaran')
            return $this->faktur();
    }

    function check()
    {
        $labels = [
            '' => '',
            'Dikirim' => 'primary',
            'Ditolak' => 'danger',
            'Diterima' => 'success',
            'Lulus' => 'success',
            'Tidak Lulus' => 'danger',
        ];
        if (isset($_GET['kode'])) 
        {
            $kode = $_GET['kode'];
            $formulir = Formulir::where('kode_formulir',$kode)->first();
            if($formulir)
                return view('check.found',compact('formulir','labels'));
            return view('check.not-found',compact('kode'));
        }
        return view('check');
    }
}
