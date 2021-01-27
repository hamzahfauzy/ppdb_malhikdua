<?php

namespace App\Http\Controllers;

use App\Models\Duitku;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    //
    function tripay()
    {
        $tripay = new Tripay;
        $callback = $tripay->callback();

        if($callback->status)
        {
            $merchantRef = $callback->merchant_ref;
            $contact = Contact::where("payment_reference",$merchantRef)->firstOrFail();
            $contact->update([
                'status' => $callback->status
            ]);
        }
    }

    function duitku()
    {
        $duitku = new Duitku;
        $callback = $duitku->callback();
        if(isset($callback['success']))
        {
            $merchantRef = $callback['reference'];
            $contact = Contact::where("payment_reference",$merchantRef)->firstOrFail();
            $contact->update([
                'status' => $callback['resultCode'] == "00" ? "SUCCESS" : "FAILED"
            ]);
        }
    }
}
