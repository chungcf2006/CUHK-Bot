<?php
	$db = new PDO("sqlite:../database.sqlite");
	$statement = $db->prepare('SELECT * FROM secret_post');
	$statement->execute();
?>

<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<title>CUHK Bot Admin - Secret</title>
	</head>
	<body>
		<div class="container-fluid center-block">
			<h1>Secret Management</h1>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>ID</th>
						<th>Time</th>
						<th>Content</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
<?php
	while ($row = $statement->fetch(PDO::FETCH_ASSOC)){
		$content = str_replace("\n","<br>",$row["content"]);
		echo "<tr>";
		echo "<td>".$row["id"]."</td>\n";
		echo "<td>".date("Y-m-d H:i:s", $row["timestamp"])."</td>\n";
		echo "<td>".$content."</td>\n";
		echo "<td>".$row["status"]."</td>\n";
		echo "</tr>";
	}
?>
				</tbody>
				</table>
		</div>
	</body>
</html>
