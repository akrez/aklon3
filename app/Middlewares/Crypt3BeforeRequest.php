<?php

namespace App\Middlewares;

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Helpers\Crypt3;
use App\Interfaces\BeforeRequestMiddleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class Crypt3BeforeRequest implements BeforeRequestMiddleware
{
    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        $crypt = new Crypt3($aklon->getBaseUrl());

        $decryptedUrl = $crypt->decryptUrl(strval($request->getUri()));
        if (! $decryptedUrl) {
            throw new NotCryptedException();
        }

        return $request->withUri(new Uri($decryptedUrl));
    }
}
