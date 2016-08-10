<?php
	require_once("common/telegram.lib.php");
	header("Content-Type:application/json");
	$json = file_get_contents('php://input');
	$data = json_decode($json);

	writeLog(print_r($data, TRUE));

	if (isset($data->message->chat->id))
		$chat_id = $data->message->chat->id;
	else
		$chat_id = $data->callback_query->message->chat->id;

	$message_id = $data->callback_query->message->message_id;
	$receivedMessage = $data->message->text;
	$command = NULL;
	if (isset($data->callback_query->data)) {
		$callbackData = $data->callback_query->data;
		$command = substr($callbackData, 0, strpos($callbackData, ","));
	} else {
		if (isset($data->message->entities)){
			foreach($data->message->entities as $entity){
				if ($entity->type == "bot_command")
					if (strpos($receivedMessage, "@") === FALSE)
						$command = substr($receivedMessage, $entity->offset+1, $entity->length-1);
					else
						$command = substr($receivedMessage, $entity->offset+1, strpos($receivedMessage, "@")-$entity->offset-1);
			}
		}
	}	

	if ($command !== NULL){
		//Determine whether to execute the command by external PHP file or directly send predefined content to the user

		//All resource related to a specific module should put under module/{command_name}
		//The entry point of the command should be named command.php or command.txt
		writeLog("module/".$command."/command.php");
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