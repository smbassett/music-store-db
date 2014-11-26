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

function tryAddCustomer($username, $c_password, $fullname, $address, $phone, $connection){
	$stmt = $connection->prepare("SELECT cid FROM Customer WHERE username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->bind_result($found_cid);
	$stmt->fetch();
	
	//Is username available?
	if (is_null($found_cid)){
		addCustomer($username, $c_password, $fullname, $address, $phone, $connection);
	} else {
		printf("<h3>Sorry, that username is already taken. Please choose a different one.</h3>");
		echo"<h3>May we suggest ".$username."_the_awesome, ".$fullname."666, or ".$username."-".$phone."?</h3>";
		}
	}

function addCustomer($username, $c_password, $fullname, $address, $phone, $connection) {
    // Generate new cid  
	$id = $connection->query("SELECT max(cid) FROM Customer");
	$id = $id->fetch_row();
	$new_id = $id[0] + 1;
	
	// Test fields for validity
	 $valid = true;

	// Prepare phone number
	$phone_length = strlen($phone);
	if ($phone_length > 12 || $phone_length < 10 || $phone_length == 11) {
		printf("<h3>We tried calling and we know that's a fake number. Please enter a valid phone number.</h3>");
		$valid = false;
	} else if ($phone_length == 10) {
		$phone = substr($phone, 0, 3) . "-" . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
	}
	
	if($valid) {
		// SQL statement
		$stmt = $connection->prepare("INSERT INTO Customer (cid, username, c_password, fullname, address, phone) VALUES (?,?,?,?,?,?)");
		// Bind the title and pub_id parameters, 'ssssss' indicates 6 strings
    	$stmt->bind_param("ssssss", $new_id, $username, $c_password, $fullname, $address, $phone);
    	// Execute the insert statement
    	$stmt->execute();	
    	// Print success or error message  
	    if($stmt->error) {       
	      printf("<b>Error: %s.</b>\n", $stmt->error);
	    } else {
	      echo "<h3>Thanks, ".$username."! Welcome to AMS!</h3>";
	      echo '<META http-equiv="refresh" content="1; shop.php?' . SID . '">';
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
	 printf("<h3>Error: %s.</h3>\n", $stmt->error);
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
	 printf("<h3>Error: %s.</h3>\n", $stmt->error);
	} else {
	 echo "<h3>We've successfully removed customer with ID ".$id.". No hard feelings! :'(</h3>";
	}
	$stmt->close();
}


function displayCustomers($connection) {
	// Select all of the customer rows
 	if (!$result = $connection->query("SELECT cid, username, c_password, fullname, 
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
			<td class=rowheader>Username</td>
			<td class=rowheader>Password</td>
			<td class=rowheader>Full Name</td>
			<td class=rowheader>Address</td>
			<td class=rowheader>Phone</td>
			<td class=rowheader>Delete?</td>
		</tr>";

	// Display each Customer databaserow as a table row
	while($row = $result->fetch_assoc()){  
		echo "<td>".$row['cid']."</td>";
		echo "<td>".$row['username']."</td>";
		echo "<td>".$row['c_password']."</td>";
		echo "<td>".$row['fullname']."</td>";
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

function addItem($title, $item_type, $category, $company, $item_year, 
	$price, $stock, $connection) {
	
	$id = $connection->query("SELECT max(upc) FROM Item");
	$id = $id->fetch_assoc();
	$id = $id['max(upc)'] + 1;

	// SQL statement
	$stmt = $connection->prepare("INSERT INTO Item (upc, title, item_type, 
		category, company, item_year, price, stock) VALUES (?,?,?,?,?,?,?,?)");
	$stmt->bind_param("ssssssss", $id, $title, $item_type, $category, $company, 
		$item_year, $price, $stock);
	$stmt->execute();	
	
	// Print success or error message  
    if($stmt->error) {       
      printf("<h3>Error: %s.</h3>\n", $stmt->error);
    } else {
      echo "<h3>Wishing you many sales of your newly added item, ".$title.".</h3>";
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
      printf("<h3>Error: %s.</h3>\n", $stmt->error);
    } elseif($quantity != NULL && $stmt2->error) {
      printf("<h3>Error: %s.</h3>\n", $stmt2->error);
    } else {
      echo "<h3>Successfully updated ".$upc.".</h3>";
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
	 printf("<h3>Error: %s.</h3>\n", $stmt->error);
	}	
	$stmt->close();
	
    //Now, delete from Item table:
    $stmt = $connection->prepare("DELETE FROM Item WHERE upc=?");
    $deleteItemUpc = $upc;
	$stmt->bind_param("s", $deleteItemUpc);
	$stmt->execute();
	  
	// Print success or error message
	if($stmt->error) {
	 printf("<h3>Error: %s.</h3>\n", $stmt->error);
	} else {
	 echo "<h3>Item deleted (UPC: ".$upc.")</h3>";
	}
	$stmt->close();
}


function displayDailySalesReport($stmt){
	$stmt->execute();
	$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6);
	// Avoid Cross-site scripting (XSS) by encoding PHP_SELF (this page) using htmlspecialchars.
	echo "<form id=\"add\" name=\"add\" action=\"";
	echo htmlspecialchars($_SERVER["PHP_SELF"]);
	echo "\" method=\"POST\">";
	// Hidden value is used if the add to cart link is clicked
	echo "<input type=\"hidden\" name=\"upc\" value=\"-1\"/>";
	// We need a submit value to detect if delete was pressed
	echo "<input type=\"hidden\" name=\"submitAdd\" value=\"ADD\"/>";
	echo "
	<table border=0 cellpadding=0 cellspacing=0 class=CustomerInfoTable><tr valign=center>
	<td class=rowheader>UPC</td>
	<td class=rowheader>Title</td>
	<td class=rowheader>Category</td>
	<td class=rowheader>Unit Price</td>
	<td class=rowheader>Units Sold</td>
	<td class=rowheader>Total Value</td>
	</tr>";
	// Display each search result field in the table
	// Columns here are individual fields for each result row.
	
	$total_amount1 = 0;
	$total_amount2 = 0;
	$category      = "";
	$row           = $stmt->fetch();
	
	$divider = "- - - - - - - - - - - - - - - - - - - - - -";
	
	do {
		if ($row){	
			if ($category == ""){
				$category = $col3;
				
				echo "<tr><td>".$col1."</td>";
				echo "<td>".$col2."</td>";
				echo "<td>".$col3."</td>";
				echo "<td>$".$col4."</td>";
				echo "<td>".$col5."</td>";
				echo "<td>$".$col6."</td></tr>";
				
				$total_amount1+=$col6;
				$total_amount2+=$col6;
				$row = $stmt->fetch();}
			
			while($row){
				if ($col3 == $category){
					echo "<tr><td>".$col1."</td>";
					echo     "<td>".$col2."</td>";
					echo     "<td>".$col3."</td>";
					echo     "<td>$".$col4."</td>";
					echo     "<td>".$col5."</td>";
					echo     "<td>$".$col6."</td></tr>";
					$total_amount1+=$col6;
					$total_amount2+=$col6;
					$row = $stmt->fetch();}
				else{
					$total = "Total";
					echo "<tr><td>&nbsp;</td>";
					echo "<td><b><u>".$total."</u></b></td>";
					echo "<td>".$divider."</td>";
					echo "<td>".$divider."</td>";
					echo "<td>".$divider."</td>";
					echo "<td><b><u>$".$total_amount1."</u></b></td></tr>";
					$total_amount1 = 0;
					
					echo "<tr><td>".$col1."</td>";
					echo     "<td>".$col2."</td>";
					echo     "<td>".$col3."</td>";
					echo     "<td>$".$col4."</td>";
					echo     "<td>".$col5."</td>";
					echo     "<td>$".$col6."</td></tr>";
					$total_amount1+=$col6;
					$total_amount2+=$col6;
					$category = $col3;					
					
					break;}			
			}
		}
	} while ($row = $stmt->fetch());
	
	$total = "Total";
	$equal = "- - - - - - - - - - - - - -";
	$grand_total = "Total Daily Sales";
	
	echo "<tr><td>&nbsp;</td>";
	echo "<td><b><u>".$total."</u></b></td>";
	echo "<td>".$divider."</td>";
	echo "<td>".$divider."</td>";
	echo "<td>".$divider."</td>";
	echo "<td><b><u>$".$total_amount1."</u></b></td></tr>";	
	
	echo "<tr><td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>".$equal."</td></tr>";	
	
	echo "<tr><td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td>&nbsp;</td>";
	echo "<td><b><u>".$grand_total."</u></b></td>";
	echo "<td><b><u>$".$total_amount2."</u></b></td></tr>";	
	
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
	echo "<h3>Customer CID: ".$cid.". Item UPC: ".$upc."</h3>";

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
    	  printf("<h3>Error: %s.</h3>\n", $stmt->error);
    	} else {
    	  echo "<h3>You've added an item to your shopping cart</h3>";
    	}
	} else {
	echo "<h3>This item is already in your shopping cart</h3>";
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
	$order->bind_param("isssss", $new_id, $date, $cid, $creditcard, $expiry, $date);
	$order->execute();
	if($order->error) {       
    	  printf("<h2>Error creating customer order: ".$order->error."</h2>\n");
    	  }
	if (!$order) {
		echo "<h2>Order creation failed. Please try again.<h2>";
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
		$new->bind_param("sss", $new_id, $upc, $quantity);
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
	echo "<h3>Order placed for ".$_SESSION['cname']." and billed to credit card with number ".$creditcard.".";
	echo " <br/>Your receipt number is: ".$new_id.". Thanks for shopping with AMS!</h3>";

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
	echo "<h3>Item with ID ".$upc." has been updated to a quantity of ".$newqty.".</h3>";

	//check to see if new quantity is 0. if so, delete this item from the cart.
	if ($newqty <= 0){
		$stmt = $connection->prepare("DELETE FROM ShoppingCart WHERE cid = ? AND upc = ?");
		$stmt->bind_param("ss", $cid, $upc);
		$stmt->execute();
	
		// Print success or error message  
		if($stmt->error) {       
    	  printf("<h3>Error: %s.</h3>\n", $stmt->error);
    	  $stmt->close();
    	} else {
    	  echo "<h3>Item removed from shopping cart</h3>";
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
    	  printf("<h3>Error: %s.</h3>\n", $stmt->error);
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
    	  printf("<h3>Error: %s.</h3>\n", $stmt->error);
    	} else {
    	  echo "<h3>Sorry, your order exceeds our available stock! Your order has automatically been reduced to ".$availableqty." items.</h3>";
    	}
		} else{

	// otherwise, if available quantity > desired quantity, add desired quantity to shopping basket.
		$stmt = $connection->prepare("UPDATE ShoppingCart SET quantity = ? WHERE cid = ? AND upc = ?");
		$stmt->bind_param("sss", $newqty, $cid, $upc);
		$stmt->execute();	
		
		// Print success or error message  
    	if($stmt->error) {       
    	  printf("<h3>Error: %s.</h3>\n", $stmt->error);
    	} else {
    	  echo "<h3>Quantity updated</h3>";
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

function processReturnSingle($receiptID, $cid, $upc, $quantity, $connection) {
	$stmt = $connection->prepare(
		"SELECT quantity, count(*) 
		FROM PurchaseItem
		WHERE receiptID=? AND upc=?");
	$stmt->bind_param("ss", $receiptID, $upc);
	$stmt->execute();
	$stmt->bind_result($quantityb, $count);
	$stmt->fetch();
	$stmt->close();
	//Make sure quantity is valid
	if ($quantity > $quantityb)
		echo "You cannot return a quantity greater than quantity purchased.";
	//Make sure UPC is valid
	elseif($count == 0)
		echo "Customer with ID ".$cid." did not purchase any item with UPC ".$upc." in order with ID ".$receiptID.".";

	else {
		// If quantity is unspecified, DELETE PurchaseItem
		if ($quantity == "" || $quantity==$quantityb) {
			$quantity = $quantityb;
			$stmt = $connection->prepare("DELETE FROM PurchaseItem WHERE receiptID=? AND upc=?");
			$stmt->bind_param("ss", $receiptID, $upc);
			$stmt->execute();
			$stmt->close();
			// DELETE Order if empty
			$stmt = $connection->prepare("SELECT count(*) FROM PurchaseItem WHERE receiptID=?");
			$stmt->bind_param("s", $receiptID);
			$stmt->execute();
			$stmt->bind_result($anyleft);
			$stmt->fetch();
			$stmt->close();
			if($anyleft == 0) {
				$stmt = $connection->prepare("DELETE FROM `Order` WHERE receiptID=?");
				$stmt->bind_param("s", $receiptID);
				$stmt->execute();
				$stmt->close();
			}
		} 
		// If quantity is specified, UPDATE PurchaseItem
		else {
			$stmt = $connection->prepare(
				"UPDATE PurchaseItem SET quantity=quantity-? WHERE receiptID=? AND upc=?");
			$stmt->bind_param("sss", $quantity, $receiptID, $upc);
			$stmt->execute();
			$stmt->close();
		}

		//Create date var
		date_default_timezone_set('America/Vancouver');
		$date = date("Y-m-d");
		
		// INSERT Return
			//Generate new id
			$id = $connection->query("SELECT max(retid) FROM `Return`");
			$id = $id->fetch_assoc();
			$id = $id['max(retid)'] + 1;

		$stmt = $connection->prepare("INSERT INTO `Return` (retid, return_date, receiptID) VALUES (?,?,?)");
		$stmt->bind_param("sss", $id, $date, $receiptID);
		$stmt->execute();
		$stmt->close();

		// INSERT ReturnItem
		$stmt = $connection->prepare("INSERT INTO ReturnItem (retid, upc, quantity) VALUES (?,?,?)");
		$stmt->bind_param("sss", $id, $upc, $quantity);
		$stmt->execute();
		$stmt->close();		

		// UPDATE Item stock
		$stmt = $connection->prepare("UPDATE Item SET stock=stock+? WHERE upc=?");
		$stmt->bind_param("ss", $quantity, $upc);
		$stmt->execute();

		//Print success message
		echo "You have successfully returned ".$quantity." items with UPC ".$upc." from order ".$receiptID.".";
	}
}

function processReturn($receiptID, $cid, $connection) {
	// INSERT Return
		//Generate new id
		$id = $connection->query("SELECT max(retid) FROM `Return`");
		$id = $id->fetch_assoc();
		$id = $id['max(retid)'] + 1;
		//Create date var
		date_default_timezone_set('America/Vancouver');
		$date = date("Y-m-d");
	$stmt = $connection->prepare("INSERT INTO `Return` (retid, return_date, receiptID) VALUES (?,?,?)");
	$stmt->bind_param("sss", $id, $date, $receiptID);
	$stmt->execute();
	$stmt->close();

	// INSERT ReturnItem for each Item
	$stmt = $connection->prepare("SELECT upc, quantity FROM PurchaseItem WHERE receiptID=?");
	$stmt->bind_param("s", $receiptID);
	$stmt->execute();
	$stmt->bind_result($upc, $quantity);
	$stmt->store_result();
	while ($row=$stmt->fetch()){
		$insert = $connection->prepare("INSERT INTO ReturnItem VALUES (?,?,?)");
		$insert->bind_param("sss", $id, $upc, $quantity);
		$insert->execute();
		// UPDATE ItemStock
		$shelving = $connection->prepare("UPDATE Item SET stock=stock+? WHERE upc=?");
		$shelving->bind_param("ss", $quantity, $upc);
		$shelving->execute();
	}
	$insert->close();
	$shelving->close();
	$stmt->close();

	// DELETE Order
	$stmt = $connection->prepare("DELETE FROM `Order` WHERE receiptID=?");
	$stmt->bind_param("s", $receiptID);
	$stmt->execute();

	//Print success message
	echo "You have successfully returned order with ID ".$receiptID.".";
}

?>
