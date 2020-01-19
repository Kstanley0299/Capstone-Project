<?php
session_start();
$_SESSION['currentpage'] = "enternewcat";

$pagetitle = "Edit Categories";

require_once 'header.php';
require_once 'connect.php';


//DATABASE CONNECTION
require_once "connect.php";

//NECESSARY VARIABLES
$cat_err = "";
$showform = 1;

if(isset($_POST['delete']))
{
	$sqlselect_items = "SELECT * FROM menu WHERE dbcatid = :bvcatid";
	$stmtselectitems = $db->prepare($sqlselect_items);
	$stmtselectitems->bindValue(':bvcatid', $_POST['catid']);
	$stmtselectitems->execute();
	$count = $stmtselectitems->rowCount();
	
	if($count < 1)
	{
	$sqldelete = 'DELETE FROM category WHERE dbcatid = :bvcatid';
    $stmtdelete = $db->prepare($sqldelete);
    $stmtdelete->bindValue(':bvcatid', $_POST['catid']);
    $stmtdelete->execute();
	} else {
		echo '<br><div class="alert alert-danger" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Cannot delete category containing menu items.</div><br>';
	}
}

    if( isset($_POST['submit']) )
    {
        echo '<p>The form was submitted.</p>';

        $formfield['ffcat'] = trim($_POST['cat']);

        if(empty($formfield['ffcat'])) {$cat_err = "Category name is empty";}

        if($cat_err == "")
        {
            try
            {
                $sqlinsert = 'INSERT INTO category (dbcatname)
                              VALUES (:bvcat)';
                $stmtinsert = $db->prepare($sqlinsert);
                $stmtinsert->bindValue(':bvcat', $formfield['ffcat']);
                $stmtinsert->execute();
            }
            catch(PDOException $e)
            {
                echo 'ERROR!' . $e->getMessage();
                exit();
            }
        }
    }

    $sqlselect = 'SELECT * FROM category';
    $result = $db->query($sqlselect);

if($_SESSION['loginpermit'] == 1) {
    ?>

    <div class="container w-75">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Enter New Menu Categories</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <div class="form-group">
                        <label for="cat">Category Name:</label>
                        <input type="text" name="cat" id="cat"
                               class="form-control form-control-lg <?php echo (!empty($cat_err)) ? 'is-invalid' : ''; ?>"
                               value="<?php if (isset($formfield['ffcat'])) {
                                   echo $formfield['ffcat'];
                               } ?>"/>
                        <span class="invalid-feedback"><?php echo $cat_err; ?></span>
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
    <br><br><br>

    <div class="align-content-center container w-75">
        <table class="table table-hover bg-white">
            <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Category</th>
				<th></th>
                <th></th>
            </tr>
            </thead>
            <tbody><?php
            while ($row = $result->fetch()) {
                echo '<tr><th scope="row">' . $row['dbcatid'] . '</th><td>' . $row['dbcatname'] . '</td>';

                echo '<td><form action="updatecategory.php" method="post">';
                echo '<input type="hidden" name="catid" value="' . $row['dbcatid'] . '">';
                echo '<input type="submit" name="edit" class="btn-sm btn-secondary" value="Edit">';
                echo '</form></td>';

				echo '<td><form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
				echo '<input type="hidden" name="catid" value="' . $row['dbcatid'] . '">';
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