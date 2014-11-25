<?php
/*
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
    }
    
    return $connection;
} 

/* 

CUSTOMER TABLE FUNCTIONS

addCustomer($password, $name, $address, $phone, $connection)
deleteCustomer($id, $connection)
displayCustomers($connection)

*/

function addCustomer($password, $name, $address, $phone, $connection) {
	// SQL statement
	$stmt = $connection->prepare("INSERT INTO Customer (cid, c_password, cname, address, phone) VALUES (?,?,?,?,?)");
    
    // Generate new cid  
	$id = $connection->query("SELECT max(cid) FROM Customer");
	$id = $id->fetch_row();
	$new_id = $id[0] + 1;
	
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
    	$stmt->bind_param("sssss", $new_id, $password, $name, $address, $phone);
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

function updateItem($upc, $price, $quantity, $connection) {

	//SQL statement
	if ($price != NULL) {
		$stmt = $connection->prepare("UPDATE Item SET price=? WHERE upc=?");
		$stmt->bind_param("ss", $price, $upc);
		$stmt->execute();
	}

	if ($quantity != NULL) {
		$stmt2 = $connection->prepare("UPDATE Item SET stock=stock+? WHERE upc=?");
		$stmt2->bind_param("ss", $quantity, $upc);
		$stmt2->execute();
	}

	// Print success or error message 
    if($price != NULL && $stmt->error) {       
      printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    } elseif($quantity != NULL && $stmt2->error) {
      printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt2->error);
    } else {
      echo "<h2><b><mark>Successfully updated ITEM ".$upc."</mark></b></h2>";
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


function displayDailySalesReport($stmt){
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
	<td class=rowheader>Price</td>
	<td class=rowheader>Quantity_Sold</td>
	<td class=rowheader>Quantity_Remaining</td>
	</tr>";
	// Display each search result field in the table
	// Columns here are individual fields for each result row.

	while($row = $stmt->fetch()){
	echo "<td>".$col1."</td>";
	echo "<td>".$col2."</td>";
	echo "<td>".$col3."</td>";
	echo "<td>".$col4."</td>";
	echo "<td>".$col5."</td>";
	echo "<td> $".$col6."</td>"; // added dollar sign for price.
	echo "<td> ".$col7."</td>"; 
	echo "<td>".$col8."</td></tr>";
	}
	echo "</form>";
	echo "</table>";
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
		echo "<td>".$col8."</td>";
     
	    // Display an option to add this Item to the shopping cart if item is in stock (col8 >= 1)
	    if ($col8 >= 1){
	    echo "<td><a href=\"javascript:formSubmit(".$col1.");\">ADD</a>";
	    } else {
	    echo '<td><font color="4D7094">SOLD OUT</font>';
	    }
	    echo "</td></tr>";   
  	}
  	
	echo "</form>";
  	echo "</table>"; 

}


function addItemToCart($cid, $upc, $connection){
	echo "<h2><b><mark>Customer CID: ".$cid.". Item UPC: ".$upc."</mark></b></h2>";

	// add this item to the customer's shopping cart
	insertCartItem($cid, $upc, "1", $connection);
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

	// Proceed to checkout and create a purchase
	echo '<form method="post" action="';
	echo htmlspecialchars($_SERVER["PHP_SELF"]); 
	echo'">';
	echo '<input type="hidden" name="cid" value="'.$cid.'">';
	echo '<input type="submit" name="checkout" value="PROCEED TO CHECKOUT" style = "display: block; margin: 0px; padding: 0px; font-size: 10px">';
	echo '</form></td></tr>';   
}

function confirmPurchase($cid, $connection) {
	// search for all items in customer's shopping cart and display in table
	$stmt = $connection->prepare("SELECT I.upc, I.title, I.item_type, I.category, I.company, I.item_year, I.price, I.stock, S.quantity FROM Item I JOIN ShoppingCart S ON I.upc=S.upc WHERE S.cid = ?");
	$stmt->bind_param("s", $cid);
	$stmt->execute();
	$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9);

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
		</tr>";

	// Display each search result field in the table
	// Columns here are individual fields for each result row. 
	while($row = $stmt->fetch()){  
		echo "<tr><td>".$col1."</td>";
		echo "<td>".$col2."</td>";
		echo "<td>".$col3."</td>";
		echo "<td>".$col4."</td>";
		echo "<td>".$col5."</td>";
		echo "<td>".$col6."</td>";
		echo "<td> $".$col7."</td>"; // added dollar sign for price.
		echo "<td>".$col8."</td>";
		echo "<td>".$col9."</td></tr>";
	}
	echo "</table>";

	$stmt = $connection->prepare("SELECT I.price, S.quantity FROM Item I JOIN ShoppingCart S ON I.upc=S.upc WHERE S.cid = ?");
	$stmt->bind_param("s", $cid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($price, $quantity);
	
	$total = 0;
	while($row = $stmt->fetch()) {
		$total += $price * $quantity;
	}

	echo "\n<h2>Your total is $".$total.".</h2>";

	echo '<form id="add" name="add" method="post" action="';
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo '">';
	echo '
	<table border=0 cellpadding=0 cellspacing=0>
		<tr><td>Credit Card Number</td><td><input type="text" size=30 name="credit_num"></td></tr>
	    <tr><td>Expiry Date (MMYY)</td><td><input type="text" size=30 name="credit_expiry"</td></tr>
		<tr><td><input type="submit" name="purchase" border=0 value="CONFIRM PURCHASE"></td></tr>
	</table>
	</form>
	';
    
}

function createPurchase($cid, $creditcard, $expiry, $connection) {
	// Generate receiptID
	$id = $connection->query("SELECT max(receiptID) FROM PurchaseItem");
	if(!$id) {
		echo "ERROR: Please try again.";
	}
	$id = $id->fetch_assoc();
	$new_id = $id['max(receiptID)'] + 1;

	// Create Order data
	date_default_timezone_set('America/Vancouver');
	$date = date("Ymd");
	$order = $connection->prepare("INSERT INTO `Order`(receiptID, order_date, cid,
		cardNo, expiryDate, expectedDate) VALUES (?,?,?,?,?,?)");
	$order->bind_param("ssssss", $new_id, $date, $cid, $creditcard, $expiry, $date);
	$order->execute();
	if (!$order) {
		echo "Order creation failed. Please try again.";
	}

	// Find customer's shopping cart
	$stmt = $connection->prepare("SELECT upc, quantity FROM ShoppingCart WHERE cid=?");
	$stmt->bind_param("s", $cid);
	$stmt->execute();
	if(!$stmt) {
		echo "ERROR: Shopping cart not found.";
	}
	$stmt->store_result();
	$stmt->bind_result($upc, $quantity);

	while($row = $stmt->fetch()) {
		// Create PurchaseItem data
		$new = $connection->prepare("INSERT INTO PurchaseItem(receiptID, upc, quantity)
			VALUES (?,?,?)");
		$new->bind_param("iss", $new_id, $upc, $quantity);
		$new->execute();
		if(!$new) {
			echo "Purchase creation failed. Please try again.";
		}

		// Update item stock
		$shelving = $connection->prepare("UPDATE Item SET stock=stock-? WHERE upc=?");
		$shelving->bind_param("ss", $quantity, $upc);
		$shelving->execute();
		if (!$shelving) {
			echo "Item stock could not be updated. Please try again.";
		}
	}

	// Clear shopping cart
	$clear = $connection->prepare("DELETE FROM ShoppingCart WHERE cid=?");
	$clear->bind_param("s", $cid);
	$clear->execute();
	if(!$clear) {
		echo "ERROR: Please try again.";
	}

	// Display success message
	echo "<h2><b><mark>Order placed for ".$_SESSION['cname']." and billed to credit card with number ".$creditcard.".";
	echo " Thanks for shopping with AMS!</mark></b></h2>";

	displayShopSearch();
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
    	  $stmt->close();
    	} else {
    	  echo "<h2><b><mark>Item removed from shopping cart</mark></b></h2>";
    	  $stmt->close();
    	  }
		} else {
	
	// check available quantity for this upc. if available quantity < desired quantity,
	// only add available quantity to shopping basket and explain this to the user. 
		$stmt = $connection->prepare("SELECT stock FROM Item WHERE upc = ?");
		$stmt->bind_param("s", $upc);
		$stmt->execute();	
		$stmt->bind_result($col1);
		
		// Print success or error message  
    	if($stmt->error) {       
    	  printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    	} 
    	while($row = $stmt->fetch()){
    	$availableqty = $col1;
    	}

		if ($availableqty < $newqty){
		$stmt = $connection->prepare("UPDATE ShoppingCart SET quantity = ? WHERE cid = ? AND upc = ?");
		$stmt->bind_param("sss", $availableqty, $cid, $upc);
		$stmt->execute();	
		
		// Print success or error message  
    	if($stmt->error) {       
    	  printf("<h2><b><mark>Error: %s.</mark></b></h2>\n", $stmt->error);
    	} else {
    	  echo "<h2><b><mark>Sorry, your order exceeds our available stock! Your order has automatically been reduced to ".$availableqty." items.</mark></b></h2>";
    	}
		} else{

	// otherwise, if available quantity > desired quantity, add desired quantity to shopping basket.
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
	}
}

function displayShopSearch(){
	echo '<div id="shop"><h2>Search for Item</h2>';
	echo '<form method="post" action="';
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo '">';
	echo '
	<div id=text_inputs>
	   Category:       <input type="text" name="category"><br><br>
	   Title:          <input type="text" name="title"><br><br>
	   Leading Singer: <input type="text" name="leading_singer"><br><br>
	                   <input type="submit" name="submit" value="SUBMIT"> 
	</form>	
	</div></div>
	';
}

function processReturn($receiptID, $cid, $upc, $connection) {
	$stmt = $connection->prepare("SELECT receiptID, cid, cardNo, deliveredDate, order_date 
		FROM `Order`
		WHERE receiptID=? AND cid=?");
	$stmt->bind_param("ss", $receiptID, $cid);
	$stmt->execute();
	if (!$stmt) {
		echo "Error processing return. Please try again.";
	}
	$stmt->bind_result($receiptIDb, $cidb, $cardNo, $deliver, $order);
	$stmt->fetch();

	//Create date var
	date_default_timezone_set('America/Vancouver');
	$date = date("Ymd");
	$datetime = strtotime($date);
	$orderday = strtotime($order);

	//Does receipt ID exist?
	if ($receiptIDb === NULL)
		echo "Receipt ID does not exist. ";
	//Has order been already delivered?
	if ($deliver == NULL)
		echo "This order has not been delivered yet and cannot be refunded. ";
	//Does it match the customer ID?
	if ($cidb === NULL)
		echo "Customer ID does not exist.";
	//Was purchase more than 15 days ago?
	if (floor(($datetime-$orderday)/(60*60*24)) > 15)
		echo "Unfortunately, we can only return items purchased less than 15 days ago.";
	//If all tests pass, then:
	else {
		$stmt->close();
		//Create record of Return
			// Generate return ID
			$id = $connection->query("SELECT max(retid) FROM `Return`");
			if(!$id) {
				$new_id = 0;
			} else {
				$id = $id->fetch_assoc();
				$new_id = $id['max(retid)'] + 1;
			}
		$return = $connection->prepare("INSERT INTO `Return` (retid, return_date, receiptID) VALUES (?,?,?)");
		$return->bind_param("isi", $new_id, $date, $receiptID);
		$return->execute();
		if (!$return) echo "Error processing return.";
		$return->close();

		//Create record of ReturnItem
		
		//Return single UPC or return full order?
		if ($upc == "") { //Return full order
			$return = $connection->prepare("SELECT upc, quantity FROM PurchaseItem WHERE receiptID=?");
			$return->bind_param("s", $receiptID);
			$return->execute();
			$return->store_result();
			$return->bind_result($upc, $quantity);
			while ($row = $return->fetch()) {
				$returnItem = $connection->prepare("INSERT INTO ReturnItem(retid, upc, quantity) VALUES (?,?,?)");
				$returnItem->bind_param("sss", $new_id, $upc, $quantity);
				$returnItem->execute();
				if (!$returnItem) echo "Error processing return.";
				$returnItem->close();

				//Update stock of Item
				$shelving = $connection->prepare("UPDATE Item SET stock=stock+? WHERE upc=?");
				$shelving->bind_param("ss", $quantity, $upc);
				$shelving->execute();
				if (!$shelving)	echo "Item stock could not be updated. Please try again.";
				$shelving->close();
			}
			$return->free_result();
			$return->close();	

			$deleteorder = $connection->prepare("DELETE FROM `Order` WHERE receiptID=?");
			$deleteorder->bind_param("s", $receiptID);
			$deleteorder->execute();
			$deleteorder->close();
			echo "You have successfully returned purchase with ID ".$receiptID.".";	

		} else { //return single item
			$returnsingle = $connection->prepare("SELECT quantity FROM PurchaseItem WHERE receiptID=? AND upc=?");
			$returnsingle->bind_param("ss", $receiptID, $upc);
			$returnsingle->execute();
			$returnsingle->bind_result($stock);
			$returnsingle->fetch();
			$returnsingle->close();

			$returnItem = $connection->prepare("INSERT INTO ReturnItem(retid, upc, quantity) VALUES (?,?,?)");
			$returnItem->bind_param("sss", $new_id, $upc, $stock);
			$returnItem->execute();
			if (!$returnItem) echo "Error processing return.";

			//Update stock of Item
			$shelving = $connection->prepare("UPDATE Item SET stock=stock+? WHERE upc=?");
			$shelving->bind_param("ss", $stock, $upc);
			$shelving->execute();
			if (!$shelving)	echo "Item stock could not be updated. Please try again.";

			//Delete purchaseitem records
			$deletepurchase = $connection->prepare("DELETE FROM PurchaseItem WHERE receiptID=? AND upc=?");
			$deletepurchase->bind_param("ss", $receiptID, $upc);
			$deletepurchase->execute();

			echo "You have successfully returned item with UPC ".$upc." from order ".$receiptID.".";
		}
		
	}
	
	
}

?>
