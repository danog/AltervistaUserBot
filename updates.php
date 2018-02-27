<?php

require '_config.php';
register_shutdown_function('failUpdates');

$MadelineProto = new \danog\MadelineProto\API("session.madeline");

register_shutdown_function('endUpdates');
echo "<h1>USERBOT PARTITO</h1>";

$running = true;
$offset = 0;
$lastser = time();

try {
  while($running)
  {
    $updates = $MadelineProto->get_updates(['offset' => $offset]);
    foreach($updates as $update)
    {
      $offset = $update['update_id'] + 1;

      if (isset($update['update']['message']['out']) && $update['update']['message']['out'] && !$leggi_messaggi_in_uscita) {
        continue;
      }
      $up = $update['update']['_'];

      if($up == 'updateNewMessage' or $up == 'updateNewChannelMessage')
      {

        if (isset($update['update']['message']['message'])){
          $msg = $update["update"]["message"]["message"];
        }

        if (isset($update['update']['message']['to_id']['channel_id'])) {
          $chatID = $update['update']['message']['to_id']['channel_id'];
          $chatID = '-100'.$chatID;
          $type = "supergruppo";
        }

        if (isset($update['update']['message']['to_id']['chat_id'])) {
          $chatID = $update['update']['message']['to_id']['chat_id'];
          $chatID = '-'.$chatID;
          $type = "gruppo";
        }

        if (isset($update['update']['message']['from_id'])) $userID = $update['update']['message']['from_id'];

        if (isset($update['update']['message']['to_id']['user_id'])) {
          $chatID = $update['update']['message']['from_id'];
          $type = "privato";
        }

        @include("_comandi.php");

      }


      if(isset($msg)) unset($msg);
      if(isset($chatID)) unset($chatID);
      if(isset($userID)) unset($userID);
      if(isset($up)) unset($up);

    }
  }
} catch (\danog\MadelineProto\RPCErrorException $e) {
    \danog\MadelineProto\Logger::log([(string)$e]);
    if (in_array($e->rpc, ['SESSION_REVOKED', 'AUTH_KEY_UNREGISTERED'])) {
        foreach (glob('session.madeline*') as $path) {
            unlink($path);
        }
    }
}
