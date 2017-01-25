<?php 
//if ($_SERVER["REQUEST_METHOD"] == "POST") {]
if ($_GET["id"]) {
	$id = $_GET["id"];
} else {
	$id = -1;
}
//} else {
//	$id = 3133;
//}
include("DBconnect.php");
// select MOvie id
$exp = "select * from Movie where id = $id;";
$MovieRes = $db->query($exp);
$MovieRow = $MovieRes->fetch_assoc();
//select director
$exp = "select concat(D.first, ' ', D.last) as Name 
		from MovieDirector MD, Movie M, Director D 
		where M.id = $id and M.id = MD.mid and MD.did = D.id;";
$DirectorRes = $db->query($exp);
$DirectorRow = $DirectorRes->fetch_assoc();
// select Genre
$exp = "select genre
		from MovieGenre 
		where mid = $id";
$GenreRes = $db->query($exp);


//select actors in this movie
$exp = "select concat(A.first, ' ', A.last) as name, role, aid from Actor A, MovieActor MA where mid = $id and A.id = MA.aid";
$RoleRes = $db->query($exp);
// average rating
$exp = "select avg(rating) as AvgRating, count(rating) as TotalNum from Review where mid = $id;";

$RatingRes = $db->query($exp);
$RatingRow = $RatingRes->fetch_assoc();
//echo $RatingRow["TotalNum"];
// show user comments
$exp = "select * from Review where mid = $id;";
$CommentRes = $db->query($exp);

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
		a:hover,
		a:focus {
		  color: #2a6496;
		  text-decoration: underline;
		}

		a:focus {
		  outline: thin dotted #333;
		  outline: 5px auto -webkit-focus-ring-color;
		  outline-offset: -2px;
		}
		.container {
		   padding-right: 15px;
		   padding-left: 15px;
		   margin-right: auto;
		   margin-left: auto;
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
			<h3><b> Movie Information Page </b></h3>
         	<hr>
 			<h4><b> Movie Information</b></h4>
 			<div class = "table-responsive">
	 			<table class = "table table-bordered table-condensed table-hover">
		 			<thread>
			 			<tr>
			 				<th>ID</th>
			 				<th>Title</th>
			 				<th>Year</th>
			 				<th>Rating</th>
			 				<th>Company</th>
			 				<th>Director</th>
			 				<th>Genre</th>
			 			</tr> 			 			
		 			</thread>
		 			<tr>
			 			<?php 
			 			foreach($MovieRow as $i) {
			 				if (!is_null($i)) {
			    					echo "<td>".$i."</td>";
			    				} else {
			    					echo "<td>Not Applicable</td>";
			    				}
			 			}

			 			?>
			 			
			 				<?php 
			 					if (count($DirectorRow) > 0) {
			 						echo "<td>".$DirectorRow["Name"]."</td>";
			 					}  else {
			 						echo "<td></td>";
			 					}
			 				?>

			 			
			 			
			 				<?php 
			 					if ($GenreRes->num_rows > 0) {
			 						echo "<td>";

			 						while ($GenreRow = $GenreRes->fetch_assoc()) {
										echo $GenreRow['genre']." ";
									}
			 						echo "</td>";
			 					} 
			 				?>
			 			
		 			</tr>
	 			</table>
	 		</div>
	 		<h4><b> Actors in this Movie:</b></h4>
	 		<div class = "table-responsive">
	 			<table class = "table table-bordered table-condensed table-hover">
	 				<thread>
	 					<tr>
	 						<th>Name</th>
	 						<th>Role</th>
	 					</tr>
	 				</thread>
	 				<?php
	 					while ($row = $RoleRes->fetch_assoc()) {
	 				?>
	 				<tr>
	 					<td><?php echo "<a href = \"ActorInfo.php?id=".$row["aid"]."\">".$row["name"]."</a>"; ?> </td>
	 					<td><?php echo $row["role"];?></td>
	 				</tr>
	 				<?php }?>
	 			</table>
	 		</div>
	 		<?php 
	 		echo "<h4><b>Rating</b></h4>";
	 		if (count($MovieRow) > 0) {
		 		if ($RatingRow["TotalNum"] > 0) {
		 			echo "<h5><b>".$RatingRow["AvgRating"]." / 5 based on".$RatingRow["TotalNum"]." Reviews</b></h5>";
		 		} else {
		 			echo "<h5><b>No rating of this Movie for now!</h5></b>";
		 		}
	 		}
	 		?>
	 		<br>
	 		<h4><b> Comments</b></h4>
	 		<?php 
	 		$count = 0;
	 		if (count($MovieRow) > 0) {
	 			while ($row = $CommentRes->fetch_assoc()) {
	 				echo $row["name"]." rates this moive with score ".$row["rating"]." and left 
	 					a review at ".$row["time"]."<br>";
	 				echo "comment:<br>";
	 				echo $row["comment"];
	 				echo "<br>";
	 				$count++;

	 			}
	 			if ($count == 0) {
	 				//echo "No Comments for now!";
	 				echo "<h5><b><a href = \"AddComment.php?id=".$id."\">Be the first to comment this movie!</a></b></h5>";
	 			} else {
	 				echo "<h5><b><a href = \"AddComment.php?id=".$id."\">Add you comment!</a></b></h5>";
	 			}
	 		}
	 		?>






	 		<hr>
			<h4><b>Search Key Words:</b></h4>
            <form class = "form" role = "form" action="./Search.php?kewords=" method="GET">
            	<input type="text" id="search_input" class="form-control" placeholder="Keywords of The Actor" name="keywords"><br>
              	<button type="submit" class="btn btn-default">Search</button>
            </form>
		</div>


		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>

	</body>
</html>