<?php
session_start();
$_SESSION['currentpage'] = "insertcustomers";

$pagetitle = "Enter New Customers";

require_once 'header.php';
require_once 'connect.php';


//NECESSARY VARIABLES
$errormsg = "";
$fname_err = $lname_err = $pass1_err = $pass2_err = $phone_err = $email_err = "";
$address1_err = $city_err = $state_err = $zip_err = $maillist_err = "";
$password = '';

// get the locations
$locations = 'SELECT * FROM locations';
$result_lo = $db->prepare($locations);
$result_lo->execute();

if( isset($_POST['submit']) )
{
    echo '<p>The form was submitted.</p>';

    $formfield['fffirstname'] = trim($_POST['BBfirstname']);
    $formfield['fflastname'] = trim($_POST['BBlastname']);
    $formfield['ffaddress1'] = trim($_POST['BBaddress1']);
    $formfield['ffaddress2'] = trim($_POST['BBaddress2']);
    $formfield['ffcity'] = trim($_POST['BBcity']);
    $formfield['ffstate'] = trim($_POST['BBstate']);
    $formfield['ffzip'] = trim($_POST{'BBzip'});
    $formfield['ffemail'] = trim(strtolower($_POST['BBemail']));
    $formfield['ffphone'] = trim($_POST['BBphone']);
    $formfield['fflocation'] = $_POST['BBlocation'];
    $formfield['ffmaillist'] = $_POST['BBmaillist'];
    $formfield['ffpass1'] = trim($_POST['BBpass1']);
    $formfield['ffpass2'] = trim($_POST['BBpass2']);

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
            $email_err = "Your email is not valid.";
        }
    }
    if(empty($formfield['ffphone'])){$phone_err = "Phone cannot be empty.";}
    if(empty($formfield['ffmaillist'])){$maillist_err = "Maillist cannot be empty";}
    if(empty($formfield['ffpass1'])){$pass1_err = "Password cannot be empty.";}
    if(empty($formfield['ffpass2'])){$pass2_err = "Confirm Password cannot be empty.";}
    //confirm passwords match
    if ($formfield['ffpass1'] != $formfield['ffpass2']) {
        $pass1_err = $pass2_err = "Your passwords do not match.";
    }

    if(empty($fname_err) && empty($lname_err) && empty($address1_err) && empty($city_err) && empty($state_err) &&
     empty($zip_err) && empty($email_err) && empty($phone_err) && empty($pass1_err) && empty($pass2_err))
    {
        $options = [
            'cost' => 12,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
        ];
        $encpass = password_hash($formfield['ffpass1'], PASSWORD_BCRYPT, $options);


    try
        {
            $sqlinsert = "INSERT into customer (dbcustfname, dbcustlname, dbcustpassword, dbcustmaillist, dblocationid, dbcustphone, dbcustemail, dbcustaddress1, dbcustaddress2, dbcustcity, dbcuststate, dbcustzip)
                    VALUES (:bvfname, :bvlname, :bvpass, :bvmaillistid, :bvlocationid, :bvphone, :bvemail, :bvaddress1, :bvaddress2, :bvcity, :bvstate, :bvzip)";
            $sqlinsert = $db->prepare($sqlinsert);
            $sqlinsert->bindValue(':bvfname', $formfield['fffirstname']);
            $sqlinsert->bindValue(':bvlname', $formfield['fflastname']);
            $sqlinsert->bindValue(':bvpass', $encpass);
            $sqlinsert->bindValue(':bvmaillistid', $formfield['ffmaillist']);
            $sqlinsert->bindValue(':bvlocationid', $formfield['fflocation']);
            $sqlinsert->bindValue(':bvphone', $formfield['ffphone']);
            $sqlinsert->bindValue(':bvemail', $formfield['ffemail']);
            $sqlinsert->bindValue(':bvaddress1', $formfield['ffaddress1']);
            $sqlinsert->bindValue(':bvaddress2', $formfield['ffaddress2']);
            $sqlinsert->bindValue(':bvcity', $formfield['ffcity']);
            $sqlinsert->bindValue(':bvstate', $formfield['ffstate']);
            $sqlinsert->bindValue(':bvzip', $formfield['ffzip']);
            $sqlinsert->execute();

            echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Customer Account Created.</div><br>';

            $formfield['fffirstname'] = '';
            $formfield['fflastname'] = '';
            $formfield['ffaddress1'] = '';
            $formfield['ffaddress2'] = '';
            $formfield['ffcity'] = '';
            $formfield['ffstate'] = '';
            $formfield['ffzip'] = '';
            $formfield['ffemail'] = '';
            $formfield['ffphone'] = '';
            $formfield['ffmaillist'] = '';
            $formfield['ffpass1'] = '';
            $formfield['ffpass2'] = '';

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
                    <h2>Enter New Customer</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                        <p>All Fields Required</p>

                        <div class="form-group row">
						<label for="BBfirstname">First Name:</label>
                            <input type="text" name="BBfirstname" placeholder="First Name"
                                   class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fffirstname'])) {
                                       echo $formfield['fffirstname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $fname_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBlastname">Last Name:</label>
                            <input type="text" name="BBlastname" placeholder="Last Name"
                                   class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fflastname'])) {
                                       echo $formfield['fflastname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $lname_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBaddress1">Address 1:</label>
                            <input type="text" name="BBaddress1" placeholder="Address 1"
                                   class="form-control <?php echo (!empty($address1_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffaddress1'])) {
                                       echo $formfield['ffaddress1'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $address1_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBaddress2">Address 2:</label>
                            <input type="text" name="BBaddress2" placeholder="Address 2" class="form-control"
                                   value="<?php if (isset($formfield['ffaddress2'])) {
                                       echo $formfield['ffaddress2'];
                                   } ?>"/>
                        </div>
                        <div class="form-group row">
						<label for="BBcity">City:</label>
                            <input type="text" name="BBcity" placeholder="City"
                                   class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffcity'])) {
                                       echo $formfield['ffcity'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $city_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBstate">State:</label>
                            <input type="text" name="BBstate" placeholder="State"
                                   class="form-control <?php echo (!empty($state_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffstate'])) {
                                       echo $formfield['ffstate'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $state_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBzip">Zip Code:</label>
                            <input type="text" name="BBzip" placeholder="Zip Code"
                                   class="form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffzip'])) {
                                       echo $formfield['ffzip'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBemail">Email:</label>
                            <input type="BBemail" name="BBemail" placeholder="E-mail"
                                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffemail'])) {
                                       echo $formfield['ffemail'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBphone">Phone:</label>
                            <input type="text" name="BBphone" placeholder="Phone"
                                   class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffphone'])) {
                                       echo $formfield['ffphone'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                        </div>
						<div class="form-group row">
                            <label for="BBlocation">Preferred Location:</label>
                            <div class="col">
                                <select name="BBlocation" id="BBlocation"
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
                        <div class="form-group row">
						<label for="BBpass1">Password:</label>
                            <input type="password" name="BBpass1" placeholder="Password"
                                   class="form-control <?php echo (!empty($pass1_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffpass1'])) {
                                       echo $formfield['ffpass1'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $pass1_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="BBpass2">Confirm Password:</label>
                            <input type="password" name="BBpass2" placeholder="Confirm Password"
                                   class="form-control <?php echo (!empty($pass2_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffpass2'])) {
                                       echo $formfield['ffpass2'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $pass2_err; ?></span>
                        </div>

                        <div class="form-group row">
                            <label class="col col-6 col-form-label" for="BBmaillist">Join our Maillist?</label>
                            <div class="col col-6">
                                <select class="form-control" name="BBmaillist" id="BBmaillist">
                                    <?php
                                    if (isset($formfield['ffmaillist']) && $formfield['ffmaillist'] != null) {
                                        if ($formfield['ffmaillist'] == 0) {
                                            echo '<option value="0" selected>No</option>';
                                            echo '<option value="1">Yes</option>';
                                        } else {
                                            echo '<option value="0">No</option>';
                                            echo '<option value="1" selected>Yes</option>';
                                        }
                                    } else {

                                        echo '<option value="0">No</option>';
                                        echo '<option value="1">Yes</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="text-center align-content-end">
                                <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      
    </div>
    <?php
}
include_once 'footer.php';
?>