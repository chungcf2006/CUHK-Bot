<?php
	$xml = simplexml_load_file("module/delivery/data.xml");
	global $callbackData, $message_id;
	if(substr($callbackData, strpos($callbackData, ",")) === FALSE){

		$keyboard_content = array();

		foreach($xml->children() as $child){
			array_push($keyboard_content, array(
				array(
					"text" => (string)$child->name[0],
					"callback_data" => "delivery,".(string)$child->attributes()
				)
			));
		}

		$keyboardArray = array(
				"inline_keyboard" => $keyboard_content,
				"resize_keyboard" => TRUE,
				"one_time_keyboard" => TRUE

			);
		sendKeyboard("請選擇餐廳", $keyboardArray);	
		
	} else {
		global $data;
		$restaurant_id = substr($callbackData, strpos($callbackData, ",")+1);
		foreach($xml->children() as $child) {
			$attributes = $child->attributes();
			if($attributes["id"] == $restaurant_id){
				answerCallbackQuery($data->callback_query->id, "Choice Received - ".(string)$child->name[0]);
				$description = (string)$child->name[0]."\n";
				foreach($child->tel as $tel)
					$description .= $tel."\n";
				
				if (isset($child->menu))
					sendPhoto((string)$child->menu[0], $description);
				if (isset($child->text)){
					sendMessage($description."\n".$child->text);
				}
					
			}
		}
	}

?>
