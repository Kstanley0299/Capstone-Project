<?php
session_start();
$_SESSION['currentpage'] = "enterticket";

$pagetitle = "Enter Ticket";

require_once 'header.php';
require_once 'connect.php';


//NECESSARY VARIABLES
$emp_err = "";
$name_err = "";
$address1_err = "";
$city_err = "";
$state_err = "";
$zip_err = "";
$phone_err = "";
$time_err = "";


$empid_arr = array();
$menu_arr = array();

$item_price = 0;

// get the employees
$employees = 'SELECT * FROM employees';
$result_emp = $db->prepare($employees);
$result_emp->execute();

// get the customers
$customers = 'SELECT * FROM customer';
$result_cust = $db->prepare($customers);
$result_cust->execute();


    if( isset($_POST['submit']))
    {
        $formfield['ffemp'] = trim($_POST['empid']);
        $formfield['ffname'] = trim($_POST['name']);
        $formfield['ffaddress1'] = trim($_POST['address1']);
        $formfield['ffaddress2'] = trim($_POST['address2']);
        $formfield['ffcity'] = trim($_POST['city']);
        $formfield['ffstate'] = trim($_POST['state']);
        $formfield['ffzip'] = trim($_POST['zip']);
        $formfield['ffphone'] = trim($_POST['phone']);
        $formfield['fftime'] = trim($_POST['time']);
        $formfield['ffticketnotes'] = trim($_POST['ticketnotes']);


        if(empty($formfield['ffemp'])) { $emp_err = "Employee cannot be empty."; }
        if(empty($formfield['ffname'])) { $name_err = "Name cannot be empty."; }
        if(empty($formfield['ffaddress1'])) { $address1_err = "Address 1 cannot be empty."; }
        if(empty($formfield['ffcity'])) { $city_err = "City cannot be empty."; }
        if(empty($formfield['ffstate'])) { $state_err = "State cannot be empty."; }
        if(empty($formfield['ffzip'])) { $zip_err = "Zip cannot be empty."; }
        if(empty($formfield['ffphone'])) { $phone_err = "Phone cannot be empty."; }
        if(empty($formfield['fftime'])) { $time_err = "Time cannot be empty."; }

        if(empty($emp_err) && empty($name_err) && empty($address1_err)&& empty($city_err)&& empty($state_err)&& empty($zip_err)&& empty($phone_err)&& empty($time_err))
        {
            try
            {
                $sqlmax = "SELECT MAX(dbticketid) AS maxid FROM ticket";
                $resultmax = $db->prepare($sqlmax);
                $resultmax->execute();
                $rowmax = $resultmax->fetch();
                $maxid = $rowmax["maxid"];
                $maxid = $maxid + 1;

                $sqlinsert = "INSERT into ticket (dbticketid, dbempid, dbtickettypeid, dbticketdate, dbticketnotes, dbtickettable, dbticketclosed)
                        VALUES (:bvticketid, :bvemp, :bvtypeid, now(), :bvnotes, 38, 0)";
                $result_ticket = $db->prepare($sqlinsert);
                $result_ticket->bindValue(':bvticketid', $maxid);
                $result_ticket->bindValue(':bvemp', $formfield['ffemp']);
                $result_ticket->bindValue(':bvtypeid', 2);
                $result_ticket->bindValue(':bvnotes', $formfield['ffnotes']);
                $result_ticket->execute();

                $sqlinsert2 = "INSERT INTO delivery (dbticketid, dbempid, dbdeliveryadd1, dbdeliveryadd2, dbdeliverycity, dbdeliverystate, dbdeliveryzip, dbdeliveryphone, dbdeliveryname, dbdeliverydate, dbdeliverytime)
                                VALUES (:bvticketid, :bvempid, :bvdeliveryadd1, :bvdeliveryadd2, :bvdeliverycity, 
                                        :bvdeliverystate, :bvdeliveryzip, :bvdeliveryphone, :bvdeliveryname, now(), :bvdeliverytime)";
                $result_delivery = $db->prepare($sqlinsert2);
                $result_delivery->bindValue(':bvticketid', $maxid);
                $result_delivery->bindValue(':bvempid', $formfield['ffemp']);
                $result_delivery->bindValue(':bvdeliveryadd1', $formfield['ffaddress1']);
                $result_delivery->bindValue(':bvdeliveryadd2', $formfield['ffaddress2']);
                $result_delivery->bindValue(':bvdeliverycity', $formfield['ffcity']);
                $result_delivery->bindValue(':bvdeliverystate', $formfield['ffstate']);
                $result_delivery->bindValue(':bvdeliveryzip', $formfield['ffzip']);
                $result_delivery->bindValue(':bvdeliveryphone', $formfield['ffphone']);
                $result_delivery->bindValue(':bvdeliveryname', $formfield['ffname']);
                $result_delivery->bindValue(':bvdeliverytime', $formfield['fftime']);
                $result_delivery->execute();

                echo '<div class="container">';
                echo    '<div class="row">';
                echo        '<div class="card card-body bg-light mt-5">';
                echo        '<h2>Ticket Number ' . $maxid . '</h2>';
                echo '<form method="post" action="insertticketdetails.php">';
                echo        '<input type="hidden" name="ticketid" value="' . $maxid . '">';
                echo        '<input type="submit" name="thesubmit" value="Enter Ticket Items">';
                echo        '</form>';
                echo        '</div>';
                echo    '</div>';
                echo '</div>';
                $showform = 0;

            } catch(PDOException $e)
            {
                echo 'Error!' .$e->getMessage();
                exit();
            }
        }
    }

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 3 || $_SESSION['loginpermit'] == 4 && $showform == 1) {
    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Enter Ticket</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">

                    <div class="row">
                        <div class="form-group col">
                            <label for="empid">Delivery Driver:</label>
                            <select name="empid" id="empid"
                                    class="form-control <?php echo (!empty($emp_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Employee</option>
                                <?php
                                while ($rowe = $result_emp->fetch()) {
                                    if ($rowe['dbempid'] == $formfield['ffempid']) {
                                        $checker = 'selected';
                                    } else {
                                        $checker = '';
                                    }
                                    echo '<option value="' . $rowe['dbempid'] . '" ' . $checker . '>' . $rowe['dbempid'] . ' &nbsp;|&nbsp; ' . $rowe['dbempfname'] . ' &nbsp;|&nbsp; ' . $rowe['dbemplname'] . '</option>';
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $table_err; ?></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col">
                            <label for="name">Name:</label>
                            <input type="text" name="name" placeholder="Name"
                                   class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffname'])) {
                                       echo $formfield['ffname'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
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

                    <div class="row">
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
                                <option value="AL">Alabama</option>
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
                                <option value="SC" selected>South Carolina</option>
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
                            <label for="zip">Zip Code:</label>
                            <input type="text" name="zip" placeholder="Zip Code"
                                   class="form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['ffzip'])) {
                                       echo $formfield['ffzip'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                        </div>
                    </div>

                    <div class="row">
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
                            <label for="time">DeliveryTime:</label>
                            <input type="time" id="time" name="time" class="form-control" min="11:00" max="22:00"
                                   value="<?php if (isset($formfield['fftime'])) {
                                       echo $formfield['fftime'];
                                   } else { echo date("H:i", strtotime('2 hour'));
                                   } ?>" />
                            <span class="invalid-feedback"><?php echo $time_err; ?></span>
                        </div>

                    </div>

                    <div class="row align-content-center">

                        <div class="form-group col w-50">
                            <label for="notes">Ticket Notes:</label>
                            <input type="text" name="notes" id="notes" maxlength="255"
                                   class="form-control"
                                   value="<?php if (isset($formfield['ffnotes'])) {
                                       echo $formfield['ffnotes'];
                                   } ?>"/>
                        </div>
                    </div>

                    <div class="form-row justify-content-center">
                        <div class="text-center">
                            <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br><br>
    <?php
}
include_once 'footer.php';
?>