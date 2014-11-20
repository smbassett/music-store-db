<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>CPSC 304 AMS Music Store</title>
<!--
    A simple stylesheet is provided so you can modify colours, fonts, etc.
-->
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
    /****************************************************
     STEP 1: Connect to the bookbiz MySQL database
     ****************************************************/

    // CHANGE this to connect to your own MySQL instance in the labs or on your own computer
    $username = "root";
	$password = "";
	$hostname = "localhost";

	$connection = new mysqli($hostname, $username, $password, "AMS");

    // Check that the connection was successful, otherwise exit
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
	else printf("Connection Successful");
    /****************************************************
     STEP 2: Detect the user action

     Next, we detect what the user did to arrive at this page
     There are 3 possibilities 1) the first visit or a refresh,
     2) by clicking the Delete link beside a book title, or
     3) by clicking the bottom Submit button to add a book title
     
     NOTE We are using POST superglobal to safely pass parameters
        (as opposed to URL parameters or GET)
     ****************************************************/

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
       /*
          Delete the selected customer using the customer_id
        */
       
       // Create a delete query prepared statement with a ? for the title_id
       $stmt = $connection->prepare("DELETE FROM Customer WHERE cid=?");
       $deleteCustID = $_POST['cid'];
       // Bind the title_id parameter, 's' indicates a string value
       $stmt->bind_param("s", $deleteCustID);
       
       // Execute the delete statement
       $stmt->execute();
          
       if($stmt->error) {
         printf("<b>Error: %s.</b>\n", $stmt->error);
       } else {
         echo "<b>Successfully deleted ".$deleteCustID."</b>";
       }
            
      } elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
       /*
        Add a book title using the post variables title_id, title and pub_id.
        */
        $customer_id = $_POST["new_customerID"];
        $customer_pw = $_POST["new_password"];
        $customer_nm = $_POST["new_name"];
		$customer_ad = $_POST["new_address"]; 
		$customer_ph = $_POST["new_phone"]; 	
		  
        $stmt = $connection->prepare("INSERT INTO Customer (cid, c_password, cname, address, phone) VALUES (?,?,?,?,?)");
          
        // Bind the title and pub_id parameters, 'sssss' indicates 5 strings
        $stmt->bind_param("sssss", $cid, $customer_pw, $customer_nm, $customer_ad, $customer_ph);
        
        // Execute the insert statement
        $stmt->execute();
          
        if($stmt->error) {       
          printf("<b>Error: %s.</b>\n", $stmt->error);
        } else {
          echo "<b>Successfully added ".$cname."</b>";
        }
      }
   }
?>

<h2>Customer Registration Menu</h2>
<!-- Set up a table to view the book titles -->
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
    /****************************************************
     STEP 3: Select the most recent list of book titles
     ****************************************************/

   // Select all of the book rows columns title_id, title and pub_id
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


    /****************************************************
     STEP 4: Display the list of book titles
     ****************************************************/
    // Display each book title databaserow as a table row
    while($row = $result->fetch_assoc()){
        
       echo "<td>".$row['cid']."</td>";
       echo "<td>".$row['c_password']."</td>";
       echo "<td>".$row['cname']."</td><td>";
	   echo "<td>".$row['address']."</td><td>";
	   echo "<td>".$row['phone']."</td><td>";
       
       //Display an option to delete this title using the Javascript function and the hidden title_id
       echo "<a href=\"javascript:formSubmit('".$row['cid']."');\">DELETE</a>";
       echo "</td></tr>";
        
    }
    echo "</form>";

    // Close the connection to the database once we're done with it.
    mysqli_close($connection);
?>

</table>

<h2>REGISTER CUSTOMER</h2>

<!--
  /****************************************************
   STEP 5: Build the form to add a book title
   ****************************************************/
    Use an HTML form POST to add a book, sending the parameter values back to this page.
    Avoid Cross-site scripting (XSS) by encoding PHP_SELF using htmlspecialchars.

    This is the simplest way to POST values to a web page. More complex ways involve using
    HTML elements other than a submit button (eg. by clicking on the delete link as shown above).
-->

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <table border=0 cellpadding=0 cellspacing=0>
        <tr><td>CustomerID</td><td><input type="text" size=30 name="new_customerID"</td></tr>
        <tr><td>Password</td><td><input type="text" size=30 name="new_password"</td></tr>
        <tr><td>Name</td><td> <input type="text" size=5 name="new_name"></td></tr>
		<tr><td>Address</td><td> <input type="text" size=5 name="new_address"></td></tr>
		<tr><td>Phone</td><td> <input type="text" size=5 name="new_phone"></td></tr>
        <tr><td></td><td><input type="submit" name="submit" border=0 value="ADD"></td></tr>
    </table>
</form>
</body>
</html>



