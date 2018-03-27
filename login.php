<?php

if (file_exists('session.madeline')) {
    echo '<h1>Gi&agrave; loggato. Per nuovo login, elimina il file session.madeline e aggiorna questa pagina.</h1>';
    exit;
}

register_shutdown_function('failLogin');

require '_config.php';

if (isset($_POST['code'])) {
    $MadelineProto = new \danog\MadelineProto\API('session.madeline');
    $MadelineProto->complete_phone_login($_POST['code']);
    if (isset($_POST['pwd2fa']) && $_POST['pwd2fa']) {
        sleep(1);
        $MadelineProto->complete_2fa_login($_POST['pwd2fa']);
    }
    echo "<center><h1><br /><br />LOGIN EFFETTUATO</h1><br /><br /><h2><a href='updates.php'>AVVIA USERBOT</a></h2></center>";
    $MadelineProto->serialize();
    register_shutdown_function('finePagina');
    exit;
} else {
    $MadelineProto = new \danog\MadelineProto\API([
        'app_info' => [
            'api_id'   => $api_id,
            'api_hash' => $api_hash,
        ],
        'connection_settings' => [
            'all' => [
                'protocol'    => 'http',
                'pfs'         => false,
                'proxy'       => '\\HttpProxy',
                'proxy_extra' => [
                    'address' => 'localhost',
                    'port'    => 80,
                ],
            ],
        ],
        'logger' => [
            'logger'       => 2,
            'logger_param' => __DIR__.'/Madeline.log',
            'logger_level' => 5,
        ],
    ]);
    $MadelineProto->session = __DIR__.'/session.madeline';
    $MadelineProto->phone_login($numero_di_telefono);
    $MadelineProto->serialize();
    register_shutdown_function('finePagina'); ?>
	<center>
		<h1>Ok stai facendo il login dell'account con numero +<?=$numero_di_telefono; ?></h1>
		<form action="#" method="post">
			<b>CODICE SMS/TELEGRAM RICEVUTO</b><br />
			<input type="number" name="code" />
			<br /><br /><b>EVENTUALE PASSWORD 2FA (lasciare vuoto se non impostata)</b><br />
			<input type="password" name="pwd2fa" />
			<br />
			<input type="submit" name="submit" value="LOGIN!" />
		</form>
	</center>
	<?php
    exit;
}
