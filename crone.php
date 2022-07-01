<?php

$config = include 'config.php';
include 'Telegram.php';

$tg_settings = [
	'token' => $config['token']
];

$tg = new Telegram($tg_settings);

function scan_index($rootDir, $allData=[]) {
	$invisibleFileNames = [".", "..", ".htaccess", ".htpasswd", "errors", "index.html"];
	$dirContent = array_diff(scandir($rootDir), ['..', '.']);
	foreach($dirContent as $key => $content) {
		$path = $rootDir.$content;
		if ( preg_match('/(.*).json/', $content)) {
	    	$allData[$path] = filemtime($path);
		}
	}
	    
	asort($allData);
	return $allData;
}

function send_notifications() {
	global $tg;
	$message = scan_index(dirname(__FILE__) . '/notifications/');

	if ( !empty( $message ) ) {
		$x = 0;
		foreach ($message as $k => $v) {
			if($x == 10){
	            sleep(2);
	            $x = 0;
			}
			$item = file_get_contents($k);
			$item = json_decode($item, TRUE);
			$chat_id = $item['chat_id'];
			if ( !empty( $item['text'] ) ) {
				$tg->send_chatAction('typing', $chat_id)->send_message( $item['text'], $chat_id );
			}

			if ( !empty( $item['photo'] ) ) {
				$caption = (!empty( $item['caption'])) ? $item['caption'] : '';
				$tg->send_chatAction('upload_photo', $chat_id)->send_photo($item['photo'], $caption, $chat_id);
			}

			if ( !empty( $item['video'] ) ) {
				$caption = (!empty( $item['caption'])) ? $item['caption'] : '';
				$tg->send_chatAction('upload_video', $chat_id)->send_video($item['video'], $caption, $chat_id);
			}

			@unlink($k);
			$x++;
		}
	}
}

while (1){
	send_notifications();
	usleep(2000);
}