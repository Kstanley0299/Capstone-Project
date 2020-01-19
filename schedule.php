<?php
session_start();
$_SESSION['currentpage'] = "schedule";

$pagetitle = "View Our Daily Hours of Operation";

require_once 'header.php';
require_once 'connect.php';

$formfield['ffhourkey'] = $_POST['hourkey'];
$formfield['ffhoursopen'] = $_POST['opentime'];
$formfield['ffhoursclosed'] = $_POST['closetime'];
$formfield['ffsetopen'] = $_POST['setopen'];
$formfield['ffsetclosed'] = $_POST['setclose'];



$change = 1;


if( isset($_POST['update']))
{
	$sql = "UPDATE hours SET dbhoursopen = :bvopen,
							 dbhoursclosed = :bvclosed
							 WHERE dbhourskey = :bvhourskey";
	$resulths = $db->prepare($sql);
	$resulths->bindValue(':bvopen', $formfield['ffsetopen']);
	$resulths->bindValue(':bvclosed', $formfield['ffsetclosed']);
	$resulths->bindValue(':bvhourskey', $formfield['ffhourkey']);
	$resulths->execute();
}


if( isset($_POST['changehours']))
{
	$change = 0;
	
	$sqlselecthk = "SELECT *
	FROM hours 
	WHERE dbhourskey = :bvhourskey";
	$resulthk = $db->prepare($sqlselecthk);
	$resulthk->bindValue(':bvhourskey', $formfield['ffhourkey']);
	$resulthk->execute();
}

$sqlselecth = "SELECT *
	FROM hours ";
$resulth = $db->prepare($sqlselecth);
$resulth->execute();


			
			



if(isset($_SESSION['frontloginname'])) {
	if($change != 0){
    ?>
    <br>
<div class="bg-white p-2 rounded-lg" style="width:35%;margin-left:32%;margin-right:5%">
<br><br>
    <table class="table-responsive-md" align="center">
        <tr>
        <td>
        <table class="table table-hover">
            <thead class="thead-light">
            <tr>
                <th>Day</th>
                <th>Opens</th>
				<th>Closes</th>
				<th>Edit</th>
            </tr>
            </thead>
                <tbody>
                <?php
					
					
						while($rowh = $resulth->fetch())
						{
								echo '<tr><td>' . $rowh['dbhoursday'] . '</td><td>' . $rowh['dbhoursopen'] . '</td>';
								echo '<td>' . $rowh['dbhoursclosed'] . '</td><td>';
								echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
								echo '<input type="hidden" name="opentime" value="' . $rowh['dbhoursopen'] . '">';
								echo '<input type="hidden" name="closetime" value="' . $rowh['dbhoursclosed'] . '">';
								echo '<input type="hidden" name="hourkey" value="' . $rowh['dbhourskey'] . '">';
								echo '<input type="submit" name="changehours" value="Change" class="w-100 btn btn-secondary">';
								echo '</form></td></tr>';
							
						}
	}else{
						 ?>
						 <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card card-body bg-light mt-5">
                    <h2>Update Schedule Information</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

                        <div class="form-group row">
                            <input type="text" name="setopen" placeholder="Open Time"
                                   class="form-control <?php echo (!empty($open_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffhoursopen'])) {
                                       echo $formfield['ffhoursopen'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $open_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="setclose" placeholder="Close Time"
                                   class="form-control <?php echo (!empty($close_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffhoursclosed'])) {
                                       echo $formfield['ffhoursclosed'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $open_err; ?></span>
                        </div>
                        <div class="form-row">
                            <div class="col text-center">
                                <input type="submit" value="Update" name="update" class="btn btn-secondary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
    </div>					 
					
						<?php						
					}					
						
					
					
                ?>
                </tbody>
        </table>
        <?php
            
        ?>
        </td></tr>
    </table>

</div>

    <?php
}
include_once 'footer.php';
?>