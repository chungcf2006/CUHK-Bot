<?php
	
	$xml = simplexml_load_file("module/directory/data.xml");
	global $callbackData, $message_id, $data;

	if(!isset($callbackData)){
		$target = -1;
	} else {
		$callbackData = explode(",", $callbackData);
		$target = intval($callbackData[1]);
	}

	writeLog("Target: ".$target);
	if ($target != -1){
		writeLog($xml->entry[0]);
		$isEntry = ($xml->entry[$target]->count() > 1);
	} else {
		$isEntry = FALSE;
	}
	

	if ($isEntry) {
		writeLog("isEntry");
		foreach($xml->children() as $child) {
			$attributes = $child->attributes();
			if($attributes["id"] == $target){
				$description = (string)$child->name[0]."\n";
				foreach($child->tel as $tel){
					$description .= $tel."\n";
					editMessageText($data->callback_query->message->message_id, $description);
				}
			}
		}
	} else {	
		
		$count = 0;
		$keyboard_content = array();
		
		foreach ($xml->children() as $child) {
			$attribute = $child->attributes();
			
			if ($attribute["parent"] == $target){
				$count++;
				writeLog(print_r($child, TRUE));
				array_push($keyboard_content, array(
					array(
						"text" => (string)$child->name,
						"callback_data" => "directory,".(string)$attribute->id
					)
				));
			}
		}
		
		$parent = $xml->entry[$target]->attributes();
		$parent = $parent["parent"];
		
		if ($target != -1){
			array_push($keyboard_content, array(
				array(
					"text" => "⬅️返回",
					"callback_data" => "directory,".$parent
				)
			));
		}
		

		if ($count <= 0){
			answerCallbackQuery($data->callback_query->id, "No Entry Found");
			exit();
		}
		
		$keyboardArray = array(
				"inline_keyboard" => $keyboard_content,
				"resize_keyboard" => TRUE,
				"one_time_keyboard" => TRUE

			);
		if (isset($data->callback_query))
			editMessageReplyMarkup($data->callback_query->message->message_id, $keyboardArray);
		else
			sendKeyboard("請選擇", $keyboardArray);
	}

?>
