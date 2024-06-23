<?php

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Helpers\Crypt3;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;
use GuzzleHttp\Psr7\Uri;

require_once '../config.php';
require_once BASE_PATH.'/vendor/autoload.php';

try {
    $aklon = new Aklon();

    $crypt3 = new Crypt3($aklon->getBaseUrl());

    $request = Aklon::buildRequestFromGlobals();

    $decryptedUrl = $crypt3->decryptUrl(strval($request->getUri()));
    if (empty($decryptedUrl)) {
        throw new NotCryptedException();
    }

    $request = $request->withUri(new Uri($decryptedUrl));

    $aklon->handle($request, [
        new SetAcceptEncoding,
    ], [
        new Log,
        new Emitter,
    ]);
} catch (NotCryptedException $e) {
} catch (\Exception $e) {
}
