<?php

namespace App\Helpers;

class Encryption
{
    public function __construct(private string $key)
    {
    }

    public function encrypt($str)
    {
        $str = $this->strRotPass($str, $this->key);

        return $this->base64Encode($str);
    }

    public function decrypt($str)
    {
        $str = $this->base64Decode($str);

        return $this->strRotPass($str, $this->key, true);
    }

    private function strRotPass($str, $key, $decrypt = false): string
    {
        $length = strlen($key);
        $result = str_repeat(' ', strlen($str));
        for ($i = 0; $i < strlen($str); $i++) {
            if ($decrypt) {
                $ascii = ord($str[$i]) - ord($key[$i % $length]);
            } else {
                $ascii = ord($str[$i]) + ord($key[$i % $length]);
            }
            $result[$i] = chr($ascii);
        }

        return $result;
    }

    private function base64Encode($input)
    {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    private function base64Decode($input)
    {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
    }
}
