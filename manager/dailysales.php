<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Manage Items</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'> 

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
    
    if (isset($_POST["submit"]) && $_POST["submit"] ==  "VIEW REPORT") {

		$year           = $_POST["year"];
		$month          = $_POST["month"];
		$day            = $_POST["day"];   
		
		if ($year && $month && $day){
			$date = $year.$month.$day;

			$stmt = $connection->prepare(
		
				"SELECT I.upc, I.title, I.item_type, I.category, I.company, I.price, SUM(quantity) as Quantity_Sold, I.stock as Quantity_Remaining
				FROM `Order` O, PurchaseItem PI, Item I
				WHERE O.receiptID = PI.receiptID and PI.upc = I.upc and order_date=?
				GROUP BY upc"); 
		 
			$stmt->bind_param("s", $date);
			displayDailySalesReport($stmt);
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

<h2>ENTER DATE FOR PURCHASE RECORD</h2>

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <table border=0 cellpadding=0 cellspacing=0>
    <tr><td>Year</td><td><input type="text" size=20 name="year"></td></tr>
    <tr><td>Month</td><td><input type="text" size=20 name="month"></td></tr>
    <tr><td>Day</td><td><input type="text" size=20 name="day"></td></tr>
    <tr><td></td><td><input type="submit" name="submit" border=0 value="VIEW REPORT"></td></tr>
  </table>
</form>

<?php include '../footer.php'; ?>
</body>
</html>