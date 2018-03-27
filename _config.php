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
        ob_end_clean();
        header('Connection: close');
        ignore_user_abort(true);
        ob_start();
        echo '<html><body><h1>OK!</h1></body</html>';
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Content-Type: text/html');
        ob_end_flush();
        flush();

        if ($try++ >= 30) {
            exit;
        }
        sleep(1);
    }
}

require __DIR__.'/madeline.php';
require __DIR__.'/functions.php';

register_shutdown_function('shutdown_function', $lock);

$leggi_messaggi_in_uscita = false;

$lista_admin = [
  40955937,  //id di Bruno :D
  101374607,  //id del creatore di MadelineProto :D
  12344567,  //un id probabilmente inesistente
];
