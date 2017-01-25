<?php 

//echo $db->connect_errno;
//echo "1234";
//if ($_SERVER["REQUEST_METHOD"] == "GET") {
if ($_GET["id"]) {
	$id = $_GET["id"];
} else {
	$id = 0;
}
//} else {
//	$id = 7097;
//}


include("DBconnect.php");
$exp = "select * from Actor where id =".$id.";";
$ActorRes = $db->query($exp);
$ActorRow = $ActorRes->fetch_assoc();
##$exp = "select M.id as id, MA.role as Role, M.title as MovieTitle from Movie M, MovieActor MA where MA.aid = '$id' and M.id = MA.mid;";
$exp = "select M.title, M.id, MA.role from Movie M, MovieActor MA where  MA.mid = M.id and MA.aid = $id;";

$MovieRes = $db->query($exp);

	
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
    <!--
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
			<h3><b> Actor Information Page :</b></h3>
         	<hr>
 			<h4><b>Actor Information</b></h4>
 			<div class = "table-responsive">
	 			<table class = "table table-bordered table-condensed table-hover">
	 				
	 				<thread>
	 					<tr>
	 						<th>ID</th>
	 						<th align="center">Last Name</th>
	 						<th align="center">First Name</th>
	 						<th align="center">Gender</th>
	 						<th align="center">Date of Birth</th>
	 						<th align="center">Date of Death</th>
	 					</tr>
	 				</thread>
	 					<?php
	 						echo "<tr>";
	 						
			    			foreach($ActorRow as $i) {
			    				if (!is_null($i)) {
			    					echo "<td>".$i."</td>";
			    				} else {
			    					echo "<td>Not Applicable</td>";
			    				}
			    			}
			    			echo "</tr>"; 
	 					?>
	 				
	 			</table>
 			</div>
 			<h4><b>Movie Information</b></h4>
 			<div class = "table-responsive"> 
 				<table class = "table table-bordered table-condensed table-hover">
	 				<thread>
	 					<tr>
	 						
	 						<th align="center">Role in the Movie</th>
	 						
	 						<th align="center">Movie Title</th>
	 					</tr>
	 				</thread>
	 					<?php
	 						//echo "dui";
	 						//$MovieRes = 0;
	 						while($row = $MovieRes->fetch_assoc()) {
	 					?>
						<tr>
							<td><?php echo $row["role"]; ?> </td>
							<td><a href = "./MovieInfo.php?id=<?php echo $row["id"]?>"><?php echo $row["title"]?></a></td>
						</tr>	
							
						<?php  } ?>
	 					
	 				
	 			</table>

 			</div>
 			<hr>
			<h4><b>Search Key Words:</b></h4>
            <form class = "form" role = "form" action="./Search.php" method="GET">
            	<input type="text" id="search_input" class="form-control" placeholder="Keywords of The Actor" name="keywords"><br>
              	<button type="submit" class="btn btn-default">Search</button>
            </form>
        </div>


		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>

	</body>
</html>
