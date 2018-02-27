<?php

file_put_contents("check.log", "ok");
require '_config.php';
register_shutdown_function('failUpdates');

$MadelineProto = new \danog\MadelineProto\API("session.madeline");
$MadelineProto->session = __DIR__.'/session.madeline';

register_shutdown_function('endUpdates');
echo "<h1>USERBOT PARTITO</h1>";

$running = true;
$offset = 0;
$lastser = time();

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

      try {
        require "_comandi.php";
      } catch(Exception $e) {
        if (isset($chatID)) {
          try {
            sm($chatID,'<code>'.$e.'</code>');
          } catch(Exception $e) { }
        }
      }

    }


    if(isset($msg)) unset($msg);
    if(isset($chatID)) unset($chatID);
    if(isset($userID)) unset($userID);
    if(isset($up)) unset($up);


  }
}
