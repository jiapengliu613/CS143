	<?php

		function validID($id) {
			if (empty($id)) {
				$result = "You should Choose an ID for that person!<br>";
				return false;
			} 
			return true;
		}
		function validGender($gender) {
			if ($gender == "Male" || $gender == "Female") {
				return true;
			}
			$result = "Invalid Gender<br>";
			return false;
		}

		function validDob($dob) {
			if (($dob[1] == 4 || $dob[1] == 6 || $dob[1] == 9 || $dob[1] == 11) && $dob[2] == 31) {
				$result = "That date of birth doesn't exist!<br>";
				return false;
			}
			if ($dob[1] == 2) {
				if ($dob[0] % 4 != 0 && $dob[2] >= 29) {
					$result = "That date of birth doesn't exist!<br>";
					return false;
				}
				if ($dob[0] % 100 == 0 && $dob[0] % 400 != 0 && $dob[2] >= 29) {
					$result = "That date of birth doesn't exist!<br>";
					return false;
				}
			}
			return true;
		}

		function validDod($dod) {
			$death = "$dod[0]"."$dod[1]"."$dod[2]";
			if ($death == "") {
				//echo "dod is empty<br>";
				return true;
			}
			//print_r($dod);
			if (($dod[1] == 4 || $dod[1] == 6 || $dod[1] == 9 || $dod[1] == 11) && $dod[2] == 31) {
				$result = "That date if death doesn't exist!<br>";
				return false;
			}
			if ($dod[1] == 2) {
				if ($dod[0] % 4 != 0 && $dod[2] >= 29) {
					$result = "That date if death doesn't exist!<br>";
					return false;
				}
				if ($dod[0] % 100 == 0 && $dod[0] % 400 != 0 && $dod[2] >= 29) {
					$result = "That date if death doesn't exist!<br>";
					return false;
				}
			}
			return true;
		}
	?>
	<?php

		if (isset($_GET["submit"])) {
			$id = $_GET["id"];
			$first = $_GET["firstname"];
			$last = $_GET["lastname"];
			$gender = $_GET["gender"];
			$dob = $_GET["dob"];
			$dod = $_GET["dod"];
			$birth = "$dob[0]-$dob[1]-$dob[2]";
			$death = "$dod[0]"."$dod[1]"."$dod[2]";
			//print_r($id);
			//$birth = date("Y-m-d", strtotime("$dob[0]-$dob[1]-$dob[2]"));
			//echo "$day<br>";
			//print_r(date_parse("$day"));
			//echo "<br>";
			//echo "$id "."$first "."$last "."$gender "."$dob<br>";
			// check wheather the input is valid
			if (!validID($id) || !validGender($gender) || !validDob($dob) || !validDod($dod)) {
				$result = $result."Invalid Info ! Please try again!<br>";
			} else {
				include("./DBconnect.php");
				
				$exp = "select dob from Actor where (first = '$first') and (last = '$last') 
						 and (sex = '$gender') union select dob from Director where (first = '$first') 
						 and (last = '$last');";

						 
				$rs = $db->query($exp);
				$row = $rs->fetch_assoc();
				
				//print_r($row);
				//print_r($row);
				if ($rs->num_rows > 0 && $row[dob] == "$dob[0]-$dob[1]-$dob[2]") {
					// This person is already in the table
					//$tmp = date_parse("$row");
					//print_r($tmp[year]);
					//$row = $rs->fetch_assoc();
						$result = "This person is already in the table! Can't add it again!";
				} else {
					$exp = "select * from MaxPersonID;";
					$rs = $db->query($exp);
					$curId = $rs->fetch_assoc();
					foreach ($curId as $i) {
						//echo "$i";
						$newId = $i + 1;
					}
					//echo "$newId";
					$exp = "update  MaxPersonID set id = $newId;";
					$rs = $db->query($exp);
					if (!$rs) {
						$result = 'Query failed: ' . mysql_error();
						die();
					}
					$number = count($id);
					//print_r($id);
					//echo "$number<br>";
					if ($number == 1) {
						if ($id[0] == "Actor") {
							//echo "is actor<br>";
							if ($death == "") {
								$exp = "insert into Actor
										values ('$newId', '$last', '$first', '$gender', '$birth', null);";
							} else {
								//$death = date("Y-m-d", strtotime("$dod[0]-$dod[1]-$dod[2]"));
								$exp = "insert into Actor
										values ('$newId', '$last', '$first', '$gender', '$birth', '$death');";
							}

						} else {
							if ($death == "") {
								$exp = "insert into Director
										values ('$newId', '$last', '$first', '$birth', null);";
							} else {
								
								//$death = date("Y-m-d", strtotime("$dod[0]-$dod[1]-$dod[2]"));
								$exp = "insert into Director
										values ('$newId', '$last', '$first', '$birth', '$death');";
							}
						}
						$rs = $db->query($exp);
						if (!$rs) {
							$result = 'Query failed: '.mysql_error();
						} else {
							$result = "This person is successfully added!<br>With id  $newId<br>";
							
						}

					} else {
						if ($death == "") {
							$exp1 = "insert into Actor
									 values ('$newId', '$last', '$first', '$gender', '$birth', null);";
							$exp2 = "insert into Director
									 values('$newId', '$last', '$first', '$birth', null);";
						} else {
							//$death = date("Y-m-d", strtotime("$dod[0]-$dod[1]-$dod[2]"));
							$exp1 = "insert into Actor values ('$newId', '$last', '$first', '$gender', '$birth', '$death');";
							$exp2 = "insert into Director values ('$newId', '$last', '$first', '$birth', '$death');";
						}
						$rs1 = $db->query($exp1);
						$rs2 = $db->query($exp2);
						if (!$rs1 || !$rs2) {
							$result = 'Query failed: '.mysql_error();
						} else {
							$result = "This person is successfully added !<br>With id : $newId<br>";
						}

					}

				}
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
			h2 {
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			  font-size: 14px;
			}
	  	</style>
	    <meta charset = "utf-8">
	    <meta http-equiv = "X-UA-Compatible" content = "IE=edge">
	    <meta name = "viewport" content = "width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    <title>Add Person</title>

	    <!-- Bootstrap -->
	    <link href = "css/bootstrap.min.css" rel = "stylesheet">
	    <link href = "./sytle.css" rel = "stylesheet">
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
            <h3>Add a new Actor/Director(You can choose both identities)</h3>
            <form class = "form" role = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
               <label class="checkbox-inline">
                    <input type = "checkbox" name = "id[]" value = "Actor">Actor
                </label>
                <label class="checkbox-inline">
                    <input type = "checkbox" name = "id[]" value = "Director">Director
                </label>
                <div class="form-group">
                  <label for="first_name">First Name</label>
                  <input type = "text" class = "form-control" name = "firstname" placeholder="First name"  
				   		 title = "Only Characters Allowed" pattern = "[A-Za-z]+" required>
                </div>
                <div class="form-group">
                  <label for="last_name">Last Name</label>
                  <input type = "text" name = "lastname" class = "form-control" placeholder="Last name" pattern = "[A-Za-z]+" 
			       title = "Only Characters Allowed" required>
                </div>
                <label class="radio-inline">
                    <input type = "radio" name = "gender" value = "Male" checked> Male
                </label>
                <label class="radio-inline">
                    <input type = "radio" name = "gender" value = "Female"> Female
                </label>
                <br>
                <div class="form-group">
                  	<label for="DOB">Date of Birth</label><br>
	                  	Year : <input type = "text" name = "dob[]" placeholder="Year" class = "form-control" 
	                  				pattern = "[0-9]{4}" title = "Four Digits Required" required> 
							
					 	Month : <input type = "text" name = "dob[]" placeholder="Month" class = "form-control" max = "12" 
					 					min = "1" pattern = "0?[1-9]|1[0-2]"  title = "Between 1 and 12" required> 
					  		
						Day : <input type = "text" name = "dob[]" placeholder="Day" class = "form-control" max = "31" min = "1" 
				   						pattern = "0?[1-9]|[1-2][0-9]|3[0-1]" title = "Between 1 and 31" required>  <br>
                </div>
                <div class="form-group">
                  <label for="DOD">Date of Death(Leave it blank if not applicable)</label><br>
                  	Year : <input type = "text" name = "dod[]" placeholder="Year" class = "form-control" pattern = "[0-9]{4}" 
							 title = "Four Digits Required"> 
					Month : <input type = "text" name = "dod[]" placeholder="Month" class = "form-control" max = "12" min = "1" 
							 pattern = "0?[1-9]|1[0-2]"  title = "Between 1 and 12"> 
					Day : <input type = "text" name = "dod[]" placeholder="Day" class = "form-control" max = "31" min = "1" 
		 			 pattern = "0?[1-9]|[1-2][0-9]|3[0-1]" title = "Between 1 and 31">  <br>  
                </div>
                <button type="submit" name = "submit" class="btn btn-default">Add!</button>
            </form>
            <h2><?php echo $result ?></h2>
        </div>
		<script src="https://code.jquery.com/jquery.js"></script>
      
      <script src="js/bootstrap.min.js"></script>
	</body>
</html>

