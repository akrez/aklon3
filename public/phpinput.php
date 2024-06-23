<?php

use App\Aklon;
use App\Exceptions\NotCryptedException;
use App\Middlewares\Emitter;
use App\Middlewares\Log;
use App\Middlewares\SetAcceptEncoding;
use GuzzleHttp\Psr7\Message;

require_once '../config.php';
require_once BASE_PATH.'/vendor/autoload.php';

try {
    $aklon = new Aklon();

    $request = Message::parseRequest(file_get_contents('php://input'));

    $aklon->handle($request, [
        new SetAcceptEncoding,
    ], [
        new Log,
        new Emitter,
    ]);
} catch (NotCryptedException $e) {
} catch (\Exception $e) {
}
