<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Manage Returns</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
</head>

<body>
<?php include '../header.php'; ?>
<?php include '../dbops.php'; ?>

<h1>Manage Returns</h1>

<?php

//Connect to database:
$connection = connectToDatabase();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_POST["submit"]) && $_POST["submit"] == "RETURN") {
		
		$valid=false;

		// Didn't enter receipt ID or customer ID
		if ($_POST["receipt"] == "" || $_POST["cid"] == ""){
			echo "Please enter both receipt ID and customer ID to process a return.";
		} else {
			$stmt = $connection->prepare( "SELECT count(*), deliveredDate FROM `Order` WHERE receiptID=? AND cid=?");
			$stmt->bind_param("ss", $_POST["receipt"], $_POST["cid"]);
			$stmt->execute();
			$stmt->bind_result($count, $deliver);
			$stmt->fetch();
			
			// Valid receiptID customerID combo?
			if($count==0) {
				echo "Receipt ID and customer ID combination does not exist. Please try again.";
			} else {
				//Ordered less than 15 days ago?
				date_default_timezone_set('America/Vancouver');
				$date = date("Ymd");
				$date = strtotime($date);
				$deliver = strtotime($deliver);
				if (floor(($date-$deliver)/(60*60*24)) > 15){
					echo "Unfortunately, we can only return items purchased less than 15 days ago.";
				} else {
					$valid=true;
				}	
			}		
			$stmt->close();				
		}

		// Batch return?
		if ($_POST["upc"] == "" && $_POST["quantity"] == "" && $valid)
			processReturn($_POST["receipt"], $_POST["cid"], $connection);
		
		// Return all quantity of a single UPC
		elseif ($_POST["upc"] != "" && $valid)
			processReturnSingle($_POST["receipt"], $_POST["cid"], $_POST["upc"], $_POST["quantity"], $connection);
		
		// Invalid combination
		elseif ($_POST["upc"] == "" && $_POST["quantity"] != "" && $valid)
			echo "Please enter an Item UPC if you wish to return a specific quantity.";
	}
}
	
		
?>


<h2>Process a Return</h2>

<form id="return" name="return" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<table border=0 cellpadding=0 cellspacing=0>
    <tr><td>Receipt ID</td><td><input type="text" size=30 name="receipt"</td></tr>
    <tr><td>Customer ID</td><td><input type="text" size=30 name="cid"</td></tr></tr>
    <tr><td>Item UPC (leave blank to return all items in order)</td><td><input type="text" size=30 name="upc"</td></tr></tr>
    <tr><td>Item Quantity (leave blank to return all)</td><td><input type="text" size=30 name="quantity"</td></tr></tr>
	<tr><td></td><td><input type="submit" name="submit" border=0 value="RETURN"></td></tr>
	</table>
</form>

<br>
<a href="../index.php" title="Home"><h2>&lt;&lt;Back</h2></a>


<?php include '../footer.php'; ?>
</body>

</html>