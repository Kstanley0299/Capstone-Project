<?php
session_start();
$_SESSION['currentpage'] = "insertPermissions";


$pagetitle = 'Insert Permissions';
require_once 'header.php';
require_once 'connect.php';

$errormsg = "";
$showform = 1;
$perm_err = "";

if(isset($_POST['delete']))
{
    $sqlselect_perms = "SELECT * FROM employees WHERE dbpermissionid = :bvpermissionid";
    $stmtselectperms = $db->prepare($sqlselect_perms);
    $stmtselectperms->bindValue(':bvpermissionid', $_POST['permid']);
    $stmtselectperms->execute();
    $count = $stmtselectperms->rowCount();

    if($count < 1)
    {
        $sqldelete = 'DELETE FROM permission WHERE dbpermissionid = :bvpermissionid';
        $stmtdelete = $db->prepare($sqldelete);
        $stmtdelete->bindValue(':bvpermissionid', $_POST['permid']);
        $stmtdelete->execute();
    } else {
        echo '<br><div class="alert alert-danger" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">User(s) currently assigned this permission level, cannot delete.</div><br>';
    }
}

		if( isset($_POST['thesubmit']) )
		{
			echo '<p>The form was submitted.</p>';

            /*  ****************************************************************************
             CHECK FOR EMPTY FIELDS
            Complete the lines below for any REQIURED form fields only.
            Do not do for optional fields.
            **************************************************************************** */
            if(empty($_POST['BBpermissionlevel']))
            {   $errormsg .= "<p>Your permission is empty.</p>";
                $perm_err = "Please enter a title for this permission level.";
            }

			//Data Cleansing
			$formfield['ffpermissionlevel'] = trim($_POST['BBpermissionlevel']);
		

			/*  ****************************************************************************
			DISPLAY ERRORS
			If we have concatenated the error message with details, then let the user know
			**************************************************************************** */
			if($errormsg != "" && $perm_err != "")
			{
				echo "<div class='error'><p>THERE ARE ERRORS!</p>";
				echo $errormsg;
				echo "</div>";
			}
			else
			{
				try
				{
					//enter data into database
					$sqlinsert = 'INSERT INTO permission (dbpermissionlevel)
								  VALUES (:bvpermissionlevel)';
					$stmtinsert = $db->prepare($sqlinsert);
					$stmtinsert->bindvalue(':bvpermissionlevel', $formfield['ffpermissionlevel']);
					$stmtinsert->execute();
					echo "<div class='success'><p>There are no errors.  Thank you.</p></div>";
				}//try
				catch(PDOException $e)
				{
					echo 'ERROR!!!' .$e->getMessage();
					exit();
				}
			}//else statement end
		}//if isset submit


$sqlselect = 'SELECT * FROM permission';
$result = $db->query($sqlselect);

if ($visible == 1)
{
	?>
		<!--<form style="color:white;" method="post" action="<?php /*echo $_SERVER['PHP_SELF']; */?>" name="theform">
			<fieldset><legend>Permission Information</legend>
				<table border>
					<tr>
						<th>Permission</th>
						<td><input type="text" name="BBpermissionlevel" id="BBpermissionlevel"
						value = "<?php /*if(isset($formfield['ffpermissionlevel'])) { echo $formfield['ffpermissionlevel']; } */?>"></td>
					</tr>
				</table>
				<input type="submit" name = "thesubmit" value="Enter">

			</fieldset>
		</form>
			<br><br>
	<table border style="color:white">
	<tr style="color:white;">
		<th>Permission ID</th>
		<th>Permission level</th>		
	</tr>
	<?php /*
		while ( $row = $result-> fetch() )
			{
				echo '<tr><td>' . $row['dbpermissionID'] . '</td><td> ' . $row['dbpermissionlevel'] . '</td></tr> ';
			}
		*/?>
	</table>-->


    <div class="container w-75">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Enter New Permission Levels</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <div class="form-group">
                        <label for="permission">Permission:</label>
                        <input type="text" name="BBpermissionlevel" id="BBpermissionlevel"
                               class="form-control form-control-lg <?php echo (!empty($perm_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffpermission'])) {
                                   echo $formfield['ffpermission'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $perm_err; ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col text-center">
                            <input type="submit" value="Enter" name="thesubmit" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br><br><br>

    <div class="align-content-center container w-75">
        <table class="table table-hover bg-white">
            <thead class="thead-light">
            <tr>
                <th class="w-25">Permission ID</th>
                <th class="w-50">Permission Level</th>
                <th class="w-25"></th>
            </tr>
            </thead>
            <tbody><?php
            while ($row = $result->fetch()) {
                echo '<tr><th scope="row">' . $row['dbpermissionid'] . '</th><td>' . $row['dbpermissionlevel'] . '</td>';
                echo '<td><form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                echo '<input type="hidden" name="permid" id="permid" value="' . $row['dbpermissionid'] . '">';
                echo '<input type="submit" name="delete" class="btn btn-secondary" value="Delete">';
                echo '</form></td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>









<?php
}
include_once 'footer.php';
?>