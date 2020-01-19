<?php
session_start();
$_SESSION['currentpage'] = "updatecustomer";
$pagetitle = "Update Customer";

require_once 'header.php';
require_once 'connect.php';

// assign custid
$custid = $_POST['custid'];

// get the customer info
$selectcust = "SELECT * FROM customer WHERE dbcustid = :bvcustid";
$result_cust = $db->prepare($selectcust);
$result_cust->bindValue(':bvcustid', $custid);
$result_cust->execute();
$row_cust = $result_cust->fetch();

// get the locations
$locations = 'SELECT * FROM locations';
$result_l = $db->prepare($locations);
$result_l->execute();

$formfield['fffirstname'] = $row_cust['dbcustfname'];
$formfield['fflastname'] = $row_cust['dbcustlname'];
$formfield['ffphone'] = $row_cust['dbcustphone'];
$formfield['ffaddress1'] = $row_cust['dbcustaddress1'];
$formfield['ffaddress2'] = $row_cust['dbcustaddress2'];
$formfield['ffcity'] = $row_cust['dbcustcity'];
$formfield['ffstate'] = $row_cust['dbcuststate'];
$formfield['ffzip'] = $row_cust['dbcustzip'];
$formfield['ffemail'] = $row_cust['dbcustemail'];
$formfield['ffmaillist'] = $row_cust['dbcustmaillist'];
$formfield['fflocation'] = $row_cust['dblocationid'];


//NECESSARY VARIABLES
$errormsg = "";
$fname_err = $lname_err = $hiredate_err = $phone_err = $email_err = "";
$address1_err = $city_err = $state_err = $zip_err = $maillist_err = "";
$password = '';

if( isset($_POST['submit']) )
{
    $custid = $_POST['custid'];
    $formfield['fffirstname'] = trim($_POST['firstname']);
    $formfield['fflastname'] = trim($_POST['lastname']);
    $formfield['ffemail'] = trim(strtolower($_POST['email']));
    $formfield['ffaddress1'] = trim($_POST['address1']);
    $formfield['ffaddress2'] = trim($_POST['address2']);
    $formfield['ffcity'] = trim($_POST['city']);
    $formfield['ffstate'] = trim($_POST['state']);
    $formfield['ffzip'] = trim($_POST{'zip'});
    $formfield['ffphone'] = trim($_POST['phone']);
    $formfield['ffmaillist'] = $_POST['maillist'];
	 $formfield['fflocation'] = $_POST['location'];

    if(empty($formfield['fffirstname'])){$fname_err = "Please enter first name.";}
    if(empty($formfield['fflastname'])){$lname_err = "Please enter last name.";}
    if(empty($formfield['ffaddress1'])){$address1_err = "Please enter address 1.";}
    if(empty($formfield['ffcity'])){$city_err = "Please enter city.";}
    if(empty($formfield['ffstate'])){$state_err = "Please enter state.";}
    if(empty($formfield['ffzip'])){$zip_err = "Please enter zip.";}
    if(empty($formfield['ffemail'])){$email_err = "Please enter Email.";}
	if(empty($formfield['fflocation'])){$location_err = "Please select a location.";}
    //VALIDATE THE EMAIL
    if(empty($email_err))
    {
        if (!filter_var($formfield['ffemail'], FILTER_VALIDATE_EMAIL))
        {
            $email_err = "Email is not valid.";
        }
    }
    if(empty($formfield['ffphone'])){$phone_err = "Please enter phone number.";}

    if(empty($fname_err) && empty($lname_err) && empty($address1_err) && empty($city_err) && empty($state_err) &&
        empty($zip_err) && empty($email_err) && empty($phone_err) && empty($maillist_err))
    {
        try
        {

            $sqlupdate = "UPDATE customer 
                          SET dbcustfname = :bvfname, 
                              dbcustlname = :bvlname,
                              dbcustemail = :bvemail,
                              dbcustaddress1 = :bvaddress1, 
                              dbcustaddress2 = :bvaddress2, 
                              dbcustcity = :bvcity, 
                              dbcuststate = :bvstate, 
                              dbcustzip = :bvzip,
                              dbcustphone = :bvphone,
                              dbcustmaillist = :bvmaillist,
			      dblocationid = :bvlocation
                          WHERE dbcustid = :bvcustid";
            $resultupdate = $db->prepare($sqlupdate);
            $resultupdate->bindValue(':bvcustid', $custid);
            $resultupdate->bindValue(':bvfname', $formfield['fffirstname']);
            $resultupdate->bindValue(':bvlname', $formfield['fflastname']);
            $resultupdate->bindValue(':bvphone', $formfield['ffphone']);
            $resultupdate->bindValue(':bvemail', $formfield['ffemail']);
            $resultupdate->bindValue(':bvaddress1', $formfield['ffaddress1']);
            $resultupdate->bindValue(':bvaddress2', $formfield['ffaddress2']);
            $resultupdate->bindValue(':bvcity', $formfield['ffcity']);
            $resultupdate->bindValue(':bvstate', $formfield['ffstate']);
            $resultupdate->bindValue(':bvzip', $formfield['ffzip']);
            $resultupdate->bindValue(':bvmaillist', $formfield['ffmaillist']);
	    $resultupdate->bindValue(':bvlocation', $formfield['fflocation']);
            $resultupdate->execute();

            echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Customer Information Updated Successfully.</div><br>';

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
                    <h2>Update Customer Information</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

                        <div class="form-group row">
						<label for="firstname">First Name:</label>
                            <input type="text" name="firstname" placeholder="First Name"
                                   class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fffirstname'])) {
                                       echo $formfield['fffirstname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $fname_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="lastname">Last Name:</label>
                            <input type="text" name="lastname" placeholder="Last Name"
                                   class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fflastname'])) {
                                       echo $formfield['fflastname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $lname_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="address1">Address 1:</label>
                            <input type="text" name="address1" placeholder="Address 1"
                                   class="form-control <?php echo (!empty($address1_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffaddress1'])) {
                                       echo $formfield['ffaddress1'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $address1_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="address2">Address 2:</label>
                            <input type="text" name="address2" placeholder="Address 2" class="form-control"
                                   value="<?php if (isset($formfield['ffaddress2'])) {
                                       echo $formfield['ffaddress2'];
                                   } ?>"/>
                        </div>
                        <div class="form-group row">
						<label for="city">City:</label>
                            <input type="text" name="city" placeholder="City"
                                   class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffcity'])) {
                                       echo $formfield['ffcity'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $city_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="state">State:</label>
                            <input type="text" name="state" placeholder="State"
                                   class="form-control <?php echo (!empty($state_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffstate'])) {
                                       echo $formfield['ffstate'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $state_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="zip">Zip Code:</label>
                            <input type="text" name="zip" placeholder="Zip Code"
                                   class="form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffzip'])) {
                                       echo $formfield['ffzip'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="email">Email:</label>
                            <input type="email" name="email" placeholder="E-mail"
                                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffemail'])) {
                                       echo $formfield['ffemail'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group row">
						<label for="phone">Phone:</label>
                            <input type="text" name="phone" placeholder="Phone"
                                   class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffphone'])) {
                                       echo $formfield['ffphone'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                        </div>
						<div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="location">Preferred Location:</label>
                            <div class="col-sm-8">
                                <select name="location" id="location" required
                                        class="form-control <?php echo (!empty($position_err)) ? 'is-invalid' : ''; ?>">
                                    <option value="">Select Location</option>
                                    <?php
                                    while ($rowl = $result_l->fetch())
                                    {
                                        if ($rowl['dblocationid'] == $formfield['fflocation']) {
                                            $checker = 'selected';
                                        } else {
                                            $checker = '';
                                        }
                                        echo '<option value="' . $rowl['dblocationid'] . '" ' . $checker . '>' . $rowl['dblocationname'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="invalid-feedback"><?php echo $position_err; ?></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="yesorno" class="col">Receive Email from us?</label>
                            <div id="yesorno" class="col">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="yes" name="maillist" class="custom-control-input" value="1" checked="checked">
                                    <label class="custom-control-label" for="yes">Yes</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="no" name="maillist" class="custom-control-input" value="0" >
                                    <label class="custom-control-label" for="no">No</label>
                                </div>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="col text-center">
                                <input type="hidden" name="custid" value="<?php echo $custid; ?>">
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
