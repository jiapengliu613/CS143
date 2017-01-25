<?php 
	
		if ($_GET["keywords"]) {
			$keywords = $_GET["keywords"];
			$keys = explode(" ", $_GET["keywords"]);
			$len = count($keys);
		} else {
			$len = 0;
		}
		
	
	include("DBconnect.php");
	//$keys[0] = 'Tom';
	
	//echo $len;
	if ($len > 0) {
	//echo $len;
	//******select Actor from the keys
	
	//$rs = $db->query($exp);
	
		$condition = "A.Name like '%".$keys[0]."%'";
		//echo $condition;
		for ($i = 1; $i < $len; $i++) {
			$condition = $condition." and "." A.Name like "."'%".$keys[$i]."%'";
		}
		$exp = " select * 
				 from (select concat_ws(' ', first, last) as Name, dob, id from Actor) A 
				 where ".$condition.";";
		//echo $exp; 
		//$exp = "select * from Actor where id = 10;";
		$ActorRes = $db->query($exp);
		if (!$ActorRes) {
			echo "fail";
			$result = "Search Failed".mysql_error();
			die();
		}
		//print $ActorRes;
	// select ****** movie
		$condition = "title like '%".$keys[0]."%'";
		for ($i = 1; $i < $len; $i++) {
			$condition = $condition." and "." title like "."'%".$keys[$i]."%'";
		}
		$exp = " select * 
				 from  Movie
				 where ".$condition.";";
		$MovieRes = $db->query($exp);
		if (!$MovieRes) {
			echo "fail";
			$result = "Search Failed".mysql_error();
			die();
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
			<h3><b> Searching Page :</b></h3>
			<hr>
          	
          	<form class = "form" role = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
          		<label for="search_input">Search here</label>
          		<input type="text" id="search_input" class="form-control" placeholder="Search..." name="keywords"><br>
                <input type="submit" value="Search!" class="btn btn-default" style="margin-bottom:10px">
          	</form>
          	<h4><b>matching Actors are:</b></h4>
          	<div class='table-responsive'> 
          		<table class='table table-bordered table-condensed table-hover'>
	          		<thead>
	          			<tr>
	          				<th>Name</th>
	          				<th>Date of Birth</th>

	          			</tr>
	          			<?php 
	          			if ($ActorRes->num_rows != 0) {
	          				while($row = $ActorRes->fetch_assoc()) {		
	          			
		 				?>
		 				<tr>
							
							<td><a href = "./ActorInfo.php?id=<?php echo $row["id"]?>"><?php echo $row["Name"]?></a></td>
							<td><?php echo $row["dob"]; ?> </td>
						</tr>			
						<?php  }} ?>
	          		</thead>
          		</table>
          		
          		
          	</div>
          	<hr> <h4><b>matching Movies are:</b></h4>
          	<div class='table-responsive'> 
	          	<table class='table table-bordered table-condensed table-hover'>
	          		<thead>
	          			<tr>
	          				<th>Title</th>
	          				<th>Year</th>
	          			</tr>
	          		</thead>
	          		<?php 
	          		if ($MovieRes->num_rows != 0) {
	          			while($row = $MovieRes->fetch_assoc()) {		
	          		
	 				?>
	 				<tr>	
						<td><a href = "./MovieInfo.php?id=<?php echo $row["id"]?>"><?php echo $row["title"]?></a></td>
						<td><?php echo $row["year"]; ?> </td>
					</tr>			
					<?php  } }?>
	          	</table>
	        </div>
		</div>
		


		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>

	</body>
</html>