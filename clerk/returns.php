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
		processReturn($_POST["receipt"], $_POST["cid"], $_POST["upc"], $_POST["quantity"], $connection);
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