<html>
<head>
<link rel='stylesheet' type='text/css' href='../Stylesheets/classic.css' />
<title>Sign In!</title>
<script type="text/javascript">
function validateForm() {
    var ck_username = /^[A-Za-z0-9_]{2,20}$/;
    var ck_password =  /^[A-Za-z0-9!@#$%^&*()_]{6,20}$/;
    var username = document.forms["signin"]["username"].value;
    var password = document.forms["signin"]["password"].value;
    if(!ck_username.test(username)) {
        alert("Username is not valid.");
        return false;
    } else if(!ck_password.test(password)) {
        alert("Password is not valid.");
        return false;
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

if((isSet($_GET['page']) || isSet($_POST['page'])) && (isSet($_GET['url']) || isSet($_POST['url']))) {
	$page = isSet($_GET['page']) ? $_GET['page'] : $_POST['page'];
	$url = isSet($_GET['url']) ? $_GET['url'] : $_POST['url'];
	$ck_location = '/((([A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[\w]*))?)/';
	$ck_page = '[\w.-]';
	
	if(preg_match($ck_page, $page) || preg_match($ck_location, $url)) {
		$pageR = $page;
		$urlR = $url;
	}
	else {
		$pageR = "";
		$urlR = "";
	}
} else {
	$pageR = "";
	$urlR = "";
}

if(isSet($_POST['username'])) {
    $ck_username = '/^[A-Za-z0-9_]{2,20}$/';
    $ck_password =  '/^[A-Za-z0-9!@#$%^&*()_]{6,20}$/';
    $username = $_POST['username'];
    $newPass = $_POST['password'];
    if(!preg_match($ck_username, $username) || !preg_match($ck_password, $newPass)) {
        //$mysqli = new mysqli("sql108.byethost7.com", "b7_15607535", "Password changed to protect the innocent", "b7_15607535_Users");
	    //$ip1 = $_SERVER['REMOTE_ADDR'];
	    //$ip2 = $_SERVER['HTTP_X_FORWARDED_FOR'];
		//$sql = "INSERT INTO `Hackers`(`'REMOTE_ADDR'`, `HTTP_X_FORWARDED_FOR`) VALUES ('$ip1', '$ip2')";
		//$mysqli->query($sql);
		//$mysqli->close();
        die("ERROR: INVALID FORMAT");
    }
	
	    $mysqli = new mysqli("sql108.byethost7.com", "b7_15607535", "Password changed to protect the innocent", "b7_15607535_Users");
	if($mysqli === false) {
		die("ERROR: COULD NOT CONNECT. " . mysqli_connect_error() . " [ERROR: SU00]");
	}
	$sql = "SELECT `Username`, `Password`, `League Name` FROM `Users` WHERE Username = '$username'";
	if($result = $mysqli->query($sql)) {
	    if($result->num_rows == 1) {
	        $row = $result->fetch_row();
	        $hashedPass = $row[1];
            if(crypt($newPass, $hashedPass) == $hashedPass) {
                session_start();
    			$_SESSION['Username'] = $username;
    			$_SESSION['loggedIn'] = 1;
    			if($mysqli->close()) {
    			    echo('<h1> Congratulations! You have signed in!</h1>');
    			    if($pageR != "") {
    			    	header('Location: ' . $urlR);
    			    } else {
    			    	header('Location: ../index.php');
    			    }
    			} else {
    			    die("<p>Sorry. Something went wrong. Please try again. [ERROR: SU02]</p>");
    			}
            	echo "<br />Password is a match = log user in";
            } else{
            	die ("<br />Password does not match = do not log in");
            }
			/**/
		} else {
			die("<p>Unknown username or password.</p>");
		}
	} else {
	    die("<p>Sorry. Something went wrong. Please try again. [ERROR: SU03]</p>");
	}
    //Keep for reference
    /*echo $hashedPass;
    if(crypt($inputPass, $hashedPass) == $hashedPass) {
    	echo "<br />Password is a match = log user in";
    } else{
    	echo "<br />Password does not match = do not log in";
    }*/

} else {
    echo "<p>Welcome to the Stratomatic Scorekeeper! Sign in here, or <a href='signup.php'>sign up</a>.</p>
          <form id='signin' method='POST' action='signin.php' onsubmit='return validateForm()'>
				Your username: <input title='Your username (20 char limit)' type='text' id='username' name='username' placeholder='Username' class='masterTooltip'></input>
				<br/><br/>
				Your password: <input title='Your password (20 char limit)' type='password' id='password' name='password' placeholder='Password' class='masterTooltip'></input>
				<br/><br/>
				<input type='hidden' value='$pageR' id='page' name='page' />
				<input type='hidden' value='$urlR' id='url' name='url' />
				<input type='submit'></input>
			</form>";
}
?>
</body>
</html>