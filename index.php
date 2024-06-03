<?php

use Src\Aklon;
use Src\Helpers\Crypt;
use Src\Helpers\Url;
use Src\Middlewares\CookieAfterRequest;
use Src\Middlewares\CookieBeforeRequest;
use Src\Middlewares\Emitter;
use Src\Middlewares\ProxyTextCss;
use Src\Middlewares\ProxyTextHtml;
use Src\Middlewares\SetAcceptEncoding;

require_once 'config.php';

require_once VENDOR_PATH;

$crypt = new Crypt(CRYPT_PASS);
$url = new Url($crypt);
$aklon = new Aklon($url, BASE_URL);

if (isset($_GET['url']) and $url = $_GET['url']) {
    $encryptedUrl = $aklon->encryptToBaseUrl($url);

    return header('Location: '.$encryptedUrl);
}

$url = $aklon->decryptFromBaseUrl('?'.http_build_query($_GET));
if (! $url) {
    return require './form.php';
}

$request = $aklon->fromGlobals($url);

$aklon->handle($request, [
    new SetAcceptEncoding,
    new CookieBeforeRequest,
], [
    new CookieAfterRequest,
    new ProxyTextCss,
    new ProxyTextHtml,
    new Emitter,
]);
