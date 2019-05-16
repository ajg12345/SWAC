<?php
include_once "includes/header.php";
include_once "includes/crudheader.php";
include_once 'includes/dbh.inc.php';
//get casting labels lke location, time, prod etc.
$production = "";
$prod_id = "";

if(isset($_GET["prod_id"]) && !empty(trim($_GET["prod_id"]))){
	// Get header text from GET variable and mysql
	$prod_id =  trim($_GET["prod_id"]);					
	
	$sql_title = "SELECT  pro.prod_id,
				pro.description as production
				FROM productions as pro
				where pro.prod_id = " . $prod_id . ";";
	if($result_title = mysqli_query($conn, $sql_title)){
		while($row_title = mysqli_fetch_array($result_title)){
			$production = $row_title['production'];
			$prod_id = $row_title['prod_id'];
		}
	}
	//find list of prods without roles to populate the drop down select list.
	$sql_copy_dest = "SELECT  pro.prod_id as new_prod_id,
				pro.description as production
				FROM productions as pro
				where pro.prod_id not in (select prod_id from roles) order by create_dt desc;";
				
	$result_destinations = mysqli_query($conn, $sql_copy_dest);
	
	
}

if(isset($_POST["prod_id"]) && !empty($_POST["prod_id"]) && isset($_POST["new_prod_id"]) && !empty($_POST["new_prod_id"])){
	
	$input_prod_id = trim($_POST["prod_id"]);
	$input_new_prod_id = trim($_POST["new_prod_id"]);
	$sql_insert_statment = "insert into roles(description, prod_id, role_count)
							select description, ?, role_count
							from roles where prod_id = ?;";
        if($stmt = mysqli_prepare($conn, $sql_insert_statment)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ii", $param_new_prod_id, $param_prod_id);
            // Set parameters
            $param_prod_id = $input_prod_id;
			$param_new_prod_id = $input_new_prod_id;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page which now has proper castings.
				$location_dest = "location: roles.php#Roles_copied_into_production_id_".$input_new_prod_id;
                header($location_dest);
                exit();
            } else{                
				$header_target = "location: error.php?error_code=9";
				header($header_target);
				exit();
            }		
		}			
}
?>

<div class= "grid">
	<div class="title">
		<h1>Copy the Roles of:</h1>
		<?php echo "<h2>".$production."</h2>"; ?>
		<h2>to ...</h2>
	</div>
	<div class="content"> 
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($prod_id_err)) ? 'has-error' : ''; ?>">
				<label>Productions without Roles</label>
				<select name="new_prod_id" class="form-control">
					<?php 
					while($dest_row = mysqli_fetch_array($result_destinations)){
						$option_string = '<option value="' . $dest_row['new_prod_id'] . '">';	//open tag
						$option_string = $option_string . $dest_row['production']; //interior
						$option_string = $option_string . '</option>'; //close tag
						echo $option_string;
					}
					?>
				</select>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>"/>
			<a href="roles.php?prod_id=<?php echo $prod_id;?>" class="btn btn-default">Cancel</a>
		</form>	
	</div>	
</div>	
	
	

<?php
include_once "includes/footer.php";
?>

