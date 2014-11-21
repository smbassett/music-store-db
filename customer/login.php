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

<h1>Login to Allegro Music Store</h1>

<?php

	/* Establish Connection to Database */
    $username = "root";
	$password = "";
	$hostname = "localhost";
	$connection = new mysqli($hostname, $username, $password, "AMS");
	
	if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    } else printf("Connection Successful!!");

	/* If the page has been reached by method POST, that is, if SUBMIT
	was clicked, then check if the credentials are present in the 
	database*/
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		if (isset($_POST["submit"]) && $_POST["submit"] ==  "SUBMIT") {
		/*
		Check if customer is in the database
		*/
			$customer_nm = $_POST["cust_name"];
			$customer_pw = $_POST["cust_password"];
			$stmt = $connection->prepare("SELECT * FROM Customer WHERE cname=? and c_password=?");
			$stmt->bind_param("ss", $customer_nm, $customer_pw);
			$stmt->execute();
		
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);} 
			else{
				if ($stmt->fetch()){
					/*header('Location: index.php');*/
					echo "<b>Welcome ".$customer_nm."!</b>";
					echo '<META http-equiv="refresh" content="1; /music-store-db/index.php">';
					exit;
					}
				else 
					printf("Customer not in database.");}
	   
			$stmt->close();		
			}
		}
?>
<h2>Customer Login Menu</h2>
	<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<table border=0 cellpadding=0 cellspacing=0>
        <tr><td>Username</td><td><input type="text" size=30 name="cust_name"</td></tr>
        <tr><td>Password</td><td><input type="password" size=30 name="cust_password"</td></tr></tr>
		<tr><td></td><td><input type="submit" name="submit" border=0 value="SUBMIT"></td></tr>
		</table>
	</form>
	<a href="registration.php">Sign up</a> 

<?php include '../footer.php'; ?>
</body>

</html>

