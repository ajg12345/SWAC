<?php
include "includes/header.php";
$error_message = "";
$error_code = trim($_GET["error_code"]);
if (!empty($error_code)){
	if(strcmp($error_code,1) == 0){
		$error_message = "The role cannot be deleted because it is referenced in a casting and/or role conflict.<br>Delete the casting and/or role conflict record first.";
	}elseif(strcmp($error_code,2) == 0){
		$error_message = "A role was not deleted because no role_id was specified in the call to the delete page.";
	}elseif(strcmp($error_code,3) == 0){
		$error_message = "A role was not updated for an unknown reason.";
	}elseif(strcmp($error_code,4) == 0){
		$error_message = "A role was not displayed in the roleupdate page for an unknown reason.";	
	}elseif(strcmp($error_code,5) == 0){
		$error_message = "The URL did not contain an ID in the role update page.";		
	}elseif(strcmp($error_code,6) == 0){
		$error_message = "The insert statement when trying to copy castings from one prod to another failed.";				
	}else{
		$error_message = "Unhandled error code in url. Please consult the code master.";
	}
}
?>
<div class="grid">
	<div class="title">
		<h1>Invalid Request</h1>
	</div>
	<div class="content">
		<?php 
		if(!empty($error_message)){
			echo "<p>".$error_message."</p>";
		} else{
			echo '<p>Sorry, you have made an invalid request. Please <a href="index.php" class="alert-link">go back</a> and try again.</p>';
		}
			
		?>
	</div>
</div>
<?php 
include "includes/footer.php";
?>				