<?php
include 'connect.php';
doDB();

if (!$_POST)  {
	//haven't seen the selection form, so show it
	$display_block = "<h1>Select an Entry</h1>";

	//get parts of records
	$get_list_sql = "SELECT id,
	                 CONCAT_WS(', ', last_name, first_name) AS display_name
	                 FROM master_name ORDER BY last_name, first_name";
	$get_list_res = mysqli_query($mysqli, $get_list_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_list_res) < 1) {
		//no records
		$display_block .= "<p class=\"subHeader\"><strong><em>Sorry, no records to select!</em></strong></br>
								<a href='addressBookMenu.html'><button>return to main menu</button></a></p>";

	} else {
		//has records, so get results and print in a form
		$display_block .= "
		<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">
		<p><label for=\"sel_id\">Select a Record:</label><br/>
		<select id=\"sel_id\" name=\"sel_id\" required=\"required\">
		<option value=\"\">-- Select One --</option>";

		while ($recs = mysqli_fetch_array($get_list_res)) {
			$id = $recs['id'];
			$display_name = stripslashes($recs['display_name']);
			$display_block .= "<option value=\"".$id."\">".$display_name."</option>";
		}

		$display_block .= "
		</select></p>
		<button type=\"submit\" name=\"submit\" value=\"del\">delete selected entry</button>
		<a href='addressBookMenu.html'><button>return to main menu</button></a>
		</form>";
	}
	//free result
	mysqli_free_result($get_list_res);
} else if ($_POST) {
	//check for required fields
	if ($_POST['sel_id'] == "")  {
		header("Location: delentry.php");
		exit;
	}

    //create safe version of ID
    $safe_id = mysqli_real_escape_string($mysqli, $_POST['sel_id']);

	//issue queries
	$del_master_sql = "DELETE FROM master_name WHERE id = '".$safe_id."'";
	$del_master_res = mysqli_query($mysqli, $del_master_sql) or die(mysqli_error($mysqli));

	$del_tel_sql = "DELETE FROM telephone WHERE master_id = '".$safe_id."'";
	$del_tel_res = mysqli_query($mysqli, $del_tel_sql) or die(mysqli_error($mysqli));

	$del_email_sql = "DELETE FROM email WHERE master_id = '".$safe_id."'";
	$del_email_res = mysqli_query($mysqli, $del_email_sql) or die(mysqli_error($mysqli));

	$del_handicap_sql = "DELETE FROM stats WHERE master_id = '".$safe_id."'";
	$del_handicap_res = mysqli_query($mysqli, $del_handicap_sql) or die(mysqli_error($mysqli));

	$del_holes_in_one_sql = "DELETE FROM stats WHERE master_id = '".$safe_id."'";
	$del_holes_in_one_res = mysqli_query($mysqli, $del_holes_in_one_sql) or die(mysqli_error($mysqli));

	$del_fav_course_sql = "DELETE FROM stats WHERE master_id = '".$safe_id."'";
	$del_fav_course_res = mysqli_query($mysqli, $del_fav_course_sql) or die(mysqli_error($mysqli));

	mysqli_close($mysqli);

	$display_block = "<h1>Record(s) Deleted</h1><p  style='text-align:center;  padding-bottom:25px; padding-top:8px; margin-bottom:0;'>Would you like to
	<a href=\"".$_SERVER['PHP_SELF']."\"><strong>Delete another</strong></a>? | Return to the <a href='addressBookMenu.html'><strong>Main Menu</strong></a>?</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>My Records</title>
<link href="css/delentry.css" type="text/css" rel="stylesheet" />
</head>
<body>
<article class="content col-6 col-s-12">
<?php echo $display_block; ?>
</article>
<footer class="col-12 col-s-12">Copyright Player's Club &copy; 2018-2019</footer>
</body>
</html>
