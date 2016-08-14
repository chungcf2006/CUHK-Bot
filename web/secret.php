<?php
	if (isset($_POST["content"])){
		$db = new PDO("sqlite:../database.sqlite");
		$statement = $db->prepare('INSERT INTO secret_post (timestamp, content, status) VALUES (?, ?, 0)');
		$statement->bindValue(1, time());
		$statement->bindValue(2, strip_tags($_POST["content"]));
		$statement->execute();
		$status = TRUE;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<title>CUHK Bot - Post a Secret</title>
	</head>
	<body>
		<div class="container-fluid center-block">
			<?php
				if ($status)
					echo '<div class="alert alert-success">Secret successfully posted.</div>';
			?>
			<h3>What is your secret? 你嘅秘密係咩？</h3>
			<form action="secret.php" method="POST">
				<p>
					<textarea name="content" class="form-control"></textarea>
				</p>
				<p>
					<button type="submit" class="btn btn-success">Submit</button>
				</p>
			</form>
		</div>
	</body>
</html>
