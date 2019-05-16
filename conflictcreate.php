<?php
// Include config file
require_once "includes/header.php";
require_once "includes/dbh.inc.php";
// Define variables and initialize with empty values
$role_conflict_err = "";
$input_role_id1 = $input_role_id2 = $role_id1 = $role_id2 = "";
$prod_desc = "";
$prod_id = "";
$input_prod_id = "";

if (isset($_GET["prod_id"]) && !empty(trim($_GET["prod_id"]))){
	$prod_id = $_GET['prod_id'];		
	$sql_roles = "select description, role_id as role_id from roles where prod_id=".$prod_id.";";
	$roles_query = mysqli_query($conn, $sql_roles);
	$roles_query2 = mysqli_query($conn, $sql_roles);
	
	$sql_prod = "select description as production from productions where prod_id=".$prod_id.";";
	$prod_query = mysqli_query($conn, $sql_prod);
	$prod_row = mysqli_fetch_array($prod_query);
	$prod_desc = $prod_row['production'];
	mysqli_free_result($prod_query);	
	
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	$input_role_id1 = trim($_POST["role_id1"]);
	$input_role_id2 = trim($_POST["role_id2"]);
	$input_prod_id = trim($_POST["prod_id"]);	
	$sql_roles = "select description, role_id as role_id from roles where prod_id=".$input_prod_id.";";
	$roles_query = mysqli_query($conn, $sql_roles);
	$roles_query2 = mysqli_query($conn, $sql_roles);
	
	$sql_prod = "select description as production from productions where prod_id=".$input_prod_id.";";
	$prod_query = mysqli_query($conn, $sql_prod);
	$prod_row = mysqli_fetch_array($prod_query);
	$prod_desc = $prod_row['production'];
	mysqli_free_result($prod_query);	
	
    if(strcmp($input_role_id1, $input_role_id2) == 0){
        $role_conflict_err = 'Roles to conflict cannot be the same.';
    }else{
        $role_id1 = $input_role_id1;
		$role_id2 = $input_role_id2;
	}
	
	//find highest conflict_pair_id and set a next highest to use.
	$sql_next_pair_id = "select max(conflict_pair_id) + 1 as next_conflict_pair_id from role_conflicts;";
	$next_pair_id = mysqli_query($conn, $sql_next_pair_id);
	$next_pair_id_row = mysqli_fetch_array($next_pair_id);
	$insert_pair_id = $next_pair_id_row['next_conflict_pair_id'];
	mysqli_free_result($next_pair_id);	
    
    // Check input errors before inserting in database
    if(empty($role_conflict_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO role_conflicts(conflict_pair_id, prod_id, role_id1, role_id2) VALUES (?, ?, ?, ?)";
        //insert first role conflict 
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iiii", $param_conflict_pair_id, $param_prod_id, $param_role_id1, $param_role_id2);
            
            // Set parameters
            $param_conflict_pair_id = $insert_pair_id;
            $param_prod_id = $input_prod_id;
            $param_role_id1 = $role_id1;
			$param_role_id2 = $role_id2;
			
			mysqli_stmt_execute($stmt);
			
        } 
		//insert second role conflict 
		if($stmt = mysqli_prepare($conn, $sql)){
			//rebind parameters to load the swapped conflict.
			 mysqli_stmt_bind_param($stmt, "iiii", $param_conflict_pair_id, $param_prod_id, $param_role_id1, $param_role_id2);
		
			// Set parameters
			$param_conflict_pair_id = $insert_pair_id;
			$param_prod_id = $input_prod_id;
			$param_role_id1 = $role_id2;
			$param_role_id2 = $role_id1;
			
			mysqli_stmt_execute($stmt);
			
			// Records created successfully. Redirect to role_conflict_page
			header("location: conflicts.php");
			exit();
		}
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
}
include_once "includes/crudheader.php";
?>
 
<div class="grid">
    <div class="Title">
		<h1>Conflict Creation</h1>
		<?php echo "<h2>For '".$prod_desc."'</h2>"; ?>
	</div>
    <div class="content">
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($role_conflict_err)) ? 'has-error' : ''; ?>">
				<label>Role 1</label>
				<select name="role_id1" class="form-control">
					<?php 
					while($roles_row = mysqli_fetch_array($roles_query)){
						echo '<option value="' . $roles_row['role_id'] . '">' . $roles_row['description'] . '</option>';
					}
					mysqli_free_result($roles_query);	
					?>
				</select>
				<span class="help-block"><?php echo $role_conflict_err;?></span>
			</div>
			<div class="form-group <?php echo (!empty($role_conflict_err)) ? 'has-error' : ''; ?>">
				<label>Role 2</label>
				<select name="role_id2" class="form-control">
					<?php 
					while($roles_row2 = mysqli_fetch_array($roles_query2)){
						echo '<option value="' . $roles_row2['role_id'] . '">' . $roles_row2['description'] . '</option>';
					}
					mysqli_free_result($roles_query2);	
					?>
				</select>
				<span class="help-block"><?php echo $role_conflict_err;?></span>
			</div>
			<input type="submit" class="btn btn-primary" value="Submit">
			<input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>"/>
			<a href="conflicts.php" class="btn btn-default">Cancel</a>
		</form>
    </div>
</div>
<?php
include_once "includes/footer.php";
?>