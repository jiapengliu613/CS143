<?php
include("./DBconnect.php");
$exp = "select id, concat(title, '(', year, ')') as Name from Movie order by Name asc;";
$MovieRes = $db->query($exp);
if (!$MovieRes) {
	$result = "Sth is Wrong with the Movie table";
	die();
}
$exp = "select id, concat(first, ' ', last, '(', dob, ')') as Name from Actor order by last asc;";
$ActorRes = $db->query($exp);
if (!$ActorRes) {
	$result = "Sth is Wrong with the Actor table";
	die();
}
?>
<?php 
if (isset($_GET["submit"])) {
	$aid = $_GET["aid"];
	$mid = $_GET["mid"];
	$role = $_GET["role"];
	$exp = "select * from MovieActor where mid = '$mid' and aid = '$aid';";
	$rs = $db->query($exp);
	if ($rs->num_rows > 0) {
		$result = "It's already in the table!";
	} else {
		$exp = "insert into MovieActor values ('$mid', '$aid','$role');";
		$rs = $db->query($exp);
		if (!$rs) {
			$result = 'Add failed: ' . mysql_error();
		} else {
			$result = "Successfully added!<br>";
		}
	}
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
  	<style>
  		body {
		  font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
		  font-size: 14px;
		  line-height: 1.428571429;
		  color: #333333;
		  background-color: #ffffff;
		}
  	</style>
    <meta charset = "utf-8">
    <meta http-equiv = "X-UA-Compatible" content = "IE=edge">
    <meta name = "viewport" content = "width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Movie Database System</title>

    <!-- Bootstrap -->
    <link href = "css/bootstrap.min.css" rel = "stylesheet">
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
	<body>
	<!--
		<p> Input pages</p>
		<li> <a href = "./AddPeople.php">Add Actor/Director</a></li> 
		<li> <a href = "./AddMovie.php">Add Movie Info</a></li>
	-->
		<nav class = "navbar navbar-inverse" role = "navigation">
		    <div class = "container-fluid">
		    	<div class = "navbar-header">
		        	<a class = "navbar-brand" href = "homepage.html">Home</a>
		    	</div>
		    	<div>
			        <ul class = "nav navbar-nav">
			            <li> 
			            	<a href = "Search.php">Search</a>
			            </li>
			            <li class = "dropdown">
			                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
			                    Add New Content <b class="caret"></b>
			                </a>
			                <ul class="dropdown-menu">
			                	<li><a href = "AddMovie.php">Add movie info</a></li>
			                    <li><a href = "AddPeople.php">Add actor/director</a></li>			         
			                  	<li><a href = "AddMovieActor.php">Add actor in a movie</a></li>
			                  	<!--<li><a href = "AddComment.php">Comment on a movie</a></li> -->
			                  	<li><a href = "AddMovieDirector.php">Add director of a movie</a></li>
			                </ul>
			            </li>

			            <li clas = "dropdown">
			            	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			                    Movie/Actor Infomation <b class="caret"></b>
			                </a>
			                <ul class =" dropdown-menu">
			                	<li><a href = "ActorInfo.php">Actor Information</a></li>
			                	<li><a href = "MovieInfo.php">Movie Information</a></li>
			                </ul>

			            </li>
			        </ul>
		    	</div>
		    </div>
		</nav>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-1 main">
            <h3>Add new Movie Actor</h3>
            <form class = "form" role = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                <div class = "form-group">
                	<label >Movie Title</label>
                	<select class = "form-control" name = "mid">
                		<option value = "" selected>Select Below</option>
                		<?php 
                			while ($row = $MovieRes->fetch_assoc()) {
                		?>
                				<option value = <?php echo $row["id"]?>> <?php echo $row["Name"]?></option>
                		<?php } ?>
						
                	</select>
                </div>
                <div class = "form-group"> 
                	<label >Actor List</label>
                	<select class = "form-control" name = "aid">
                		<option value = "" selected>Select Below</option>
                		<?php 
                			while ($row = $ActorRes->fetch_assoc()) {
                		?>
                				<option value = <?php echo $row["id"]?>> <?php echo $row["Name"]?></option>
                		<?php } ?>
						
                	</select>

                </div>
                <div class = "form-group">
                	<label> Role </label>
                	<input type="text" class="form-control" placeholder="Role in the film" name="role" required>
                </div>                                                                                                                     
                <button type="submit" name = "submit" class="btn btn-default">Add!</button>
            </form>
            <h2><?php echo $result ?></h2>
        </div>


		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>

	</body>
</html>