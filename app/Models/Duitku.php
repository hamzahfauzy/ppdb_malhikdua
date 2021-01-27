<?php

namespace App\Models;


class Duitku
{
    function pay($amount, $method, $personal)
    {
        $name = $personal['name'];
        $email = $personal['email'];
        $phoneNumber = $personal['phone'];
        $merchantCode = getenv('DUITKU_MERCHANT_CODE'); // from duitku
        $merchantKey = getenv('DUITKU_MERCHANT_KEY'); // from duitku
        $paymentAmount = $amount;
        $paymentMethod = $method; // WW = duitku wallet, VC = Credit Card, MY = Mandiri Clickpay, BK = BCA KlikPay
        $merchantOrderId = time(); // from merchant, unique
        $productDetails = 'PPDB Malhikdua';
        $customerVaName = $name; // display name on bank confirmation display
        $callbackUrl = route('login'); // getenv('DUITKU_CALLBACK'); // url for callback
        $returnUrl = route('login'); // url for redirect

        $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);

        $params = array(
            'merchantCode' => $merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $productDetails,
            'customerVaName' => $customerVaName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'signature' => $signature,
        );

        // return $params;

        $params_string = json_encode($params);
        // $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
        $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($params_string))                                                                       
        );   
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        //execute post
        $request = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($httpCode == 200)
        {
            $result = json_decode($request, true);
            return $result;
            // header('location: '. $result['paymentUrl']);
            // echo "paymentUrl :". $result['paymentUrl'] . "<br />";
            // echo "merchantCode :". $result['merchantCode'] . "<br />";
            // echo "reference :". $result['reference'] . "<br />";
            // echo "vaNumber :". $result['vaNumber'] . "<br />";
            // echo "amount :". $result['amount'] . "<br />";
            // echo "statusCode :". $result['statusCode'] . "<br />";
            // echo "statusMessage :". $result['statusMessage'] . "<br />";
        }
        else
            return $httpCode;
    }
}
