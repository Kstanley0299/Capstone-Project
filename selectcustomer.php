<?php
session_start();
$_SESSION['currentpage'] = 'selectcustomer';
$pagetitle = 'Select Customers';
require_once 'header.php';
require_once 'connect.php';

$errormsg = "";
$showform = 1;

//grab info from mailing list to populate drop down
$sqlselect = 'select * from customer';
$result = $db->prepare($sqlselect);
$result->execute();

// get the locations
$locations = 'SELECT * FROM locations';
$result_lo = $db->prepare($locations);
$result_lo->execute();

if( isset($_POST['submit']) )
		{
			
			$stringclause = ' ';
			$formfield['ffcustfname'] = trim($_POST['BBfirstname']);
			$formfield['ffcustlname'] = trim(strtolower($_POST['BBlastname']));
			$formfield['ffcustaddress1'] = trim($_POST['BBaddress1']);
			$formfield['ffcustaddress2'] = trim($_POST['BBaddress2']);
			$formfield['ffcustcity'] = trim($_POST['BBcity']);
			$formfield['ffcuststate'] = trim($_POST['BBstate']);
			$formfield['ffcustzip'] = trim($_POST['BBzip']);
			$formfield['ffcustphone'] = trim($_POST['BBphone']);
			$formfield['ffmaillist'] = trim($_POST['BBmaillist']);
            $formfield['fflocation'] = $_POST['location'];
			
			if ($formfield['ffcustmlist'] != '') {
				$stringclause .= " AND dbcustmaillist = :bvmaillist";
			}
			
			
			$sqlselect = "SELECT * from customer
							where dbcustfname like CONCAT('%', :bvfname, '%')
							AND dbcustlname like CONCAT('%', :bvlname, '%')
							AND dbcustaddress1 like CONCAT('%', :bvaddress1, '%')
							AND dbcustaddress2 like CONCAT('%', :bvaddress2, '%')
							AND dbcustcity like CONCAT('%', :bvcity, '%')
							AND dbcuststate like CONCAT('%', :bvstate, '%')
							AND dbcustzip like CONCAT('%', :bvzip, '%')
							AND dblocationid like CONCAT('%', :bvlocation, '%')
							AND dbcustphone like CONCAT('%', :bvphone, '%')" .$stringclause;
							
			$result = $db->prepare($sqlselect);
			
			$result->bindValue(':bvfname', $formfield['ffcustfname']);
			$result->bindValue(':bvlname', $formfield['ffcustlname']);
			$result->bindValue(':bvaddress1', $formfield['ffcustaddress1']);
			$result->bindValue(':bvaddress2', $formfield['ffcustaddress2']);
			$result->bindValue(':bvcity', $formfield['ffcustcity']);
			$result->bindValue(':bvstate', $formfield['ffcuststate']);
			$result->bindValue(':bvzip', $formfield['ffcustzip']);
            $result->bindValue(':bvlocation', $formfield['fflocation']);
			$result->bindValue(':bvphone', $formfield['ffcustphone']);
			if ($formfield['ffcustmlist'] !='') {
				$result->bindValue(':bvmaillist', $formfield['ffmaillist']);
			}
	$result->execute();
		}

	if($visible == 1 && $_SESSION['loginpermit'] == 1) {
	?>

        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="card card-body bg-light mt-5">
                        <h2>Search Customers</h2>
                        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="BBfirstname">First Name:</label>
                                    <input type="text" name="BBfirstname" placeholder="First Name"
                                           class="form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustfname'])) {
                                               echo $formfield['ffcustfname'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $fname_err; ?></span>
                                </div>
                                <div class="form-group col">
                                    <label for="BBlastname">Last Name:</label>
                                    <input type="text" name="BBlastname" placeholder="Last Name"
                                           class="form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustlname'])) {
                                               echo $formfield['ffcustlname'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $lname_err; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col">
                                    <label for="location">Restaurant Location:</label>
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
                                    <label for="BBaddress1">Address 1:</label>
                                    <input type="text" name="BBaddress1" placeholder="Address 1"
                                           class="form-control <?php echo (!empty($address1_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustaddress1'])) {
                                               echo $formfield['ffcustaddress1'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $address1_err; ?></span>
                                </div>
                                <div class="form-group col">
                                    <label for="BBaddress2">Address 2:</label>
                                    <input type="text" name="BBaddress2" placeholder="Address 2" class="form-control"
                                           value="<?php if (isset($formfield['ffcustaddress2'])) {
                                               echo $formfield['ffcustaddress2'];
                                           } ?>"/>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col">
                                    <label for="BBcity">City:</label>
                                    <input type="text" name="BBcity" placeholder="City"
                                           class="form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustcity'])) {
                                               echo $formfield['ffcustcity'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $city_err; ?></span>
                                </div>
                                <div class="form-group col">
                                    <label for="BBstate">State:</label>
                                    <select name="BBstate" class="form-control <?php echo (!empty($state_err)) ? 'is-invalid' : ''; ?>"
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
                                        <option value="SC" <?php if(isset($formfield['ffcuststate']) && $formfield['ffcuststate'] == "SC") { echo 'selected'; } ?>>South Carolina</option>
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
                                </div>
                                <div class="form-group col">
                                    <label for="BBzip">Zip Code:</label>
                                    <input type="text" name="BBzip" placeholder="Zip Code"
                                           class="form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustzip'])) {
                                               echo $formfield['ffcustzip'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col">
                                    <label for="BBemail">Email:</label>
                                    <input type="BBemail" name="BBemail" placeholder="E-mail"
                                           class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustemail'])) {
                                               echo $formfield['ffcustemail'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                                </div>
                                <div class="form-group col">
                                    <label for="BBphone">Phone:</label>
                                    <input type="text" name="BBphone" placeholder="Phone"
                                           class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffcustphone'])) {
                                               echo $formfield['ffcustphone'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                                </div>
                                <div class="form-group col">
                                    <label for="BBmaillist">In the Mailing List?</label>
                                    <select class="form-control" name="BBmaillist" id="BBmaillist">
                                        <option value="">Select Option</option>
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
                                <div class="col text-center">
                                    <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="align-content-center container-fluid bg-white rounded-lg">
            <br>

            <table class="table table-responsive-sm table-striped w-auto table-hover bg-white display rounded-lg" id="customers">
                <thead class="thead-light">
                <tr>
                    <th>First&nbsp;Name</th>
                    <th>Last&nbsp;Name</th>
                    <th>Email</th>
                    <th>Address&nbsp;1</th>
                    <th>Address&nbsp;2</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                    <th>Phone</th>
                    <th>Mailing&nbsp;List</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = $result->fetch()) {

                    echo '<tr><td>' . $row['dbcustfname'] . '</td>';
                    echo '<td>' . $row['dbcustlname'] . '</td>';
                    echo '<td>' . $row['dbcustemail'] . '</td>';
                    echo '<td>' . $row['dbcustaddress1'] . '</td>';
                    echo '<td>' . $row['dbcustaddress2'] . '</td>';
                    echo '<td>' . $row['dbcustcity'] . '</td>';
                    echo '<td>' . $row['dbcuststate'] . '</td>';
                    echo '<td>' . $row['dbcustzip'] . '</td>';
                    echo '<td>' . $row['dbcustphone'] . '</td>';
                    if($row['dbcustmaillist'] == 0) {
                        echo '<td>No</td>';
                    } else {
                        echo '<td>Yes</td>';
                    }
                    echo '<td>';
                    echo '<form action="updatecustomer.php" method="post">';
                    echo '<input type="hidden" name="custid" value="' . $row['dbcustid'] . '">';
                    echo '<input type="submit" name="edit" class="btn-sm btn-secondary" value="Edit">';
                    echo '</form></td>';
                }
                ?>
                </tbody>
            </table>
        </div>

        <script>
            $(document).ready( function () {
                $('#customers').DataTable();
            } );
        </script>

        <br>

<?php
	}
include_once 'footer.php';
?>
				'</td></td>' .