<?php
        include("config.php");

	global $chat_id, $data, $receivedMessage, $db;
	
	//Create global $db variable
	

	function writeLog($message){
		//Write log into log file
		$date = date("Y-m-d");
		$logFile = fopen("log/".$date.".txt", "a+");
		fwrite($logFile, date("H:i:s\t"));
		fwrite($logFile, print_r($message, TRUE)."\n");
		fclose($logFile);
		chmod("log/".$date.".txt", 0644);
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
			"text" => $message
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
			"reply_markup" => $keyboardArray
		);
		request("sendMessage", $payload_array);
	}

	function editMessageText($message_id, $message){
		//Used for editing an exist message
		global $chat_id;
		$payload_array = array(
			"chat_id" => $chat_id,
			"message_id" => $message_id,
			"text" => $message
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

		writeLog($parameters);
		$ch = curl_init(API_URL);
		//Only enable the following line when the server require a Proxy server for Internet connection
		curl_setopt($ch, CURLOPT_PROXY, PROXY);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
		writelog("Response:\n".print_r(json_decode(curl_exec($ch)),TRUE));
		curl_close($ch);
		return true;

	}

?>
