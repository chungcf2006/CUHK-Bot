<?php
	$db = new PDO("sqlite:database.sqlite");

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

	echo path_find(6);
?>