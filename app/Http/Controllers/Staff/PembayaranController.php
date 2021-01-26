<?php

namespace App\Http\Controllers\Staff;

use App\Models\Fonnte;
use App\Models\Tripay;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PembayaranController extends Controller
{

    function __construct()
    {
        $this->contact = new Contact;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $contacts = $this->contact->orderby('created_at','desc')->get();
        return view('staff.pembayaran.index',compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contact = $this->contact->findOrFail($id);
        return view('staff.pembayaran.show',compact('contact'));
    }

    public function check(Contact $contact)
    {
        if($contact->payment_gateway=='tripay')
        {
            $privateKey = getenv('TRIPAY_PRIVATE_KEY');
            $tripay = new Tripay($privateKey, getenv('TRIPAY_API_KEY'));
            $response = $tripay->curlAPI($tripay->URL_transDetailMp,['reference'=>$contact->payment_reference],'GET');
            if($response['success'])
            {
                $response_data = $response['data'];
                $contact->update([
                    'status' => $response_data['status']
                ]);
            }
        }
        return redirect()->route('staff.pembayaran.show',$contact->id)->with(['success'=>'Status Pembayaran Sudah Update']);
    }

    public function approve(Contact $contact)
    {
        $tiket = $contact->id;
        if($tiket < 10)
            $tiket = "000".$tiket;
        elseif($tiket < 100)
            $tiket = "00".$tiket;
        elseif($tiket < 1000)
            $tiket = "0".$tiket;
        
        $tiket = "MDTKT".$tiket;
        $tiket = md5($tiket);
        $tiket = substr($tiket,0,8);
        $contact->update([
            'tiket' => $tiket
        ]);

        $wa = new Fonnte;
        $message = "Terima kasih $contact->nama_pendaftar ($contact->alamat) telah melakukan pembayaran PPDB Malhikdua melalui $contact->tipe_pembayaran";
        $message .= "\nBerikut adalah tiket pengisian formulir anda : $tiket";
        $message .= "\nGunakan tiket ini untuk mengisi/mengedit formulir PPDB hingga lengkap.";
        $message .= "\nFormulir PPDB di ".route('login')." (ONLINE)";
        $message .= "\nManfaatkan tombol SAVE untuk menyimpan isian formulir.";
        $message .= "\nJika sudah, klik tombol VERIFIKASI BERKAS/PENDAFTARAN untuk diperiksa petugas.";
        $wa->send_text("62".$contact->no_wa,$message);

        return redirect()->route('staff.pembayaran.show',$contact->id)->with(['success'=>'Pendaftaran berhasil di approve']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
