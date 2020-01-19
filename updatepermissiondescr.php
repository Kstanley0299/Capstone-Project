<?php
session_start();
$_SESSION['currentpage'] = "updatepermission";
$pagetitle = "Update Permission Description";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$name_err = $descr_err = $cat_err = $price_err = "";

// assign empid
$permissionid = $_POST['permissionid'];



// select the Permissions 
$permissions = 'SELECT * FROM permission WHERE dbpermissionid = :bvpermissionid';
$resultp = $db->prepare($permissions);
$resultp->bindValue(':bvpermissionid', $permissionid);
$resultp->execute();
$rowp = $resultp->fetch();

$formfield['ffpermissiondescr'] = $rowp['permissiondescr'];


if( isset($_POST['edit']) ) {
			$showform = 1;
			$formfield['ffpermissionid'] = $_POST['permissionid'];
			$sqlselect = 'SELECT * from permission where dbpermissionid = :bvpermissionid';
			$result = $db->prepare($sqlselect);
			$result->bindValue(':bvpermissionid', $formfield['ffpermissionid']);
			$result->execute();
			$row = $result->fetch(); 
		}


if(isset($_POST['submit']))
{
	$showform = 2;
			$formfield['ffpermissionid'] = $_POST['permissionid'];
			echo '<p>Permission Description updated!</p>';
			
    $formfield['ffpermissiondescr'] = trim($_POST['permissiondescr']);

    if(empty($formfield['ffpermissiondescr'])){$name_err = "<p>Description cannot be empty.</p>";}

    if(empty($name_err) && empty($descr_err) && empty($cat_err) && empty($price_err))
    {
        try
        {
            $sqlinsert = "UPDATE permission SET dbpermissionlevel = :bvpermissiondescr
                                           WHERE dbpermissionid = :bvpermissionid;";
            $sqlinsert = $db->prepare($sqlinsert);
            $sqlinsert->bindValue(':bvpermissionid', $permissionid);
            $sqlinsert->bindValue(':bvpermissiondescr', $formfield['ffpermissiondescr']);
            $sqlinsert->execute();

            echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Permissions Updated Successfully.</div><br>';

        }
        catch(PDOException $e)
        {
            echo 'Error!' .$e->getMessage();
            exit();
        }
    }
}

if($showform == 1  && $visible == 1 && $_SESSION['loginpermit'] == 1) {
    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Update Permission Description</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <p>All Fields Required</p>
                    <div class="form-group">
                        <label for="permissiondescr">Permission Description:</label>
                        <input type="text" name="permissiondescr" id="permissiondescr"
                               class="form-control form-control-lg <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffpermissiondescr'])) {
                                   echo $formfield['ffpermissiondescr'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col text-center">
                            <input type="hidden" name="permissionid" value="<?php echo $permissionid ?>">
                            <input type="submit" value="Update" name="submit" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}else if ($showform == 2 && $visible == 1 && $_SESSION['loginpermit'] == 1) {
	?>
	<div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Update Permission Description</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <p>All Fields Required</p>
                    <div class="form-group">
                        <label for="permissiondescr">Permission Description:</label>
                        <input type="text" name="permissiondescr" id="permissiondescr"
                               class="form-control form-control-lg <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffpermissiondescr'])) {
                                   echo $formfield['ffpermissiondescr'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col text-center">
                            <input type="hidden" name="permissionid" value="<?php echo $permissionid ?>">
                            <input type="submit" value="Update" name="submit" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
	<?php
}
include_once 'footer.php';
?>