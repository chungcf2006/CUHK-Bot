<?php
	ini_set("log_errors", 1);
	ini_set("error_log", "logs/".date("Y-m-d").".txt");

	$command_list = array("start", "turtle", "weather");

	require_once("common/telegram.lib.php");
	header("Content-Type:application/json");
	$json = file_get_contents('php://input');
	$data = json_decode($json);
	
	//writeLog($data);
	if (isset($data->message->text)){
		writeLog("Handled By:\tCUHK Bot");
		writeLog("Chat ID:\t\t".$data->message->chat->id);
		$sender = $data->message->from->first_name;
		if (isset($data->message->from->last_name))
			$sender .= " ".$data->message->from->last_name;
		writeLog("Sender:\t\t".$sender);
		if (isset($data->message->chat->title))
			writeLog("Chat Title:\t".$data->message->chat->title);
		writeLog("Chat Type:\t".$data->message->chat->type);
		writeLog("Message:\t\t".$data->message->text);
		writeLog("");
	}
	

	if (isset($data->callback_query)){
		$chat_id = $data->callback_query->message->chat->id;
		$message_id = $data->callback_query->message->message_id;
	} else {
		$chat_id = $data->message->chat->id;
	}

	
	$receivedMessage = $data->message->text;
	$command = NULL;
	if (isset($data->callback_query->data)) {
		$callbackData = $data->callback_query->data;
		$command = substr($callbackData, 0, strpos($callbackData, ","));
	} else {
		if (isset($data->message->entities))
			foreach($data->message->entities as $entity)
				if ($entity->type == "bot_command"){
					$command = explode("@", $receivedMessage);
					$command = trim($command[0], "/");
					if (strpos($receivedMessage, "@") === FALSE)
						$command_opt = substr($receivedMessage, $entity->offset + $entity->length);
					else
						$command_opt = substr($receivedMessage, $entity->length + 1);
				}
	}	

	if (in_array($command, $command_list)){
		//Determine whether to execute the command by external PHP file or directly send predefined content to the user

		//All resource related to a specific module should put under module/{command_name}
		//The entry point of the command should be named command.php or command.txt
		//writeLog("module/".$command."/command.php");

		if (file_exists("module/".$command."/command.php")){
			//Execute PHP file for the command
			include("module/".$command."/command.php");
		} else {
			if (($response = file_get_contents("module/".$command."/command.txt")) !== FALSE){
				//Directly send back text file content
				sendMessage($response);
			} else {
				//No defined command found
			}
		}
	}
		
	
?>
