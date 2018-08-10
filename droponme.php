
<?php
	function droponme()
	{
		//first time we run this, do this stuff here:
		if($_SESSION['next_page'] == "dropdown")
		{
			//grab the values 'username' and 'password'
			//from the POST superglobal array, named $_POST
			//store them locally so we can use them
			$username = strip_tags($_POST['username']);
			$password = $_POST['password'];
			
			//storing these values into $_SESSION for later use
			$_SESSION['username'] = $username;
			$_SESSION['password'] = $password;
		}
		else
		{
			//if we run it again, do this stuff:
			$username = $_SESSION['username'];
			$password = $_SESSION['password'];
		}

		//this is the way we connect to NRS projects, only.
		$db_conn_str = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)
                                       (HOST = cedar.humboldt.edu)
                                       (PORT = 1521))
                                       (CONNECT_DATA = (SID = STUDENT)))";
	//this is the string we need to make connection
        $conn = oci_connect($username, $password, $db_conn_str);

	//if we can't connect, display a warning.
        if (! $conn)
        {
        	?>
            <p> Could not log into Oracle, sorry. </p>

            <?php
           		require_once("328footer.html");
           		session_destroy();
            		
			//exit is a call that doesn't process anything below,
			//"exits" anything beyond this point.
			exit;        
        }

		//if we can connect, display all this stuff below.
		?>
		<p class="title"> Dropdown </p>
		<?php
			//make a new query
			$query_isbn = 'select isbn, title_name '.
                      	  'from title';
			//get it ready
			$stmt = oci_parse($conn, $query_isbn);
        //send it to the server
	oci_execute($stmt, OCI_DEFAULT);
        ?>

	<!-- new form, on the submit button click (also known as a 
	event handler), check for the RETURN
	of the function, "validation()".  
	IF true, proceed.
	IF false, do not allow form to post.  -->
        <form method="post" onsubmit="return (validation() );" 
              action="<?= htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES) ?>" 
			  name="drop_down">
        	<fieldset>
            	<legend class="legend"> Select a Title and Quantity </legend>
				
				<label for = "isbn" > Title: </label>
				<div>
					<select name="isbn" size="3" class="select">
              	<?php
			//remember that statement we sent?  go get it, display results.
                	//keep grabbing until fetch is empty, all ISBNS and titles.
			while(oci_fetch($stmt))
              		{
                		$curr_isbn = oci_result($stmt, "ISBN");
                		$curr_title = oci_result($stmt, "TITLE_NAME");
                ?>
                		<option value= <?= $curr_isbn ?>> <?= $curr_title ?> (<?= $curr_isbn ?>) </option>
                <?php
              		}
			//basic maintenance, close what you open.
                	oci_free_statement($stmt);
                	oci_close($conn);
                ?>
					</select>
				</div>
				<label for = "quantity" > Quantity Sold: </label>
				<div>
					<input type="number" class="select" name="quantity" value = 1 
					       min="1" id="quantity" required="required">
				</div>
				<div class="buttons">
					<input type="submit" name="submit" value="Proceed" />
					<input type="submit" name="submit" value="Exit" />
				</div>
			</fieldset>
		</form>
        <?php 
	}
?>
