<?php
session_start();
$_SESSION['currentpage'] = "enternewusers";

$pagetitle = "Enter New Employees";

require_once 'header.php';
require_once 'connect.php';

// get the positions
$positions = 'SELECT * FROM positions';
$result_p = $db->prepare($positions);
$result_p->execute();

// get the locations
$locations = 'SELECT * FROM locations';
$result_lo = $db->prepare($locations);
$result_lo->execute();

//NECESSARY VARIABLES
$errormsg = "";
$fname_err = $lname_err = $pass1_err = $pass2_err = $hiredate_err = $phone_err = $email_err = "";
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
        $formfield['ffpass1'] = trim($_POST['pass1']);
        $formfield['ffpass2'] = trim($_POST['pass2']);

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
        if(empty($formfield['ffhiredate'])){$hiredate_err = "Hire Date cannot be empty.";}
        if(empty($formfield['ffposition'])){$position_err = "Position cannot be empty";}
        if(empty($formfield['fflocation'])){$location_err = "Location cannot be empty";}
        if(empty($formfield['ffpass1'])){$pass1_err = "Password cannot be empty.";}
        if(empty($formfield['ffpass2'])){$pass2_err = "Confirm Password cannot be empty.";}
        //confirm passwords match
        if ($formfield['ffpass1'] != $formfield['ffpass2']) {
            $pass1_err = $pass2_err = "Your passwords do not match.";
        }

        if(empty($fname_err) && empty($lname_err) && empty($address1_err) && empty($city_err) && empty($state_err) &&
         empty($zip_err) && empty($email_err) && empty($phone_err) && empty($location_err) && empty($pass1_err) && empty($pass2_err))
        {
            $password = $formfield['ffpass1'];
            $options = [
                'cost' => 12,
            ];
            $encpassword = password_hash($password, PASSWORD_BCRYPT, $options);

            try
            {
                $sqlinsert = "INSERT into employees (dbempfname, dbemplname, dbemppass, dbemphiredate, dbpositionid, dblocationid, dbempphone, dbempemail, dbempaddress1, dbempaddress2, dbempcity, dbempstate, dbempzip)
                        VALUES (:bvfname, :bvlname, :bvpass, :bvhiredate, :bvpositionid, :bvlocationid, :bvphone, :bvemail, :bvaddress1, :bvaddress2, :bvcity, :bvstate, :bvzip)";
                $sqlinsert = $db->prepare($sqlinsert);
                $sqlinsert->bindValue(':bvfname', $formfield['fffirstname']);
                $sqlinsert->bindValue(':bvlname', $formfield['fflastname']);
                $sqlinsert->bindValue(':bvpass', $encpassword);
                $sqlinsert->bindValue(':bvhiredate', $formfield['ffhiredate']);
                $sqlinsert->bindValue(':bvpositionid', $formfield['ffposition']);
				$sqlinsert->bindValue(':bvlocationid', $formfield['fflocation']);
                $sqlinsert->bindValue(':bvphone', $formfield['ffphone']);
                $sqlinsert->bindValue(':bvemail', $formfield['ffemail']);
                $sqlinsert->bindValue(':bvaddress1', $formfield['ffaddress1']);
                $sqlinsert->bindValue(':bvaddress2', $formfield['ffaddress2']);
                $sqlinsert->bindValue(':bvcity', $formfield['ffcity']);
                $sqlinsert->bindValue(':bvstate', $formfield['ffstate']);
                $sqlinsert->bindValue(':bvzip', $formfield['ffzip']);
                $sqlinsert->execute();

                echo '<br><div class="alert alert-success text-center" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">New Employee Added.</div><br>';

                $formfield['fffirstname'] = '';
                $formfield['fflastname'] = '';
                $formfield['ffaddress1'] = '';
                $formfield['ffaddress2'] = '';
                $formfield['ffcity'] = '';
                $formfield['ffstate'] = '';
                $formfield['ffzip'] = '';
                $formfield['ffemail'] = '';
                $formfield['ffphone'] = '';
                $formfield['ffhiredate'] = '';
                $formfield['ffposition'] = '';
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
            <div class="col">
                <div class="card card-body bg-light mt-5">
                    <h2>Enter New Employee</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

                        <div class="row align-content-center">
                            <div class="form-group col">
                                <label for="firstname">First Name:</label>
                                <input type="text" name="firstname" placeholder="First Name"
                                       class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['fffirstname'])) {
                                           echo $formfield['fffirstname'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $fname_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="lastname">Last Name:</label>
                                <input type="text" name="lastname" placeholder="Last Name"
                                       class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['fflastname'])) {
                                           echo $formfield['fflastname'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $lname_err; ?></span>
                            </div>
                        </div>

                        <div class="row align-content-center">
                            <div class="form-group col">
                                <label for="address1">Address 1:</label>
                                <input type="text" name="address1" placeholder="Address 1"
                                       class="form-control <?php echo (!empty($address1_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffaddress1'])) {
                                           echo $formfield['ffaddress1'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $address1_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="address2">Address 2:</label>
                                <input type="text" name="address2" placeholder="Address 2" class="form-control"
                                       value="<?php if (isset($formfield['ffaddress2'])) {
                                           echo $formfield['ffaddress2'];
                                       } ?>"/>
                            </div>
                        </div>

                        <div class="row align-content-center">
                            <div class="form-group col">
                                <label for="city">City:</label>
                                <input type="text" name="city" placeholder="City"
                                       class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffcity'])) {
                                           echo $formfield['ffcity'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $city_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="state">State:</label>
                                <select name="state" class="form-control <?php echo (!empty($state_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php if (isset($formfield['ffcuststate'])) {
                                            echo $formfield['ffcuststate'];
                                        } ?>">
                                    <option value="">Select State</option>
                                    <option value="AL" <?php if(isset($formfield['ffcuststate']) && $formfield['ffcuststate'] == "AL") { echo 'selected'; } ?>>Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District Of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC" <?php if(isset($formfield['ffstate']) && $formfield['ffstate'] == "SC") { echo 'selected'; } ?>>South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                                <span class="invalid-feedback"><?php echo $state_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="zip">Zip:</label>
                                <input type="text" name="zip" placeholder="Zip Code"
                                       class="form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffzip'])) {
                                           echo $formfield['ffzip'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                            </div>
                        </div>

                        <div class="row align-content-center">
                            <div class="form-group col">
                                <label for="email">Email:</label>
                                <input type="email" name="email" placeholder="E-mail"
                                       class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffemail'])) {
                                           echo $formfield['ffemail'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="phone">Phone:</label>
                                <input type="text" name="phone" placeholder="Phone"
                                       class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffphone'])) {
                                           echo $formfield['ffphone'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="hiredate">Hire&nbsp;Date:</label>
                                <input type="date" name="hiredate"
                                       class="col form-control <?php echo (!empty($hiredate_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffhiredate'])) {
                                           echo $formfield['ffhiredate'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $hiredate_err; ?></span>
                            </div>
                        </div>


                        <div class="row align-content-center">
                            <div class="form-group col">
                                <label for="location">Restaurant&nbsp;Location:</label>
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
                            <div class="form-group col">
                                <label for="position">Position:</label>
                                <select name="position" id="position"
                                        class="col form-control <?php echo (!empty($position_err)) ? 'is-invalid' : ''; ?>">
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

                        <div class="row align-content-center">
                            <div class="form-group col">
                                <label for="pass1">Password:</label>
                                <input type="password" name="pass1" placeholder="Password"
                                       class="form-control <?php echo (!empty($pass1_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffpass1'])) {
                                           echo $formfield['ffpass1'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $pass1_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="pass2">Confirm&nbsp;Password:</label>
                                <input type="password" name="pass2" placeholder="Confirm Password"
                                       class="form-control <?php echo (!empty($pass2_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffpass2'])) {
                                           echo $formfield['ffpass2'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $pass2_err; ?></span>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col text-center">
                                <input class="btn btn-secondary" type="submit" name="submit" value="Submit">
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