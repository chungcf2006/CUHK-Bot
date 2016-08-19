<?php
	global $callbackData, $message_id, $data, $command_opt;

	function path_find($id){
		global $db;
		if ($id != 0){
			$statement = $db->prepare('SELECT name, parent FROM directory WHERE id = ?');
			$statement->bindParam(1, $id);
			$statement->execute();
			$row = $statement->fetch();
			return path_find($row["parent"])."/".$row["name"];
		} else {
			return $row["name"];
		}
	}

	if(!isset($callbackData)){
		$command_opt = trim($command_opt);
		writeLog("Alias: ".$command_opt);
		if ($command_opt == "") {
			$target = 0;
		} else {
			$statement = $db->prepare('SELECT id FROM directory_alias WHERE alias = ?');
			$statement->bindParam(1, $command_opt);
			$statement->execute();
			if ($statement->columnCount() > 0) {
				$target = (int)$statement->fetchColumn();
			} else {
				sendMessage("No Entry Found");
				exit();
			}
		}
	} else {
		$callbackData = explode(",", $callbackData);
		if ($callbackData[1] == "cancel")
			editMessageText($data->callback_query->message->message_id, "Operation Cancelled");
		else
			$target = intval($callbackData[1]);
	}

	writeLog("Target: ".$target);
	if ($target != 0){
		$statement = $db->prepare('SELECT isEntry FROM directory WHERE id = ?');
		$statement->bindParam(1, $target, PDO::PARAM_INT);
		$statement->execute();
		$isEntry = $statement->fetchColumn();
	} else {
		$isEntry = FALSE;
	}
	

	if ($isEntry) {
		writeLog("isEntry");
		$statement = $db->prepare('SELECT * FROM directory WHERE id = ?');
		$statement->bindParam(1, $target, PDO::PARAM_INT);
		$statement->execute();
		$item = $statement->fetch();

		$description = "*".trim(path_find($item["id"]), "/")."*\n";
		$description .= "🏛 地址： ".$item["location"]."\n";
		$description .= "☎️ 電話： ".$item["tel"]."\n";
		$description .= "📠 傳真： ".$item["fax"]."\n";
		$description .= "📧 電郵： ".$item["email"]."\n";
		$description .= "🖥 網頁： ".$item["homepage"]."\n";

		if (isset($data->callback_query))
			editMessageText($data->callback_query->message->message_id, $description);
		else
			sendMessage($description);
	} else {
		$count = 0;
		$keyboard_content = array();
		
		$statement = $db->prepare('SELECT id, name FROM directory WHERE parent = ?');
		$statement->bindParam(1, $target, PDO::PARAM_INT);
		$statement->execute();
		$items = $statement->fetchAll();

		foreach ($items as $item) {
			$count++;
			array_push($keyboard_content, array(
				array(
					"text" => (string)$item["name"],
					"callback_data" => "directory,".(string)$item["id"]
				)
			));
		}
		
		if ($target != 0){
			$statement = $db->prepare('SELECT parent FROM directory WHERE id = ?');
			$statement->bindParam(1, $target, PDO::PARAM_INT);
			$statement->execute();
			$item = $statement->fetch();

			array_push($keyboard_content, array(
				array(
					"text" => "⬅️返回",
					"callback_data" => "directory,".$item["parent"]
				)
			));
		} else {
			array_push($keyboard_content, array(
				array(
					"text" => "❌退出",
					"callback_data" => "directory,cancel"
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
		if (isset($data->callback_query)){
			editMessageReplyMarkup($data->callback_query->message->message_id, $keyboardArray);
		} else {
			$prompt = "*歡迎使用中大通訊錄*\n\n";
			$prompt .= "免責聲明：本通訊錄所載資訊均為人手搜集所得，CUHK Bot不保證所載資料準確。\n\n";
			$prompt .= "請選擇項目";
			sendKeyboard($prompt, $keyboardArray);
		}
	}

?>
