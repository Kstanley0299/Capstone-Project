<?php
session_start();
$_SESSION['currentpage'] = "updatecategory";
$pagetitle = "Update Employee";

require_once 'header.php';
require_once 'connect.php';

// assign catid
$catid = $_POST['catid'];


// get the user info
$selectcat = "SELECT * FROM category WHERE dbcatid = :bvcatid";
$result_cat = $db->prepare($selectcat);
$result_cat->bindValue(':bvcatid', $catid);
$result_cat->execute();
$row_user = $result_cat->fetch();

$formfield['ffcategoryname'] = $row_user['dbcatname'];

//NECESSARY VARIABLES
$errormsg = "";


if( isset($_POST['submit']) )
{
    $formfield['ffcategoryname'] = trim($_POST['categoryname']);

    if(empty($formfield['ffcategoryname'])){$cat_err = "Category name cannot be empty.";}


    if(empty($cat_err))
    {
        try
        {
            $sqlinsert = "UPDATE category SET dbcatname = :bvcatname
                                           WHERE dbcatid = :bvcatid";
            $sqlinsert = $db->prepare($sqlinsert);
            $sqlinsert->bindValue(':bvcatid', $catid);
            $sqlinsert->bindValue(':bvcatname', $formfield['ffcategoryname']);
            $sqlinsert->execute();

            echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Category Information Updated Successfully. Redirecting...</div><br>';
			echo '<script type="text/javascript">';
            
			echo 'function Redirect(){';
            echo '  window.location="insertcategory.php";';
            echo '}';
            echo '  setTimeout("Redirect()", 2500);';
            echo '</script>';
        }
        catch(PDOException $e)
        {
            echo 'Error!' .$e->getMessage();
            exit();
        }
    }
}//if isset submit

if($_SESSION['loginpermit'] == 1) {
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card card-body bg-light mt-5">
                    <h2>Update Category Information</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

                        <div class="form-group row">
                            <input type="text" name="categoryname" placeholder="Category Name"
                                   class="form-control <?php echo (!empty($cat_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffcategoryname'])) {
                                       echo $formfield['ffcategoryname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $cat_err; ?></span>
                        </div>
                        <div class="form-row">
                            <div class="col text-center">
                                <input type="hidden" name="catid" value="<?php echo $catid ?>">
                                <input type="submit" value="Update" name="submit" class="btn btn-secondary">
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
include_once 'footer.php';
?>