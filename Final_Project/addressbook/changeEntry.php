<?php
session_start();
include 'connect.php';
doDB();

if (!$_POST)  {
	//haven't seen the selection form, so show it
	$display_block = "<h1>Select an Entry to Update</h1>";

	//get parts of records
	$get_list_sql = "SELECT id,
	                 CONCAT_WS(', ', last_name, first_name) AS display_name
	                 FROM master_name ORDER BY last_name, first_name";
	$get_list_res = mysqli_query($mysqli, $get_list_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_list_res) < 1) {
		//no records
		$display_block .= "<p class=\"subHeader\" style=\"padding-bottom:0px;\"><strong><em>Sorry, no records to select!</em></strong></br>
		<a href='addressBookMenu.html'><button>return to main menu</button></a></p>";

	} else {
		//has records, so get results and print in a form
		$display_block .= "
		<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" style='text-align:center;'>
		<p><label class=\"subHeader\" for=\"change_id\"><strong>Select a Record:</strong></label><br/>
		<select id=\"change_id\" name=\"change_id\" required=\"required\">
		<option value=\"\">-- Select One --</option>";

		while ($recs = mysqli_fetch_array($get_list_res)) {
			$id = $recs['id'];
			$display_name = stripslashes($recs['display_name']);
			$display_block .= "<option value=\"".$id."\">".$display_name."</option>";
		}

		$display_block .= "
		</select></p>
		<button type=\"submit\" name=\"submit\" value=\"change\">change selected entry</button>
		</form>
		<a href='addressBookMenu.html'><button>return to main menu</button></a>";
	}
	//free result
	mysqli_free_result($get_list_res);

} else if ($_POST) {
	//check for required fields
	if ($_POST['change_id'] == "")  {
		header("Location: changeEntry.php");
		exit;
	}

	//create safe version of ID
	$safe_id = mysqli_real_escape_string($mysqli, $_POST['change_id']);
	$_SESSION["id"]=$safe_id;
	$_SESSION["telephone"]="true";
	$_SESSION["email"]="true";
	$_SESSION["stats"]="true";

	//get master_info
	$get_master_sql = "SELECT first_name, last_name FROM master_name WHERE id = '".$safe_id."'";
	$get_master_res = mysqli_query($mysqli, $get_master_sql) or die(mysqli_error($mysqli));

	while ($name_info = mysqli_fetch_array($get_master_res)) {
		$display_fname = stripslashes($name_info['first_name']);
		$display_lname = stripslashes($name_info['last_name']);		
	}

	$display_block = "<h1>Record Update</h1>";
	$display_block.="<form method='post' action='change.php'>";
	$display_block.="<fieldset><legend><strong class='subHeader'>First/Last Names</strong></legend><br/>";
	$display_block.="<input type='text' name='first_name' size='20' maxlength='75' required='required' value='" . $display_fname . "'/>";
	$display_block.="<input type='text' name='last_name' size='30' maxlength='75' required='required' value='" . $display_lname . "'/></fieldset>";
	//free result
	mysqli_free_result($get_master_res);

	//get all tel
	$get_tel_sql = "SELECT phone_number, type_phone FROM telephone
	                WHERE master_id = '".$safe_id."'";
	$get_tel_res = mysqli_query($mysqli, $get_tel_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_tel_res) > 0) {

		while ($tel_info = mysqli_fetch_array($get_tel_res)) {
			$phone_number = stripslashes($tel_info['phone_number']);
			$tel_type = $tel_info['type_phone'];

			$display_block .="<fieldset><legend><strong class='subHeader'>Telephone Number</strong></legend><br/>";
			$display_block .="<input type='text' name='phone_number' size='30' maxlength='25' value='".$phone_number."'/>";
			if ($tel_type=="home"){
				$display_block .="<input type='radio' id='tel_type_h' name='tel_type' value='home' checked='checked' /><label for='tel_type_h'>Home</label>";
				$display_block .="<input type='radio' id='tel_type_w' name='tel_type' value='work' /><label for='tel_type_w'>Work</label>";
				$display_block .="<input type='radio' id='tel_type_o' name='tel_type' value='other' /><label for='tel_type_o'>Other</label>";
			}
			else if ($tel_type=="work") {
				$display_block .="<input type='radio' id='tel_type_h' name='tel_type' value='home'  /><label for='tel_type_h'>Home</label>";
				$display_block .="<input type='radio' id='tel_type_w' name='tel_type' value='work' checked='checked' /><label for='tel_type_w'>Work</label>";
				$display_block .="<input type='radio' id='tel_type_o' name='tel_type' value='other' /><label for='tel_type_o'>Other</label>";
			}
			else{
				$display_block .="<input type='radio' id='tel_type_h' name='tel_type' value='home' /><label for='tel_type_h'>Home</label>";
				$display_block .="<input type='radio' id='tel_type_w' name='tel_type' value='work' /><label for='tel_type_w'>Work</label>";
				$display_block .="<input type='radio' id='tel_type_o' name='tel_type' value='other' checked='checked' /><label for='tel_type_o'>Other</label>";
			}
		}
	$display_block .="</fieldset>";
	}
	else{
	$_SESSION["telephone"]='false';	
	$display_block .= <<<END_OF_BLOCK
	<fieldset>
	<legend><strong class='subHeader'>Telephone Number</strong></legend><br/>
	<input type="text" name="phone_number" size="30" maxlength="25" />
	<input type="radio" id="tel_type_h" name="tel_type" value="home" checked />
	    <label for="tel_type_h">Home</label>
	<input type="radio" id="tel_type_w" name="tel_type" value="work" />
	    <label for="tel_type_w">Work</label>
	<input type="radio" id="tel_type_o" name="tel_type" value="other" />
	    <label for="tel_type_o">Other</label>
	</fieldset>
END_OF_BLOCK;
	}
	//free result
	mysqli_free_result($get_tel_res);

	//get all email
	$get_email_sql = "SELECT email, type_email FROM email
	                  WHERE master_id = '".$safe_id."'";
	$get_email_res = mysqli_query($mysqli, $get_email_sql) or die(mysqli_error($mysqli));
	 if (mysqli_num_rows($get_email_res) > 0) {

		while ($email_info = mysqli_fetch_array($get_email_res)) {
			$email = stripslashes($email_info['email']);
			$email_type = $email_info['type_email'];
			$display_block .= "<fieldset><legend><strong class='subHeader'>Email Address</strong></legend><br/>";
			$display_block .= "<input type='email' name='email' size='30' maxlength='150' value='".$email."' />";

			if ($email_type=="home"){
				$display_block .= "<input type='radio' id='email_type_h' name='email_type' value='home' checked='checked' /><label for='email_type_h'>Home</label>";
	    		$display_block .= "<input type='radio' id='email_type_w' name='email_type' value='work' /><label for='email_type_w'>Work</label>";
	    		$display_block .= "<input type='radio' id='email_type_o' name='email_type' value='other' /><label for='email_type_o'>Other</label>";
		    } else if ($email_type=="work"){
				$display_block .= "<input type='radio' id='email_type_h' name='email_type' value='home'  /><label for='email_type_h'>Home</label>";
	    		$display_block .= "<input type='radio' id='email_type_w' name='email_type' value='work' checked='checked'/><label for='email_type_w'>Work</label>";
	    		$display_block .= "<input type='radio' id='email_type_o' name='email_type' value='other' /><label for='email_type_o'>Other</label>";
		    } else{
				$display_block .= "<input type='radio' id='email_type_h' name='email_type' value='home'  /><label for='email_type_h'>Home</label>";
	    		$display_block .= "<input type='radio' id='email_type_w' name='email_type' value='work' /><label for='email_type_w'>Work</label>";
	    		$display_block .= "<input type='radio' id='email_type_o' name='email_type' value='other' checked='checked'/><label for='email_type_o'>Other</label>";
		    }
		}

		$display_block .= "</fieldset>";
	}
	else{
	$_SESSION["email"]='false';
	$display_block .= '<fieldset><legend><strong class="subHeader">Email Address</strong></legend><br/><input type="email" name="email" size="30" maxlength="150" />	<input type="radio" id="email_type_h" name="email_type" value="home" checked />';
	$display_block.= '<label for="email_type_h">Home</label><input type="radio" id="email_type_w" name="email_type" value="work" /><label for="email_type_w">Work</label>';
	$display_block.='<input type="radio" id="email_type_o" name="email_type" value="other" /><label for="email_type_o">Other</label></fieldset>';
	}
	
	//free result
	mysqli_free_result($get_email_res);

	//get personal note
	$get_stats_sql = "SELECT * FROM stats
	                  WHERE master_id = '".$safe_id."'";
	$get_stats_res = mysqli_query($mysqli, $get_stats_sql) or die(mysqli_error($mysqli));

	if (mysqli_num_rows($get_stats_res) == 1) {
		while ($stat_info = mysqli_fetch_array($get_stats_res)) {
			$handicap = $stat_info['handicap'];
			$holes_in_one = $stat_info['holes_in_one'];
			$fav_course = $stat_info['fav_course'];

			$display_block .= <<<END_OF_BLOCK
			
			<fieldset><legend><strong class='subHeader'>Personal Statistics</strong></legend><br/>
			<label for='handicap'>Handicap:</label>
			<input type='text' name='handicap' size='2' maxlength='2' value= "$handicap" /></br></br>
			<label for='holes_in_one'>Holes in One:</label>
			<input type='text' name='holes_in_one' size='3' maxlength='3' value= "$holes_in_one" /></br></br>
			<label for='fav_course'>Favorite Course:</label>
			<input type='text' name='fav_course' size='30' maxlength='50' value= "$fav_course" /></br></br>
			</fieldset>
END_OF_BLOCK;
		}
		
	}	
	else{
	$_SESSION["stats"]='false';
	$display_block .= "<fieldset><legend><strong class='subHeader'>Personal Statistics</strong></legend><br/>";
	$display_block .= "<label for='handicap'>Handicap:</label>";
	$display_block .= "<input type='text' name='handicap' size='2' maxlength='2' /></br></br>";
	$display_block .= "<label for='holes_in_one'>Holes in One:</label>";
	$display_block .= "<input type='text' name='holes_in_one' size='3' maxlength='3' /></br></br>";
	$display_block .= "<label for='fav_course'>Favorite Course:</label>";
	$display_block .= "<input type='text' name='fav_course' size='30' maxlength='50' /></br></br></fieldset>";
	}
	
	//free result
	mysqli_free_result($get_stats_res);

	$display_block .= "<p style=\"margin:0px;\"><button type='submit' name='submitChange' id='submitChange' value='submitChange'>change entry</button>";
	$display_block .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href='addressBookMenu.html' style='background-color:transparent; margin:px;'><button type='button'>cancel and return to main menu</button></a></p></form>";
}
//close connection to MySQL
mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Records</title>
<link href="css/changeEntry.css" type="text/css" rel="stylesheet" />
</head>
<body>
	<article class="content col-6 col-s-12">
		<div>
<?php echo $display_block; ?>
</div>
</article>
<footer class="col-12 col-s-12">Copyright Player's Club &copy; 2018-2019</footer>
</body>
</html>