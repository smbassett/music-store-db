<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<!--
    Javascript to submit an Item UPC as a POST form, used with the "ADD" links (add to cart).
-->
<script>
function formSubmit(itemUpc) {
    'use strict';
      // Set the value of a hidden HTML element in this form
      var form = document.getElementById('add');
      form.upc.value = itemUpc;
      // Post this form
      form.submit();
}
</script>


<title>AMS Online</title>

<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>


<body>
<!-- Include header -->
<?php include '../header.php'; ?>

<h1>Welcome to AMS Online!</h1>

<?php
	// Include basic database operations
	include '../dbops.php';
	
	//Connect to database
	$connection = connectToDatabase();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
		if (isset($_POST["submit"])) {
			$category       = $_POST["category"];
			$title          = $_POST["title"];
			$leading_singer = $_POST["leading_singer"];
		
		if (!$category && !$title && !$leading_singer){
				echo("Please enter item specifications!");
				echo '<META http-equiv="refresh" content="1; shop.php">';

		}
		elseif ($category && $title && $leading_singer){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE category=? AND title=? AND upc IN (SELECT upc FROM LeadSinger WHERE singer_name = ?)");
			$stmt->bind_param("sss", $category, $title, $leading_singer);
			displaySearchResults($stmt);	
		}		
		elseif (!$category && !$title){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE upc IN (SELECT upc FROM LeadSinger WHERE singer_name = ?)");
			$stmt->bind_param("s", $leading_singer);
			displaySearchResults($stmt);	
		}
		elseif (!$category && !$leading_singer){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE title=?");
			$stmt->bind_param("s", $title);
			displaySearchResults($stmt);	
		}
		elseif (!$title && !$leading_singer){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE category=?");
			$stmt->bind_param("s", $category);
			displaySearchResults($stmt);	
		}
		elseif (!$category){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE title=? AND upc IN (SELECT upc FROM LeadSinger WHERE singer_name = ?)");
			$stmt->bind_param("ss", $title, $leading_singer);
			displaySearchResults($stmt);	
		}
		elseif (!$title){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE category=? AND upc IN (SELECT upc FROM LeadSinger WHERE singer_name = ?)");
			$stmt->bind_param("ss", $category, $leading_singer);
			displaySearchResults($stmt);	
		}
		elseif (!$leading_singer){
			$stmt = $connection->prepare("SELECT upc, title, item_type, category, company, item_year, price, stock FROM Item WHERE category=? and title=?");
			$stmt->bind_param("ss", $category, $title);	
			displaySearchResults($stmt);			
		}
		
		}
		
	// Detect user action
  	if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST["submitAdd"]) && $_POST["submitAdd"] == "ADD") {
      // Add item to cart. To do this, need to grab customer CID that was passed to this page
      // from login.php via a php 'session'.
      session_start();     		  
      addItemToCart($_SESSION['cid'], $_POST["upc"], $connection);       
    }
  }	
}
?>

<div id="shop">
<h2>Search for Item</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
<div id=text_inputs>
   Category:       <input type="text" name="category"><br><br>
   Title:          <input type="text" name="title"><br><br>
   Leading Singer: <input type="text" name="leading_singer"><br><br>
                   <input type="submit" name="submit" value="SUBMIT"> 
</form>	
</div></div>
<?php include '../footer.php'; ?>
</body>
</html>
