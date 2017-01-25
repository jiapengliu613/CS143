<?php 
	if (isset($_GET["submit"])) {
		$name = $_GET["title"];
		$year = $_GET["year"];
		$company = $_GET["company"];
		$rating = $_GET["rating"];
		$genre = $_GET["genre"];
		include("./DBconnect.php");
		$exp = "select * from Movie where title = '$name' and year = '$year' and company = '$company';";
		$rs = $db->query($exp);
		$row = $rs->fetch_assoc();
		if ($rs->num_rows > 0) {
			$result = "That Movie is already in the database!<br>";
		} else {
			$exp = "select * from MaxMovieID;";
			$rs = $db->query($exp);
			$curId = $rs->fetch_assoc();
			foreach ($curId as $i) {
				//echo "$i";
				$newId = $i + 1;
			}
			//echo "$newId";
			$exp = "update  MaxMovieID set id = $newId;";
			$rs = $db->query($exp);
			if (!$rs) {
				$result = 'Query failed: ' . mysql_error();
				die();
			}
			$exp = "insert into Movie values ('$newId', '$name', '$year', '$rating', '$company');";
			$rs = $db->query($exp);
			if (!$rs) {
				$result = 'Failed to add into Movie table: ' . mysql_error();
				die();
			}
			foreach($genre as $i=>$value) {


				$exp = "insert into MovieGenre values ('$newId', '$value');";
				$rs = $db->query($exp);
				if (!$rs) {
					$result = 'Failed to add into MovieGenre table: ' . mysql_error();
					die();
				}
			}
			$result = "This movie is successfully added!<br>With id  $newId<br>";
			$db->close();

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
    <link href = "/style.css" rel = "stylesheet">
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
			                  	<!-- <li><a href = "AddComment.php">Comment on a movie</a></li> -->
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
            <h3>Add new Movie</h3>
            <form class = "form" role = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
                <div class="form-group">
                  <label for="title">Title:</label>
                  <input type="text" class="form-control" name = "title" placeholder = "Movie Name" required>
                </div>
                <div class="form-group">
                  <label for="year">Year</label>
                  <input type = "text" name = "year" class = "form-control" pattern = [0-9]{4} placeholder = "Year" title = "Four Digits Required" required >
                </div>
                <div class="form-group">
                  <label for="company">Company</label>
                  <input type="text" class="form-control" placeholder="Company Name" name="company" required>
                </div>
                <div class="form-group">
                    <label for="rating">MPAA Rating</label>
                    <select   class="form-control" name="rating">
                        <option value="G">G</option>
                        <option value="NC-17">NC-17</option>
                        <option value="PG">PG</option>
                        <option value="PG-13">PG-13</option>
                        <option value="R">R</option>
                        <option value="surrendere">surrendere</option>
                    </select>
                </div>
                <div class="form-group">
                   
                    <label for="genre">Genre</label>
      
	                    <input  type="checkbox"  name = "genre[]" value="Action">Action</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Adult">Adult</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Adventure">Adventure</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Animation">Animation</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Comedy">Comedy</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Crime">Crime</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Documentary">Documentary</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Drama">Drama</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Family">Family</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Fantasy">Fantasy</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Horror">Horror</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Musical">Musical</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Mystery">Mystery</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Romance">Romance</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Sci-Fi">Sci-Fi</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Short">Short</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Thriller">Thriller</input>
	                    <input  type="checkbox"  name = "genre[]"  value="War">War</input>
	                    <input  type="checkbox"  name = "genre[]"  value="Western">Western</input>
                   
                </div>
                <button type="submit" name = "submit" class="btn btn-default">Add!</button>
            </form>
            <h2><?php echo $result ?></h2>
        </div>
		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>

	</body>
</html>
