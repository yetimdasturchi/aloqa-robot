<?php

function setUserConfig($chat_id='', $key='', $value='') {
	if (file_exists( 'data/users.json' )) {
		$users = file_get_contents( 'data/users.json' );
		$users = json_decode( $users, TRUE );
	}else{
		$users = [];
		write_file( 'data/users.json', json_encode( $users ) );
	}
	if (!isset($users[$chat_id])) {
		$users[$chat_id] = [];
	}
	$users[$chat_id][$key] = $value; 
	write_file( 'data/users.json', json_encode( $users ) );

	return TRUE;
}

function getUserConfig($chat_id='', $key='') {
	$users = [];
	if (file_exists( 'data/users.json' )) {
		$users = file_get_contents( 'data/users.json' );
		$users = json_decode( $users, TRUE );
	}

	if (isset($users[$chat_id])) {
		if (array_key_exists($key, $users[$chat_id])) {
			return $users[$chat_id][$key];
		}
	}

	return FALSE;
}

function write_file( $path, $data, $mode = 'wb') {
	if ( ! $fp = @fopen( $path, $mode ) ) return FALSE;

	flock( $fp, LOCK_EX );

	for ( $result = $written = 0, $length = strlen( $data ); $written < $length; $written += $result ) {
		if ( ( $result = fwrite( $fp, substr( $data, $written ) ) ) === FALSE ) break;
	}

	flock( $fp, LOCK_UN );
	fclose( $fp );

	return is_int( $result );
}

function getDefaultLang($chat_id=FALSE){
	global $tg;
	$chat_id = $chat_id ?: $tg->get_chatId();
	if( getUserConfig( $tg->get_chatId(), 'language' ) ){
		return getUserConfig( $tg->get_chatId(), 'language' );
	}

	return 'latin';
}

function language_parser($key='', $chat_id=FALSE) {
	$lng_file = 'lang/'. strtolower(getDefaultLang($chat_id)) . '.lng';
	if (file_exists($lng_file)) {
		$lng = parse_ini_file($lng_file);
	}else {
		$lng = [];
	}
		
	if ( array_key_exists($key, $lng) ) {
		return $lng[$key];
	}

	return ucwords( str_replace( '_',' ',$key ) );
}

function get_phrase( $keyword='', $replacement=[], $chat_id=FALSE ) {
	$result = language_parser( $keyword, $chat_id );

	if ( ! empty( $replacement ) ) foreach ($replacement as $k => $v) $result = str_replace( '{'. $k .'}', $v, $result);

	return str_replace('\n', PHP_EOL, $result);
}

function addRequest($data=[]) {
	global $config, $tg;
	
	if (file_exists( 'data/requests.json' )) {
		$requests = file_get_contents( 'data/requests.json' );
		$requests = json_decode( $requests, TRUE );
	}else{
		$requests = [];
	}
	
	$requests[] = $data; 
	write_file( 'data/requests.json', json_encode( $requests ) );

	$message = "<b>Yangi murojaat:</b>".PHP_EOL.PHP_EOL;
	$message .= "<b>Idenfikator</b>: <a href=\"tg://user?id={$data['chat_id']}\">{$data['chat_id']}</a>". PHP_EOL;

	if ( !empty( getUserConfig($data['chat_id'], 'first_name') ) ) {
		$message .= "<b>Ism</b>: ". getUserConfig($data['chat_id'], 'first_name') . PHP_EOL;   
    }
    if ( !empty( getUserConfig($data['chat_id'], 'last_name') ) ) {
		$message .= "<b>Familiya</b>: ". getUserConfig($data['chat_id'], 'last_name') .PHP_EOL;   
    }
    if ( !empty( getUserConfig($data['chat_id'], 'username') ) ) {
		$message .= "<b>Login</b>: ". getUserConfig($data['chat_id'], 'username') .PHP_EOL;   
    }

	$message .= "<b>Murojaat vaqti:</b> ".date('Y-m-d | H:i:s', $data['time']).PHP_EOL;
	$message .= "----------------\n";
	$message .= $data['text'];

	foreach ($config['owners'] as $onwer) {
		$tg->send_message($message, $onwer);
	}
	return TRUE;
}

function category_items( $cat ) {
	$contacts = file_get_contents( 'data/contacts.json' );
	$contacts = json_decode($contacts, TRUE);
	$temp = [];
	foreach ($contacts as $item) {
		if($item['status'] && $cat == $item['category']) $temp[] = $item;
	}

	usort($temp, function($a, $b) {
    	if($a['rating']==$b['rating']) return 0;
    	return $a['rating'] < $b['rating']?1:-1;
	});

	return $temp;
}

function contact($data='') {
	$message = $data['name'] . PHP_EOL;
	$message .= str_repeat('-', 20) . PHP_EOL . PHP_EOL;
	$keys = ['phone', 'email', 'website', 'address', 'services', 'description'];
	foreach ($keys as $key) {
		if ( !empty( $data[$key] ) ) {
			$message .= '<b>' . get_phrase('contact_'.$key) .'</b> '. str_replace('\n', PHP_EOL, $data[$key]) . PHP_EOL. PHP_EOL;
			//$message .= str_repeat(' ', 10) . PHP_EOL;
		}
	}	

	//$message = substr($message, 0, strrpos(trim($message), "\n"));

	return $message;
}

function get_contact($id='') {
	$contacts = file_get_contents( 'data/contacts.json' );
	$contacts = json_decode($contacts, TRUE);
	foreach ($contacts as $item) {
		if($item['status'] && $id == $item['id']) return $item;
	}

	return FALSE;
}

function getPagination( $query, $current, $maxpage, $type ) {
    $q = $query;
    $keys = [];
    if ($current>0) $keys[] = ['text' => get_phrase('prev'), 'callback_data' => http_build_query([$type => $q, 'prev' => strval(($current-1))])];
    if ($current != $maxpage-1)  $keys[] = ['text' => get_phrase('next'), 'callback_data' => http_build_query([$type => $q, 'next' => strval(($current+1))])];
    //if ($current<$maxpage) $keys[] = ['text' => strval($maxpage).'»', 'callback_data' => strval($maxpage)];
	return [$keys];
}

function like($chat_id='', $contact_id='') {
	if (file_exists( 'data/likes.json' )) {
		$likes = file_get_contents( 'data/likes.json' );
		$likes = json_decode( $likes, TRUE );
	}else{
		$likes = [];
	}

	$event_id = $chat_id . '_' . $contact_id;
	
	if ( in_array( $event_id, $likes ) ) return FALSE;
	
	$likes[] = $event_id;
	write_file( 'data/likes.json', json_encode( $likes ) );
	return update_rating($contact_id, 'like');
}

function dislike($chat_id='', $contact_id='') {
	if (file_exists( 'data/dislikes.json' )) {
		$dislikes = file_get_contents( 'data/dislikes.json' );
		$dislikes = json_decode( $dislikes, TRUE );
	}else{
		$dislikes = [];
	}

	$event_id = $chat_id . '_' . $contact_id;
	
	if ( in_array( $event_id, $dislikes ) ) return FALSE;
	
	$dislikes[] = $event_id;
	write_file( 'data/dislikes.json', json_encode( $dislikes ) );
	return update_rating($contact_id, 'dislike');
}

function update_rating($id='', $event=NULL) {
	$contacts = file_get_contents( 'data/contacts.json' );
	$contacts = json_decode($contacts, TRUE);
	$rating = 0;
	foreach ($contacts as $key => $item) {
		if($item['status'] && $id == $item['id']){
			if ($event == 'like') {
				$contacts[$key]['rating'] = $item['rating'] + 1;
			}
			if ($event == 'dislike') {
				$contacts[$key]['rating'] = $item['rating'] - 1;
			}
			$rating = $contacts[$key]['rating'];
		}
	}
	write_file( 'data/contacts.json', json_encode( $contacts ) );
	return $rating;
}

function search( $query='' ){
	$contacts = file_get_contents( 'data/contacts.json' );
	$contacts = json_decode($contacts, TRUE);
    $results = [];

    foreach( $contacts as $item ){

        if( is_array( $item ) ){
			if( array_filter($item, function($var) use ($query) { return ( !is_array( $var ) )? stristr( $var, $query ): false; } ) ){
                $results[] = $item;
                continue;
            }
        }
    }

    usort($results, function($a, $b) {
    	if($a['rating']==$b['rating']) return 0;
    	return $a['rating'] < $b['rating']?1:-1;
	});

    return $results;
}

function inline_search($query='') {
	global $tg;
	$contacts = search($query);
	if (empty($contacts)) return FALSE;
	$res = [];
	foreach ($contacts as $item) {
		
		$message = $item['name'] . PHP_EOL;
		$message .= str_repeat('-', 20) . PHP_EOL;
		$keys = ['phone', 'email', 'website', 'address', 'services', 'description'];
		foreach ($keys as $key) {
			if ( !empty( $item[$key] ) ) {
				$message .= get_phrase('contact_'.$key) .' '. str_replace('\n', PHP_EOL, $item[$key]) . PHP_EOL;
				$message .= str_repeat(' ', 20) . PHP_EOL;
			}
		}	

		$message .= "https://t.me/kosonsoyaloqa_bot";

		$tmp = [
			'type' => 'article',
			'id' => $item['id'],
			'title' => $item['name'],
			'url' => 'https://t.me/kosonsoyaloqa_bot',
			'thumb_url' => 'https://ui-avatars.com/api/?name='.urlencode($item['name']),
			'parse_mode' => 'html',
			'message_text' => $message,
           	'description'  => $item['phone'],
		];

		$res[] = $tmp;
	}
	
	return $res;
}

function generate_uuid() {
    return sprintf( '%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function addContact($data=[]) {
	global $config, $tg;
	
	if (file_exists( 'data/contacts.json' )) {
		$contacts = file_get_contents( 'data/contacts.json' );
		$contacts = json_decode( $contacts, TRUE );
	}else{
		$contacts = [];
	}
	
	$contacts[] = $data; 
	write_file( 'data/contacts.json', json_encode( $contacts ) );

	foreach ($config['owners'] as $owners) {
		$tg->send_message("<b>🔔 Yangi aloqa kontakti kiritildi</b>", $owners);
	}
	return TRUE;
}

function get_users() {
	global $config;
	if (file_exists( 'data/users.json' )) {
		$users = file_get_contents( 'data/users.json' );
        $users = json_decode( $users, TRUE );
	}else{
    	$users = array();
	}
    if(count($users) > 0){
    	$temp_users = [];
        foreach ($users as $id => $user) {
        	if ( in_array( $id, $config['owners'] ) ) continue;
			$user['id'] = $id;
			$temp_users[] = $user;
		}
		usort($temp_users, function( $a, $b ) {
			return $b['lastaction'] <=> $a['lastaction'];
		});
		return $temp_users;	    
	}

	return FALSE;
}

function user($user, $users_count) {
	$message = "";
	if ( !empty( $user['id'] ) ) {
    	$message .= get_phrase('id') . " <a href=\"tg://user?id={$user['id']}\">{$user['id']}</a>".PHP_EOL;
    	$message .= str_repeat("-", 40).PHP_EOL;
    }
	if ( !empty( $user['first_name'] ) ) {
    	$message .= get_phrase('first_name') . " {$user['first_name']}".PHP_EOL;   
	}
    if ( !empty( $user['last_name'] ) ) {
    	$message .= get_phrase('last_name') . " {$user['last_name']}".PHP_EOL;
    }
    if ( !empty( $user['username'] ) ) {
    	$message .= get_phrase('username') . " @{$user['username']}".PHP_EOL;
    }
    if ( !empty( $user['lastmessage'] ) || !empty( $user['lastaction'] ) ) {
    	$message .= PHP_EOL.str_repeat("-", 40).PHP_EOL;
    }
    if ( !empty( $user['lastmessage'] ) ) {
    	$message .= get_phrase('lastmessage') . " {$user['lastmessage']}".PHP_EOL;
	}
    if ( !empty( $user['lastaction'] ) ) {
    	$lastaction = date("Y-m-d | H:i:s", $user['lastaction']);
        $message .= get_phrase('lastaction') . " {$lastaction}".PHP_EOL;
	}

	$message .= str_repeat("-", 40);
	$message .= PHP_EOL.get_phrase('all_users') . " {$users_count}";
	return $message;
}

function add_notifications($message) {
	$users = get_users();
	$users_count = count( $users );
	if ($users_count == 0) return FALSE;

	foreach ($users as $user) {
		$message['chat_id'] = $user['id'];
    	$id = 'notifications/' . md5( generate_uuid() . time() ).'.json';
		file_put_contents($id, json_encode($message));
	}

	return TRUE;
}

function get_applications() {
	global $config;
	if (file_exists( 'data/requests.json' )) {
		$requests = file_get_contents( 'data/requests.json' );
        $requests = json_decode( $requests, TRUE );
	}else{
    	$requests = [];
	}
    if(count($requests) > 0){
    	usort($requests, function( $a, $b ) {
			return $b['time'] <=> $a['time'];
		});
		
		return $requests;	    
	}

	return FALSE;
}

function delete_applications($time) {
	global $config;
	if (file_exists( 'data/requests.json' )) {
		$requests = file_get_contents( 'data/requests.json' );
        $requests = json_decode( $requests, TRUE );
	}else{
    	$requests = [];
	}
    if(count($requests) > 0){
    	$temp = [];
    	foreach ($requests as $req) {
    	 	if (intval($req['time']) != intval($time)) {
    	 		$temp[] = $req;
    	 	}
    	}

    	write_file( 'data/requests.json', json_encode( $temp ) );
	}

	return FALSE;
}


function application($application, $applications_count) {
	$users = get_users();
	$user = [];
	foreach ($users as $u) {
		if ($u['id'] == $application['chat_id']) {
			$user = $u;
			break;	
		}
	}

	$message = "";
	if ( !empty( $user['id'] ) ) {
    	$message .= get_phrase('id') . " <a href=\"tg://user?id={$user['id']}\">{$user['id']}</a>".PHP_EOL;
    	$message .= str_repeat("-", 40).PHP_EOL;
    }
	if ( !empty( $user['first_name'] ) ) {
    	$message .= get_phrase('first_name') . " {$user['first_name']}".PHP_EOL;   
	}
    if ( !empty( $user['last_name'] ) ) {
    	$message .= get_phrase('last_name') . " {$user['last_name']}".PHP_EOL;
    }
    if ( !empty( $user['username'] ) ) {
    	$message .= get_phrase('username') . " @{$user['username']}".PHP_EOL;
    }

    $message .= str_repeat("-", 40).PHP_EOL;
    $message .= get_phrase('lastaction') . " ".date("Y-m-d | H:i:s", $application['time']).PHP_EOL;
    $message .= str_repeat("-", 40).PHP_EOL;
    $message .= $application['text'].PHP_EOL;

	$message .= str_repeat("-", 40);
	$message .= PHP_EOL.get_phrase('all_applications') . " {$applications_count}";
	return $message;
}