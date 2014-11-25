<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Top Selling Items</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'> 

</head>
<body>
<!-- Include header -->
<?php include '../header.php'; ?>

<h1>Top Selling Items</h1>
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
    
    if (isset($_POST["top-selling"]) && $_POST["top-selling"] ==  "SHOW") {

		$year           = $_POST["year"];
		$month          = $_POST["month"];
		$day            = $_POST["day"];
		$num_items      = $_POST["num_items"];
		
		if ($year && $month && $day){
			$date = $year.$month.$day;
			$stmt = $connection->prepare(
			   "SELECT P.upc, sum(P.quantity), I.title, I.company, I.stock
				FROM `Order` O JOIN PurchaseItem P JOIN Item I 
				ON O.receiptID=P.receiptID AND P.upc = I.upc
				WHERE O.order_date=?
				GROUP BY P.upc
				HAVING sum(P.quantity)
				ORDER BY sum(P.quantity) DESC, P.upc");
		 
			$stmt->bind_param("s", $date);
			$stmt->execute();
			$stmt->bind_result($upc, $sumqty, $title, $company, $stock);

			echo "<h2>Best Sales For ".$year."-".$month."-".$day."</h2>";
			
			echo "<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'><tr valign=center>
				<td class=rowheader>UPC</td>
				<td class=rowheader>Item Name</td>
				<td class=rowheader>Company</td>
				<td class=rowheader>Current Stock</td>
				<td class=rowheader>Quantity Sold</td>
				</tr>";

			$i = 0;
			while($row=$stmt->fetch() && $i < $_POST["num_items"]){
				echo "<tr><td>".$upc."</td>";
				echo "<td>".$title."</td>";
				echo "<td>".$company."</td>";
				echo "<td>".$stock."</td>";
				echo "<td>".$sumqty."</td></tr>";
				$i++;
			}
			echo "</table>";
			echo "<h3>Showing ".$i." items</h3>";

		}
			
		elseif(!$year && !$month && !$day){
			printf("Enter the date for which to view sales report");
		}	
		else{
			printf("Not enough information to conduct sales report");
		}
	}
}

?>

<h2>QUERY BEST SELLING ITEMS BY DATE</h2>

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table border=0 cellpadding=0 cellspacing=0>
    <tr><td>Year</td><td><input type="text" size=20 name="year"></td></tr>
    <tr><td>Month</td><td><input type="text" size=20 name="month"></td></tr>
    <tr><td>Day</td><td><input type="text" size=20 name="day"></td></tr>
    <tr><td>Number of Items</td><td><input type="text" size=20 name="num_items"></td></tr>
    <tr><td></td><td><input type="submit" name="top-selling" border=0 value="SHOW"></td></tr>
  </table>
</form>

<br>

<a href="home.php" title="Manager's Page"><h2>&lt;&lt;Back</h2></a>

<?php include '../footer.php'; ?>
</body>
</html>