<?php

use App\Aklon;
use App\Crypt;
use App\Middlewares\CookieAfterRequest;
use App\Middlewares\CookieBeforeRequest;
use App\Middlewares\Emitter;
use App\Middlewares\ProxyTextCss;
use App\Middlewares\ProxyTextHtml;
use App\Middlewares\SetAcceptEncoding;

require_once 'config.php';

require_once VENDOR_PATH;

$crypt = new Crypt(CRYPT_PASS, BASE_URL);
$aklon = new Aklon($crypt);

$request = $aklon->buildRequestFromGlobals();

if (! $aklon->isEncrypted($request)) {
    return require './form.php';
}

$aklon->handle($request, [
    new SetAcceptEncoding,
    new CookieBeforeRequest,
], [
    new CookieAfterRequest,
    new ProxyTextCss,
    new ProxyTextHtml,
    new Emitter,
]);
