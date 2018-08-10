<?php
	function completeme()
	{
		$value = $_POST['submit'];
		if($value == "Cancel")
		{
			$_SESSION['continue'] = "false";
		}
		else
		{
			?>
			<h2 class="title"> Sale Complete! Enjoy your whiskey. </h2>
			
			<?php
			$username = $_SESSION['username'];
			$password = $_SESSION['password'];
			
			$isbn = $_SESSION['isbn'];
			$quantity = $_POST['quantity'];
			
			$db_conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)
                                       (HOST = cedar.humboldt.edu)
                                       (PORT = 1521))
                            (CONNECT_DATA = (SID = STUDENT)))";

        
			$conn = oci_connect($username, $password, $db_conn_str);


			//heres the magic!
			// local variable "$sell_book" is set to the RETURN
			//of the value, given in the PL,SQL procedure "sell_books"
			//sell_books() takes two bind variables as its parameters
			//these parameters are the ":isbn" and the ":quantity"

			$sell_book= 'BEGIN :b := sell_books(:isbn,:quantity);END;';
			
			$stmt = oci_parse($conn, $sell_book);
			
			oci_bind_by_name($stmt, ":b", $sell_book, 10000);   
			oci_bind_by_name($stmt, ":isbn", $isbn);
			oci_bind_by_name($stmt, ":quantity", $quantity);
			
			oci_execute($stmt, OCI_DEFAULT);

			//commit all changes locally to your database
			oci_commit($conn);
			
			oci_free_statement($stmt);
			oci_close($conn);
			
			
			
			
			$db_conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)
                                       (HOST = cedar.humboldt.edu)
                                       (PORT = 1521))
                            (CONNECT_DATA = (SID = STUDENT)))";

        
			$conn = oci_connect($username, $password, $db_conn_str);
			
			$summary = "select qty_on_hand ".
					   "from title ".
					   "where isbn = :isbn";
					   
			$stmt = oci_parse($conn, $summary);
			
			oci_bind_by_name($stmt, ":isbn", $isbn);
			
			oci_execute($stmt, OCI_DEFAULT);
			
			while(oci_fetch($stmt))
			{
				$qty_on_hand = oci_result($stmt, "QTY_ON_HAND");
			}
			oci_free_statement($stmt);
			oci_close($conn);
			
			?>

			<img src="meme.jpg" alt="Just for you, Sharon." >
			
			<form method="post" 
                  action="<?= htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES) ?>">
        		<fieldset>
					<p> Copies of ISBN successfully sold: <?= $quantity ?> </p>
					<p> Copies left: <?= $qty_on_hand ?> </p>
					<div class="buttons">
						<input type="submit" name="submit" value="Ok" />
					</div>
				</fieldset>
			</form>
			<?php
		}
	}
?>
