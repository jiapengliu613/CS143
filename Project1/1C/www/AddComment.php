<?php 
include("./DBconnect.php");
if($_GET["id"]) {
	$id = $_GET["id"];
} else {
	$id = -1;
}
include("DBconnect.php");
$exp = "select id, concat(title, '(', year, ')') as Name from Movie where id = $id;";
$MovieRes = $db->query($exp);
$row = $MovieRes->fetch_assoc();

?>

<?php
if (isset($_GET["submit"])) {
	include("./DBconnect.php");
	$name = $_GET["name"];
	$mid = $_GET["mid"];
	$rating = $_GET["rating"];
	$comment = $_GET["comment"];
	//echo $name.$mid.$rating.$comment."<br>";
	$exp = "insert into Review values ('$name', CURRENT_TIMESTAMP(), '$mid', '$rating', '$comment');";
	//echo $exp;
	$rs = $db->query($exp);
	if (!$rs) {
		$result = "Comment Add Failed".mysql_error();
	} else {
		$result = "Comment successfully added!";
	}
}
?>

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
			<form class = "form" role = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
				<h3>Add your comment here</h3>
				<div class= "form-group">
					<label >Movie Title</label>
					<select class = "form-control" name = "mid">
						<option value = <?php echo $row["id"]?>> <?php echo $row["Name"]?></option>
					</select>
				</div>
				<div class= "form-group">
					<label >Movie ID</label>
					<select class = "form-control" name = "id">
						<option value = <?php echo $row["id"]?>> <?php echo $row["id"]?></option>
					</select>
				</div>

				<div class = "form-group">
					<label>Your Name</label>
					<input type="text" class="form-control" value="Mr.Anonymous" name="name" required>
				</div>
				<div class = "form-group">
					<label>Rating</label>
					<select class = "form-control" name = "rating" sytle="width:100px">
						<option value = 5>5 - Perfect !</option>
						<option value = 4>4 - Excellent !</option>
						<option value = 3>3 - Good !</option>
						<option value = 2>2-Boring !</option>
						<option value = 1>1-Bad !</option>
					</select>
				</div>
				<div class="form-froup">
					<label>Write your comment</label>
                  	<textarea class="form-control" name="comment" rows="5"  placeholder="no more than 500 characters" required>
                  	</textarea><br> 

                </div>
                <button type="submit" name = "submit" class="btn btn-default">Submit</button>
			</form>
			<h2><?php echo $result ?></h2>
			<h5><b><a href = "MovieInfo.php?id=<?php echo $mid;?>">Return to the MovieInfo page</b></h5>
		</div>

		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>

	</body>
</html>