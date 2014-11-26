<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Login</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
</head>

<body>

<!-- Include header -->
<?php include '../header.php'; ?>
<?php include '../dbops.php'; ?>

<h1>Login to Allegro Music Store</h1>

<?php

//Connect to database:
$connection = connectToDatabase();

	/* If the page has been reached by method POST, that is, if SUBMIT
	was clicked, then check if the credentials are present in the 
	database*/
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		if (isset($_POST["submit"]) && $_POST["submit"] ==  "SUBMIT") {
		/*
		Check if customer is in the database
		*/
			$username = $_POST["username"];
			$c_password = $_POST["c_password"];

			$stmt = $connection->prepare("SELECT fullname, cid FROM Customer WHERE username=? and c_password=?");
			$stmt->bind_param("ss", $username, $c_password);
			$stmt->execute();
			
			$stmt->bind_result($col1, $col2);
		
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);
			} 
			else{

			// need to pass along the customer's cid to the store page,
			// so that items can be added to that customer's shopping cart.
			// will do this via a PHP 'session'.
				while ($stmt->fetch()){
					$_SESSION['cid'] = $col2;
					$_SESSION['cname'] = $col1;
					echo "<b>Welcome ".$col1."!</b>";
					echo '<META http-equiv="refresh" content="1; shop.php?' . SID . '">';
					exit;
				}
			}
			$stmt->close();		
		}

	}
		
?>
<h2>Customer Login Menu</h2>
	<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Username</td><td><input type="text" size=30 name="username"</td></tr>
        <tr><td>Password</td><td><input type="password" size=30 name="c_password"</td></tr></tr>
		<tr><td></td><td><input type="submit" name="submit" border=0 value="SUBMIT"></td></tr>
		</table>
	</form>
	<a href="registration.php" title="Sign up">Sign up</a> 

<br>
<a href="../index.php" title="Home"><h2>&lt;&lt;Back</h2></a>

<?php include '../footer.php'; ?>
</body>

</html>


