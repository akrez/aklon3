<?php

use App\Helpers\Crypt2;
use App\Helpers\Encryption2;
use App\Helpers\Url;

require_once '../config.php';
require_once BASE_PATH . '/vendor/autoload.php';

$baseUrl = Url::suggestBaseUrl();

if (isset($_GET['url']) and $url = $_GET['url']) {

    $crypt2 = new Crypt2($baseUrl . '/crypt2.php', new Encryption2(SECRET));

    $encryptedUrl = $crypt2->encryptUrl($_GET['url']);

    return header('Location: ' . $encryptedUrl);
}

require_once BASE_PATH . '/view/crypt2.php';
