<?php

$config = include 'config.php';
include 'Telegram.php';
include 'Tlk.php';
include 'functions.php';

$tg_settings = [
	'token' => $config['token']
];

$tg = new Telegram($tg_settings);
$updates = $tg->get_webhookUpdates();

if (! empty( $updates ) ) {
	//$tg->set_chatId('441307831')->send_chatAction('typing')->send_message( print_r($updates, TRUE) );
	if (!empty($updates['message']['chat']['id'])) {
		$tg->set_chatId( $updates['message']['chat']['id'] );
	}
	
	if( ! empty( $updates['message']['text'] ) ){
		$text = $updates['message']['text'];

		if (!empty( $updates['message']['chat']['first_name'] )){
			setUserConfig( $tg->get_chatId(), 'first_name', $updates['message']['chat']['first_name'] );
		}else{
			setUserConfig( $tg->get_chatId(), 'first_name', '');
		}
		if (!empty( $updates['message']['chat']['last_name'] )){
			setUserConfig( $tg->get_chatId(), 'last_name', $updates['message']['chat']['last_name'] );
		}else{
			setUserConfig( $tg->get_chatId(), 'last_name', '');
		}
		if (!empty( $updates['message']['chat']['username'] )){
			setUserConfig( $tg->get_chatId(), 'username', $updates['message']['chat']['username'] );
		}else{
			setUserConfig( $tg->get_chatId(), 'username', '');
		}

        setUserConfig( $tg->get_chatId(), 'lastaction', time() );

		if ($text == '/start' || $text == '/asosiy') {
			menu('/start');
		}else if ($text == '🔸 Lotin' || $text == '/lotin') {
			if(! getUserConfig( $tg->get_chatId(), 'welcome' ) ){
				setUserConfig( $tg->get_chatId(), 'welcome', true );
			}
			setUserConfig( $tg->get_chatId(), 'language', 'latin' );
			$tg->send_chatAction('typing')->send_message(get_phrase('language_settings_accepted'));
			menu('/start');
		}else if (in_array( $tg->get_chatId(), $config['owners'] ) && ( $text == '✍️ Bildirishnoma' || $text == '✍️ Билдиришнома' ) ) {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/send_notification');
			menu('/send_notification');
		}else if (in_array( $tg->get_chatId(), $config['owners'] ) && ( $text == '👨‍👩‍👧 Foydalanuvchilar' || $text == '👨‍👩‍👧 Фойдаланувчилар' ) ) {
			menu('/users');
		}else if (in_array( $tg->get_chatId(), $config['owners'] ) && ( $text == '📝 Murojaatlar' || $text == '📝 Мурожаатлар' ) ) {
			menu('/applications');
		}else if ($text == '🔸 Кирил' || $text == '/kiril') {
			if(! getUserConfig( $tg->get_chatId(), 'welcome' ) ){
				setUserConfig( $tg->get_chatId(), 'welcome', true );
			}
			setUserConfig( $tg->get_chatId(), 'language', 'cyrillic' );
			$tg->send_chatAction('typing')->send_message(get_phrase('language_settings_accepted'));
			menu('/start');
		}else if ($text == '📕 Qo‘llanma' || $text == '📕 Қўлланма' || $text == '/qollanma') {
			//setUserConfig($tg->get_chatId(), 'lastmessage', '/contact');
			menu('/instruction');
		}else if ($text == '🤖 Bot haqida' || $text == '🤖 Бот ҳақида' || $text == '/bothaqida') {
			menu('/about');
		}else if ($text == '☎️ Aloqa' || $text == '☎️ Алоқа' || $text == '/aloqa') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/contact');
			menu('/contact');
		}else if ($text == '⚙️ Sozlamalar' || $text == '⚙️ Созламалар' || $text == '/sozlamalar') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/settings');
			menu('/settings');
		}else if ($text == '🔔 Bildirishnomalar' || $text == '🔔 Билдиришномалар' || $text == '/bildirishnomalar') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/notifications');
			menu('/notifications');
		}else if ($text == '🌍 Alifbo' || $text == '🌍 Алифбо' || $text == '/alifbo') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/alphabet');
			menu('/alphabet');
		}else if ($text == '🔙 Orqaga' || $text == '🔙 Орқага' || $text == '/orqaga') {
			menu('/back');
		}else if ($text == '❌ Bekor qilish' || $text == '❌ Бекор қилиш' || $text == '/bekorqilish') {
			menu('/cancel');
		}else if ($text == '➕ Kontakt qo‘shish' || $text == '➕ Контакт қўшиш' || $text == '/qoshish') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/addcontact');
			setUserConfig($tg->get_chatId(), 'addcontact_step', '0');
			setUserConfig($tg->get_chatId(), 'addcontact_data_name', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_category', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_phone', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_address', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_working_hours', '');

			menu('/addcontact');
		}else if ($text == '🔍 Izlash' || $text == '🔍 Излаш' || $text == '/izlash') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/search');
			menu('/search');
		}else if ($text == '📒 Bo‘limlar' || $text == '📒 Бўлимлар' || $text == '/bolimlar') {
			setUserConfig($tg->get_chatId(), 'lastmessage', '/categories');
			menu('/categories');
		}else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/addcontact'){
			if ( getUserConfig( $tg->get_chatId(), 'addcontact_step') == '1' ) {
				if (strlen($text) > 4) {
					setUserConfig($tg->get_chatId(), 'addcontact_step', '2');
					setUserConfig($tg->get_chatId(), 'addcontact_data_name', $text);
					menu('/addcontact_2');
				}else{
					$tg->send_chatAction('typing')->send_message( get_phrase('addcontact_short_name', ['count' => '5']) );
				}
			}else if ( getUserConfig( $tg->get_chatId(), 'addcontact_step') == '2' ) {
				if ( in_array( $gc8d9->I479j($text), $config['categories'] ) ) {
            		setUserConfig($tg->get_chatId(), 'addcontact_step', '3');
					setUserConfig($tg->get_chatId(), 'addcontact_data_category', $text);
					menu('/addcontact_3');
            	}else{
            		$tg->send_chatAction('typing')->send_message( get_phrase('category_not_found') );
            	}
			}else if ( getUserConfig( $tg->get_chatId(), 'addcontact_step') == '3' ) {
				setUserConfig($tg->get_chatId(), 'addcontact_step', '4');
				setUserConfig($tg->get_chatId(), 'addcontact_data_phone', $text);
				menu('/addcontact_4');
			}else if ( getUserConfig( $tg->get_chatId(), 'addcontact_step') == '4' ) {
				if ($text == '👉 O‘tkazib yuborish' || $text == '👉 Ўтказиб юбориш' || $text == '/otkazibyuborish') {
					setUserConfig($tg->get_chatId(), 'addcontact_step', '5');
					menu('/addcontact_5');
				}else{
					setUserConfig($tg->get_chatId(), 'addcontact_step', '5');
					setUserConfig($tg->get_chatId(), 'addcontact_data_address', $text);
					menu('/addcontact_5');
				}
			}else if ( getUserConfig( $tg->get_chatId(), 'addcontact_step') == '5' ) {
				if ($text == '👉 O‘tkazib yuborish' || $text == '👉 Ўтказиб юбориш' || $text == '/otkazibyuborish') {
					setUserConfig($tg->get_chatId(), 'addcontact_step', '6');
					menu('/addcontact_6');
				}else{
					setUserConfig($tg->get_chatId(), 'addcontact_step', '6');
					setUserConfig($tg->get_chatId(), 'addcontact_data_working_hours', $text);
					menu('/addcontact_6');
				}
			}else if ( getUserConfig( $tg->get_chatId(), 'addcontact_step') == '6' ) {
				if ($text == '👉 O‘tkazib yuborish' || $text == '👉 Ўтказиб юбориш' || $text == '/otkazibyuborish') {
					$text = '';
				}
				$st = ( in_array( $tg->get_chatId(), $config['owners'] ) ) ? TRUE : FALSE;
				$nm = getUserConfig( $tg->get_chatId(), 'addcontact_data_name');
				$cat = getUserConfig( $tg->get_chatId(), 'addcontact_data_category');
				$ph = getUserConfig( $tg->get_chatId(), 'addcontact_data_phone');
				$addr = getUserConfig( $tg->get_chatId(), 'addcontact_data_address');
				$wh = getUserConfig( $tg->get_chatId(), 'addcontact_data_working_hours');

				addContact([
					'id' => generate_uuid(),
					'chat_id' => $tg->get_chatId(),
					'name' => $nm ?: '',
					'category' => $gc8d9->I479j($cat) ?: '',
					'phone' => $ph ?: '',
					'address' => $addr ?: '',
					'email' => '',
					'website' => '',
					'description' => $text,
					'services' => '',
					'working_hours' => $wh ?: '',
					'rating' => 0,
					'status' => $st,
				]);

				$tg->send_chatAction('typing')->send_message( get_phrase('addcontact_success') );
				menu('/start');
			}else{
				if ($text == '👉 Davom etish' || $text == '👉 Давом этиш' || $text == '/davometish') {
					setUserConfig($tg->get_chatId(), 'addcontact_step', '1');
					menu('/addcontact_1');
				}else{
					$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
				}
			}
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/search'){
			$contacts = search( $gc8d9->I479j( $text ) );
			$contacts_count = count($contacts);
			if( !empty( $contacts ) ){
				$contact = contact( $contacts[0]);
				$pagination = getPagination($gc8d9->I479j( $text ), 0, $contacts_count, 'search');
				array_unshift($pagination , [
					[
						'text' => '👍',
						'callback_data' => 'contact_like='.$contacts[0]['id']
					],
					[
						'text' => '📊 ('.$contacts[0]['rating'].')',
						'callback_data' => 'contact_status='.$contacts[0]['id']
					],
					[
						'text' => '👎',
						'callback_data' => 'contact_dislike='.$contacts[0]['id']
					]
				]);
				$tg->send_chatAction('typing')->set_inlineKeyboard($pagination)->send_message( $contact );
			}else{
				$tg->send_chatAction('typing')->send_message( get_phrase('contacts_not_found') );
			}
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/categories'){
            if ( in_array( $gc8d9->I479j($text), $config['categories'] ) ) {
            	$contacts = category_items( $gc8d9->I479j( $text ) );
				$contacts_count = count($contacts);
				if ( $contacts_count == 0 ) {
					$tg->send_chatAction('typing')->send_message( get_phrase('contacts_not_found') );
					exit(1);
				}
				$contact = contact( $contacts[0]);
				$pagination = getPagination($gc8d9->I479j( $text ), 0, $contacts_count, 'contacts');
				array_unshift($pagination , [
					[
						'text' => '👍',
						'callback_data' => 'contact_like='.$contacts[0]['id']
					],
					[
						'text' => '📊 ('.$contacts[0]['rating'].')',
						'callback_data' => 'contact_status='.$contacts[0]['id']
					],
					[
						'text' => '👎',
						'callback_data' => 'contact_dislike='.$contacts[0]['id']
					]
				]);
				$tg->send_chatAction('typing')->set_inlineKeyboard($pagination)->send_message( $contact );
            }else{
            	$tg->send_chatAction('typing')->send_message( get_phrase('category_not_found') );
            }
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/contact'){
            if(in_array($tg->get_chatId(), $config['owners'])){
            	$tg->send_chatAction('typing')->send_message( get_phrase('contact_onwer') );
            	menu('/back');
            	exit(1);
            }
            addRequest([
                'chat_id' => $tg->get_chatId(),
                'time' => time(),
                'text' => $text
            ]);
            $tg->send_chatAction('typing')->send_message( get_phrase('contact_success') );
            menu('/back');
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/notifications'){
            if ($text == '✅ Yoqish' || $text == '✅ Ёқиш') {
            	setUserConfig($tg->get_chatId(), 'notifications', 'on');
            	$tg->send_chatAction('typing')->send_message( get_phrase('settings_notification_on') );
            	menu('/back');
            }else if ($text == '❌ O‘chirish' || $text == '❌ Ўчириш') {
            	setUserConfig($tg->get_chatId(), 'notifications', 'off');
            	$tg->send_chatAction('typing')->send_message( get_phrase('settings_notification_off') );
            	menu('/back');
            }else{
            	$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
            }
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/send_notification'){
            if ( strlen( $text ) > 50) {
            	add_notifications([
            		'text' => $text
            	]);
            	$tg->send_chatAction('typing')->send_message( get_phrase('notification_sended') );
            	menu('/back');
            }else{
	            $tg->send_chatAction('typing')->send_message( get_phrase('send_notification_short_message', ['count' => '50']) );	
            }
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/applications_answer'){
            $chat_id = getUserConfig( $tg->get_chatId(), 'applications_answer');
            $tg->send_chatAction('typing', $chat_id)->send_message( get_phrase('answered_receive', [], $chat_id) . $text, $chat_id );
            $tg->send_chatAction('typing')->send_message( get_phrase('answered') );
            menu('/back');
        }else{
        	$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
        }
	}else if( ! empty( $updates['message']['photo'] ) ){
		if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/contact'){
            $tg->send_chatAction('typing')->send_message( get_phrase('contact_error') );
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/send_notification'){
        	$photo = end($updates['message']['photo']);
        	$caption = (!empty($updates['message']['caption'])) ? $updates['message']['caption'] : '';
			add_notifications([
            	'photo' => $photo['file_id'],
            	'caption' => $caption
            ]);
            $tg->send_chatAction('typing')->send_message( get_phrase('notification_sended') );
			menu('/back');
        }else{
        	$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
        }
	}else if( ! empty( $updates['message']['voice'] ) ){
		if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/contact'){
            $tg->send_chatAction('typing')->send_message( get_phrase('contact_error') );
        }else{
        	$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
        }
	}else if( ! empty( $updates['message']['video'] ) ){
		if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/contact'){
            $tg->send_chatAction('typing')->send_message( get_phrase('contact_error') );
        }else if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/send_notification'){
        	$video = $updates['message']['video']['file_id'];
        	$caption = (!empty($updates['message']['caption'])) ? $updates['message']['caption'] : '';
			add_notifications([
            	'video' => $video,
            	'caption' => $caption
            ]);
            $tg->send_chatAction('typing')->send_message( get_phrase('notification_sended') );
			menu('/back');
        }else{
        	$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
        }
	}else if( ! empty( $updates['message']['document'] ) ){
		if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/contact'){
            $tg->send_chatAction('typing')->send_message( get_phrase('contact_error') );
        }else{
        	$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
        }
	}else if( ! empty( $updates['callback_query']['data'] ) ){
		$tg->set_chatId($updates['callback_query']['message']['chat']['id']);
		parse_str($updates['callback_query']['data'], $query);
		if (count($query) > 0) {
			if ( ! empty( $query['contacts'] ) ) {
				$contacts = category_items( $query['contacts'] );
				$contacts_count = count($contacts);
				if ( $contacts_count == 0 ) {
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('contacts_not_found')]);
					exit(1);
				}
				$page = ( array_key_exists('prev', $query) ) ? intval($query['prev']) : intval($query['next']);
				$contact = array_slice($contacts, $page, 1, true);
                if (count($contact) > 0) {
                	$contact = reset($contact);
                	
                	$message = contact( $contact);
					$pagination = getPagination($query['contacts'], $page, $contacts_count, 'contacts');
					array_unshift($pagination , [
						[
							'text' => '👍',
							'callback_data' => 'contact_like='.$contact['id']
						],
						[
							'text' => '📊 ('.$contact['rating'].')',
							'callback_data' => 'contact_status='.$contact['id']
						],
						[
							'text' => '👎',
							'callback_data' => 'contact_dislike='.$contact['id']
						]
					]);
                	$req = $tg->request('editMessageText', [
                    	'chat_id' => $updates['callback_query']['message']['chat']['id'],
                        'message_id' => $updates['callback_query']['message']['message_id'],
                        'reply_markup' => [
                        	'inline_keyboard' => $pagination
                        ],
                        'text' => $message,
                        'parse_mode' => 'html',
                        'disable_web_page_preview' => true
                    ]);
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_updated')]);
                }else{
                	$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_not_found')]);
                }
			}

			if ( ! empty( $query['search'] ) ) {
				$contacts = search( $query['search'] );
				$contacts_count = count($contacts);
				if ( $contacts_count == 0 ) {
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('contacts_not_found')]);
					exit(1);
				}
				$page = ( array_key_exists('prev', $query) ) ? intval($query['prev']) : intval($query['next']);
				$contact = array_slice($contacts, $page, 1, true);
                if (count($contact) > 0) {
                	$contact = reset($contact);
                	
                	$message = contact( $contact);
					$pagination = getPagination($query['search'], $page, $contacts_count, 'search');
					array_unshift($pagination , [
						[
							'text' => '👍',
							'callback_data' => 'contact_like='.$contact['id']
						],
						[
							'text' => '📊 ('.$contact['rating'].')',
							'callback_data' => 'contact_status='.$contact['id']
						],
						[
							'text' => '👎',
							'callback_data' => 'contact_dislike='.$contact['id']
						]
					]);
                	$req = $tg->request('editMessageText', [
                    	'chat_id' => $updates['callback_query']['message']['chat']['id'],
                        'message_id' => $updates['callback_query']['message']['message_id'],
                        'reply_markup' => [
                        	'inline_keyboard' => $pagination
                        ],
                        'text' => $message,
                        'parse_mode' => 'html',
                        'disable_web_page_preview' => true
                    ]);
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_updated')]);
                }else{
                	$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_not_found')]);
                }
			}

			if ( ! empty( $query['contact_status'] ) ) {
				$contact = get_contact($query['contact_status']);
				$message = get_phrase('contact_status', ['number' => $contact['rating']]);
				$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => $message, 'show_alert' => true]);
			}

			if ( ! empty( $query['contact_like'] ) ) {
				if( $rating = like($updates['callback_query']['message']['chat']['id'], $query['contact_like']) ){
					$updates['callback_query']['message']['reply_markup']['inline_keyboard']['0']['1']['text'] = '📊 ('.$rating.')';
					$req = $tg->request('editMessageText', [
                    	'chat_id' => $updates['callback_query']['message']['chat']['id'],
                        'message_id' => $updates['callback_query']['message']['message_id'],
                        'reply_markup' => [
                        	'inline_keyboard' => $updates['callback_query']['message']['reply_markup']['inline_keyboard']
                        ],
                        'text' => $updates['callback_query']['message']['text'],
                        'parse_mode' => 'html',
                        'disable_web_page_preview' => true
                    ]);
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('reaction_accepted'), 'show_alert' => true]);
				}else{
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('already_expressed_reaction'), 'show_alert' => true]);
				}
			}

			if ( ! empty( $query['contact_dislike'] ) ) {
				if( $rating = dislike($updates['callback_query']['message']['chat']['id'], $query['contact_dislike']) ){
					$updates['callback_query']['message']['reply_markup']['inline_keyboard']['0']['1']['text'] = '📊 ('.$rating.')';
					$req = $tg->request('editMessageText', [
                    	'chat_id' => $updates['callback_query']['message']['chat']['id'],
                        'message_id' => $updates['callback_query']['message']['message_id'],
                        'reply_markup' => [
                        	'inline_keyboard' => $updates['callback_query']['message']['reply_markup']['inline_keyboard']
                        ],
                        'text' => $updates['callback_query']['message']['text'],
                        'parse_mode' => 'html',
                        'disable_web_page_preview' => true
                    ]);
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('reaction_accepted'), 'show_alert' => true]);
				}else{
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('already_expressed_reaction'), 'show_alert' => true]);
				}
			}

			if ( ! empty( $query['users'] ) ) {
				$users = get_users();
				$users_count = count($users);
				if ( $users_count == 0 ) {
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('users_not_found')]);
					exit(1);
				}
				$page = ( array_key_exists('prev', $query) ) ? intval($query['prev']) : intval($query['next']);
				$user = array_slice($users, $page, 1, true);
                if (count($user) > 0) {
                	$user = reset($user);
                	
                	$message = user( $user, $users_count);
					$pagination = getPagination($user['id'], $page, $users_count, 'users');
					$req = $tg->request('editMessageText', [
                    	'chat_id' => $updates['callback_query']['message']['chat']['id'],
                        'message_id' => $updates['callback_query']['message']['message_id'],
                        'reply_markup' => [
                        	'inline_keyboard' => $pagination
                        ],
                        'text' => $message,
                        'parse_mode' => 'html',
                        'disable_web_page_preview' => true
                    ]);
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_updated')]);
                }else{
                	$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_not_found')]);
                }
			}

			if ( ! empty( $query['applications'] ) ) {
				$applications = get_applications();
				$applications_count = count($applications);
				if ( $applications_count == 0 ) {
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('applications_not_found')]);
					exit(1);
				}
				$page = ( array_key_exists('prev', $query) ) ? intval($query['prev']) : intval($query['next']);
				$application = array_slice($applications, $page, 1, true);
                if (count($application) > 0) {
                	$application = reset($application);
                	
                	$message = application( $application, $applications_count);
					$pagination = getPagination($application['chat_id'], $page, $applications_count, 'applications');
					array_unshift($pagination , [
						[
							'text' => '💬',
							'callback_data' => 'applications_answer='.$application['chat_id']
						],
						[
							'text' => '🗑',
							'callback_data' => 'applications_delete='.$application['time']
						]
					]);
					$req = $tg->request('editMessageText', [
                    	'chat_id' => $updates['callback_query']['message']['chat']['id'],
                        'message_id' => $updates['callback_query']['message']['message_id'],
                        'reply_markup' => [
                        	'inline_keyboard' => $pagination
                        ],
                        'text' => $message,
                        'parse_mode' => 'html',
                        'disable_web_page_preview' => true
                    ]);
					$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_updated')]);
                }else{
                	$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('result_not_found')]);
                }
			}

			if ( ! empty( $query['applications_answer'] ) ) {
				setUserConfig($updates['callback_query']['message']['chat']['id'], 'lastmessage', '/applications_answer');
				setUserConfig($updates['callback_query']['message']['chat']['id'], 'applications_answer', $query['applications_answer']);
				$tg->set_chatId($updates['callback_query']['message']['chat']['id'])->send_chatAction('typing')->set_replyKeyboard([
					[get_phrase('back_button')]
				])->send_message( get_phrase('application_answer_text') );
				$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('application_answer')]);
			}

			if ( ! empty( $query['applications_delete'] ) ) {
				delete_applications($query['applications_delete']);
				$tg->request('answerCallbackQuery', ['callback_query_id' => $updates['callback_query']['id'], 'text' => get_phrase('application_deleted'), 'show_alert' => true]);
				$tg->request('deleteMessage', ['chat_id' => $updates['callback_query']['message']['chat']['id'], 'message_id' => $updates['callback_query']['message']['message_id']]);
			}
		}
	}else if( ! empty( $updates['inline_query']['query'] ) ){
		$content['inline_query_id'] = $updates['inline_query']['id'];
        $results = inline_search($gc8d9->I479j( $updates['inline_query']['query'] ));
        $content['results'] =  $results;
        $content['cache_time'] = 0;
        $res = $tg->request('answerInlineQuery', $content);
	}
}

function menu( $act = NULL ) {
	global $tg, $config, $gc8d9;
	switch ( $act ) {
		case '/start':
			if(! getUserConfig( $tg->get_chatId(), 'welcome' ) ){
				$tg->send_chatAction('typing')->send_message("👋 Assalomu alaykum <em>Kosonsoy aloqa robotga</em> xush kelibsiz. Ushbu bot orqali siz Kosonsoy tumanidagi o'zingizga kerakli manzillarni qidirishingiz va ularning aloqa kontaktlari haqida ma'lumot olishingiz mumkin.");
				
				$tg->set_replyKeyboard([
        			['🔸 Lotin', '🔸 Кирил'],
				])->send_message('Iltimos, o‘zingizga qulay bo‘lgan alifbo turini tanlang 📖');
			}else{
				$buttons = [
        			[get_phrase('search_button'), get_phrase('category_button')],
        			[get_phrase('add_button'), get_phrase('settings_button')],
        			[get_phrase('contact_button'), get_phrase('about_button')],
        			//[get_phrase('instruction_button')],
				];

				if ( in_array( $tg->get_chatId(), $config['owners'] ) )  {
					$buttons[] = [get_phrase('admin_contact'), get_phrase('admin_notifications')];
					$buttons[] = [get_phrase('admin_users')];
				}
                $tg->set_replyKeyboard($buttons)->send_message( get_phrase('start') );
            }
		break;

		case '/about':
			$tg->send_chatAction('typing')->send_message( get_phrase('about') );
		break;

		case '/instruction':
			$tg->send_chatAction('typing')->send_message( get_phrase('instruction') );
		break;

		case '/contact':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('back_button')]
			])->send_message( get_phrase('contact') );
		break;

		case '/search':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('back_button')]
			])->send_message( get_phrase('search') );
		break;

		case '/addcontact':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('continue')],
        		[get_phrase('back_button')]
			])->send_message( get_phrase('addcontact') );
		break;

		case '/addcontact_1':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('cancel')]
			])->send_message( get_phrase('addcontact_1') );
		break;

		case '/addcontact_2':
			$buttons = $config['categories'];
			if (getDefaultLang() == 'cyrillic' ) {
				$temp = [];
				foreach ($buttons as $item) {
					$temp[] = $gc8d9->jyyD9($item);
				}	
				$buttons = $temp;
			}
			$buttons = array_chunk($buttons, 2);
			$buttons[] = [get_phrase('cancel')];
			$tg->send_chatAction('typing')->set_replyKeyboard($buttons)->send_message( get_phrase('addcontact_2') );
		break;

		case '/addcontact_3':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('cancel')]
			])->send_message( get_phrase('addcontact_3') );
		break;

		case '/addcontact_4':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('skip')],
        		[get_phrase('cancel')]
			])->send_message( get_phrase('addcontact_4') );
		break;

		case '/addcontact_5':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		['08:00 - 14:00', '09:00 - 14:00'],
        		['08:00 - 16:00', '09:00 - 16:00'],
        		['08:00 - 17:00', '09:00 - 18:00'],
        		['08:00 - 20:00', '09:00 - 20:00'],
        		['08:00 - 22:00', '09:00 - 22:00'],
        		[get_phrase('skip'), get_phrase('cancel')]
			])->send_message( get_phrase('addcontact_5') );
		break;

		case '/addcontact_6':
			$tg->send_chatAction('typing')->set_replyKeyboard([
        		[get_phrase('skip')],
        		[get_phrase('cancel')]
			])->send_message( get_phrase('addcontact_6') );
		break;

		case '/settings':
			$tg->send_chatAction('typing')->set_replyKeyboard([
				[get_phrase('settings_notification_button'), get_phrase('settings_alphabet_button')],
        		[get_phrase('back_button')]
			])->send_message( get_phrase('settings') );
		break;

		case '/notifications':
			$tg->send_chatAction('typing')->set_replyKeyboard([
				[get_phrase('settings_notification_on_button'), get_phrase('settings_notification_off_button')],
        		[get_phrase('back_button')]
			])->send_message( get_phrase('settings_notification') );
		break;

		case '/alphabet':
			$tg->send_chatAction('typing')->set_replyKeyboard([
				['🔸 Lotin', '🔸 Кирил'],
        		[get_phrase('back_button')]
			])->send_message( get_phrase('settings_alphabet') );
		break;

		case '/categories':
			$buttons = $config['categories'];
			if (getDefaultLang() == 'cyrillic' ) {
				$temp = [];
				foreach ($buttons as $item) {
					$temp[] = $gc8d9->jyyD9($item);
				}	
				$buttons = $temp;
			}
			$buttons = array_chunk($buttons, 2);
			$buttons[] = [get_phrase('back_button')];
			$tg->send_chatAction('typing')->set_replyKeyboard($buttons)->send_message( get_phrase('categories') );
		break;

		case '/back':
			if(getUserConfig( $tg->get_chatId(), 'lastmessage') == '/notifications' || getUserConfig( $tg->get_chatId(), 'lastmessage') == '/alphabet'){
				setUserConfig($tg->get_chatId(), 'lastmessage', '/settings');
				menu('/settings');
			}else{
				setUserConfig($tg->get_chatId(), 'lastmessage', '/start');
				menu('/start');
			}
		break;

		case '/cancel':
			setUserConfig($tg->get_chatId(), 'addcontact_step', '0');
			setUserConfig($tg->get_chatId(), 'addcontact_data_name', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_category', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_phone', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_address', '');
			setUserConfig($tg->get_chatId(), 'addcontact_data_working_hours', '');
			setUserConfig($tg->get_chatId(), 'lastmessage', '/start');
			menu('/start');
		break;

		case '/users':
			$users = get_users();
			$users_count = count($users);
			if ( $users_count == 0 ) {
				$tg->send_chatAction('typing')->send_message( get_phrase('users_not_found') );
				exit(1);
			}
			$user = user( $users[0], $users_count);
			$pagination = getPagination($user['id'], 0, $users_count, 'users');
			$tg->send_chatAction('typing')->set_inlineKeyboard($pagination)->send_message( $user );
		break;

		case '/send_notification':
			$tg->send_chatAction('typing')->set_replyKeyboard([
				[get_phrase('back_button')]
			])->send_message( get_phrase('send_notification') );
		break;

		case '/applications':
			$applications = get_applications();
			if ( empty($applications) ) {
				$tg->send_chatAction('typing')->send_message( get_phrase('applications_not_found') );
				exit(1);
			}
			$application = application( $applications[0], $applications_count);
			$pagination = getPagination($application['time'], 0, $applications_count, 'applications');
			array_unshift($pagination , [
				[
					'text' => '💬',
					'callback_data' => 'applications_answer='.$applications[0]['chat_id']
				],
				[
					'text' => '🗑',
					'callback_data' => 'applications_delete='.$applications[0]['time']
				]
			]);
			$tg->send_chatAction('typing')->set_inlineKeyboard($pagination)->send_message( $application );
		break;
			
		default:
			$tg->send_chatAction('typing')->send_message( get_phrase('undestand') );
		break;
	}	


}