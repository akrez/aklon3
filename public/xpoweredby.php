<?php

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;
use GuzzleHttp\Psr7\Uri;

require_once '../aklon2.core/config.php';
require_once BASE_PATH.'/vendor/autoload.php';

try {
    $aklon = new Aklon();

    $request = Aklon::buildRequestFromGlobals();

    $XPoweredBy = implode('', $request->getHeader('X-Powered-By'));
    if (empty($XPoweredBy)) {
        throw new NotCryptedException();
    }

    $request = $request->withoutHeader('X-Powered-By')
        ->withUri(new Uri($XPoweredBy));

    $aklon->handle($request, [
        new SetAcceptEncoding,
    ], [
        new Log,
        new Emitter,
    ]);
} catch (NotCryptedException $e) {
} catch (\Exception $e) {
}
