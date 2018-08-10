<?php
	function confirme()
	{
		$value = $_POST['submit'];
		if($value == "Exit")
		{
			$_SESSION['continue'] = "false";
		}
		else
		{
			?>
			<h2 class="title"> Confirmation of Whiskey/book sale: </h2>
			
			<?php
			$username = $_SESSION['username'];
			$password = $_SESSION['password'];
			
			//accessing the superglobal $_POST, 
			// the key 'isbn' to grab its value, and
			//store it locally in the variable $isbn
			$isbn = $_POST['isbn'];

			//take that local variable
			//save it for later in the $_SESSION superglobal
			//with the key, 'isbn'
			$_SESSION['isbn'] = $isbn;

			//do the same thing for quantity
			$quantity = $_POST['quantity'];
			$_SESSION['quantity'] = $quantity;
			
			//login to NRS projects
			$db_conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)
                                           (HOST = cedar.humboldt.edu)
                                           (PORT = 1521))
									       (CONNECT_DATA = (SID = STUDENT)))";
			//open connection
			$conn = oci_connect($username, $password, $db_conn_str);
			
			//make our query we need for the server
			$isbn_query = "select pub_name, title_name, author, title_price ".
						  "from title T, publisher P ".
						  "where T.pub_id = P.pub_id and (isbn = :isbn)";
			//in the above query,   
			//we are now using a bind variable, named ':isbn'			

			//get it ready
			$stmt = oci_parse($conn, $isbn_query);
			
			//use our bind variable, from the '$stmt' statement,
			//grab the variable ':isbn'
			//store into it, the value that was in the
			// variable, '$isbn'
			oci_bind_by_name($stmt, ":isbn", $isbn);
			
			//yea, fucking do it.
			oci_execute($stmt, OCI_DEFAULT);
			?>
			<!-- make and display results in table -->
			<div class="table">
			<table>
			<tr> <th scope="col"> ISBN </th>
				 <th scope="col"> Publisher </th>
				 <th scope="col"> Sold </th>
				 <th scope="col"> Title </th>
				 <th scope="col"> Auther </th> 
				 <th scope="col"> Price </th>
				 <th scope="col"> Subtotal </th>
				 <th scope="col"> Tax </th>
				 <th scope="col"> Total </th>
			</tr>
		
			<?php
			//while theres still stuff in fetch, display ALL
			//the results
			while(oci_fetch($stmt))
			{
			$tax = 0.05;

			//grab the pub_name from the query string '$stmt'
			//store it locally in the variable '$pub_name'
			$pub_name = oci_result($stmt, "PUB_NAME");
			$title_name = oci_result($stmt, "TITLE_NAME");
			$author = oci_result($stmt, "AUTHOR");
			$title_price = oci_result($stmt, "TITLE_PRICE");
            ?>

			<tr> <td> <?= $isbn ?> </td>
				 <td> <?= $pub_name ?> </td> 
				 <td> <?= $quantity ?> </td>
				 <td> <?= $title_name ?> </td>
				 <td> <?= $author ?> </td>
				 <td> <?= $title_price ?> </td>
				 <td> <?= ($title_price * $quantity) ?> </td>
				 <td> <?= $tax ?> </td>
				 <td> <?= round(((($title_price * $quantity) * $tax) + ($title_price * $quantity)), 2)?> </td>
			</tr>
			</table>
			</div>
		<?php
			}
		//what we open we must close
		oci_free_statement($stmt);
        	oci_close($conn);
		?>
        	<form method="post" onsubmit="return (validateForm() );" 
                  action="<?= htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES) ?>" 
			      name="confirmation">
        		<fieldset>
					<label for = "quantity" > Confirm Quantity: </label>
					<div>
						<input type="number" name="quantity" class="select" value = <?= $quantity ?> 
						       min="1" id="quantity" required="required">
					</div>
					<div class="buttons">
						<input type="submit" name="submit" value="Complete" />
						<input type="submit" name="submit" value="Cancel" />
					</div>
				</fieldset>
			</form>
			<?php
		}
	}
?>
