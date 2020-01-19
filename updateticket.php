<?php
session_start();
$_SESSION['currentpage'] = "updateticket";

$pagetitle = "Update Ticket";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$table_err = "";
$date_err = "";
$type_err = "";

// get the ticket types
$types = 'SELECT * FROM tickettype';
$result_type = $db->prepare($types);
$result_type->execute();

if( isset($_POST['theedit'])) {
    $showform = 1;
    $formfield['ffticketid'] = $_POST['ticketid'];
    $sqlselect = 'SELECT * from ticket where dbticketid = :bvticketid';
    $result = $db->prepare($sqlselect);
    $result->bindValue(':bvticketid', $formfield['ffticketid']);
    $result->execute();
    $row = $result->fetch();
}

if(isset($_POST['submit']))
{
    $showform = 2;
    $formfield['ffticketid'] = $_POST['ticketid'];
    $formfield['fftickettable'] = $_POST['tickettable'];
    $formfield['fftickettype'] = $_POST['tickettype'];
    $formfield['ffticketdate'] = $_POST['ticketdate'];
    $formfield['ffticketnotes'] = $_POST['ticketnotes'];
    $formfield['ffticketstatus'] = $_POST['ticketstatus'];

    if ($_POST['ticketdate'] != '') {
        $formfield['ffticketdate'] = date_create(trim($_POST['ticketdate']));
        $formfield['ffticketdate'] = date_format($formfield['ffticketdate'], 'Y-m-d');
    }
    if(empty($formfield['fftickettype'])) {
        $type_err = "Ticket type cannot be empty.";
    } else {
        if($formfield['fftickettype'] == 1)
        {
            if(empty($formfield['fftickettable']))
            {
                $table_err = "Table cannot be empty with ticket type of \"Dine-In\"";
            }
        } else if (empty($formfield['fftickettype']))
        {
            $type_err = "Please select a ticket type.";
        }
    }
    if(empty($formfield['ffticketdate'])){$date_err = "<p>Ticket date is empty.</p>";}

    if(empty($type_err) && empty($table_err) && empty($date_err))
    {
        try
        {
            $sqlinsert = "UPDATE ticket SET dbtickettypeid = :bvtypeid, dbticketdate = :bvticketdate, dbtickettable = :bvtickettable,
                                            dbticketnotes = :bvticketnotes, dbticketclosed = :bvstatus
                                           WHERE dbticketid = :bvticketid";
            $sqlinsert = $db->prepare($sqlinsert);
            $sqlinsert->bindValue(':bvticketid', $formfield['ffticketid']);
            $sqlinsert->bindValue(':bvtypeid', $formfield['fftickettype']);
            $sqlinsert->bindValue(':bvticketdate', $formfield['ffticketdate']);
            $sqlinsert->bindValue(':bvtickettable', $formfield['fftickettable']);
            $sqlinsert->bindValue(':bvticketnotes', $formfield['ffticketnotes']);
            $sqlinsert->bindValue(':bvstatus', $formfield['ffticketstatus']);
            $sqlinsert->execute();

            echo '<br><div class="alert alert-success text-center" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Ticket updated successfully. 
                    <a href="selecttickets.php">Return</a></div><br>';

        } catch(PDOException $e)
        {
            echo 'Error!' .$e->getMessage();
            exit();
        }
    }
}//if isset submit
if($_SESSION['loginpermit'] == 1 || $_SESSION['loginpermit'] == 3 || $_SESSION['loginpermit'] == 4 && $showform == 1) {
    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Ticket Information</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="tickettable">Table:</label>
                            <input type="text" name="tickettable" id="tickettable"
                                   class="form-control <?php echo (!empty($table_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($row['dbtickettable'])) {
                                       echo $row['dbtickettable'];
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
                                    if ($rowt['dbtickettypeid'] == $row['dbtickettypeid']) {
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
                            <label for="ticketdate">Ticket Date:</label>
                            <input type="date" name="ticketdate" id="ticketdate"
                                   class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($row['dbtickettime'])) {
                                       $dateholder = date_create($row['dbtickettime']);
                                       $dateholder = date_format($dateholder, 'Y-m-d');
                                       echo $dateholder;
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $date_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
                            <label for="ticketstatus">Ticket Status:</label>
                            <select name="ticketstatus" id="ticketstatus"
                                    class="form-control">
                                <?php
                                if($row['dbticketclosed'] == 0) { echo '<option value="0" selected>Open</option><option value="1">Closed</option>'; }
                                if($row['dbticketclosed'] == 1) { echo '<option value="0">Open</option><option value="1" selected>Closed</option>'; }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ticketnotes">Ticket Notes:</label>
                        <input type="text" name="ticketnotes" id="ticketnotes" maxlength="255"
                               class="form-control"
                               value="<?php if (isset($row['dbticketnotes'])) {
                                   echo $row['dbticketnotes'];
                               } ?>"/>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="text-center">
                            <input type="hidden" name="ticketid" value=<?php echo $formfield['ffticketid'] ?>>
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
else if($showform == 2) {
    ?>

    <div class="container">
        <div class="row">
            <div class="card card-body bg-light mt-5">
                <h2>Ticket Information</h2>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
                    <div class="row align-content-center">
                        <div class="form-group col w-50">
                            <label for="tickettable">Table:</label>
                            <input type="text" name="tickettable" id="tickettable"
                                   class="form-control form-control-lg <?php echo (!empty($table_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($formfield['fftable'])) {
                                       echo $formfield['fftable'];
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $table_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
                            <label for="tickettype">Ticket Type:</label>
                            <select name="tickettype" id="tickettype"
                                    class="form-control form-control-lg <?php echo (!empty($type_err)) ? 'is-invalid' : ''; ?>">
                                <option value="">Select Type</option>
                                <?php
                                while ($rowt = $result_type->fetch()) {
                                    if ($rowt['dbtickettypeid'] == $formfield['fftickettype']) {
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
                            <label for="ticketdate">Ticket Date:</label>
                            <input type="date" name="ticketdate" id="ticketdate"
                                   class="form-control form-control-lg <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>"
                                   value="<?php if (isset($row['dbtickettime'])) {
                                       $dateholder = date_create($row['dbtickettime']);
                                       $dateholder = date_format($dateholder, 'Y-m-d');
                                       echo $dateholder;
                                   } ?>"/>
                            <span class="invalid-feedback"><?php echo $date_err; ?></span>
                        </div>
                        <div class="form-group col w-50">
                            <label for="ticketstatus">Ticket Status:</label>
                            <select name="ticketstatus" id="ticketstatus"
                                    class="form-control form-control-lg">
                                <?php
                                if($row['dbticketclosed'] == 0) { echo '<option value="0" selected>Open</option><option value="1">Closed</option>'; }
                                if($row['dbticketclosed'] == 1) { echo '<option value="0">Open</option><option value="1" selected>Closed</option>'; }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ticketnotes">Ticket Notes:</label>
                        <input type="text" name="ticketnotes" id="ticketnotes" maxlength="255"
                               class="form-control form-control-lg"
                               value="<?php if (isset($formfield['ffticketnotes'])) {
                                   echo $formfield['ffticketnotes'];
                               } ?>"/>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="text-center">
                            <input type="hidden" name="ticketid" value=<?php echo $formfield['ffticketid'] ?>>
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