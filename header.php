<?php
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if(isset($_POST["logout"]) && $_POST["logout"] == "Logout") {
			if (isset($_SESSION['cname'])) {
				session_unset();
				echo '<META http-equiv="refresh" content="1; /music-store-db/customer/login.php">';
			}			
		}
	}
?>

<div id="main">

<div id="header">

	<a href="/music-store-db/index.php" title="Home"><h1>AMS</h1></a> 
	
	
	<a href="/music-store-db/customer/login.php" title="Customers"><h2>Customers</h2></a>
	<a href="/music-store-db/clerk/returns.php" title="Clerks"><h2>Clerks</h2></a>
	<a href="/music-store-db/manager/home.php" title="Managers"><h2>Managers</h2></a>

	<?php
	if (isset($_SESSION['cname'])) {
		echo "<h2>Welcome ".$_SESSION['cname']."!</h2>";
		echo '<form id="logout" name="logout" method="post" action="';
		echo htmlspecialchars($_SERVER["PHP_SELF"]);
		echo '">';
		echo '<input type="submit" name="logout" border=0 value="Logout">
			</form>';
	} else {
		
	}	
	?>
	

	


	<img src="/music-store-db/header_image.jpg" alt="Header image" title="AMS Music Store">
</div>