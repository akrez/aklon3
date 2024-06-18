<?php

namespace App\Interfaces;

interface Crypt
{
    public function decryptUrl(string $encryptedUrl);

    public function encryptUrl(string $url, ?string $mainUrl = null);
}
