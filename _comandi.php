<?php

include '_config.php';

/*
QUESTO FILE SERVE PER AVERE SEPARATI I COMANDI DELL'USERBOT
DAI FILE BASE DI FUNZIONAMENTO DELLO STESSO
*/

if (isset($userID) && in_array($userID, $lista_admin)) {
    $isadmin = true;
} else {
    $isadmin = false;
}

if (isset($msg) && isset($chatID)) {
    if ($isadmin) {
        if (stripos($msg, '!say ') === 0) {
            sm($chatID, explode(' ', $msg, 2)[1]);
        }

        if ($msg == '!off' and (time() - $started) > 5) {
            sm($chatID, 'Mi spengo.');
            exit;
        }

        if (stripos($msg, '!join ') === 0) {
            joinChat(explode(' ', $msg, 2)[1], $chatID);
        }

        if ($msg == '!leave' && stripos($chatID, '-100') === 0) {
            abbandonaChat($chatID);
        }
        //ALTRI COMANDI RISERVATI AGLI ADMIN
    }
    if ($msg == '!on') {
        sm($chatID, "Hello world, I'm alive.");
    }

    if ($msg == '!pony') {
        sm($chatID, "This bot is powered by altervistabot & MadelineProto.\n\nCreated by a pony and a bruno.");
    }

    //COMANDI DESTINATI AL PUBBLICO
}
