<?php

if (!file_exists('bot.lock')) {
    touch('bot.lock');
}
$lock = fopen('bot.lock', 'r+');

$try = 1;
$locked = false;
while (!$locked) {
    $locked = flock($lock, LOCK_EX | LOCK_NB);
    if (!$locked) {
        closeConnection();

        if ($try++ >= 30) {
            exit;
        }
        sleep(1);
    }
}

require __DIR__.'/madeline.php';
require __DIR__.'/functions.php';


$leggi_messaggi_in_uscita = false;

$lista_admin = [
  40955937,  //id di Bruno :D
  101374607,  //id del creatore di MadelineProto :D
  12344567,  //un id probabilmente inesistente
];
