<?php
	$db = new PDO("sqlite:database.sqlite");
	global $callbackData, $message_id, $data, $command_opt;
	$statement = $db->prepare('INSERT INTO secret_post (timestamp, content, status) VALUES (?, ?, 0)');
	$statement->bindValue(1, time());
	$statement->bindValue(2, strip_tags($command_opt));
	$statement->execute();
	sendMessage("Secret Posted Successfully");
?>
