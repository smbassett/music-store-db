<?php

// CUSTOMER

function addCustomer($id, $password, $name, $address, $phone, $connection) {
	// SQL statement
	$stmt = $connection->prepare("INSERT INTO Customer (cid, c_password, cname, address, phone) VALUES (?,?,?,?,?)");
          
    // Bind the title and pub_id parameters, 'sssss' indicates 5 strings
    $stmt->bind_param("sssss", $id, $password, $name, $address, $phone);
    
    // Execute the insert statement
    $stmt->execute();
    
	// Print success or error message  
    if($stmt->error) {       
      printf("<b>Error: %s.</b>\n", $stmt->error);
    } else {
      echo "<b>Successfully added ".$name."</b>";
    }
}

function deleteCustomer($id, $connection) {
	// Create a delete query prepared statement with a ? for the title_id
    $stmt = $connection->prepare("DELETE FROM Customer WHERE cid=?");
    $deleteCustID = $id;

    // Bind the title_id parameter, 's' indicates a string value
	$stmt->bind_param("s", $deleteCustID);

	// Execute the delete statement
	$stmt->execute();
	  
	// Print success or error message
	if($stmt->error) {
	 printf("<b>Error: %s.</b>\n", $stmt->error);
	} else {
	 echo "<b>Successfully deleted ".$id."</b>";
	}
}

?>