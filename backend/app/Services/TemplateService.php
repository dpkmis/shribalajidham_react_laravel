<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TemplateService
{

    public static function file_curl_content(string $url,array $payload,array $headers): array{

        if (!$url) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $data = curl_exec($ch);
        curl_close($ch);
    
        $play_list = json_decode($data, true);
    //    dd($play_list);
        return $play_list;

    }

    
}