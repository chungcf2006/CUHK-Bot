<?php
	global $db;
	if ($data->message->chat->type == "private"){
		$subscribe_list = $db->prepare("SELECT COUNT(*) FROM werewolf_subscriber WHERE chat_id=?");
		$subscribe_list->bindParam(1, $chat_id);
		$subscribe_list->execute();
		$row = $subscribe_list->fetch();

		if ($row[0] > 0) {
			sendMessage($data->message->from->first_name." ".$data->message->from->last_name." - You have already subscribed the notification service!");
		} else {
			$statement = $db->prepare("INSERT INTO werewolf_subscriber (chat_id, first_name, last_name) VALUES (?,?,?)");
			$statement->bindParam(1, $chat_id);
			$statement->bindParam(2, $data->message->from->first_name);
			$statement->bindParam(3, $data->message->from->last_name);
			$statement->execute();
			sendMessage($data->message->from->first_name." ".$data->message->from->last_name." - Subscribe the notification service successfully!");
			$chat_id = $data->message->chat->id;
			sendMessage("If you haven't use the bot in PM mode before, PM @cuhk_werewolf_bot and say /start with it.");
		}
	} else {
		sendMessage("Please use this command in Group mode.");
	}