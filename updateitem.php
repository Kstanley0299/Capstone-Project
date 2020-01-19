<?php
session_start();

$pagetitle = "Update Menu Item";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$name_err = $descr_err = $cat_err = $price_err = "";

// assign empid
$menuid = $_POST['menuid'];

// select the categories
$categories = 'SELECT * FROM category';
$resultc = $db->prepare($categories);
$resultc->execute();

// select the menu items
$menuitems = 'SELECT * FROM menu WHERE dbmenuid = :bvmenuid';
$resultm = $db->prepare($menuitems);
$resultm->bindValue(':bvmenuid', $menuid);
$resultm->execute();
$rowm = $resultm->fetch();

$formfield['ffmenuitemname'] = $rowm['menuitemname'];
$formfield['ffmenuitemdescr'] = $rowm['menuitemdescr'];
$formfield['ffmenucategory'] = $rowm['catid'];
$formfield['ffmenuitemprice'] = $rowm['menuitemprice'];


if(isset($_POST['submit']))
{
    $formfield['ffmenuitemname'] = trim($_POST['menuitemname']);
    $formfield['ffmenuitemdescr'] = trim($_POST['menuitemdescr']);
    $formfield['ffmenucategory'] = trim($_POST['menucategory']);
    $formfield['ffmenuitemprice'] = trim($_POST['menuitemprice']);

    if(empty($formfield['ffmenuitemname'])){$name_err = "<p>Item name cannot be empty.</p>";}
    if(empty($formfield['ffmenuitemdescr'])){$descr_err = "<p>Item Description cannot be empty.</p>";}
    if(empty($formfield['ffmenucategory'])){$cat_err = "<p>Item category cannot be empty.</p>";}
    if(empty($formfield['ffmenuitemprice'])){$price_err = "<p>Item price cannot be empty.</p>";}

    if(empty($name_err) && empty($descr_err) && empty($cat_err) && empty($price_err))
    {
        try
        {
            $sqlinsert = "UPDATE menu SET menuitemname = :bvmenuitemname, menuitemdescr = :bvmenuitemdescr,  
                                              catid = :bvcatid, menuitemprice = :bvmenuitemprice
                                           WHERE menuid = :bvmenuid;";
            $sqlinsert = $db->prepare($sqlinsert);
            $sqlinsert->bindValue(':bvmenuid', $menuid);
            $sqlinsert->bindValue(':bvmenuitemname', $formfield['ffmenuitemname']);
            $sqlinsert->bindValue(':bvmenuitemdescr', $formfield['ffmenuitemdescr']);
            $sqlinsert->bindValue(':bvcatid', $formfield['ffmenucategory']);
            $sqlinsert->bindValue(':bvmenuitemprice', $formfield['ffmenuitemprice']);
            $sqlinsert->execute();

            echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Menu Item Updated Successfully.</div><br>';

        }
        catch(PDOException $e)
        {
            echo 'Error!' .$e->getMessage();
            exit();
        }
    }
}

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 2) {
    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Enter New Menu Items</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <p>All Fields Required</p>
                    <div class="form-group">
                        <label for="menuitemname">Item Name:</label>
                        <input type="text" name="menuitemname" id="menuitemname"
                               class="form-control form-control-lg <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffmenuitemname'])) {
                                   echo $formfield['ffmenuitemname'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="menuitemdescr">Item Description:</label>
                        <input type="text" name="menuitemdescr" id="menuitemdescr"
                               class="form-control form-control-lg <?php echo (!empty($descr_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffmenuitemdescr'])) {
                                   echo $formfield['ffmenuitemdescr'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $descr_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="menucategory">Menu Category:</label>
                        <select name="menucategory" id="menucategory">
                            <option value="">Select Category</option>
                            <?php
                            while ($rowc = $resultc->fetch()) {
                                if ($rowc['catid'] == $formfield['ffmenucategory']) {
                                    $checker = 'selected';
                                } else {
                                    $checker = '';
                                }
                                echo '<option value="' . $rowc['catid'] . '" ' . $checker . '>' . $rowc['catname'] . '</option>';
                            }
                            ?>
                        </select>
                        <span class="invalid-feedback"><?php echo $cat_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="menuitemprice">Item Price:</label>
                        <input type="number" name="menuitemprice" id="menuitemprice" min="0.00" step="0.01"
                               class="form-control form-control-lg <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffmenuitemprice'])) {
                                   echo $formfield['ffmenuitemprice'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $price_err; ?></span>
                    </div>
                    <div class="form-row">
                        <div class="col text-center">
                            <input type="hidden" name="menuid" value="<?php echo $menuid ?>">
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