<?php

namespace App\Controllers;

trait TransmissionRequestTrait
{
    public function transmissionRequest(string $method, string $jsonEncodedArguments)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('TRANSMISSION_ENDPOINT'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>'{"method": "'.$method.'", "arguments": '.$jsonEncodedArguments.'}',
            CURLOPT_HTTPHEADER => [
                "X-Transmission-Session-Id: ".TRANSMISSIONID,
                "Content-Type: application/json"
            ],
        ));
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        return $response;
    }
}