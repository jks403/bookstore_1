  <?php
	session_start();
	//$_SESSION[] is a super global like post and get,
	//allows you to store and recall information, starts empty.
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<!--
	Sell Book
	URL: http://nrs-projects.humboldt.edu/~jks403/homework10/sellbooks.php
	By: Josh Stewart
	Last Modified: 4.29.17
-->

<head>
	<title> Sell Book </title>
	<meta charset="utf-8" />
	<?php
		//i need these pages to display, please send them to me.
		// "require" doesn't work without them, include will only warn.
		// "once" says, only do this action one time.
		require_once("logintome.php");
		require_once("droponme.php");
		require_once("confirme.php");
		require_once("completeme.php");
	?>

	<!--normalize this page for many browsers -->
	<link href="http://users.humboldt.edu/smtuttle/styles/normalize.css" 
          type="text/css" rel="stylesheet" />
		  
	<!-- style my page -->
	<link href="bks.css" type="text/css" rel="stylesheet" />
	
	<!-- link a javascript i wrote; validates negative and null values -->
	<script src="validate.js" type="text/javascript"> </script>

</head>

<body> 
	<div id="sell_book">
		<h1> Joshua Kane's  Books </h1>
		<!--PUT YOUR PICTURE HERE: -->
		<img src="book.jpg" alt="Hi Jack"> 
	</div>

	<?php
		//save this value, "true"
		// into the variable 'continue'
		//inside the superglobal array, $_SESSION

		$_SESSION['continue'] = "true";

		//array_key_exists checks for a value
		//if its not there, returns false.  if there, true.

		//array key checks the variable named 'next_page'
		// in the array $_SESSION.  does it exist?

		if(!array_key_exists('next_page', $_SESSION))
		{
			//if it doesn't exist; do this:
			logintome();
			$_SESSION['next_page'] = "dropdown";
		}
		elseif($_SESSION['next_page'] == "dropdown")
		{
			//if in $_SESSION the variable 'next_page'
			//has a value of dropdown, do this stuff:
			droponme();
			$_SESSION['next_page'] = "confirmation";
		}
		elseif($_SESSION['next_page'] == "confirmation")
		{	
			confirme();
			if($_SESSION['continue'] == "false")
			{
				logintome();
				$_SESSION['next_page'] = "dropdown";
				$_SESSION['continue'] = "true";		
			}
			else
			{
				$_SESSION['next_page'] = "complete";
			}
		}
		elseif($_SESSION['next_page'] == "complete")
		{
			completeme();
			if($_SESSION['continue'] == "false")
			{
				create_dropdown();
				$_SESSION['next_page'] = "confirmation";
				$_SESSION['continue'] = "true";
			}
			else
			{
				$_SESSION['next_page'] = "loop";
			}
		}
		elseif($_SESSION['next_page'] == "loop")
		{
			droponme();
			$_SESSION['next_page'] = "confirmation";
		}
		else
		{
			?>
				<p> <strong> YIKES! should NOT have been able to reach 
            	             here! </strong> </p>
        	<?php

        	session_destroy();
        	session_regenerate_id(TRUE);
        	session_start();
    	}
	
    	require_once("328footer.html");

		?>

</body>

</html>
