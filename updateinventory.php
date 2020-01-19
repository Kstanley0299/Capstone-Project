<?php
session_start();
$_SESSION['currentpage'] = "enterinventory";

$pagetitle = "Inventory";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$inv_err = "";
$current_id = 1;
$invid_arr = array();
$menuinv_arr = array();

$sqlupdate = "UPDATE inventory SET dbinvamount = ";

$inventory = 'SELECT inventory.dbinvid, inventory.dbmenuid, inventory.dbinvamount, menu.dbmenuitemname  FROM inventory, menu WHERE inventory.dbmenuid = menu.dbmenuid ';
$resultinv = $db->prepare($inventory);
$resultinv->execute();


// populate the $menuid_arr with menu IDs
while($rowinv = $resultinv->fetch())
{
    array_push($invid_arr, $rowinv['dbinvid']);

    $formid = $rowinv['dbinvid'] . "inv";

    $formfield[$formid] = trim($_POST[$formid]);

    array_push($menuinv_arr, $formfield[$formid]);


//    array_push($menuid_arr, $rowinv['dbinvid']);
//
//    $formid = $current_id . "inv";
//
//    $formfield[$formid] = trim($_POST[$formid]);
//
//    array_push($menuinv_arr, $formfield[$formid]);
//    $current_id = $current_id + 1;
}

// echo $menuinv_arr . '+' ;

if( isset($_POST['submit']) )
{
    $index = 0;
    foreach($menuinv_arr as $value)
    {

        $current_id = $invid_arr[$index];
		$sqlupdate2 = $sqlupdate . $value . " WHERE dbinvid = " . $current_id;

        try
        {
            $sqlupdate3 = $db->prepare($sqlupdate2);
            $sqlupdate3->execute();

            unset($sqlupdate3);
            $index++;
            $sqlupdate = "UPDATE inventory SET dbinvamount = ";
        }
        catch(PDOException $e)
        {
            echo 'Error!' .$e->getMessage();
            exit();
        }
    }
}

if($_SESSION['loginpermit'] == 1) {
    ?>

    <br>
    <div class="container bg-white">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
            <table class="table table-hover">
                <thead class="thead-light">
                <tr>
                    <th class="w-25">ID</th>
                    <th class="w-50">Menu Item</th>
                    <th class="w-25">Inventory</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $resultinv = $db->prepare($inventory);
                $resultinv->execute();

                while ($row = $resultinv->fetch()) {
                    ?>
                    <?php $currentinv = $row['dbinvamount']; ?>

                    <tr>
                        <th scope="row"><?php echo $row['dbinvid']; ?></th>
                        <td><?php echo $row['dbmenuitemname']; ?></td>
                        <td><input type="number" name="<?php echo $row['dbinvid'] . 'inv'; ?>"
                                   id="<?php echo $row['dbinvid'] . 'inv'; ?>" min="0" step="1"
                                   class="form-control form-control-sm"
                                   value="<?php echo $currentinv; ?>"/></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <div class="form-row">
                <div class="col text-center">
                    <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                </div>
            </div>
            <br>
        </form>
    </div>
    <br><br>
    <?php
}
include_once 'footer.php';
?>
