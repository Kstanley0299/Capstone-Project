<?php
session_start();
$_SESSION['currentpage'] = "selecttickets";

$pagetitle = "View Orders";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$table_err = "";
$date_err = "";
$type_err = "";
$emp_err = "";



$formfield['ffemp'] = trim($_POST['dbemp']);
$formfield['fftable'] = trim($_POST['dbtable']);
$formfield['fftypeid'] = trim($_POST['dbtickettype']);
$formfield['ffdate'] = ($_POST['dbticketdate']);
$formfield['ffstatus'] = trim($_POST['dbstatus']);


try
{
    $tickets = "SELECT * FROM ticket ;";
    $result_tickets = $db->prepare($tickets);
    $result_tickets->execute();

} catch(PDOException $e)
{
    echo 'Error!' .$e->getMessage();
    exit();
}



$menu_arr = array();

$item_price = 0;

// get the employees
$employees = 'SELECT * FROM employees';
$result_emp = $db->prepare($employees);
$result_emp->execute();

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
    $formfield['fftable'] = trim($_POST['table']);
    $formfield['fftypeid'] = trim($_POST['tickettype']);
    $formfield['ffdate'] = ($_POST['ticketdate']);
    $formfield['ffstatus'] = trim($_POST['status']);


    try
    {
        $tickets = "SELECT * FROM ticket WHERE dbempid like CONCAT('%', :bvempid, '%')
                            AND dbtickettable like CONCAT('%', :bvtickettable, '%')
                            AND dbtickettypeid like CONCAT('%', :bvtypeid, '%')
                            AND dbticketdate like CONCAT('%', :bvticketdate, '%')
                            AND dbticketclosed like CONCAT('%', :bvticketclosed, '%');";
        $result_tickets = $db->prepare($tickets);
        $result_tickets->bindValue(':bvempid', $formfield['ffemp']);
        $result_tickets->bindValue(':bvtickettable', $formfield['fftable']);
        $result_tickets->bindValue(':bvtypeid', $formfield['fftypeid']);
        $result_tickets->bindValue(':bvticketdate', $formfield['ffdate']);
        $result_tickets->bindValue(':bvticketclosed', $formfield['ffstatus']);
        $result_tickets->execute();

    } catch(PDOException $e)
    {
        echo 'Error!' .$e->getMessage();
        exit();
    }
}

if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 3 || $_SESSION['loginpermit'] == 4) {
    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Search tickets</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="table">Table:</label>
                            <input type="text" name="table" id="table" class="form-control form-control-md" placeholder="Table"
                                   value="<?php if (isset($formfield['fftable'])) {
                                       echo $formfield['fftable'];
                                   } ?>"/>
                        </div>
                        <div class="form-group col w-50">
                            <label for="ticketdate">Date:</label>
                            <input type="date" name="ticketdate" id="ticketdate" class="form-control form-control-md"
                                   value="<?php if (isset($formfield['ffdate'])) {
                                       echo $formfield['ffdate'];
                                   }; ?>"/>
                        </div>
                    </div>
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="emp">Server:</label>
                            <select name="emp" id="emp"
                                    class="form-control form-control-md <?php echo (!empty($emp_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Server</option>
                                <?php
                                while ($rowe = $result_emp->fetch()) {
                                    if ($rowe['dbempid'] == $formfield['ffemp']) {
                                        $checker = 'selected';
                                    } else {
                                        $checker = '';
                                    }
                                    echo '<option value="' . $rowe['dbempid'] . '" ' . $checker . '>' . $rowe['dbempfname'] . ' ' . $rowe['dbemplname'] . '</option>';
                                }
                                ?>
                            </select>
                            <span class="invalid-feedback"><?php echo $emp_err; ?></span>
                        </div>
                        <div class="form-group col w-25">
                            <label for="tickettype">Ticket Type:</label>
                            <select name="tickettype" id="tickettype"
                                    class="form-control form-control-md <?php echo (!empty($type_err)) ? 'is-invalid' : ''; ?>">
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
                        <div class="form-group col w-25">
                            <label for="status">Ticket Status:</label>
                            <select name="status" id="status" class="form-control form-control-md">
                                <option value="">Select Status</option>
                                <?php
                                if (isset($formfield['ffstatus']) && $formfield['ffstatus'] != null) {
                                    if ($formfield['ffstatus'] == 0) {
                                        echo '<option value="0" selected>Open</option>';
                                        echo '<option value="1">Closed</option>';
                                    } else {
                                        echo '<option value="0">Open</option>';
                                        echo '<option value="1" selected>Closed</option>';
                                    }
                                } else {

                                    echo '<option value="0">Open</option>';
                                    echo '<option value="1">Closed</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row justify-content-center">
                        <div class="col text-center">
                            <input type="submit" value="Submit" name="submit" class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br><br>

    <div class="align-content-center container-fluid bg-white rounded-lg">
        <br>

        <table class="table table-responsive-sm table-striped w-auto table-hover bg-white display rounded-lg" id="tickets">
            <thead class="thead-light">
            <tr>
                <th>Ticket&nbsp;ID</th>
                <th>Table</th>
                <th>Server&nbsp;Last&nbsp;Name</th>
                <th>Date&nbsp;of&nbsp;Ticket</th>
                <th>Ticket&nbsp;Type</th>
                <th>Status</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $result_tickets->fetch()) {
                try {
                    $empid = $row['dbempid'];

                    $getemplname = "SELECT * FROM employees WHERE dbempid = '" . $empid . "'";
                    $result_lname = $db->prepare($getemplname);
                    $result_lname->execute();
                    $emplname = "";

                    while ($r = $result_lname->fetch()) {
                        $emplname = $r['dbemplname'];
                    }

                    $typeid = $row['dbtickettypeid'];

                    $sqlgettype = "SELECT * FROM tickettype WHERE dbtickettypeid = '" . $typeid . "'";
                    $result_type = $db->prepare($sqlgettype);
                    $result_type->execute();
                    $type = "";

                    while ($t = $result_type->fetch()) {
                        $type = $t['dbtickettypename'];
                    }

                    echo '<tr><th scope="row">' . $row['dbticketid'] . '</th>';
                    echo '<td>' . $row['dbtickettable'] . '</td>';
                    echo '<td>' . $emplname . '</td>';
                    echo '<td>' . $row['dbticketdate'] . '</td>';
                    echo '<td>' . $type . '</td>';


                    if ($row['dbticketclosed'] == 0) {
                        echo '<td>Open</td>';

                        echo '<td><form action="updateticket.php" method="post">';
                        echo '<input type="hidden" name="ticketid" value="' . $row['dbticketid'] . '">';
                        echo '<input type="submit" name="theedit" class="btn btn-sm btn-secondary" value="Edit">';
                        echo '</form></td>';
                        echo '<td><form action="insertticketdetails.php" method="post">';
                        echo '<input type="hidden" name="ticketid" value="' . $row['dbticketid'] . '">';
                        echo '<input type="submit" name="thesubmit" class="btn btn-sm btn-secondary" value="Edit Ticket Items">';
                        echo '</form></td>';
                    } else {
                        echo '<td>Closed</td>';

                        echo '<td><form action="updateticket.php" method="post">';
                        echo '<input type="hidden" name="ticketid" value="' . $row['dbticketid'] . '">';
                        echo '<input type="hidden" name="theedit" class="btn btn-sm btn-secondary" value="Edit" disabled >';
                        echo '</form></td>';
                        echo '<td><form action="insertticketdetails.php" method="post">';
                        echo '<input type="hidden" name="ticketid" value="' . $row['dbticketid'] . '">';
                        echo '<input type="hidden" name="thesubmit" class="btn btn-sm btn-secondary" value="Edit Ticket Items" disabled>';
                        echo '</form></td>';
                    }

                    echo '</tr>';

                } catch (PDOException $e) {
                    echo 'Error!  ' . $e->getMessage();
                    exit();
                }
            }
            ?>
            </tbody>
        </table>

        <script>
            $(document).ready( function () {
                $('#tickets').DataTable();
            } );
        </script>

    </div><br><br>
    <?php
}
include_once 'footer.php';
?>