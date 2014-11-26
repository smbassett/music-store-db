<!DOCTYPE html>
<html>
<head>
<title>AMS Music Store</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
</head>

<body>

<?php include 'header.php'; ?>

	<h1>Welcome to AMS Music Store!</h1>
	<h2>Please select one.</h2>
	<br>
	<?php
		if (isset($_SESSION['cname']))
			echo '<a href="/music-store-db/customer/shop.php" title="Search the shop">';
		else echo '<a href="/music-store-db/customer/login.php" title="Customer Login">';
	?><h3>Customers</h3></a>
	<a href="clerk/home.php" title="Clerk's Page"><h3>Clerks</h3></a>
	<a href="manager/home.php" title="Manager's Page"><h3>Managers</h3></a>

<?php include 'footer.php'; ?> 

</body>

</html>
