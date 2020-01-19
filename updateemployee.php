<?php
session_start();

$pagetitle = "Update Employee";

require_once 'header.php';
require_once 'connect.php';

// assign empid
$empid = $_POST['empid'];

// get the positions
$positions = 'SELECT * FROM positions';
$result_p = $db->prepare($positions);
$result_p->execute();

// get the locations
$locations = 'SELECT * FROM locations';
$result_lo = $db->prepare($locations);
$result_lo->execute();

// get the user info
$selectuser = "SELECT * FROM employees WHERE dbempid = :bvempid";
$result_user = $db->prepare($selectuser);
$result_user->bindValue(':bvempid', $empid);
$result_user->execute();
$row_user = $result_user->fetch();

$formfield['fffirstname'] = $row_user['dbempfname'];
$formfield['fflastname'] = $row_user['dbemplname'];
$formfield['ffaddress1'] = $row_user['dbempaddress1'];
$formfield['ffaddress2'] = $row_user['dbempaddress2'];
$formfield['ffcity'] = $row_user['dbempcity'];
$formfield['ffstate'] = $row_user['dbempstate'];
$formfield['ffzip'] = $row_user['dbempzip'];
$formfield['ffemail'] = $row_user['dbempemail'];
$formfield['ffphone'] = $row_user['dbempphone'];
$formfield['ffhiredate'] = $row_user['dbemphiredate'];
$formfield['ffposition'] = $row_user['dbpositionid'];
$formfield['fflocation'] = $row_user['dblocationid'];

//NECESSARY VARIABLES
$errormsg = "";
$fname_err = $lname_err = $hiredate_err = $phone_err = $email_err = "";
$address1_err = $city_err = $state_err = $zip_err = $position_err = $location_err = "";
$password = '';

if( isset($_POST['submit']) )
{
    $formfield['fffirstname'] = trim($_POST['firstname']);
    $formfield['fflastname'] = trim($_POST['lastname']);
    $formfield['ffaddress1'] = trim($_POST['address1']);
    $formfield['ffaddress2'] = trim($_POST['address2']);
    $formfield['ffcity'] = trim($_POST['city']);
    $formfield['ffstate'] = trim($_POST['state']);
    $formfield['ffzip'] = trim($_POST{'zip'});
    $formfield['ffemail'] = trim(strtolower($_POST['email']));
    $formfield['ffphone'] = trim($_POST['phone']);
    $formfield['ffhiredate'] = trim($_POST['hiredate']);
    $formfield['ffposition'] = $_POST['position'];
    $formfield['fflocation'] = $_POST['location'];

    if(empty($formfield['fffirstname'])){$fname_err = "First name cannot be empty.";}
    if(empty($formfield['fflastname'])){$lname_err = "Last name cannot be empty.";}
    if(empty($formfield['ffaddress1'])){$address1_err = "Address 1 cannot be empty.";}
    if(empty($formfield['ffcity'])){$city_err = "City cannot be empty.";}
    if(empty($formfield['ffstate'])){$state_err = "State cannot be empty.";}
    if(empty($formfield['ffzip'])){$zip_err = "Zip cannot be empty.";}
    if(empty($formfield['ffemail'])){$email_err = "E-mail cannot be empty.";}
    //VALIDATE THE EMAIL
    if(empty($email_err))
    {
        if (!filter_var($formfield['ffemail'], FILTER_VALIDATE_EMAIL))
        {
            $email_err = "Email is not valid.";
        }
    }
    if(empty($formfield['ffphone'])){$phone_err = "Phone cannot be empty.";}
    if(empty($formfield['ffhiredate'])){$hiredate_err = "Hire Date cannot be empty.";}
    if(empty($formfield['fflocation'])){$location_err = "Location cannot be empty";}
    if($formfield['ffposition'] === null ){$position_err = "Position cannot be empty";}

    if(empty($fname_err) && empty($lname_err) && empty($address1_err) && empty($city_err) && empty($state_err) &&
        empty($zip_err) && empty($email_err) && empty($phone_err) && empty($position_err) && empty($location_err))
    {
        try
        {
            $sqlinsert = "UPDATE employees SET dbempfname = :bvfname, dbemplname = :bvlname, dbemphiredate = :bvhiredate, 
                                              dbpositionid = :bvpositionid, dbempphone = :bvphone, dbempemail = :bvemail, 
                                              dbempaddress1 = :bvaddress1, dbempaddress2 = :bvaddress2, dbempcity = :bvcity, 
                                              dbempstate = :bvstate, dbempzip = :bvzip, dblocationid = :bvlocation
                                           WHERE dbempid = :bvempid";
            $sqlinsert = $db->prepare($sqlinsert);
            $sqlinsert->bindValue(':bvempid', $empid);
            $sqlinsert->bindValue(':bvfname', $formfield['fffirstname']);
            $sqlinsert->bindValue(':bvlname', $formfield['fflastname']);
            $sqlinsert->bindValue(':bvhiredate', $formfield['ffhiredate']);
            $sqlinsert->bindValue(':bvpositionid', $formfield['ffposition']);
            $sqlinsert->bindValue(':bvphone', $formfield['ffphone']);
            $sqlinsert->bindValue(':bvemail', $formfield['ffemail']);
            $sqlinsert->bindValue(':bvaddress1', $formfield['ffaddress1']);
            $sqlinsert->bindValue(':bvaddress2', $formfield['ffaddress2']);
            $sqlinsert->bindValue(':bvcity', $formfield['ffcity']);
            $sqlinsert->bindValue(':bvstate', $formfield['ffstate']);
            $sqlinsert->bindValue(':bvzip', $formfield['ffzip']);
            $sqlinsert->bindValue(':bvlocation', $formfield['fflocation']);
            $sqlinsert->execute();

            echo '<br><div class="alert alert-success text-center" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Employee Information Updated Successfully.</div><br>';

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
                    <h2>Update Employee Information</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

                        <div class="form-group row">
                            <input type="text" name="firstname" placeholder="First Name"
                                   class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fffirstname'])) {
                                       echo $formfield['fffirstname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $fname_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="lastname" placeholder="Last Name"
                                   class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fflastname'])) {
                                       echo $formfield['fflastname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $lname_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="address1" placeholder="Address 1"
                                   class="form-control <?php echo (!empty($address1_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffaddress1'])) {
                                       echo $formfield['ffaddress1'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $address1_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="address2" placeholder="Address 2" class="form-control"
                                   value="<?php if (isset($formfield['ffaddress2'])) {
                                       echo $formfield['ffaddress2'];
                                   } ?>"/>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="city" placeholder="City"
                                   class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffcity'])) {
                                       echo $formfield['ffcity'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $city_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="state" placeholder="State"
                                   class="form-control <?php echo (!empty($state_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffstate'])) {
                                       echo $formfield['ffstate'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $state_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="zip" placeholder="Zip Code"
                                   class="form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffzip'])) {
                                       echo $formfield['ffzip'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="email" name="email" placeholder="E-mail"
                                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffemail'])) {
                                       echo $formfield['ffemail'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <input type="text" name="phone" placeholder="Phone"
                                   class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffphone'])) {
                                       echo $formfield['ffphone'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="hiredate">Hire Date:</label>
                            <div class="col-sm-8">
                                <input type="date" name="hiredate"
                                       class="form-control <?php echo (!empty($hiredate_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffhiredate'])) {
                                           echo $formfield['ffhiredate'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $hiredate_err; ?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="position">Position:</label>
                            <div class="col-sm-8">
                                <select name="position" id="position" required
                                        class="form-control <?php echo (!empty($position_err)) ? 'is-invalid' : ''; ?>">
                                    <option value="">Select Position</option>
                                    <?php
                                    while ($rowp = $result_p->fetch())
                                    {
                                        if ($rowp['dbpositionid'] == $formfield['ffposition']) {
                                            $checker = 'selected';
                                        } else {
                                            $checker = '';
                                        }
                                        echo '<option value="' . $rowp['dbpositionid'] . '" ' . $checker . '>' . $rowp['dbpositiondescr'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $position_err; ?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="location">Restaurant Location:</label>
                            <div class="col">
                                <select name="location" id="location"
                                        class="col form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>">
                                    <option value="">Select Location</option>
                                    <?php
                                    while ($rowlo = $result_lo->fetch())
                                    {
                                        if ($rowlo['dblocationid'] == $formfield['fflocation']) {
                                            $checker = 'selected';
                                        } else {
                                            $checker = '';
                                        }
                                        echo '<option value="' . $rowlo['dblocationid'] . '" ' . $checker . '>' . $rowlo['dblocationname'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $location_err; ?></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col text-center">
                                <input type="hidden" name="empid" value="<?php echo $empid ?>">
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