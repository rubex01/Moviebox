<?php

namespace App\Controllers;

class TransmissionSessionController
{
    public function getSessionId()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv('TRANSMISSION_ENDPOINT'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        $headers = [];
        $output = rtrim($output);
        $data = explode("\n",$output);
        $headers['status'] = $data[0];
        array_shift($data);

        foreach($data as $part){
            $middle = explode(":",$part,2);
            if ( !isset($middle[1]) ) { $middle[1] = null; }
            $headers[trim($middle[0])] = trim($middle[1]);
        }

        return $headers['X-Transmission-Session-Id'];
    }
}