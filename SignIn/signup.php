<html>
<head>
<link rel='stylesheet' type='text/css' href='../Stylesheets/classic.css' />
<title>Sign Up!</title>
<script type="text/javascript">
function validateForm() {
    var ck_name = /^[A-Za-z0-9 ]{3,35}$/;
    var ck_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    var ck_username = /^[A-Za-z0-9_]{2,20}$/;
    var ck_password =  /^[A-Za-z0-9!@#$%^&*()_]{6,20}$/;
    var ck_lname = /^[A-Za-z0-9_ ]{2,35}$/;
    var name = document.forms["signup"]["name"].value;
    var email = document.forms["signup"]["email"].value;
    var username = document.forms["signup"]["username"].value;
    var password = document.forms["signup"]["password"].value;
    var compassword = document.forms["signup"]["compassword"].value;
    var lname = document.forms["signup"]["lname"].value;
    if (!ck_name.test(name)) {
        alert("Name is not valid.");
        return false;
    } else if(!ck_email.test(email)) {
        alert("Email is not valid.");
        return false;
    } else if(!ck_username.test(username)) {
        alert("Username is not valid.");
        return false;
    } else if(!ck_password.test(password) || password != compassword) {
        alert("Password is not valid.");
        return false;
    } else if(!ck_lname.test(lname)) {
        alert("League Name is not valid.");
        return false;
    } else {
        return true;
    }
}
</script>
</head>
<body>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="../Scripts/hoverTooltip.js"></script>
<link rel="stylesheet" type="text/css" href="../Stylesheets/hoverTooltip.css">

<?php
function cryptPass($input, $rounds = 12){ //Sequence - cryptPass, save hash in db, crypt(input, hash) == hash
	$salt = "";
	$saltChars = array_merge(range('A','Z'), range('a','z'), range(0,9));
	for($i = 0; $i < 22; $i++){
		$salt .= $saltChars[array_rand($saltChars)];
	}
	return crypt($input, sprintf('$2y$%02d$', $rounds) . $salt);
}

if(isSet($_POST['username'])) {
    $ck_name = '/^[A-Za-z0-9 ]{3,35}$/';
    $ck_email = '/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i';
    $ck_username = '/^[A-Za-z0-9_]{2,20}$/';
    $ck_password =  '/^[A-Za-z0-9!@#$%^&*()_]{6,20}$/';
    $ck_lname = '/^[A-Za-z0-9_ ]{2,35}$/';
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $newPass = $_POST['password'];
    $lname = $_POST['lname'];
    if(!preg_match($ck_name, $name) || !preg_match($ck_email, $email) || !preg_match($ck_username, $username) || !preg_match($ck_password, $newPass) || !preg_match($ck_lname, $lname)) {
	    $mysqli = new mysqli("sql108.byethost7.com", "b7_15607535", "Password changed to protect the innocent", "b7_15607535_Users");
	    //$ip1 = $_SERVER['REMOTE_ADDR'];
	    //$ip2 = $_SERVER['HTTP_X_FORWARDED_FOR'];
		//$sql = "INSERT INTO `Hackers`(`'REMOTE_ADDR'`, `HTTP_X_FORWARDED_FOR`) VALUES ('$ip1', '$ip2')";
		//$mysqli->query($sql);
		//$mysqli->close();
        die("ERROR: Invalid format");
    }
    $hashedPass = cryptPass($newPass);
	    $mysqli = new mysqli("sql108.byethost7.com", "b7_15607535", "Password changed to protect the innocent", "b7_15607535_Users");
	if($mysqli === false) {
		die("ERROR: COULD NOT CONNECT. " . mysqli_connect_error() . " [ERROR: SU00]");
	}
	$sql = "SELECT `Username` FROM `Users` WHERE Username = '$username'";
	if($result = $mysqli->query($sql)) {
	    if($result->num_rows == 0) {
			session_start();
			$sql = "INSERT INTO `Users`(`Name`, `Password`, `Username`, `Email`, `League Name`) VALUES ('$name', '$hashedPass', '$username', '$email', '$lname')";
			if(!$mysqli->query($sql)) {
			    echo("<p>Sorry. Something went wrong. Please try again. [ERROR: SU01]</p>");
			}
			$_SESSION['Username'] = $username;
			$_SESSION['loggedIn'] = 1;
			if($mysqli->close()) {
			    $dir = "../Leagues/$username";
			    mkdir($dir, 0777);
			    simplexml_load_string("<?xml version='1.0'?><league name='$lname'><teams></teams></league>")->asXML($dir . "/league.xml");
			    simplexml_load_string("<?xml version='1.0'?><schedule></schedule>")->asXML($dir . "/schedule.xml");
			    chmod($dir . "/league.xml", 0777);
			    chmod($dir . "/schedule.xml", 0777);
			    chmod($dir, 0777);
			    echo('<h1> Congratulations! You have signed up!</h1>');
			    header('Location: ../index.php');
			} else {
			    die("<p>Sorry. Something went wrong. Please try again. [ERROR: SU02]</p>");
			}
		} else {
			echo("<p>Username already used.</p>");
		}
	} else {
	    echo("<p>Sorry. Something went wrong. Please try again. [ERROR: SU03]</p>");
	}
    //Keep for reference
    /*echo $hashedPass;
    if(crypt($inputPass, $hashedPass) == $hashedPass) {
    	echo "<br />Password is a match = log user in";
    } else{
    	echo "<br />Password does not match = do not log in";
    }*/

} else {
    echo "<p>Welcome to the Stratomatic Scorekeeper! Sign up here, or <a href='signin.php'>sign in</a>.</p>
          <form id='signup' method='POST' action='signup.php' onsubmit='return validateForm()'>
				Your name: <input title='Your name (35 char limit)' type='text' id='name' name='name' placeholder='Name' class='masterTooltip'></input>
				<br/><br/>
				Your username: <input title='Your username (20 char limit)' type='text' id='username' name='username' placeholder='Username' class='masterTooltip'></input>
				<br/><br/>
				Your password: <input title='Your password (20 char limit)' type='password' id='password' name='password' placeholder='Password' class='masterTooltip'></input>
				<br/><br/>
				Comfirm password: <input title='Comfirm password (20 char limit)' type='password' id='compassword' name='compassword' placeholder='Password' class='masterTooltip'></input>
				<br/><br/>
				Your email: <input title='Your email (youremail@example.com)' type='text' id='email' name='email' placeholder='Email' class='masterTooltip'></input>
				<br/><br/>
				Your league name: <input title='Your league name for Stratomatic (35 char limit)' type='text' id='lname' name='lname' placeholder='League Name' class='masterTooltip'></input>
				<br/><br/>
				<input type='submit'></input>
			</form>";
}
?>
</body>
</html>