<?php
session_start();
$_SESSION['currentpage'] = "enterticket";

$pagetitle = "Enter Ticket";

require_once 'header.php';
require_once 'connect.php';


//NECESSARY VARIABLES
$table_err = "";

$empid = $_SESSION['loginid'];

$item_price = 0;


// get the ticket types
$tables = 'SELECT * FROM tables';
$result_tables = $db->prepare($tables);
$result_tables->execute();

// get the locations
$locations = 'SELECT * FROM locations';
$result_l = $db->prepare($locations);
$result_l->execute();


  // get the employee info
    $selectuser = "SELECT * FROM employees WHERE dbempid = :bvempid";
    $result_user = $db->prepare($selectuser);
    $result_user->bindValue(':bvempid', $empid);
    $result_user->execute();
    $row_user = $result_user->fetch();
	
	$formfield['fflocation'] = $row_user['dblocationid'];
	
	
    if( isset($_POST['submit']))
    {

        $formfield['fftable'] = trim($_POST['table']);
        $formfield['ffticketnotes'] = trim($_POST['ticketnotes']);
		$formfield['fflocation'] = $_POST['location'];

        if(empty($formfield['fftable'])) {
            $table_err = "Please select table number.";
        }

        if(empty($table_err))
        {
            try
            {
                $sqlmax = "SELECT MAX(dbticketid) AS maxid FROM ticket";
                $resultmax = $db->prepare($sqlmax);
                $resultmax->execute();
                $rowmax = $resultmax->fetch();
                $maxid = $rowmax["maxid"];
                $maxid = $maxid + 1;

                $sqlinsert = "INSERT into ticket (dbticketid, dbempid, dbcustid, dbtickettypeid, dbtickettable,dbticketlocation, dbticketdate, dbticketnotes)
                        VALUES (:bvticketid, :bvemp, :bvcustid, :bvtypeid, :bvtable,:bvlocation, now(), :bvticketnotes)";
                $sqlinsert = $db->prepare($sqlinsert);
                $sqlinsert->bindValue(':bvticketid', $maxid);
                $sqlinsert->bindValue(':bvemp', $empid);
                $sqlinsert->bindValue(':bvcustid', 1000);
                $sqlinsert->bindValue(':bvtypeid', 1);
                $sqlinsert->bindValue(':bvtable', $formfield['fftable']);
				$sqlinsert->bindValue(':bvlocation', $formfield['fflocation']);
                $sqlinsert->bindValue(':bvticketnotes', $formfield['ffticketnotes']);
                $sqlinsert->execute();

                echo '<div class="container">';
                echo    '<div class="row">';
                echo        '<div class="card card-body bg-light mt-5">';
                echo            '<h2>Ticket Number ' . $maxid . '</h2>';
                echo            '<form method="post" action="insertticketdetails.php">';
                echo            '<input type="hidden" name="ticketid" value="' . $maxid . '">';
                echo            '<input type="submit" name="thesubmit" value="Enter Ticket Items">';
                echo            '</form>';
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
                    <div class="row align-content-center">
                        <div class="form-group col">
                            <label for="table">Table:</label>
                            <select name="table" id="table" class="form-control <?php echo (!empty($table_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Table</option>
                                <?php
                                while ($rowt = $result_tables->fetch()) {
                                    if ($rowt['dbtableid'] == $formfield['fftable']) {
                                        $checker = 'selected';
                                    } else {
                                        $checker = '';
                                    }
                                    echo '<option value="' . $rowt['dbtableid'] . '" ' . $checker . '>' . $rowt['dbtablename'] . '</option>';
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $table_err; ?></span>
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
                        <div class="form-group col">
                            <label for="ticketnotes">Ticket Notes:</label>
                            <input type="text" name="ticketnotes" id="ticketnotes" maxlength="255"
                                   class="form-control"
                                   value="<?php if (isset($formfield['ffticketnotes'])) {
                                       echo $formfield['ffticketnotes'];
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
