<?php
session_start();
$_SESSION['currentpage'] = "viewusers";
$pagetitle = "View Employees";

require_once 'header.php';
require_once 'connect.php';


// get the positions
$positions = 'SELECT * FROM positions';
$result_p = $db->prepare($positions);
$result_p->execute();

$locations = 'SELECT * FROM locations';
$result_lo = $db->prepare($locations);
$result_lo->execute();

////NECESSARY VARIABLES
//$sqlselect = "SELECT * FROM employees;";
//$result_emps = $db->prepare($sqlselect);
//$result_emps->execute();

$sqlselect = "SELECT employees.*, positions.dbpositiondescr, locations.dblocationname FROM employees, positions, locations
              WHERE employees.dbpositionid = positions.dbpositionid
              AND employees.dblocationid = locations.dblocationid";
$result_emps = $db->prepare($sqlselect);
$result_emps->execute();

if(isset($_POST['delete']))
{
    $formfield['ffempid'] = $_POST['empid'];

    $sqldelete = 'DELETE FROM employees WHERE dbempid = :bvempid';
    $stmtdelete = $db->prepare($sqldelete);
    $stmtdelete->bindValue(':bvempid', $formfield['ffempid']);
    $stmtdelete->execute();

    $sqlselect = "SELECT employees.*, positions.dbpositiondescr, locations.dblocationname FROM employees, positions, locations
              WHERE employees.dbpositionid = positions.dbpositionid
              AND employees.dblocationid = locations.dblocationid";
    $result_emps = $db->prepare($sqlselect);
    $result_emps->execute();
}

if( isset($_POST['submit']) )
{
    $formfield['ffempid'] = $_POST['dbempid'];
    $formfield['fffirstname'] = trim($_POST['firstname']);
    $formfield['fflastname'] = trim($_POST['lastname']);
    $formfield['ffaddress1'] = trim($_POST['address1']);
    $formfield['ffaddress2'] = trim($_POST['address2']);
    $formfield['ffcity'] = trim($_POST['city']);
    $formfield['ffstate'] = trim($_POST['state']);
    $formfield['ffzip'] = trim($_POST{'zip'});
    $formfield['ffemail'] = trim(strtolower($_POST['email']));
    $formfield['ffphone'] = trim($_POST['phone']);
    $formfield['ffhiredate'] = $_POST['hiredate'];
    $formfield['ffposition'] = $_POST['position'];
    $formfield['fflocation'] = $_POST['location'];

    try
    {

        $sqlselect_emps = "SELECT emp.*, pos.dbpositiondescr, loc.dblocationname
                        FROM employees emp, positions pos, locations loc
                        WHERE emp.dbpositionid = pos.dbpositionid
                        AND emp.dblocationid = loc.dblocationid
                        AND emp.dbempfname like CONCAT('%', :bvfname, '%')
                        AND emp.dbemplname like CONCAT('%', :bvlname, '%')
                        AND emp.dbemphiredate like CONCAT('%', :bvhiredate, '%') 
                        AND emp.dbpositionid like CONCAT('%', :bvpositionid, '%')
                        AND emp.dbempphone like CONCAT('%', :bvphone, '%') 
                        AND emp.dbempemail like CONCAT('%', :bvemail, '%')
                        AND emp.dbempaddress1 like CONCAT('%', :bvaddress1, '%') 
                        AND emp.dbempaddress2 like CONCAT('%', :bvaddress2, '%') 
                        AND emp.dbempcity like CONCAT('%', :bvcity, '%') 
                        AND emp.dbempstate like CONCAT('%', :bvstate, '%') 
                        AND emp.dbempzip like CONCAT('%', :bvzip, '%')
                        AND emp.dblocationid like CONCAT('%', :bvlocation, '%')";

        $result_emps = $db->prepare($sqlselect_emps);
        $result_emps->bindValue(':bvfname', $formfield['fffirstname']);
        $result_emps->bindValue(':bvlname', $formfield['fflastname']);
        $result_emps->bindValue(':bvhiredate', $formfield['ffhiredate']);
        $result_emps->bindValue(':bvpositionid', $formfield['ffposition']);
        $result_emps->bindValue(':bvphone', $formfield['ffphone']);
        $result_emps->bindValue(':bvemail', $formfield['ffemail']);
        $result_emps->bindValue(':bvaddress1', $formfield['ffaddress1']);
        $result_emps->bindValue(':bvaddress2', $formfield['ffaddress2']);
        $result_emps->bindValue(':bvcity', $formfield['ffcity']);
        $result_emps->bindValue(':bvstate', $formfield['ffstate']);
        $result_emps->bindValue(':bvzip', $formfield['ffzip']);
        $result_emps->bindValue(':bvlocation', $formfield['fflocation']);
        $result_emps->execute();

    }
    catch(PDOException $e)
    {
        echo 'Error!' .$e->getMessage();
        exit();
    }

}//if isset submit

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 2) {
    ?>
<br>

    <div class="container">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="card card-body bg-light mt-5">
                    <h2>Search Employees</h2>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                        <div class="row">
                            <div class="form-group col">
                                <label for="firstname">First Name:</label>
                                <input type="text" name="firstname" placeholder="First Name"
                                       class="col form-control <?php echo (!empty($fname_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['fffirstname'])) {
                                           echo $formfield['fffirstname'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $fname_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="lastname">Last Name:</label>
                                <input type="text" name="lastname" placeholder="Last Name"
                                       class="col form-control <?php echo (!empty($lname_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['fflastname'])) {
                                           echo $formfield['fflastname'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $lname_err; ?></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col">
                                <label for="address1">Address 1:</label>
                                <input type="text" name="address1" placeholder="Address 1"
                                       class="col form-control <?php echo (!empty($address1_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffaddress1'])) {
                                           echo $formfield['ffaddress1'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $address1_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="address2">Address 2:</label>
                                <input type="text" name="address2" placeholder="Address 2" class="col form-control"
                                       value="<?php if (isset($formfield['ffaddress2'])) {
                                           echo $formfield['ffaddress2'];
                                       } ?>"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col">
                                <label for="city">City:</label>
                                <input type="text" name="city" placeholder="City"
                                       class="col form-control <?php echo (!empty($city_err)) ? 'is-invalid' : ''; ?>"
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
                                    <option value="AL" <?php if(isset($formfield['ffstate']) && $formfield['ffstate'] == "AL") { echo 'selected'; } ?>>Alabama</option>
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
                            </div>
                            <div class="form-group col">
                                <label for="zip">Zip:</label>
                                <input type="text" name="zip" placeholder="Zip Code"
                                       class="col form-control <?php echo (!empty($zip_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffzip'])) {
                                           echo $formfield['ffzip'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $zip_err; ?></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col">
                                <label for="email">Email:</label>
                                <input type="text" name="email" placeholder="E-mail"
                                       class="col form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffemail'])) {
                                           echo $formfield['ffemail'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $email_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="phone">Phone:</label>
                                <input type="text" name="phone" placeholder="Phone"
                                       class="col form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>"
                                       value="<?php if (isset($formfield['ffphone'])) {
                                           echo $formfield['ffphone'];
                                       } ?>"/>
                                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                            </div>
                            <div class="form-group col">
                                <label for="hiredate">Hire Date:</label>
                                    <input type="date" name="hiredate"
                                           class="col form-control <?php echo (!empty($hiredate_err)) ? 'is-invalid' : ''; ?>"
                                           value="<?php if (isset($formfield['ffhiredate'])) {
                                               echo $formfield['ffhiredate'];
                                           } ?>"/>
                                    <span class="invalid-feedback"><?php echo $hiredate_err; ?></span>
                                
                            </div>
                        </div>

                        <div class="row">
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
                        </div>
                        <div class="form-row">
                            <div class="col text-center">
                                <input type="submit" value="Search" name="submit" class="btn btn-secondary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
    </div>

    <div class="align-content-center container-fluid bg-white rounded-lg">
        <br>

        <table class="table table-responsive-sm table-striped w-auto table-hover bg-white display rounded-lg" id="employees">
            <thead class="thead-light">
            <tr>
                <th>First&nbsp;Name</th>
                <th>Last&nbsp;Name</th>
                <th>Hire&nbsp;Date</th>
                <th>Position</th>
				<th>Permission&nbsp;Level</th>
                <th>Phone</th>
                <th>Email</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
		
            <?php
            while ($row = $result_emps->fetch()) {

                echo '<tr><td>' . $row['dbempfname'] . '</td>';
                echo '<td>' . $row['dbemplname'] . '</td>';
                echo '<td>' . $row['dbemphiredate'] . '</td>';
                echo '<td>' . $row['dbpositiondescr'] . '</td>';
                echo '<td>' . $row['dbpermissionlevel'] . '</td>';
                echo '<td>' . $row['dbempphone'] . '</td>';
                echo '<td>' . $row['dbempemail'] . '</td>';
                echo '<td>';
                echo '<form action="updateemployee.php" method="post">';
                echo '<input type="hidden" name="empid" value="' . $row['dbempid'] . '">';
                echo '<input type="submit" name="edit" class="btn-sm btn-secondary" value="Edit">';
				echo '</form></td><td>';
				echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
				echo '<input type="hidden" name="empid" value="' . $row['dbempid'] . '">';
				echo '<input type="submit" name="delete" class="btn btn-sm btn-secondary" value="Delete">';
				echo '</form></td></tr>';
                
            }
            ?>
            </tbody>
        </table>

        <script>
            $(document).ready( function () {
                $('#employees').DataTable();
            } );
        </script>

    </div><br><br>
    <?php
}
include_once 'footer.php';
?>