<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Registration</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>

</head>

<body>

<!-- Include header -->
<?php include '../header.php'; ?>

<h1>Register as an AMS Customer!</h1>

<?php

  // Include basic database operations
  include '../dbops.php';

// Connect to database
	$connection = connectToDatabase();
?>


<?php
  // Detect user action
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
      // Add customer    		  
      tryAddCustomer($_POST["new_username"], $_POST["new_password"], $_POST["new_fullname"], $_POST["new_address"], 
          $_POST["new_phone"], $connection);
    }

  }

?>

<h2>Please fill out the following to sign up.</h2>

<!-- Form for adding a new customer -->

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table border=0 cellpadding=0 cellspacing=0>
    <tr><td>Username</td><td><input type="text" size=20 name="new_username"></td></tr>
    <tr><td>Password</td><td><input type="password" size=20 name="new_password"</td></tr>
    	<tr><td>Full Name</td><td> <input type="text" size=20 name="new_fullname"></td></tr>
		<tr><td>Address</td><td> <input type="text" size=20 name="new_address"></td></tr>
		<tr><td>Phone</td><td> <input type="text" size=20 name="new_phone"></td></tr>
    <tr><td></td><td><input type="submit" name="submit" border=0 value="ADD"></td></tr>
  </table>
</form>

<br>
<a href="login.php" title="Login instead"><h2>&lt;&lt;Back</h2></a>

<?php include '../footer.php'; ?>
</body>
</html>


