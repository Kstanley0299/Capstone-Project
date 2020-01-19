<?php
session_start();
$_SESSION['currentpage'] = "updatepermission";

$pagetitle = "Edit Permissions";

require_once 'header.php';
require_once 'connect.php';

$userpermissions = 'SELECT * FROM permission';
$resultm = $db->prepare($userpermissions);
$resultm->execute();

if(isset($_POST['submit']))
{
    $formfield['ffdescr'] = trim($_POST['permissionDescription']);

    $userpermissions = "SELECT * FROM permission WHERE dbpermissionlevel LIKE CONCAT('%', :bvdescr, '%');";
    $resultm = $db->prepare($userpermissions);
    $resultm->bindValue(':bvdescr', $formfield['ffdescr']);
    $resultm->execute();
}

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 2) {
?>


<div class="container">
    <div class="row">
        <div class="card card-body bg-light mt-5">
            <h2>Search Permissions</h2>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                <div class="row align-content-center">
                    <div class="form-group col w-50">
                        <label for="permissionDescription">Permission Description:</label>
                        <input type="text" name="permissionDescription" id="permissionDescription" class="form-control form-control-md" placeholder="Description"
                               value="<?php if (isset($formfield['ffdescr'])) {
                                   echo $formfield['ffdescr'];
                               } ?>"/>
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
            <th>Permission ID</th>
            <th>Permission Description</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $resultm->fetch()) {

            echo '<tr><td>' . $row['dbpermissionid'] . '</td>';
            echo '<td>' . $row['dbpermissionlevel'] . '</td>';
            echo '<td>';
            echo '<form action="updatepermissiondescr.php" method="post">';
            echo '<input type="hidden" name="permissionid" value="' . $row['dbpermissionid'] . '">';
            echo '<input type="submit" name="edit" class="btn btn-sm btn-secondary" value="Edit">';
            echo '</form></td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>
</div><br>
    <?php
}
include_once 'footer.php';
?>