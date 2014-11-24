<?php

/* 

CUSTOMER TABLE FUNCTIONS
connectToDatabase()
addCustomer($password, $name, $address, $phone, $connection)
deleteCustomer($id, $connection)
displayCustomers($connection)
displaySearchResults($stmt)
addItemToCart($upc, $connection)

*/

function connectToDatabase() {

	// DATABASE CONNECTION CONFIG
	// Connect to AMS database
	$username = "root";
	$password = "";
	//$hostname = "localhost";				//$hostname for Crystal
	$hostname = "127.0.0.1";				//$hostname for Scott (bug workaround on OS X)

	$connection = new mysqli($hostname, $username, $password, "AMS");
	
	if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    } else printf("Connection Successful!!");
    
    return $connection;
} 


function addCustomer($password, $name, $address, $phone, $connection) {
	// SQL statement
	$stmt = $connection->prepare("INSERT INTO Customer (cid, c_password, cname, address, phone) VALUES (?,?,?,?,?)");
    
    // Generate new cid
	$id = $connection->query("SELECT max(cid) FROM Customer");
	$id = $id->fetch_array();
	$id[0] = $id[0] + 1;

	// Test fields for validity
	$valid = true;

	// Prepare phone number
	$phone_length = strlen($phone);
	if ($phone_length > 12 || $phone_length < 10 || $phone_length == 11) {
		printf("<h2><b><mark>Please enter a valid phone number.</mark></b></h2>");
		$valid = false;
	} else if ($phone_length == 10) {
		$phone = substr($phone, 0, 3) . "-" . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
	} 

	if($valid) {
		// Bind the title and pub_id parameters, 'sssss' indicates 5 strings
    	$stmt->bind_param("sssss", $id[0], $password, $name, $address, $phone);
    	// Execute the insert statement
    	$stmt->execute();	
    	// Print success or error message  
	    if($stmt->error) {       
	      printf("<b>Error: %s.</b>\n", $stmt->error);
	    } else {
	      echo "<h2><b><mark>Successfully added ".$name."</mark></b></h2>";
	    }
	} 
}


function deleteCustomer($id, $connection) {
    // Deleting a customer involves deleting the customer's shopping cart and then deleting the customer.
    
    //Delete customer shopping cart:
    $stmt = $connection->prepare("DELETE FROM ShoppingCart WHERE cid=?");
    $deleteCustID = $id;
	$stmt->bind_param("s", $deleteCustID);
	$stmt->execute();
	  
	// Print success or error message
	if($stmt->error) {
	 printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
	}	
	$stmt->close();

	// Create a delete query prepared statement with a ? for the title_id
    $stmt = $connection->prepare("DELETE FROM Customer WHERE cid=?");
    $deleteCustID = $id;

    // Bind the title_id parameter, 's' indicates a string value
	$stmt->bind_param("s", $deleteCustID);

	// Execute the delete statement
	$stmt->execute();
	  
	// Print success or error message
	if($stmt->error) {
	 printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
	} else {
	 echo "<h2><b><mark>Successfully deleted customer (CID: ".$id.")</mark></b></h2>";
	}
	$stmt->close();
}


function displayCustomers($connection) {
	// Select all of the customer rows
 	if (!$result = $connection->query("SELECT cid, c_password, cname, 
 		address, phone FROM Customer ORDER BY cid")) {
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

	echo "<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'>";
	echo "<tr valign=center>";
	echo "
			<td class=rowheader>CustomerID</td>
			<td class=rowheader>Name</td>
			<td class=rowheader>Password</td>
			<td class=rowheader>Address</td>
			<td class=rowheader>Phone</td>
			<td class=rowheader>Delete?</td>
		</tr>";

	// Display each Customer databaserow as a table row
	while($row = $result->fetch_assoc()){  
		echo "<td>".$row['cid']."</td>";
		echo "<td>".$row['cname']."</td>";
		echo "<td>".$row['c_password']."</td>";
		echo "<td>".$row['address']."</td>";
		echo "<td>".$row['phone']."</td><td>";
     
	    //Display an option to delete this Customer
	    echo "<a href=\"javascript:formSubmit('".$row['cid']."');\">DELETE</a>";
	    echo "</td></tr>";   
  	}
  	
	echo "</form>";
  	echo "</table>";  	
}


/* 

ITEM TABLE FUNCTIONS

addItem($upc, $title, $item_type, $category, $company, $item_year, $price, $stock, $connection)
deleteItem($upc, $connection)
displayItems($connection)

*/


function addItem($upc, $title, $item_type, $category, $company, $item_year, 
	$price, $stock, $connection) {
	
	// SQL statement
	$stmt = $connection->prepare("INSERT INTO Item (upc, title, item_type, 
		category, company, item_year, price, stock) VALUES (?,?,?,?,?,?,?,?)");
	$stmt->bind_param("ssssssss", $upc, $title, $item_type, $category, $company, 
		$item_year, $price, $stock);
	$stmt->execute();	
	
	// Print success or error message  
    if($stmt->error) {       
      printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    } else {
      echo "<h2><b><mark>Successfully added ".$title."</mark></b></h2>";
    }
}


function deleteItem($upc, $connection) {
    // Deleting an item involves deleting it from all customer shopping carts and from the Item table.
    
    //Delete from Shopping Carts:
    $stmt = $connection->prepare("DELETE FROM ShoppingCart WHERE upc=?");
    $deleteItemUpc = $upc;
	$stmt->bind_param("s", $deleteItemUpc);
	$stmt->execute();
	  
	// Print success or error message
	if($stmt->error) {
	 printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
	}	
	$stmt->close();
	
    //Now, delete from Item table:
    $stmt = $connection->prepare("DELETE FROM Item WHERE upc=?");
    $deleteItemUpc = $upc;
	$stmt->bind_param("s", $deleteItemUpc);
	$stmt->execute();
	  
	// Print success or error message
	if($stmt->error) {
	 printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
	} else {
	 echo "<h2><b><mark>Item deleted (UPC: ".$upc.")</mark></b></h2>";
	}
	$stmt->close();
}


function displayItems($connection) {
	// Select all of the item rows
 	if (!$result = $connection->query("SELECT upc, title, item_type, 
		category, company, item_year, price, stock FROM Item ORDER BY upc")) {
		    die('There was an error running the query [' . $db->error . ']');
    }

	// Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
	echo "<form id=\"delete\" name=\"delete\" action=\"";
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo "\" method=\"POST\">";
	// Hidden value is used if the delete link is clicked
	echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
	// We need a submit value to detect if delete was pressed 
	echo "<input type=\"hidden\" name=\"submitDelete\" value=\"DELETE\"/>";

	echo "
		<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'><tr valign=center>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Item Type</td>
			<td class=rowheader>Category</td>
			<td class=rowheader>Company</td>
			<td class=rowheader>Item Year</td>
			<td class=rowheader>Price</td>
			<td class=rowheader>Stock</td>
			<td class=rowheader>Delete?</td>
		</tr>";

	// Display each Item databaserow as a table row
	while($row = $result->fetch_assoc()){  
		echo "<td>".$row['upc']."</td>";
		echo "<td>".$row['title']."</td>";
		echo "<td>".$row['item_type']."</td>";
		echo "<td>".$row['category']."</td>";
		echo "<td>".$row['company']."</td>";
		echo "<td>".$row['item_year']."</td>";
		echo "<td>".$row['price']."</td>";
		echo "<td>".$row['stock']."</td><td>";
     
	    //Display an option to delete this Item
	    echo "<a href=\"javascript:formSubmit('".$row['upc']."');\">DELETE</a>";
	    echo "</td></tr>";   
  	}
	echo "</form>";
  	echo "</table>";  	
}


/*
 * Creates a table of item search results for the customer. 
 */
function displaySearchResults($stmt){

	$stmt->execute();
	$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8);

	// Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
	echo "<form id=\"add\" name=\"add\" action=\"";
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo "\" method=\"POST\">";
	// Hidden value is used if the add to cart link is clicked
	echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
	// We need a submit value to detect if delete was pressed 
	echo "<input type=\"hidden\" name=\"submitAdd\" value=\"ADD\"/>";

	echo "
		<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'><tr valign=center>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Item Type</td>
			<td class=rowheader>Category</td>
			<td class=rowheader>Company</td>
			<td class=rowheader>Item Year</td>
			<td class=rowheader>Price</td>
			<td class=rowheader>Stock</td>
			<td class=rowheader>Add to Cart?</td>
		</tr>";

	// Display each search result field in the table
	// Columns here are individual fields for each result row. 
	while($row = $stmt->fetch()){  
		echo "<td>".$col1."</td>";
		echo "<td>".$col2."</td>";
		echo "<td>".$col3."</td>";
		echo "<td>".$col4."</td>";
		echo "<td>".$col5."</td>";
		echo "<td>".$col6."</td>";
		echo "<td> $".$col7."</td>"; // added dollar sign for price.
		echo "<td>".$col8."</td><td>";
     
	    //Display an option to add this Item to the shopping cart
	    echo "<a href=\"javascript:formSubmit(".$col1.");\">ADD</a>";
	    echo "</td></tr>";   
  	}
  	
	echo "</form>";
  	echo "</table>"; 

}


function addItemToCart($cid, $upc, $connection){
	echo "<h2><b><mark>Customer CID: ".$cid.". Item UPC: ".$upc."</mark></b></h2>";

	// add this item to the customer's shopping cart
	insertCartItem($cid, $upc, "1", $connection);

	// display all the items now in the customer's shopping cart
	displayShoppingCart($cid, $connection);
}


function insertCartItem($cid, $upc, $quantity, $connection) {
	
	// check to see if shopping cart already contains item to add
	$stmt = $connection->prepare("SELECT upc FROM ShoppingCart WHERE cid=? AND upc=?");
	$stmt->bind_param("ss", $cid, $upc);
	$stmt->execute();
	$stmt->bind_result($col1);
	$row = $stmt->fetch();

	// if item is not already in the cart, add it
	if (empty($row)){
		$stmt->close();
		// SQL statement to add an item to a customer's shopping cart
		$stmt = $connection->prepare("INSERT INTO ShoppingCart (cid, upc, quantity) VALUES (?, ?, ?)");
		$stmt->bind_param("sss", $cid, $upc, $quantity);
		$stmt->execute();	
		
		// Print success or error message  
    	if($stmt->error) {       
    	  printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    	} else {
    	  echo "<h2><b><mark>You've added an item to your shopping cart</mark></b></h2>";
    	}
	} else {
	echo "<h2><b><mark>This item is already in your shopping cart</mark></b></h2>";
	}
}


function displayShoppingCart($cid, $connection) {
// search for all items in customer's shopping cart and display in table
$stmt = $connection->prepare("SELECT I.upc, I.title, I.item_type, I.category, I.company, I.item_year, I.price, I.stock, S.quantity FROM Item I JOIN ShoppingCart S ON I.upc=S.upc WHERE S.cid = ?");
			$stmt->bind_param("s", $cid);
			createShoppingCartTable($stmt, $connection);
}


function createShoppingCartTable($stmt, $connection) {
	$stmt->execute();
	$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9);

	// Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
	echo "<form id=\"add\" name=\"add\" action=\"";
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo "\" method=\"POST\">";
	// Hidden value is used if the add to cart link is clicked
	echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
	// We need a submit value to detect if delete was pressed 
	echo "<input type=\"hidden\" name=\"submitAdd\" value=\"ADD\"/>";

	echo "
		<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'><tr valign=center>
			<td class=rowheader>UPC</td>
			<td class=rowheader>Title</td>
			<td class=rowheader>Item Type</td>
			<td class=rowheader>Category</td>
			<td class=rowheader>Company</td>
			<td class=rowheader>Item Year</td>
			<td class=rowheader>Price</td>
			<td class=rowheader>Stock</td>
			<td class=rowheader>Order Qty</td>
			<td class=rowheader>Update Qty</td>
		</tr>";

	// Display each search result field in the table
	// Columns here are individual fields for each result row. 
	while($row = $stmt->fetch()){  
		echo "<td>".$col1."</td>";
		echo "<td>".$col2."</td>";
		echo "<td>".$col3."</td>";
		echo "<td>".$col4."</td>";
		echo "<td>".$col5."</td>";
		echo "<td>".$col6."</td>";
		echo "<td> $".$col7."</td>"; // added dollar sign for price.
		echo "<td>".$col8."</td>";
		echo "<td>".$col9."</td>";
		// Display an option to update the order quantity
     	echo '</form><td><form method="post" action="';
     		echo htmlspecialchars($_SERVER["PHP_SELF"]); 
     		echo'">';
			echo '<input type="text" name="updateqty" maxlength="3" size="3" style = "display: block; margin: 0px; padding: 0px; float: left;">';
			echo '<input type="hidden" name="upc" value="'.$col1.'">';
			echo '<input type="submit" name="submitUpdate" value="UPDATE" style = "display: block; margin: 0px; padding: 0px; font-size: 10px">';
			echo '</form></td></tr>';   
  	}
	echo "</form>";
  	echo "</table>"; 
}

function updateItemQty($cid, $upc, $newqty, $connection) {
	echo "<h2><b><mark>DEBUG STATEMENT: Updating order by CID ".$cid." for Item UPC: ".$upc." to qty ".$newqty."</mark></b></h2>";

	//check to see if new quantity is 0. if so, delete this item from the cart.
	if ($newqty <= 0){
		$stmt = $connection->prepare("DELETE FROM ShoppingCart WHERE cid = ? AND upc = ?");
		$stmt->bind_param("ss", $cid, $upc);
		$stmt->execute();
	
		// Print success or error message  
		if($stmt->error) {       
    	  printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    	} else {
    	  echo "<h2><b><mark>Item removed from shopping cart</mark></b></h2>";
    	  }
		} else {
	// update item qty with SQL statement
		$stmt = $connection->prepare("UPDATE ShoppingCart SET quantity = ? WHERE cid = ? AND upc = ?");
		$stmt->bind_param("sss", $newqty, $cid, $upc);
		$stmt->execute();	
		
		// Print success or error message  
    	if($stmt->error) {       
    	  printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    	} else {
    	  echo "<h2><b><mark>Quantity updated</mark></b></h2>";
    	}
	}
	// display updated shopping cart
	displayShoppingCart($cid, $connection);
}

?>