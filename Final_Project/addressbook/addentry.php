<?php
include 'connect.php';

if (!$_POST) {
	//haven't seen the form, so show it
	$display_block = <<<END_OF_BLOCK
	<h1>Add an Entry</h1>
	<form method="post" action="$_SERVER[PHP_SELF]">

	<fieldset>
	<legend><strong class='subHeader'>First/Last Names</strong></legend><br/>
	<input type="text" name="first_name" size="20" maxlength="75" required="required" />
	<input type="text" name="last_name" size="30" maxlength="75" required="required" />
	</fieldset></br>

	<fieldset>	
	<legend><strong class='subHeader'>Telephone Number</strong></legend><br/>
	<input type="text" name="phone_number" size="30" maxlength="25" />
	<input type="radio" id="tel_type_h" name="type_phone" value="home" checked />
	    <label for="tel_type_h">Home</label>
	<input type="radio" id="tel_type_w" name="type_phone" value="work" />
	    <label for="tel_type_w">Work</label>
	<input type="radio" id="tel_type_o" name="type_phone" value="other" />
	    <label for="tel_type_o">Other</label>
	</fieldset></br>

	<fieldset>
	<legend><strong class='subHeader'>Email Address</strong></legend><br/>
	<input type="email" name="email" size="30" maxlength="150" />
	<input type="radio" id="email_type_h" name="type_email" value="home" checked />
	    <label for="email_type_h">Home</label>
	<input type="radio" id="email_type_w" name="type_email" value="work" />
	    <label for="email_type_w">Work</label>
	<input type="radio" id="email_type_o" name="type_email" value="other" />
	    <label for="email_type_o">Other</label>
	</fieldset></br>

	<fieldset>
	<legend><strong class='subHeader'>Personal Statistics</strong></legend></br>
	
	<label for="handicap">Handicap:</label>	
	<input type="text" id="handicap" name="handicap" size="2" maxlength="2" /></br></br>	
	
	<label for="holes_in_one">Holes in One:</label>	
	<input type="text" id="holes_in_one" name="holes_in_one" size="3" maxlength="3"/></br></br>
	
	<labe for="fav_course">Favorite Course:</label>
	<input type="text" id="fav_course" name="fav_course" size="50" maxlength="50"/></br></br>
	</fieldset>

	<p style='margin:0px;'><button type="submit" name="submit" value="send">add entry</button>
		<a href='addressBookMenu.html' style='background-color:transparent;'><button type='button'>cancel and return to main menu</button></a></p>
	</form>
END_OF_BLOCK;

} else if ($_POST) {
	//time to add to tables, so check for required fields
	if (($_POST['first_name'] == "") || ($_POST['last_name'] == "")) {
		header("Location: addentry.php");
		exit;
	}

	//connect to database
	doDB();

	//create clean versions of input strings
	$safe_first_name = mysqli_real_escape_string($mysqli, $_POST['first_name']);
	$safe_last_name = mysqli_real_escape_string($mysqli, $_POST['last_name']);
	$safe_phone_number = mysqli_real_escape_string($mysqli, $_POST['phone_number']);
	$safe_email = mysqli_real_escape_string($mysqli, $_POST['email']);
	$safe_handicap = mysqli_real_escape_string($mysqli, $_POST['handicap']);
	$safe_holes_in_one = mysqli_real_escape_string($mysqli, $_POST['holes_in_one']);
	$safe_fav_course = mysqli_real_escape_string($mysqli, $_POST['fav_course']);


	//add to master_name table
	$add_master_sql = "INSERT INTO master_name (date_added, date_modified, first_name, last_name)
                       VALUES (now(), now(), '".$safe_first_name."', '".$safe_last_name."')";
	$add_master_res = mysqli_query($mysqli, $add_master_sql) or die(mysqli_error($mysqli));

	//get master_id for use with other tables
	$master_id = mysqli_insert_id($mysqli);

	if ($_POST['phone_number']) {
		//something relevant, so add to telephone table
		$add_phone_sql = "INSERT INTO telephone (master_id, date_added, date_modified,
		                phone_number, type_phone)  
						VALUES ('".$master_id."', now(), now(),
		                '".$safe_phone_number."', '".$_POST['type_phone']."')";
		$add_phone_res = mysqli_query($mysqli, $add_phone_sql) or die(mysqli_error($mysqli));
	}

	if ($_POST['email']) {
		//something relevant, so add to email table
		$add_email_sql = "INSERT INTO email (master_id, date_added, date_modified,
		                  email, type_email)  
						  VALUES ('".$master_id."', now(), now(),
		                  '".$safe_email."', '".$_POST['type_email']."')";
		$add_email_res = mysqli_query($mysqli, $add_email_sql) or die(mysqli_error($mysqli));
	}


	if ($_POST['handicap'] || $_POST['holes_in_one'] || $_POST['fav_course']) {
		//something relevant, so add to stats table
		if($safe_handicap == "" && $safe_holes_in_one == "") {
			$add_stats_sql = "INSERT INTO stats (master_id, date_added, date_modified,
		                  handicap, holes_in_one, fav_course)  
						  VALUES ('".$master_id."', now(), now(),
		                  null, null, '".$safe_fav_course."')";
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}
		else if($safe_handicap == "") {
			$add_stats_sql = "INSERT INTO stats (master_id, date_added, date_modified,
		                  handicap, holes_in_one, fav_course)  
						  VALUES ('".$master_id."', now(), now(),
		                  null, '".$safe_holes_in_one."', '".$safe_fav_course."')";
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}
		else if($safe_holes_in_one == "") {
			$add_stats_sql = "INSERT INTO stats (master_id, date_added, date_modified,
		                  handicap, holes_in_one, fav_course)  
						  VALUES ('".$master_id."', now(), now(),
		                  '".$safe_handicap."', null, '".$safe_fav_course."')";
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}
		else {
			$add_stats_sql = "INSERT INTO stats (master_id, date_added, date_modified,
		                  handicap, holes_in_one, fav_course)  
						  VALUES ('".$master_id."', now(), now(),
		                  '".$safe_handicap."', '".$safe_holes_in_one."', '".$safe_fav_course."')";
			$add_stats_res = mysqli_query($mysqli, $add_stats_sql) or die(mysqli_error($mysqli));
		}		
	}
	
	mysqli_close($mysqli);
	$display_block = "
	<h1>".$safe_first_name." ".$safe_last_name." Added</h1>
	<p style='text-align:center; padding-bottom:25px; padding-top:8px; margin-bottom:0;'>The following entry has been added: <strong style='color:rgb(80, 78, 78);'>'".$safe_first_name." ".$safe_last_name."'</strong> Would you like to <a href=\"addentry.php\"><strong>Add another</strong></a>? | Would you like to return to the <a href='addressBookMenu.html'><strong>Main Menu</strong></a>?</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add an Entry</title>
<link href="css/addentry.css" type="text/css" rel="stylesheet" />
</head>
<body>

<article class="content col-9 col-s-12">
<?php echo $display_block; ?>
</article>
<footer class="col-12 col-s-12">Copyright Player's Club &copy; 2018-2019</footer>
</body>
</html>