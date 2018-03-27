<?php

//li ottieni da my.telegram.org
$api_id = 12345;
$api_hash = 'ksdjfsdkjvksjvbdksvjdsv';

$numero_di_telefono = '123456789'; //devi inserire anche il prefisso nazionale senza +

$leggi_messaggi_in_uscita = false;

$lista_admin = [
  40955937,  //id di Bruno :D
  12344567,  //un id probabilmente inesistente
];

//header("Content-Type: text/plain");
ini_set('display_errors', true);
error_reporting(E_ALL);
if (!file_exists('bot.lock')) {
    touch('bot.lock');
}
$lock = fopen('bot.lock', 'r+');
flock($lock, LOCK_EX);

require __DIR__.'/phar.php';
require __DIR__.'/HttpProxy.php';
require __DIR__.'/functions.php';
