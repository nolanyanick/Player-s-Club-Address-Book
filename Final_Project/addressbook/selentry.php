<?php
include 'connect.php';
doDB();

if (!$_POST)  {
	//haven't seen the selection form, so show it
	$display_header = "<h1>Select an Entry</h1>";

	//get parts of records
	$get_list_sql = "SELECT id,
	                 CONCAT_WS(', ', last_name, first_name) AS display_name
	                 FROM master_name ORDER BY last_name, first_name";
	$get_list_res = mysqli_query($mysqli, $get_list_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_list_res) < 1) {
		//no records
		$display_block = "<p class=\"subHeader\"><strong><em>Sorry, no records to select!</em></strong></br>
								<a href='addressBookMenu.html'><button>return to main menu</button></a></p>";

	} else {
		//has records, so get results and print in a form
		$display_block = "
		<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
		<p><label class=\"subHeader\" for=\"sel_id\"><strong>Select a Record:</strong></label><br/>
		<select id=\"sel_id\" name=\"sel_id\" required=\"required\">
		<option value=\"\">-- Select One --</option>";

		while ($recs = mysqli_fetch_array($get_list_res)) {
			$id = $recs['id'];
			$display_name = stripslashes($recs['display_name']);
			$display_block .= "<option value=\"".$id."\">".$display_name."</option>";
		}

		$display_block .= "
		</select></p>
		<button type=\"submit\" name=\"submit\" value=\"view\">view selected entry</button>
		</form>
		<a href='addressBookMenu.html'><button>return to main menu</button></a>";
	}
	//free result
	mysqli_free_result($get_list_res);

} else if ($_POST) {
	//check for required fields
	if ($_POST['sel_id'] == "")  {
		header("Location: selentry.php");
		exit;
	}

	//create safe version of ID
	$safe_id = mysqli_real_escape_string($mysqli, $_POST['sel_id']);

	//get master_info
	$get_master_sql = "SELECT concat_ws(' ', first_name, last_name) as display_name
	                   FROM master_name WHERE id = '".$safe_id."'";
	$get_master_res = mysqli_query($mysqli, $get_master_sql) or die(mysqli_error($mysqli));

	while ($name_info = mysqli_fetch_array($get_master_res)) {
		$display_name = stripslashes($name_info['display_name']);
	}

	$display_header = "<h1>Showing Record for ".$display_name."</h1>";

	//free result
	mysqli_free_result($get_master_res);

	//get all phone numbers
	$get_phone_sql = "SELECT phone_number, type_phone FROM telephone
	                WHERE master_id = '".$safe_id."'";
	$get_phone_res = mysqli_query($mysqli, $get_phone_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_phone_res) > 0) {

		$display_block = "<p><span class='subHeader'><strong>Telephone:</strong></span><br/>
		<ul class='uList' style=' border-bottom: darkgreen solid 1.5px;'>";

		while ($phone_info = mysqli_fetch_array($get_phone_res)) {
			$phone_number = stripslashes($phone_info['phone_number']);
			$phone_type = $phone_info['type_phone'];

			if($phone_number == null) {
				$phone_number = 'N/A';
				$display_block .= "<li>$phone_number</br></li>";				
			}
			else {
				$display_block .= "<li>$phone_number</br> ($phone_type)</li>";
			}
		}

		$display_block .= "</ul></p>";
	}
	else {
		$display_block = "<p><span class='subHeader'><strong>Telephone:</strong></span><br/>
		<ul class='uList' style=' border-bottom: darkgreen solid 1.5px;'><li>N/A</li></ul></p>";
	}

	//free result
	mysqli_free_result($get_phone_res);

	//get all emails
	$get_email_sql = "SELECT email, type_email FROM email
	                  WHERE master_id = '".$safe_id."'";
	$get_email_res = mysqli_query($mysqli, $get_email_sql) or die(mysqli_error($mysqli));

	 if (mysqli_num_rows($get_email_res) > 0) {

		$display_block .= "<p><span class='subHeader'><strong>Email:</strong></span><br/>
		<ul class='uList' style=' border-bottom: darkgreen solid 1.5px;'>";

		while ($email_info = mysqli_fetch_array($get_email_res)) {
			$email = stripslashes($email_info['email']);
			$email_type = $email_info['type_email'];

			if($email == null) {
				$email = "N/A";
			$display_block .= "<li>$email</li>";
			}
			else {
			$display_block .= "<li>$email</br> ($email_type)</li>";
			}

		}

		$display_block .= "</ul></p>";
	}
	else {
		$display_block .= "<p><span class='subHeader'><strong>Email:</strong></span><br/>
		<ul class='uList' style=' border-bottom: darkgreen solid 1.5px;'><li>N/A</li></ul></p>";
	}


	//free result
	mysqli_free_result($get_email_res);

	//get personal stats
	$get_stats_sql = "SELECT * FROM stats
	                  WHERE master_id = '".$safe_id."'";
	$get_stats_res = mysqli_query($mysqli, $get_stats_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_stats_res) == 1){
		while ($stats_info = mysqli_fetch_array($get_stats_res)) {	
			$handicap = $stats_info['handicap'];
			$holes_in_one = $stats_info['holes_in_one'];
			$fav_course = $stats_info['fav_course'];				
		}

		//check for null on handicap
		if($handicap == null) {
			$display_block .= "<p><span class='subHeader'><strong>Personal Statistics:</strong>
			<ul class='uList' style='border-bottom: darkgreen solid 1.5px;'>
			<li>Handicap: N/A</li>";
		}
		else {
			$display_block .= "<p><span class='subHeader'><strong>Personal Statistics:</strong>
			<ul class='uList' style='border-bottom: darkgreen solid 1.5px;'>
			<li>Handicap: $handicap</li>";
		}

		//check for null on holes_in_one
		if($holes_in_one == null) {
			$display_block .= "<li>Holes in One: N/A</li>";
		}
		else {
			$display_block .= "<li>Holes in One: $holes_in_one</li>";
		}

		//check for null on av_course
		if($fav_course == null) {
			$display_block .= "<li>Favorite Course: N/A</li>
			</ul></p>";
		}
		else {
			$display_block .= "<li>Favorite Course: $fav_course</li>
			</ul></p>";
		}
	}
	else {
		$display_block .= "<p><span class='subHeader'><strong>Personal Statistics:</strong>
		<ul class='uList' style='border-bottom: darkgreen solid 1.5px;'>
		<li>Handicap: N/A</li>
		<li>Holes in One: N/A</li>
		<li>Favorite Course: N/A</li>
		</ul></p>";
	}

	//free result
	mysqli_free_result($get_stats_res);

	$display_block .= "<br/>
	<p style=\"text-align: center; margin-top:0px; padding-top:0px; font-size:16px; margin-bottom: 10px;\"><strong><a href=\"addentry.php?master_id=".$_POST['sel_id']."\">Add new player</a> | <a href=\"".$_SERVER['PHP_SELF']."\">Select another player</a> | <a href='addressBookMenu.html'>Main Menu</a></strong></p>";
}
//close connection to MySQL
mysqli_close($mysqli);
?>


<!DOCTYPE html>
<html>
<head>
<title>Select Records</title>
<link href="css/selentry.css" type="text/css" rel="stylesheet" />
</head>
<body>
<article class="content col-6 col-s-12">
	<?php echo $display_header?>
	<section class="menu">
<div>
<?php echo $display_block; ?>    

</div>
</section>
</article>
</body>
<footer class="col-12 col-s-12">Copyright Player's Club &copy; 2018-2019</footer>
</html>