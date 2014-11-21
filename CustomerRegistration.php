<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Registration</title>
<link href="bookbiz.css" rel="stylesheet" type="text/css">

<!--
    Javascript to submit a title_id as a POST form, used with the "delete" links
-->
<script>
function formSubmit(CustId) {
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
<!-- Set up a table to view the customers -->
<table border=0 cellpadding=0 cellspacing=0>
<!-- Create the table column headings -->

<tr valign=center>
<td class=rowheader>CustomerID</td>
<td class=rowheader>Password</td>
<td class=rowheader>Name</td>
<td class=rowheader>Address</td>
<td class=rowheader>Phone</td>
</tr>

<?php

  // Select all of the customer rows
  if (!$result = $connection->query("SELECT cid, c_password, cname, address, phone FROM Customer ORDER BY cid")) {
    die('There was an error running the query [' . $db->error . ']');
  }

  // Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
  echo "<form id=\"delete\" name=\"delete\" action=\"";
  echo htmlspecialchars($_SERVER["PHP_SELF"]);
  echo "\" method=\"POST\">";
  // Hidden value is used if the delete link is clicked
  echo "<input type=\"hidden\" name=\"cid\" value=\"-1\"/>";
  // We need a submit value to detect if delete was pressed 
  echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";


  // Display each Customer databaserow as a table row
  while($row = $result->fetch_assoc()){  
    echo "<td>".$row['cid']."</td>";
    echo "<td>".$row['c_password']."</td>";
    echo "<td>".$row['cname']."</td>";
    echo "<td>".$row['address']."</td>";
    echo "<td>".$row['phone']."</td><td>";
     
    //Display an option to delete this Customer
    echo "<a href=\"javascript:formSubmit('".$row['cid']."');\">DELETE</a>";
    echo "</td></tr>";   
  }

  echo "</form>";

  // Disconnect from database
  mysqli_close($connection);

?>

</table>

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
</body>
</html>