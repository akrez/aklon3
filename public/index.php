<?php

use App\Aklon;
use App\Crypt;
use App\Helpers\Url;
use App\Middlewares\CookieAfterRequest;
use App\Middlewares\CookieBeforeRequest;
use App\Middlewares\Emitter;
use App\Middlewares\ProxyTextCss;
use App\Middlewares\ProxyTextHtml;
use App\Middlewares\SetAcceptEncoding;

require_once '../vendor/autoload.php';

$baseUrl = (new Url)->unparse([
    'scheme' => $_SERVER['REQUEST_SCHEME'],
    'host' => $_SERVER['HTTP_HOST'],
    'path' => pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME),
]);

$crypt = new Crypt($baseUrl);
$aklon = new Aklon($crypt);

dd($baseUrl);

$request = $aklon->buildRequestFromGlobals();

if ($aklon->isEncrypted($request)) {
    $aklon->handle($request, [
        new SetAcceptEncoding,
        new CookieBeforeRequest,
    ], [
        new CookieAfterRequest,
        new ProxyTextCss,
        new ProxyTextHtml,
        new Emitter,
    ]);
} else { ?>
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Akrez!</title>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo $baseUrl ?>/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="container text-center">
        <div class="row my-3">
            <div class="col-12">
                <form enctype="multipart/form-data" method="GET">
                    <div class="input-group">
                        <input name="url" class="form-control">
                        <button class="btn btn-success btn-lg" type="submit">Surf</button>
                    </div>
                </form>
            </div>
        </div>
    </body>

    </html>
<?php } ?>