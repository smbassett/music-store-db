<?php
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["logout"]) && $_POST["logout"] == "Logout") {
			if (isset($_SESSION['cname'])) {
				session_unset();
				echo '<META http-equiv="refresh" content="1; /music-store-db/customer/login.php?">';
			}			
		}
	}
?>

<div id="main">

<div id="header">
<div style = "position: relative;">

	<a href="/music-store-db/index.php" title="Home"><h1>AMS</h1></a> 
	
	<?php
		if (isset($_SESSION['cname']))
			echo '<a href="/music-store-db/customer/shop.php" title="Customers">';
		else echo '<a href="/music-store-db/customer/login.php" title="Customers">';
	?>
	<h2>Customers</h2></a>
	<a href="/music-store-db/clerk/home.php" title="Clerks">
	<h2>Clerks</h2></a>
	<a href="/music-store-db/manager/home.php" title="Managers">
	<h2>Managers</h2></a>

	<?php
	if (isset($_SESSION['cname'])) {
		echo '<div style="padding:0; margin:0; position: absolute; top: 12px; right: 50px" >';
		echo "<h3>Welcome ".$_SESSION['cname']."!</h3></div>";
		echo '<form id="logout" name="logout" method="post" action="';
		echo htmlspecialchars($_SERVER["PHP_SELF"]);
		echo '">';
		echo '<div style="float:right; position: absolute; top: 20px; right: 0px" > <input type="image" name="logout" value="Logout" src="/music-store-db/logout-freepik.png" alt="logout" width="30" height="30"></div>
			</form>';
	} else {
		
	}	
	?>
</div>

	


	<img src="/music-store-db/header_image.jpg" alt="Header image" title="AMS Music Store">
</div>