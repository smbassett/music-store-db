<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Process Orders</title>
<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>

</head>

<body>

<!-- Include header -->
<?php include '../header.php'; ?>

<h1>Process Outstanding Orders</h1>

<?php

  // Include basic database operations
  include '../dbops.php';

// Connect to database
	$connection = connectToDatabase();
?>

<br>

<?php
// Detect user action
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST["submit"]) && $_POST["submit"] == "DELIVER THESE ORDERS") {    
    if (isset($_POST["deliver"]){ 
      
    }
  }
}
?>

<h2>Orders</h2>
<!-- Note: table CSS generated with this useful online tool: http://www.csstablegenerator.com/?table_id=7 -->
<?php

echo "<table border=0 cellpadding=0 cellspacing=0 class='CustomerInfoTable'>";
echo "<tr valign=center>";
echo "
    <td class=rowheader>Deliver?</td>
    <td class=rowheader>Receipt ID</td>
    <td class=rowheader>Customer ID</td>
    <td class=rowheader>Date Ordered</td>
    <td class=rowheader>Date Expected</td>
  </tr>";

$stmt = $connection->query("SELECT receiptID, order_date, cid, expectedDate FROM `Order` WHERE deliveredDate IS NULL
  ORDER BY receiptID");

while($row = $stmt->fetch_assoc()){  
  echo "<tr><td><input type=checkbox name='deliver' value='";
  echo $row['receiptID']."' border=0></td>";
  echo "<td>".$row['receiptID']."</td>";
  echo "<td>".$row['cid']."</td>";
  echo "<td>".$row['order_date']."</td>";
  echo "<td>".$row['expectedDate']."</td></tr>";
}
  
echo "</table>";    
  
  
 // Disconnect from database
mysqli_close($connection);


?>


<br>

<form id="add" name="add" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
  <input type="submit" name="submit" border=0 value="DELIVER THESE ORDERS">
</form>

<br>

<a href="home.php" title="Manager's Page"><h2>&lt;&lt;Back</h2></a>

<?php include '../footer.php'; ?>
</body>
</html>