<?php
session_start();
$_SESSION['currentpage'] = "enterticket";

$pagetitle = "Enter Ticket";

require_once 'header.php';
require_once 'connect.php';


//NECESSARY VARIABLES
$table_err = "";
$type_err = "";
$cust_err = "";

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

// get the menu items
$menuitems = 'SELECT * FROM menu';
$result_menu = $db->prepare($menuitems);
$result_menu->execute();

// get the ticket types
$types = 'SELECT * FROM tickettype';
$result_type = $db->prepare($types);
$result_type->execute();


    if( isset($_POST['submit']))
    {
        $formfield['ffemp'] = trim($_POST['emp']);
        $formfield['ffcustid'] = $_POST['custid'];
        $formfield['fftable'] = trim($_POST['table']);
        $formfield['fftypeid'] = trim($_POST['tickettype']);
        $formfield['ffdate'] = ($_POST['ticketdate']);
        $formfield['ffticketnotes'] = trim($_POST['ticketnotes']);

        if(empty($formfield['fftypeid'])) {
            $type_err = "Ticket type cannot be empty.";
        } else {
            if($formfield['fftypeid'] == 1)
            {
                if(empty($formfield['fftable']))
                {
                    $table_err = "Table cannot be empty with ticket type of \"Dine-In\"";
                }
            } else if (empty($formfield['fftypeid']))
            {
                $type_err = "Please select an ticket type.";
            }
        }

        if(empty($formfield['ffcustid'])) { $cust_err = "Customer cannot be empty."; }

        if(empty($type_err) && empty($table_err) && empty($cust_err))
        {
            try
            {
                $sqlmax = "SELECT MAX(dbticketid) AS maxid FROM ticket";
                $resultmax = $db->prepare($sqlmax);
                $resultmax->execute();
                $rowmax = $resultmax->fetch();
                $maxid = $rowmax["maxid"];
                $maxid = $maxid + 1;

                $sqlinsert = "INSERT into ticket (dbticketid, dbempid, dbcustid, dbtickettypeid, dbtickettable, dbticketdate, dbticketnotes)
                        VALUES (:bvticketid, :bvemp, :bvcustid, :bvtypeid, :bvtable, now(), :bvticketnotes)";
                $sqlinsert = $db->prepare($sqlinsert);
                $sqlinsert->bindValue(':bvticketid', $maxid);
                $sqlinsert->bindValue(':bvemp', $_SESSION['loginid']);
                $sqlinsert->bindValue(':bvcustid', $formfield['ffcustid']);
                $sqlinsert->bindValue(':bvtypeid', $formfield['fftypeid']);
                $sqlinsert->bindValue(':bvtable', $formfield['fftable']);
                $sqlinsert->bindValue(':bvticketnotes', $formfield['ffticketnotes']);
                $sqlinsert->execute();

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
                    <p>All Fields Required</p>
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="table">Table:</label>
                            <input type="text" name="table" id="table"
                                   class="form-control <?php echo (!empty($table_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fftable'])) {
                                       echo $formfield['fftable'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $table_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
                            <label for="tickettype">Ticket Type:</label>
                            <select name="tickettype" id="tickettype"
                                    class="form-control <?php echo (!empty($type_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Type</option>
                                <?php
                                while ($rowt = $result_type->fetch()) {
                                    if ($rowt['dbtickettypeid'] == $formfield['fftypeid']) {
                                        $checker = 'selected';
                                    } else {
                                        $checker = '';
                                    }
                                    echo '<option value="' . $rowt['dbtickettypeid'] . '" ' . $checker . '>' . $rowt['dbtickettypename'] . '</option>';
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $type_err; ?></span>
                        </div>
                    </div>
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="custid">Customer:</label>
                            <select name="custid" id="custid"
                                    class="form-control <?php echo (!empty($type_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Customer</option>
                                <?php
                                while ($rowc = $result_cust->fetch()) {
                                    if ($rowc['dbcustid'] == $formfield['ffcustid']) {
                                        $checker = 'selected';
                                    } else {
                                        $checker = '';
                                    }
                                    echo '<option value="' . $rowc['dbcustid'] . '" ' . $checker . '>' . $rowc['dbcustid'] . ' &nbsp;|&nbsp; ' . $rowc['dbcustfname'] . ' &nbsp;|&nbsp; ' . $rowc['dbcustlname'] . '</option>';
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $table_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
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