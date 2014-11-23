<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Registration</title>
<link href="style.css" rel="stylesheet" type="text/css">

<!--
    Javascript to submit a title_id as a POST form, used with the "delete" links
-->
<script>
function formSubmit(CustId){
    'use strict';
    if (confirm('Are you sure you want to delete this customer?')) {
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('delete');
      form.cid.value = CustId;
      // Post this form
      form.submit();
    }
}
</script>
</head>

<body>

<!-- Include header -->
<?php include 'header.php'; ?>

<h1>Manage Customer Registration</h1>

<?php

  // Include basic database operations
  include 'dbops.php';

  // Connect to AMS database
  $username = "root";
  $password = "";
  $hostname = "localhost";

  $connection = new mysqli($hostname, $username, $password, "AMS");

  // Check that the connection was successful, otherwise exit
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  } else printf("Connection Successful");
?>
<br>
<?php
  // Detect user action
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
      // Delete customer
      deleteCustomer($_POST['cid'], $connection);        
    
    } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
      // Add customer    		  
      addCustomer($_POST["new_password"], $_POST["new_name"], $_POST["new_address"], 
          $_POST["new_phone"], $connection);
    }
  }

?>

<h2>Customer Registration Menu</h2>

<?php
  // Display Customers
  displayCustomers($connection);
  
  // Disconnect from database
  mysqli_close($connection);

?>

<h2>REGISTER CUSTOMER</h2>

<!-- Form for adding a new customer -->

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table border=0 cellpadding=0 cellspacing=0>
    <tr><td>Name</td><td><input type="text" size=20 name="new_name"></td></tr>
    <tr><td>Password</td><td><input type="password" size=20 name="new_password"</td></tr>
		<tr><td>Address</td><td> <input type="text" size=20 name="new_address"></td></tr>
		<tr><td>Phone</td><td> <input type="text" size=20 name="new_phone"></td></tr>
    <tr><td></td><td><input type="submit" name="submit" border=0 value="ADD"></td></tr>
  </table>
</form>

<?php include 'footer.php'; ?>
</body>
</html>