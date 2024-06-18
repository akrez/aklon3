<?php

namespace App\Middlewares;

use App\Aklon;
use App\Exceptions\NotCrypted2Exception;
use App\Helpers\Crypt2;
use App\Helpers\Encryption2;
use App\Interfaces\BeforeRequestMiddleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class Crypt2BeforeRequest implements BeforeRequestMiddleware
{
    public function __construct(private string $encryptionKey)
    {
    }

    public function handle(Aklon $aklon, RequestInterface $request): RequestInterface
    {
        $crypt = new Crypt2($aklon->getBaseUrl(), new Encryption2($this->encryptionKey));

        $decryptedUrl = $crypt->decryptUrl(strval($request->getUri()));
        if (! $decryptedUrl) {
            throw new NotCrypted2Exception();
        }

        return $request->withUri(new Uri($decryptedUrl));
    }
}
