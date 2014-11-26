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

<h1>View Purchases</h1>

<?php
//Connect to database:
$connection = connectToDatabase();
?>

<h2>Purchases</h2>

<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'>
	<tr valign=center>
		<td class=rowheader>Purchase ID</td>
		<td class=rowheader>Customer ID</td>
		<td class=rowheader>Item UPC</td>
		<td class=rowheader>Item Name</td>
		<td class=rowheader>Quantity</td>
		<td class=rowheader>Order Placed Date</td>
		<td class=rowheader>Expected Date</td>
		<td class=rowheader>Delivered Date</td>
	</tr>

<?php

$stmt = $connection->query(
	"SELECT O.receiptID, O.cid, P.upc, I.title, P.quantity, O.order_date, O.expectedDate, O.deliveredDate
	FROM `Order` O JOIN PurchaseItem P JOIN Item I
	ON O.receiptID=P.receiptID AND P.upc=I.upc
	ORDER BY O.receiptID, P.upc");

while($row=$stmt->fetch_assoc()) {
	echo "<tr><td>".$row['receiptID']."</td>";
	echo "<td>".$row['cid']."</td>";
	echo "<td>".$row['upc']."</td>";
	echo "<td>".$row['title']."</td>";
	echo "<td>".$row['quantity']."</td>";
	echo "<td>".$row['order_date']."</td>";
	echo "<td>".$row['expectedDate']."</td>";
	echo "<td>".$row['deliveredDate']."</td></tr>";
}
?>
</table>




<br>
<a href="home.php" title="Home"><h2>&lt;&lt;Back</h2></a>


<?php include '../footer.php'; ?>
</body>

</html>