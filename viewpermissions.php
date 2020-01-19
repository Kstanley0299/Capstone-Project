<?php
session_start();
$_SESSION['currentpage'] = "viewpermissions";

$pagetitle = "View Permissions";

require_once 'header.php';
require_once 'connect.php';

// select the permissions
$permissions = 'SELECT permission.*
              FROM permission';
$resultp = $db->prepare($permissions);
$resultp->execute();

if(isset($_POST['submit']))
{
    $formfield['fflevel'] = trim($_POST['level']);

    $permissions = "SELECT permission.*
              FROM permission
              WHERE permission.dbpermissionlevel LIKE CONCAT('%', :bvlevel, '%');";
    $resultp = $db->prepare($permissions);
    $resultp->bindValue(':bvlevel', $formfield['fflevel']);
    $resultp->execute();
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
                        <label for="level">Permission level:</label>
                        <input type="text" name="level" id="level" class="form-control form-control-md" placeholder="Permission Level"
                               value="<?php if (isset($formfield['fflevel'])) {
                                   echo $formfield['fflevel'];
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
            <th>Permission Level</th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($row = $resultp->fetch()) {

            echo '<tr><td>' . $row['dbpermissionid'] . '</td>';
            echo '<td>' . $row['dbpermissionlevel'] . '</td>';
            echo '<td>';
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