<?php
session_start();
$_SESSION['currentpage'] = "editmenu";

$pagetitle = "Edit Menu";

require_once 'header.php';
require_once 'connect.php';

// select the categories
$categories = 'SELECT * FROM category';
$resultc = $db->prepare($categories);
$resultc->execute();

if(isset($_POST['delete']))
{
    $formfield['ffmenuid'] = $_POST['menuid'];

    $sqldelete = 'DELETE FROM menu WHERE dbmenuid = :bvmenuid';
    $stmtdelete = $db->prepare($sqldelete);
    $stmtdelete->bindValue(':bvmenuid', $formfield['ffmenuid']);
    $stmtdelete->execute();

    $sqldelete = 'DELETE FROM inventory WHERE dbmenuid = :bvmenuid';
    $stmtdelete = $db->prepare($sqldelete);
    $stmtdelete->bindValue(':bvmenuid', $formfield['ffmenuid']);
    $stmtdelete->execute();
}

// select the menu items
$menuitems = 'SELECT menu.*, category.dbcatname
              FROM menu, category
              WHERE menu.dbcatid = category.dbcatid';
$resultm = $db->prepare($menuitems);
$resultm->execute();

if(isset($_POST['submit']))
{
    $formfield['ffname'] = trim($_POST['name']);
    $formfield['ffitemdescr'] = trim($_POST['itemdescr']);
    $formfield['ffcat'] = $_POST['category'];
    $formfield['ffitemprice'] = trim($_POST['price']);

    $menuitems = "SELECT menu.*, category.dbcatname
              FROM menu, category
              WHERE menu.dbcatid = category.dbcatid
              AND menu.dbmenuitemname LIKE CONCAT('%', :bvname, '%')
              AND menu.dbmenuitemdescr LIKE CONCAT('%', :bvitemdescr, '%')
              AND menu.dbcatid LIKE CONCAT('%', :bvcat, '%')
              AND menu.dbmenuitemprice LIKE CONCAT('%', :bvitemprice, '%');";
    $resultm = $db->prepare($menuitems);
    $resultm->bindValue(':bvname', $formfield['ffname']);
    $resultm->bindValue(':bvitemdescr', $formfield['ffitemdescr']);
    $resultm->bindValue(':bvcat', $formfield['ffcat']);
    $resultm->bindValue(':bvitemprice', $formfield['ffitemprice']);
    $resultm->execute();
}

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 2) {
?>


<div class="container">
    <div class="row">
        <div class="card card-body bg-light mt-5">
            <h2>Search Menu Items</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                <div class="row align-content-center">
                    <div class="form-group col w-50">
                        <label for="name">Item Name:</label>
                        <input type="text" name="name" id="name" class="form-control form-control-md" placeholder="Name"
                               value="<?php if (isset($formfield['ffname'])) {
                                   echo $formfield['ffname'];
                               } ?>"/>
                    </div>
                    <div class="form-group col w-50">
                        <label for="itemdescr">Description:</label>
                        <input type="text" name="itemdescr" id="itemdescr" class="form-control form-control-md" placeholder="Description"
                               value="<?php if (isset($formfield['ffitemdescr'])) {
                                   echo $formfield['ffitemdescr'];
                               }; ?>"/>
                    </div>
                </div>
                <div class="row align-content-center">
                    <div class="form-group col w-50">
                        <label for="category">Category:</label>
                        <select name="category" id="category" class="form-control form-control-md">
                            <option value="">Select Category</option>
                            <?php
                            while ($rowc = $resultc->fetch()) {
                                if ($rowc['dbcatid'] == $formfield['ffcat']) {
                                    $checker = 'selected';
                                } else {
                                    $checker = '';
                                }
                                echo '<option value="' . $rowc['dbcatid'] . '" ' . $checker . '>' . $rowc['dbcatname'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col w-50">
                        <label for="price">Item Price:</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control form-control-md" placeholder="Price"
                                value="<?php if (isset($formfield['ffitemprice'])) { echo $formfield['ffitemprice']; } ?>">
                    </div>
                </div>
                <div class="form-row justify-content-center">
                    <div class="col text-center">
                        <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="align-content-center container-fluid">
    <br>
    <table class="table table-hover bg-white">
        <thead class="thead-light">
        <tr>
            <th>Item&nbsp;Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Item&nbsp;Description&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Item&nbsp;Category</th>
            <th>Item&nbsp;Price</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $resultm->fetch()) {

            echo '<tr><td>' . $row['dbmenuitemname'] . '</td>';
            echo '<td>' . $row['dbmenuitemdescr'] . '</td>';
            echo '<td>' . $row['dbcatname'] . '</td>';
            echo '<td>' . $row['dbmenuitemprice'] . '</td>';
            echo '<td>';
            echo '<form action="updateitem.php" method="post">';
            echo '<input type="hidden" name="menuid" value="' . $row['dbmenuid'] . '">';
            echo '<input type="submit" name="edit" class="btn btn-sm btn-secondary" value="Edit">';
            echo '</form></td>';
            echo '<td>';
            echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
            echo '<input type="hidden" name="menuid" value="' . $row['dbmenuid'] . '">';
            echo '<input type="submit" name="delete" class="btn btn-sm btn-secondary" value="Delete">';
            echo '</form></td></tr>';
        }
        ?>
        </tbody>
    </table>
</div><br>
    <?php
}
include_once 'footer.php';
?>