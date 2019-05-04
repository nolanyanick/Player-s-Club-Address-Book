<?php
session_start();
//connect to database
include 'connect.php';

//time to update tables, so check for required fields
	if (($_POST['first_name'] == "") || ($_POST['last_name'] == "")) {
		header("Location: changeEntry.php");
		exit;
	}
	//connect to database
	doDB();
	//create clean versions of input strings
	$master_id=$_SESSION["id"];
	$safe_first_name = mysqli_real_escape_string($mysqli, $_POST['first_name']);
	$safe_last_name = mysqli_real_escape_string($mysqli, $_POST['last_name']);
	$safe_phone_number = mysqli_real_escape_string($mysqli, $_POST['phone_number']);
	$safe_email = mysqli_real_escape_string($mysqli, $_POST['email']);
	$safe_handicap = mysqli_real_escape_string($mysqli, $_POST['handicap']);
	if($safe_handicap == '/') {
		$safe_handicap = null;
	}
	$safe_holes_in_one = mysqli_real_escape_string($mysqli, $_POST['holes_in_one']);
	if($safe_holes_in_one == '/') {
		$safe_holes_in_one = null;
	}
	$safe_fav_course = mysqli_real_escape_string($mysqli, $_POST['fav_course']);
	
	//update master_name table
	$add_master_sql = "UPDATE master_name SET date_added=now(),date_modified=now(),first_name='".$safe_first_name."',last_name='". $safe_last_name."'".
	                   "WHERE id=".$master_id;
	$add_master_res = mysqli_query($mysqli, $add_master_sql) or die(mysqli_error($mysqli));

	if ($_SESSION["telephone"]=="true"){
		//update telephone table
		$add_phone_sql = "UPDATE telephone SET master_id=".$master_id.", date_added=now(),date_modified=now()".
		                ",phone_number='".$safe_phone_number."',type_phone='".$_POST['tel_type']."'".
		                 "WHERE master_id=".$master_id;
		$add_phone_res = mysqli_query($mysqli, $add_phone_sql) or die(mysqli_error($mysqli));
	   } else if ($_POST['phone_number']){
	   // add new record to telephone table
		$add_phone_sql = "INSERT INTO telephone (master_id, date_added, date_modified,
		                phone_number, type_phone)  VALUES ('".$master_id."', now(), now(),
		                '".$safe_phone_number."', '".$_POST['tel_type']."')";
		$add_phone_res = mysqli_query($mysqli, $add_phone_sql) or die(mysqli_error($mysqli));
	   }

	if ($_SESSION["email"]=="true"){
		//update email table
		$add_email_sql = "UPDATE  email  SET master_id=".$master_id.", date_added=now(),date_modified=now()".
		                ",email='".$safe_email."',type_email='".$_POST['email_type']."'".
		                 "WHERE master_id=".$master_id;
		$add_email_res = mysqli_query($mysqli, $add_email_sql) or die(mysqli_error($mysqli));
	}else if ($_POST['email']) {
	// add new record to email table
		$add_email_sql = "INSERT INTO email (master_id, date_added, date_modified,
		                  email, type_email)  VALUES ('".$master_id."', now(), now(),
		                  '".$safe_email."', '".$_POST['email_type']."')";
		$add_email_res = mysqli_query($mysqli, $add_email_sql) or die(mysqli_error($mysqli));
	}
	

	if ($_SESSION["stats"]=="true"){
		//update notes table & check for null values
		if($safe_handicap == null && $safe_holes_in_one != null) {
			$add_stats_sql = "UPDATE stats SET master_id=".$master_id.", date_added=now(),date_modified=now()".
			",handicap=null, holes_in_one='".$safe_holes_in_one."', fav_course='".$safe_fav_course."'".
			"WHERE master_id=".$master_id;
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}
		else if($safe_holes_in_one == null && $safe_handicap != null) {
			$add_stats_sql = "UPDATE stats SET master_id=".$master_id.", date_added=now(),date_modified=now()".
			",handicap='".$safe_handicap."', holes_in_one=null, fav_course='".$safe_fav_course."'".
			"WHERE master_id=".$master_id;
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}
		else if($safe_handicap == null && $safe_holes_in_one == null) {
			$add_stats_sql = "UPDATE stats SET master_id=".$master_id.", date_added=now(),date_modified=now()".
			",handicap=null, holes_in_one=null, fav_course='".$safe_fav_course."'".
			"WHERE master_id=".$master_id;
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}
		else{
			$add_stats_sql = "UPDATE stats SET master_id=".$master_id.", date_added=now(),date_modified=now()".
			",handicap='".$safe_handicap."', holes_in_one='".$safe_holes_in_one."', fav_course='".$safe_fav_course."'".
			"WHERE master_id=".$master_id;
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}

	} else 	if ($_POST['handicap'] || $_POST['holes_in_one'] || $_POST['fav_course']) {
	  // add new record to notes table
		$add_stats_sql = "INSERT INTO stats (master_id, date_added, date_modified,
		                  handicap, holes_in_one, fav_course)  VALUES ('".$master_id."', now(), now(), '".$safe_handicap."', '".$safe_holes_in_one."', '".$safe_fav_course."')";
		$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
	}

	mysqli_close($mysqli);
	$display_block = "<p style='text-align:center; padding-bottom:25px; padding-top:8px;'>Your entry has been changed...Would you like to return to the <strong><a href='addressBookMenu.html'>Main Menu</a></strong>? | <strong><a href='changeEntry.php'>Change another record</a></strong>?</p>";

?>
<!DOCTYPE html>
<html>
<head>
<title>Player Update</title>
<link href="css/change.css" type="text/css" rel="stylesheet" />
</head>
<body>
<article class="content col-6 col-s-12">
<h1>Record(s) Updated</h1>
<?php echo $display_block; ?>
</article>
<footer class="col-12 col-s-12">Copyright Player's Club &copy; 2018-2019</footer>
</body>
</html>