<?php
session_start();
$_SESSION['currentpage'] = "insertpositions";
	$pagetitle = "Insert Position Info";
	require_once 'header.php';

	$errormsg = "";
	$showform = 1;
    $posit_err = "";

	require_once "connect.php";


if(isset($_POST['delete']))
{
    $sqlselect_emps = "SELECT * FROM employees WHERE dbpositionid = :bvpositionid";
    $stmtselectemps = $db->prepare($sqlselect_emps);
    $stmtselectemps->bindValue(':bvpositionid', $_POST['positionid']);
    $stmtselectemps->execute();
    $count = $stmtselectemps->rowCount();

    if($count < 1)
    {
        $sqldelete = 'DELETE FROM positions WHERE dbpositionid = :bvpositionid';
        $stmtdelete = $db->prepare($sqldelete);
        $stmtdelete->bindValue(':bvpositionid', $_POST['positionid']);
        $stmtdelete->execute();
    } else {
        echo '<br><div class="alert alert-danger" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Employees currently assigned this position label, cannot delete.</div><br>';
    }
}



		if( isset($_POST['thesubmit']) )
		{
			echo '<p>The form was submitted.</p>';

			$formfield['ffposition'] = trim($_POST['BBposition']);

			if(empty($formfield['ffposition'])){$errormsg .= "<p>Your position label is empty.</p>";}

			if($errormsg != "")
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
					$sqlinsert = 'INSERT INTO positions (dbpositiondescr)
								  VALUES (:bvposition)';
					$stmtinsert = $db->prepare($sqlinsert);
					$stmtinsert->bindvalue(':bvposition', $formfield['ffposition']);
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

	$sqlselect = "SELECT * FROM positions";
	$result = $db->query($sqlselect);
 if($_SESSION['loginpermit'] == 1){
	?>

     <div class="container w-75">
         <div class="row">
             <div class="card card-body bg-light mt-5">
                 <h2>Enter New Positions</h2>
                 <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                     <div class="form-group">
                         <label for="permission">Position Label:</label>
                         <input type="text" name="BBposition" id="BBposition"
                                class="form-control form-control-lg <?php echo (!empty($posit_err)) ? 'is-invalid' : ''; ?>"
                                value="<?php if (isset($formfield['ffposition'])) {
                                    echo $formfield['ffposition'];
                                } ?>"/>
                         <span class="invalid-feedback"><?php echo $posit_err; ?></span>
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
                 <th class="w-25">Position ID</th>
                 <th class="w-50">Position Label</th>
                 <th class="w-25"></th>
             </tr>
             </thead>
             <tbody><?php
             while ($row = $result->fetch()) {
                 echo '<tr><th scope="row">' . $row['dbpositionid'] . '</th><td>' . $row['dbpositiondescr'] . '</td>';
                 echo '<td><form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                 echo '<input type="hidden" name="positionid" id="positionid" value="' . $row['dbpositionid'] . '">';
                 echo '<input type="submit" name="delete" class="btn btn-secondary" value="Delete">';
                 echo '</form></td></tr>';
             }
             ?>
             </tbody>
         </table>
     </div>





<!--	<form method="post" action="--><?php //echo $_SERVER['PHP_SELF']; ?><!--" name="SRform">-->
<!--			<fieldset><legend style="color:white">Insert Position Information</legend>-->
<!--				<table border style="color:white">-->
<!--					<tr>-->
<!--						<th><label for="SRname">Position Label:</label></th>-->
<!--						<td><input type="text" name="BBposition" id="BBposition" size="10" value="--><?php //if( isset($formfield['ffposition'])){echo $formfield['ffposition'];}?><!--"/></td>-->
<!--					</tr>-->
<!--					<tr>-->
<!--						<th>Submit:</th>-->
<!--						<td><input type="submit" name="SRsubmit" value="SELECT" /></td>-->
<!--					</tr>-->
<!--				</table>-->
<!--			</fieldset>-->
<!--		</form>-->
<!--			<br><br>-->
<!--	<table border style="color:white">-->
<!--	<tr>-->
<!--		<th>Position ID</th>-->
<!--		<th>Position Description</th>-->
<!--	</tr>-->
<!---->
<!--	--><?php
//		while ( $row = $result-> fetch() )
//			{
//				echo '<tr><td>' . $row['dbpositionid'] . '</td><td>' .
//							$row['dbpositiondescr'] . '</td></tr>';
//			}
//		?>
<!--	</table>-->
<?php
}
include_once 'footer.php';

?>