<?php
	$db = new PDO("sqlite:../../../database.sqlite");
	$statement = $db->prepare('SELECT * FROM college');
	$statement->execute();
?>
<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<script>
			function showResult(str, q, result) {
				if (window.XMLHttpRequest) {
					// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				} else {  // code for IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						document.getElementById(result).innerHTML = xmlhttp.responseText;
						if (q == "sex")
							document.getElementById("hostal").selectedIndex = 0;
						document.getElementById("sex").selectedIndex = 0;
					}
				}
				xmlhttp.open("GET","search.php?"+q+"="+str,true);
				xmlhttp.send();
			}
		</script>
		<title>CUHK Bot - Homepage</title>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<h1 class="text-center">Part房搜尋器</h1>
				<div class="col-md-4 alert alert-danger">
					<label for="college"><h2>書院</h2></label>
					<select class="form-control" id="college" name="college" onchange="showResult(this.value, 'college', 'hostal')">
						<option value="default">- 請選擇書院 -</option>
<?php
while ($row = $statement->fetch(PDO::FETCH_ASSOC))
	echo "<option value=\"".$row["id"]."\">".$row["name"]."</option>\n";
?>
					</select>
				</div>
				<div class="col-md-4 alert alert-warning">
					<label for="hostal"><h2>宿舍</h2></label>
					<select class="form-control" id="hostal" name="hostal" onchange="showResult(this.value, 'hostal', 'sex')">
						<option value="default">- 請選擇宿舍 -</option>
					</select>
				</div>
				<div class="col-md-4 alert alert-success">
					<label for="sex"><h2>男宿/女宿</h2></label>
					<select class="form-control" id="sex" name="sex">
						<option value="default">- 請選擇男宿/女宿 -</option>
					</select>
				</div>
			</div>
		</div>
	</body>
</html>
