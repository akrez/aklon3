<?php

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Middlewares\CookieAfterRequest;
use App\Middlewares\CookieBeforeRequest;
use App\Middlewares\Crypt2AfterRequest;
use App\Middlewares\Crypt2BeforeRequest;
use App\Middlewares\Crypt3AfterRequest;
use App\Middlewares\Crypt3BeforeRequest;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;

require_once '../vendor/autoload.php';

$aklon = new Aklon();

$request = Aklon::buildRequestFromGlobals();

try {
    $aklon->handle($request, [
        new Crypt2BeforeRequest(123),
        new SetAcceptEncoding,
        new CookieBeforeRequest,
    ], [
        new CookieAfterRequest,
        new Crypt2AfterRequest(123),
        new Log,
        new Emitter,
    ]);
} catch (NotCryptedException $e) {
    $baseUrl = $aklon->getBaseUrl();
    require_once '../view/form-crypt2.php';
} catch (\Exception $e) {
}
