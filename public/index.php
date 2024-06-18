<?php

use App\Aklon;
use App\Exceptions\NotCrypted3Exception;
use App\Middlewares\CookieAfterRequest;
use App\Middlewares\CookieBeforeRequest;
use App\Middlewares\Crypt3AfterRequest;
use App\Middlewares\Crypt3BeforeRequest;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;

require_once '../vendor/autoload.php';

$baseUrl = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);

$aklon = new Aklon($baseUrl);

$request = $aklon->buildRequestFromGlobals();

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
} catch (NotCrypted3Exception $e) {
    require_once '../view/form3.php';
} catch (\Exception $e) {
}
