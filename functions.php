<?php


function failLogin()
{
    echo '<h1>LOGIN FALLITO</h1>';
}

function failUpdates()
{
    echo '<h1>USERBOT NON AVVIATO. RIAVVIO.</h1>';
    file_get_contents($_SERVER['SCRIPT_URI']);
}

function finePagina()
{
    return true;
}

function endUpdates()
{
    file_get_contents($_SERVER['SCRIPT_URI']);
    echo 'Timeout, lanciato nuovo script.';
}

function sm($chatID, $text, $parsemode = 'HTML', $reply = 0)
{
    global $MadelineProto;
    if ($reply) {
        $MadelineProto->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'parse_mode' => $parsemode, 'reply_to_msg_id' => $reply]);
    } else {
        $MadelineProto->messages->sendMessage(['peer' => $chatID, 'message' => $text, 'parse_mode' => $parsemode]);
    }
}

function setProfilo($nome, $cognome = '')
{
    global $MadelineProto;
    $MadelineProto->account->updateProfile(['first_name' => $nome, 'last_name' => $cognome]);
}

function joinChat($chatLink, $chatLOG)
{
    //ACCETTA SOLO https://t.me/joinchat/ksjdvbdskvhbsdk o @usernameChat in questo formato
    global $MadelineProto;

    try {
        if (stripos($chatLink, 'joinchat')) {
            $MadelineProto->messages->importChatInvite([
      'hash' => str_replace('https://t.me/joinchat/', '', $chatLink),
    ]);
        } else {
            $MadelineProto->channels->joinChannel([
      'channel' => '@'.str_replace('@', '', $chatLink),
    ]);
        }
        sm($chatLOG, 'Sono entrato nel canale/gruppo');
    } catch (\danog\MadelineProto\RPCErrorException $e) {
        sm($chatLOG, 'NON sono entrato nel canale/gruppo.');
    } catch (\danog\MadelineProto\Exception $e2) {
        sm($chatLOG, 'NON sono entrato nel canale/gruppo.');
    }
}

function abbandonaChat($chatID)
{
    //USARE SOLO SU SUPERGRUPPI o CRASH
    global $MadelineProto;
    $MadelineProto->channels->leaveChannel(['channel' => $chatID]);
}
