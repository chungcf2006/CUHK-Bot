<?php
	if (isset($_POST["hostal"])){
		$db = new PDO("sqlite:../../../database.sqlite");
		$statement = $db->prepare('SELECT * FROM hostal_supply WHERE hostal = ? AND sex = ?');
		$statement->bindValue(1, $_POST["hostal"]);
		$statement->bindValue(2, $_POST["sex"]);
		$statement->execute();
		while ($row = $statement->fetch()){
			$time = date("Y-m-d H:i:s", $row["timestamp"]);
			echo "<tr>";
			echo "<td>".$time."</td>";
			echo "<td>".$row["room"]."</td>";
			echo "</tr>\n";
		}
		exit();
	}
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
			var Xhttp = new XMLHttpRequest();
			var XMLSource = "hostal.xml"
			Xhttp.open("GET", XMLSource, true);
			Xhttp.send();
			var parser;
			var XMLDoc;
			Xhttp.onreadystatechange = function() {
				if (Xhttp.readyState == 4 && Xhttp.status == 200) {
					parser = new DOMParser();
					XMLDoc = parser.parseFromString(Xhttp.responseText, "application/xml");
					var colleges = XMLDoc.getElementsByTagName("college");

					for (var i = 0; i < colleges.length; i++){
						var tempNode = document.createElement("option");
						tempNode.setAttribute("value", colleges[i].getAttribute("id"));
						tempNode.innerHTML = colleges[i].getAttribute("name");
						document.getElementById("college").appendChild(tempNode);
					}
				}
			}

			function loadFilter(e){
				while (document.getElementById("sex").getElementsByTagName("option").length > 1)
					document.getElementById("sex").removeChild(document.getElementById("sex").lastChild);
				switch (e.getAttribute("id")){
					case "college":
						while (document.getElementById("hostal").getElementsByTagName("option").length > 1)
							document.getElementById("hostal").removeChild(document.getElementById("hostal").lastChild);

						var selectedValue = document.getElementById("college").options[document.getElementById("college").selectedIndex].getAttribute("value");

						if (selectedValue != "default"){
							var hostals = XMLDoc.getElementById(selectedValue).getElementsByTagName("hostal");
							for (var i = 0; i < hostals.length; i++){
								var tempNode = document.createElement("option");
								tempNode.setAttribute("value", hostals[i].getAttribute("id"));
								tempNode.innerHTML = hostals[i].getAttribute("name");
								document.getElementById("hostal").appendChild(tempNode);
							}
						}
					break;

					case "hostal":
						var selectedValue = document.getElementById("hostal").options[document.getElementById("hostal").selectedIndex].getAttribute("value");

						if (selectedValue != "default"){
							var sexString = XMLDoc.getElementById(selectedValue).getAttribute("sex");
							var sex = sexString.split(",");
							for (var i = 0; i < sex.length; i++){
								switch (sex[i]){
									case "M":
										var tempNode = document.createElement("option");
										tempNode.setAttribute("value", "M");
										tempNode.innerHTML = "男";
										document.getElementById("sex").appendChild(tempNode);
									break;

									case "F":
										var tempNode = document.createElement("option");
										tempNode.setAttribute("value", "F");
										tempNode.innerHTML = "女";
										document.getElementById("sex").appendChild(tempNode);
									break;
								}
							}

						}
					break;
				}
			}

			function loadResult(){
				var Xhttp = new XMLHttpRequest();
				var XMLSource = "search.php"
				var hostal = document.getElementById("hostal").options[document.getElementById("hostal").selectedIndex].value;
				var sex = document.getElementById("sex").options[document.getElementById("sex").selectedIndex].value;
				Xhttp.open("POST", XMLSource, true);
				Xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				Xhttp.send("hostal="+hostal+"&sex="+sex);
				Xhttp.onreadystatechange = function() {
					if (Xhttp.readyState == 4 && Xhttp.status == 200) {
						document.getElementById("resultContainer").setAttribute("style", "display:block");
						console.log(Xhttp.responseText);
						if (Xhttp.responseText == "")
							document.getElementById("resultContainer").innerHTML = "<div class=\"alert alert-danger\">Not Found.</div>";
						else
							document.getElementById("result").innerHTML = Xhttp.responseText;
					}
				}
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
					<select class="form-control" id="college" name="college" onchange="loadFilter(this)">
						<option value="default">- 請選擇書院 -</option>
					</select>
				</div>
				<div class="col-md-4 alert alert-warning">
					<label for="hostal"><h2>宿舍</h2></label>
					<select class="form-control" id="hostal" name="hostal" onchange="loadFilter(this)">
						<option value="default">- 請選擇宿舍 -</option>
					</select>
				</div>
				<div class="col-md-4 alert alert-success">
					<label for="sex"><h2>男宿/女宿</h2></label>
					<select class="form-control" id="sex" name="sex" onchange="loadResult()">
						<option value="default">- 請選擇男宿/女宿 -</option>
					</select>
				</div>
				<div id="resultContainer" style="display:none">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>日期</th>
								<th>房號</th>
							</tr>
						</thead>
						<tbody id="result">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
