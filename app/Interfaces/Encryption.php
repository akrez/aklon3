<?php

namespace App\Interfaces;

interface Encryption
{
    public function encrypt($str);

    public function decrypt($str);
}
