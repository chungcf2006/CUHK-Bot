<?php
	$db = new SQLite3("database.sqlite");
	global $callbackData, $message_id, $data;

	if(!isset($callbackData)){
		$target = 0;
	} else {
		$callbackData = explode(",", $callbackData);
		if ($callbackData[1] == "cancel")
			editMessageText($data->callback_query->message->message_id, "Operation Cancelled");
		else
			$target = intval($callbackData[1]);
	}

	writeLog("Target: ".$target);
	if ($target != 0){
		$query = $db->prepare('SELECT isEntry FROM directory WHERE id = ?');
		$query->bindValue(1, $target, SQLITE3_INTEGER);
		$result = $query->execute()->fetchArray();
		$isEntry = $result["isEntry"];
	} else {
		$isEntry = FALSE;
	}


	if ($isEntry) {
		writeLog("isEntry");
		$query = $db->prepare('SELECT name, tel FROM directory WHERE id = ?');
		$query->bindValue(1, $target, SQLITE3_INTEGER);
		$item = $query->execute()->fetchArray();
		$description = (string)$item["name"]."\n".$item["tel"];
		editMessageText($data->callback_query->message->message_id, $description);
	} else {	
		writeLog("isNotEntry");
		$count = 0;
		$keyboard_content = array();		
		$query = $db->prepare('SELECT id, name FROM directory WHERE parent = ?');
		$query->bindValue(1, $target, SQLITE3_INTEGER);
		$result = $query->execute();
		while ($item = $result->fetchArray()) {
			$count++;
			array_push($keyboard_content, array(
				array(
					"text" => $item["name"],
					"callback_data" => "directory2,".$item["id"]
				)
			));
		}
		
		if ($target != 0){
			$parent = $db->querySingle('SELECT parent FROM directory WHERE id = ?');

			array_push($keyboard_content, array(
				array(
					"text" => "⬅️返回",
					"callback_data" => "directory2,".$parent
				)
			));
		} else {
			array_push($keyboard_content, array(
				array(
					"text" => "❌退出",
					"callback_data" => "directory2,cancel"
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
