
<!DOCTYPE html>
<html>
<h2>Web Query Interface<br></h2>
<p>
 16/10/2016 by @Jiapeng Liu<br>
</p>
<body>

<p>Please do not run a complex query here. You may kill the server. </p>
Type an SQL query in the following box: <p>
Example: <tt>SELECT * FROM Actor WHERE id=10;</tt><br />
<p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
<textarea name="query" cols="60" rows="8"></textarea><br />
<input type="submit" value="Submit" />
</form>
</p>

<style> 
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
</style>

<?php 
$db = new mysqli('localhost', 'cs143', '', 'CS143');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
} 

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	if (empty($_GET["query"])) {
    	echo  "Please type in the query";
  	} else {
		$exp = $_GET["query"];
		$rs = $db->query($exp);
		if (!$rs) {
			die('Query failed: ' . mysql_error());
		} else {
			echo "<h3>Results : <br></h3>";
			echo "<br>";
			outPut($rs);
		}
	}
}

$rs -> free();
$db -> close();

function outPut($rs) {
	echo "<table>";
	echo "<tr>";

	if ($rs ->num_rows > 0) {
		$meta = $rs->fetch_fields();    
		foreach ($meta as $val) {
		    
		    echo "<th>" . $val->name . "</th>";
		}
		echo "</tr>";



		while($row = $rs->fetch_assoc()) {
			echo "<tr>";
		    	foreach($row as $i) {
		    		if (!is_null($i)) {
		    			print "<td>".$i."</td>";
		    		} else {
		    			print "<td>NULL</td>";
		    		}
		    	}
		    echo "</tr>";
		}

		echo "</table>";
	} else {
		echo "No Match!";
	}
}

?>

</body>
</html>