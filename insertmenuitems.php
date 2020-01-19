<?php
session_start();
$_SESSION['currentpage'] = "entermenuitems";

$pagetitle = "Enter Menu Items";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$name_err = $descr_err = $cat_err = $price_err = "";

$categories = 'SELECT * FROM category';
$resultc = $db->prepare($categories);
$resultc->execute();

$menuitems = 'SELECT * FROM menu';
$resultm = $db->prepare($menuitems);
$resultm->execute();

    if( isset($_POST['submit']) )
    {
        $formfield['ffmenuitemname'] = trim($_POST['menuitemname']);
        $formfield['ffmenuitemdescr'] = trim($_POST['menuitemdescr']);
        $formfield['ffmenucategory'] = trim($_POST['menucategory']);
        $formfield['ffmenuitemprice'] = trim($_POST['menuitemprice']);


        if(empty($formfield['ffmenuitemname'])){$name_err .= "<p>Item name cannot be empty.</p>";}
        if(empty($formfield['ffmenuitemdescr'])){$descr_err .= "<p>Item Description cannot be empty.</p>";}
        if(empty($formfield['ffmenucategory'])){$cat_err .= "<p>Item category cannot be empty.</p>";}
        if(empty($formfield['ffmenuitemprice'])){$price_err .= "<p>Item price cannot be empty.</p>";}


        if(empty($name_err) && empty($descr_err) && empty($cat_err) && empty($price_err))
        {
            try
            {

                $sqlmax = "SELECT MAX(dbmenuid) AS maxid FROM menu";
                $resultmax = $db->prepare($sqlmax);
                $resultmax->execute();
                $rowmax = $resultmax->fetch();
                $maxid = $rowmax["maxid"];
                $maxid = $maxid + 1;

                $sqlinsert = "INSERT into menu (dbmenuid, dbmenuitemname, dbmenuitemdescr, dbcatid, dbmenuitemprice)
                        VALUES (:bvmenuid, :bvmenuitemname, :bvmenuitemdescr, :bvmenucategory, :bvmenuitemprice)";
                $sqlinsert = $db->prepare($sqlinsert);
                $sqlinsert->bindValue(':bvmenuid', $maxid);
                $sqlinsert->bindValue(':bvmenuitemname', $formfield['ffmenuitemname']);
                $sqlinsert->bindValue(':bvmenuitemdescr', $formfield['ffmenuitemdescr']);
                $sqlinsert->bindValue(':bvmenucategory', $formfield['ffmenucategory']);
                $sqlinsert->bindValue(':bvmenuitemprice', $formfield['ffmenuitemprice']);
                $sqlinsert->execute();

                $sqlinsertinv = "INSERT INTO inventory (dbmenuid, dbinvamount) VALUES (:bvmenuid, :bvinvamount)";
                $sqlinsertinv = $db->prepare($sqlinsertinv);
                $sqlinsertinv->bindValue(':bvmenuid', $maxid);
                $sqlinsertinv->bindValue(':bvinvamount', 0);
                $sqlinsertinv->execute();

                $formfield['ffmenuitemname'] = '';
                $formfield['ffmenuitemdescr'] = '';
                $formfield['ffmenucategory'] = '';
                $formfield['ffmenuitemprice'] = '';

                echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">New menu item added.</div><br>';
            }
            catch(PDOException $e)
            {
                echo 'Error!' .$e->getMessage();
                exit();
            }
        }
    }//if isset submit

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 2) {

    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Enter New Menu Items</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <p>All Fields Required</p>

                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="menuitemname">Item Name:</label>
                            <input type="text" name="menuitemname" id="menuitemname" placeholder="Name"
                                   class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffmenuitemname'])) {
                                       echo $formfield['ffmenuitemname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
                            <label for="menuitemdescr">Description:</label>
                            <input type="text" name="menuitemdescr" id="menuitemdescr" placeholder="Description"
                                   class="form-control <?php echo (!empty($descr_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffmenuitemdescr'])) {
                                       echo $formfield['ffmenuitemdescr'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $descr_err; ?></span>
                        </div>
                    </div>
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="menucategory">Category:</label>
                            <select name="menucategory" id="menucategory" class="form-control <?php echo (!empty($cat_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Category</option>
                                <?php
                                while ($rowc = $resultc->fetch()) {
                                    if ($rowc['dbcatid'] == $formfield['ffmenucategory']) {
                                        $checker = 'selected';
                                    } else {
                                        $checker = '';
                                    }
                                    echo '<option value="' . $rowc['dbcatid'] . '" ' . $checker . '>' . $rowc['dbcatname'] . '</option>';
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $cat_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
                            <label for="menuitemprice">Item Price:</label>
                            <input type="number" name="menuitemprice" id="menuitemprice" min="0.00" step="0.01" placeholder="Price"
                                   class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffmenuitemprice'])) {
                                       echo $formfield['ffmenuitemprice'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $price_err; ?></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col text-center">
                            <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br><br>

    <div class="container">
        <?php
        $categories = 'SELECT * FROM category';
        $resultc = $db->prepare($categories);
        $resultc->execute();
        $menuitems = 'SELECT * FROM menu';
        $resultm = $db->prepare($menuitems);
        $resultm->execute();
        while ($rowc = $resultc->fetch()) {
            echo '<h1 style="font-family: AvenirLTStd-Roman" class="font-italic text-warning">' . $rowc['dbcatname'] . '</h1>';
            while ($rowm = $resultm->fetch()) {
                if ($rowc['dbcatid'] == $rowm['dbcatid']) {
                    echo '<div style="font-family: AvenirLTStd-Roman" class="row text-white border border-white border-left-0 border-right-0 border-bottom-0">';
                    echo '<div class="col-4">'.$rowm['dbmenuitemname'].'</div>';
                    echo '<div class="col-6">'.$rowm['dbmenuitemdescr'].'</div>';
                    echo '<div class="col-2 text-right">'.$rowm['dbmenuitemprice'].'</div></div>';
//                    echo '<tr><td style="width:25%">' . $rowm['dbmenuitemname'] . '</td><td style="width:65%">' . $rowm['dbmenuitemdescr'] . '</td><td style="width:10%">$' . $rowm['dbmenuitemprice'] . '</td></tr>';
                }
            }
            $resultm = $db->prepare($menuitems);
            $resultm->execute();
            echo '<br>';
        }
        ?>
    </div>
    <?php
}
include_once 'footer.php';
?>