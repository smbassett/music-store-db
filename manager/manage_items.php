<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Manage Items</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>

<!--Javascript to submit a title_id as a POST form, used with the "delete" links-->
<script>
function formSubmit(itemUpc) {
    'use strict';
    if (confirm('Are you sure you want to delete this item?')) {
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('delete');
      form.upc.value = itemUpc;
      // Post this form
      form.submit();
    }
}

</script>
</head>
<body>

<!-- Include header -->
<?php include '../header.php'; ?>

<h1>Manage Items</h1>

<?php

// Include basic database operations
include '../dbops.php';

//Connect to database:
$connection = connectToDatabase();
?>
<br>
<?php
  // Detect user action
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // If manager has clicked the 'delete' button next to an item, then delete that item:
    if (isset($_POST["submitDelete"]) && $_POST["submitDelete"] == "DELETE") {
    	// Delete Item
    	deleteItem($_POST['upc'], $connection);        
     } 
    // If manager has typed in information about a new item and clicked 'add':
    elseif (isset($_POST["submit"]) && $_POST["submit"] ==  "ADD") {       
    	// Add Item    		  
      	addItem($_POST["new_title"], $_POST["new_item_type"], $_POST["new_category"],
      	$_POST["new_company"], $_POST["new_item_year"], $_POST["new_price"], $_POST["new_quantity"], 
      	$connection);
    }
    //If manager wants to update item quantity/price
    elseif (isset($_POST["submit"]) && $_POST["submit"] == "UPDATE") {
      //Update Item
      updateItem($_POST["upc"], $_POST["new_price"], $_POST["update_quantity"], $connection);
    }
  }
?>

<h2>Items</h2>

<?php
  // Display Items
  displayItems($connection);
  
  // Disconnect from database
  mysqli_close($connection);
?>

<br>
<h2>ADD NEW ITEM</h2>

<!-- Form for adding a new Item 
Adding Items:  It adds new copies of an item.  
The user has to specify the item's upc  the quantity and the unit price (optional).  
If a unit price is specified this will be the new price for this item and it will 
override any old price that may exist for this item. If no new unit price is provided 
the old unit price will be retained.

-->

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table border=0 cellpadding=0 cellspacing=0>
    <tr><td>Title</td><td><input type="text" size=20 name="new_title"></td></tr>
    <tr><td>Item Type</td><td><input type="text" size=20 name="new_item_type"></td></tr>
    <tr><td>Category</td><td><input type="text" size=20 name="new_category"></td></tr>
    <tr><td>Company</td><td><input type="text" size=20 name="new_company"></td></tr>
    <tr><td>Item Year</td><td><input type="text" size=20 name="new_item_year"></td></tr>
    <tr><td>Price</td><td> <input type="text" size=20 name="new_price"></td></tr>
    <tr><td>Quantity</td><td><input type="text" size=20 name="new_quantity"</td></tr>	
    <tr><td></td><td><input type="submit" name="submit" border=0 value="ADD"></td></tr>
  </table>
</form>
<br>
<h2>UPDATE ITEM</h2>

<form id="update" name="update" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table border=0 cellpadding=0 cellspacing=0>
    <tr><td>UPC</td><td><input type="text" size=20 name="upc"></td></tr>
    <tr><td>New Price (Optional)</td><td> <input type="text" size=20 name="new_price"></td></tr>
    <tr><td>Quantity to add/sub (Optional)</td><td><input type="text" size=20 name="update_quantity"</td></tr> 
    <tr><td></td><td><input type="submit" name="submit" border=0 value="UPDATE"></td></tr>
  </table>
</form>

<br>

<a href="home.php" title="Manager's Page"><h2>&lt;&lt;Back</h2></a>

<?php include '../footer.php'; ?>
</body>
</html>