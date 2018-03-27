<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

if (!file_exists(__DIR__.'/madeline.php') || !filesize(__DIR__.'/madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', __DIR__.'/madeline.php');
}

if (!isset($remote)) {
    $remote = 'danog/AltervistaUserbot';
}
if (!isset($branch)) {
    $branch = 'master';
}
$url = "https://raw.githubusercontent.com/$remote/$branch";
$version = file_get_contents("$url/.version");

if (!file_exists(__DIR__.'/.version') || file_get_contents(__DIR__.'/.version') !== $version) {
    foreach (explode("\n", file_get_contents("$url/files")) as $file) {
        copy("$url/$file", __DIR__."/$file");
    }
}

require '_config.php';

$MadelineProto = new \danog\MadelineProto\API('session.madeline');
$MadelineProto->start();

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

$running = true;
$offset = 0;
$started = time();

try {
    while ($running) {
        $updates = $MadelineProto->get_updates(['offset' => $offset]);
        foreach ($updates as $update) {
            $offset = $update['update_id'] + 1;

            if (isset($update['update']['message']['out']) && $update['update']['message']['out'] && !$leggi_messaggi_in_uscita) {
                continue;
            }
            $up = $update['update']['_'];

            if ($up == 'updateNewMessage' or $up == 'updateNewChannelMessage') {
                if (isset($update['update']['message']['message'])) {
                    $msg = $update['update']['message']['message'];
                }

                if (isset($update['update']['message']['to_id']['channel_id'])) {
                    $chatID = $update['update']['message']['to_id']['channel_id'];
                    $chatID = '-100'.$chatID;
                    $type = 'supergruppo';
                }

                if (isset($update['update']['message']['to_id']['chat_id'])) {
                    $chatID = $update['update']['message']['to_id']['chat_id'];
                    $chatID = '-'.$chatID;
                    $type = 'gruppo';
                }

                try {
                    require '_comandi.php';
                } catch (Exception $e) {
                    if (isset($chatID)) {
                        try {
                            sm($chatID, '<code>'.$e.'</code>');
                        } catch (Exception $e) {
                        }
                    }
                }

                if (isset($update['update']['message']['to_id']['user_id'])) {
                    $chatID = $update['update']['message']['from_id'];
                    $type = 'privato';
                }

                @include '_comandi.php';
            }

            if (isset($msg)) {
                unset($msg);
            }
            if (isset($chatID)) {
                unset($chatID);
            }
            if (isset($userID)) {
                unset($userID);
            }
            if (isset($up)) {
                unset($up);
            }
        }
    }
} catch (\danog\MadelineProto\RPCErrorException $e) {
    \danog\MadelineProto\Logger::log([(string) $e]);
    if (in_array($e->rpc, ['SESSION_REVOKED', 'AUTH_KEY_UNREGISTERED'])) {
        foreach (glob('session.madeline*') as $path) {
            unlink($path);
        }
    }
}
