<?php
	include("common/config.php");

	global $chat_id, $data, $receivedMessage;
	
	//Create global $db variable
	$db = new PDO("sqlite:database.sqlite");
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

	function writeLog($message){
		//Write log into log file
		$date = date("Y-m-d");
		$logFile = fopen("logs/".$date.".txt", "a+");
		if ($message !== "")
			fwrite($logFile, date("[d-M-Y H:i:s] "));
		fwrite($logFile, print_r($message, TRUE)."\n");
		fclose($logFile);
		chmod("logs/".$date.".txt", 0644);
	}

	function timerlock($service){
		global $db, $chat_id;
		$statement = $db->prepare("SELECT COUNT(*) FROM timer WHERE service=? AND chat_id=?");
		$statement->bindValue(1, $service);
		$statement->bindValue(2, $chat_id);
		$statement->execute();
		$row = $statement->fetch();
		
		if ($row[0] == 0){
			$statement = $db->prepare("INSERT INTO timer (chat_id, timestamp, service) VALUES (?, ?, ?)");
			$statement->bindValue(1, $chat_id);
			$statement->bindValue(2, time());
			$statement->bindValue(3, $service);
			$statement->execute();
			$lock =  false;
		} else {
			$statement = $db->prepare("SELECT timestamp FROM timer WHERE chat_id=? AND service=?");
			$statement->bindValue(1, $chat_id);
			$statement->bindValue(2, $service);
			$statement->execute();
			$row = $statement->fetch();

			$statement = $db->prepare("UPDATE timer SET timestamp=? WHERE chat_id=? AND service=?");
			$statement->bindValue(1, time());
			$statement->bindValue(2, $chat_id);
			$statement->bindValue(3, $service);
			$statement->execute();
			$lock = (!(time() - $row["timestamp"] >= 20));
		}
		if ($lock){
			$content = "請勿頻繁使用此功能";
			if ($chat_id < 0)
				$content .= "\n\n群組使用者可選擇PM此Bot並傳送指令";
			sendMessage($content);
		}
		return $lock;
	}

	function sendSticker($file_id){
		//Send sticker to the user
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"sticker" => $file_id
		);

		request("sendSticker", $payload_array);
	}

	function sendMessage($message){
		//Send text message to the user
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"text" => $message,
			"parse_mode" => "Markdown"
		);
		request("sendMessage", $payload_array);
	}

	function sendPhoto($photo, $caption){
		writeLog("Start Call sendPhoto");
		//Send image to the user
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"photo" => $photo,
			"caption" => $caption
		);
		request("sendPhoto", $payload_array);
	}

	function sendKeyboard($message, $keyboardArray){
		//Send a keyboard for the user to select predefined choices.
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"text" => $message,
			"reply_markup" => $keyboardArray,
			"parse_mode" => "Markdown"
		);
		request("sendMessage", $payload_array);
	}

	function editMessageText($message_id, $message){
		//Used for editing an exist message
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"message_id" => $message_id,
			"text" => $message,
			"parse_mode" => "Markdown"
		);
		request("editMessageText", $payload_array);
	}

	function editMessageReplyMarkup($message_id, $reply_markup){
		//Used for editing an exist message
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"message_id" => $message_id,
			"reply_markup" => $reply_markup
		);
		request("editMessageReplyMarkup", $payload_array);
	}

	function answerCallbackQuery($callback_query_id, $text){
		//Execute after the user press the inline buttons
		$payload_array = array(
			"callback_query_id" => $callback_query_id,
			"text" => $text
		);
		request("answerCallbackQuery", $payload_array);
	}

	function request($method, $parameters){
		global $ch;
		if (!is_string($method)) {
			//Method name must be a string
			return false;
		}

		if (!$parameters) {
			$parameters = array();
		} else if (!is_array($parameters)) {
			//Parameters must be an array
			return false;
		}

		$parameters["method"] = $method;
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		if(($response = curl_exec($ch)) === false)
			echo 'Curl error: ' . curl_error($ch);
		else
			echo 'Operation completed without any errors';
		//writelog("Response:\n".print_r(json_decode($response),TRUE));
		curl_close($ch);
		return true;

	}

?>
