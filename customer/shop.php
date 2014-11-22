<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">

<title>AMS Online</title>

<link href="../style.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>


<body>
<!-- Include header -->
<?php include '../header.php'; ?>

<h1>Welcome to AMS Online!</h1>

<?php

	/* Establish Connection to Database */
    $username = "root";
	$password = "";
	$hostname = "localhost";
	$connection = new mysqli($hostname, $username, $password, "AMS");
	
	if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
		if (isset($_POST["submit"])) {
			$category       = $_POST["category"];
			$title          = $_POST["title"];
			$leading_singer = $_POST["leading_singer"];
		
		if (!$category && !$title && !$leading_singer){
				echo("Please enter item specifications!");
				echo '<META http-equiv="refresh" content="1; shop.php">';
			}
				
		elseif (!$category && !$title){
		$stmt = $connection->prepare("SELECT * FROM Item WHERE leading_singer=?");
			$stmt->bind_param("s", $leading_singer);
			$stmt->execute();
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);} 
			elseif($stmt->fetch())
				printf("LeadSinger in DB.");
			$stmt->close();		
		}
		elseif (!$category && !$leading_singer){
			$stmt = $connection->prepare("SELECT * FROM Item WHERE title=?");
			$stmt->bind_param("s", $title);
			$stmt->execute();
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);} 
			elseif($stmt->fetch())
				printf("Title in DB.");
			$stmt->close();	
		}
		elseif (!$title && !$leading_singer){
			$stmt = $connection->prepare("SELECT * FROM Item WHERE category=?");
			$stmt->bind_param("s", $category);
			$stmt->execute();
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);} 
			elseif($stmt->fetch())
				printf("Category in DB.");
			$stmt->close();		
		}
		elseif (!$category){
			$stmt = $connection->prepare("SELECT * FROM Item WHERE title=? and leading_singer=?");
			$stmt->bind_param("ss", $title, $leading_singer);
			$stmt->execute();
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);} 
			elseif($stmt->fetch())
				printf("TS in DB.");
			$stmt->close();	
		}
		elseif (!$title){
			$stmt = $connection->prepare("SELECT * FROM Item WHERE category=? and leading_singer=?");
			$stmt->bind_param("ss", $category, $leading_singer);
			$stmt->execute();
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);} 
			elseif($stmt->fetch())
				printf("CS in DB.");
			$stmt->close();	
		}
		elseif (!$leading_singer){
			
			$stmt = $connection->prepare("SELECT title, item_type, category, company, price, stock FROM Item WHERE category=? and title=?");
			$stmt->bind_param("ss", $category, $title);
			$stmt->execute();
			
			$stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6);
			
			if($stmt->error) {
				printf("<b>Error: %s.</b>\n", $stmt->error);
			} 
			else{
			echo "<table>";
				while ($stmt->fetch()){
					echo "<tr><td>".$col1."</td><td>".$col2."</td><td>".$col3."</td></tr>";
				}
			echo "</table>";
			}
			$stmt->close();		
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
</div>
<?php include '../footer.php'; ?>
</body>
</html>
