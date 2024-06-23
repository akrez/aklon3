<?php

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;
use GuzzleHttp\Psr7\Uri;

require_once '../config.php';
require_once BASE_PATH.'/vendor/autoload.php';

try {
    $aklon = new Aklon();

    $XPoweredBy = explode(' ', implode('', $request->getHeader('X-Powered-By')), 3);
    if (count($XPoweredBy) !== 3) {
        throw new NotCryptedException();
    }

    $request = $request->withoutHeader('X-Powered-By')
        ->withProtocolVersion($XPoweredBy[0])
        ->withMethod($XPoweredBy[1])
        ->withUri(new Uri($XPoweredBy[2]));

    $aklon->handle($request, [
        new SetAcceptEncoding,
    ], [
        new Log,
        new Emitter,
    ]);
} catch (NotCryptedException $e) {
} catch (\Exception $e) {
}
