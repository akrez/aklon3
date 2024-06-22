<?php

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Middlewares\CookieAfterRequest;
use App\Middlewares\CookieBeforeRequest;
use App\Middlewares\Crypt2AfterRequest;
use App\Middlewares\Crypt2BeforeRequest;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;

require_once '../config.php';
require_once BASE_PATH . '/vendor/autoload.php';

$aklon = new Aklon();

$request = Aklon::buildRequestFromGlobals();

try {
    $aklon->handle($request, [
        new Crypt2BeforeRequest(SECRET),
        new SetAcceptEncoding,
        new CookieBeforeRequest,
    ], [
        new CookieAfterRequest,
        new Crypt2AfterRequest(SECRET),
        new Log,
        new Emitter,
    ]);
} catch (NotCryptedException $e) {
    return header('Location: ./index.php');
} catch (\Exception $e) {
}
