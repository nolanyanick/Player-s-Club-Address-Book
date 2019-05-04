<?php
if (($_POST['username']=="") || ($_POST['password']=="")) {
    header("Location: userLogin.html");
    exit;
}
$display_block = "";

//production server
$mysqli = mysqli_connect("localhost", "lisabalbach_yanickn", "CIT190102", "lisabalbach_Yanick");

//test
//$mysqli = mysqli_connect("localhost", "root", "", "players") or die(mysql_error());

$safe_username = mysqli_real_escape_string($mysqli, $_POST['username']);
$safe_password = mysqli_real_escape_string($mysqli, $_POST['password']);

$sql = "SELECT f_name, l_name FROM auth_users 
        WHERE username = '".$safe_username."' AND password = '".$safe_password."'";

$result = mysqli_query($mysqli, $sql) or die(mysqli_error($mysqli));           

if (mysqli_num_rows($result) == 1) {
    header("Location: ../addressbook/addressBookMenu.html");
    $display_block = "";            
    exit;
}
else {
    
    $display_block = "<p style='text-align:center;color:red;font-size:1em;'>username or password are not valid</p>";        
}

mysqli_close($mysqli);
?>
<!DOCTYPE html>
<html>
<head>
<title>User Login Form</title>
<link href="css/login.css" rel="stylesheet" type="text/css">
</head>
<body >
<div class="col-3 center">
<form method="post" action="userlogin.php">
<p><label for="username" style="color:darkgreen;">Username:</label><br/>
<input type="text" id="username" name="username" autofocus /></p>
<p><label for="password" style="color:darkgreen;">Password:</label><br/>
<input type="password" id="password" name="password" /></p>

<?php echo $display_block?>

<button type="submit" name="submit" id="login" value="submit">Login</button>
</form>
</div>
</body>
<footer>
    <small>Copyright Player's Club &copy; 2018-2019</small>
</footer>
</html>