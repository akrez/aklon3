<?php

use App\Aklon;
use App\Exceptions\NotCrypted2Exception;
use App\Exceptions\NotCrypted3Exception;
use App\Middlewares\CookieAfterRequest;
use App\Middlewares\CookieBeforeRequest;
use App\Middlewares\Crypt3AfterRequest;
use App\Middlewares\Crypt3BeforeRequest;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;

require_once '../vendor/autoload.php';

$baseUrl = Aklon::suggestBaseUrl();

$aklon = new Aklon($baseUrl);

$request = Aklon::buildRequestFromGlobals();

try {
    $aklon->handle($request, [
        new Crypt3BeforeRequest,
        new SetAcceptEncoding,
        new CookieBeforeRequest,
    ], [
        new CookieAfterRequest,
        new Crypt3AfterRequest,
        new Log,
        new Emitter,
    ]);
} catch (NotCrypted2Exception $e) {
    require_once '../view/form2.php';
} catch (NotCrypted3Exception $e) {
    require_once '../view/form3.php';
} catch (\Exception $e) {
}
