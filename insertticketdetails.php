<?php
session_start();
$_SESSION['currentpage'] = "enterticketdetails";

$pagetitle = "Enter Ticket Details";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES
$item_err = "";
$ticketid_err = "";
$price_err = "";

$formfield['ffticketid'] = $_POST['ticketid'];
$formfield['ffticketdetailid'] = $_POST['ticketdetailid'];
$formfield['ffmenuid'] = $_POST['menuid'];
$formfield['ffticketitemprice'] = $_POST['ticketitemprice'];

// get the categories for items
$selectcats = "SELECT * FROM category";
$resultcats = $db->prepare($selectcats);
$resultcats->execute();

// enter ticket item
if(isset($_POST['OIEnter']))
{
    $sqlinsert = 'INSERT INTO ticketdetail (dbticketid, dbmenuid, dbticketpricecharged, dbticketitemopen)
                                  VALUES (:bvticketid, :bvmenuid, :bvticketpricecharged, 1)';
    $stmtinsert = $db->prepare($sqlinsert);
    $stmtinsert->bindValue(':bvticketid', $formfield['ffticketid']);
    $stmtinsert->bindValue(':bvmenuid', $formfield['ffmenuid']);
    $stmtinsert->bindValue(':bvticketpricecharged', $formfield['ffticketitemprice']);
    $stmtinsert->execute();
	
	$sqlupdatein = 'UPDATE inventory
					SET dbinvamount = dbinvamount - 1
						WHERE dbmenuid = :bvmenuid';
				$stmtupdatein = $db->prepare($sqlupdatein);
				$stmtupdatein->bindValue(':bvmenuid', $formfield['ffmenuid']);
				$stmtupdatein->execute();
}

// delete item
if(isset($_POST['DeleteItem']))
{
    $sqldelete = 'DELETE FROM ticketdetail WHERE dbticketdetailid = :bvticketdetailid';
    $stmtdelete = $db->prepare($sqldelete);
    $stmtdelete->bindValue(':bvticketdetailid', $formfield['ffticketdetailid']);
    $stmtdelete->execute();
	
	$sqlupdatein = 'UPDATE inventory
					SET dbinvamount = dbinvamount + 1
						WHERE dbmenuid = :bvmenuid';
				$stmtupdatein = $db->prepare($sqlupdatein);
				$stmtupdatein->bindValue(':bvmenuid', $formfield['ffmenuid']);
				$stmtupdatein->execute();
}

// update item
if(isset($_POST['UpdateItem']))
{
    $formfield['ffticketitemprice'] = $_POST['newprice'];
    $formfield['ffticketseat'] = trim($_POST['newseat']);
    $formfield['ffticketitemnotes'] = trim($_POST['newnote']);
    $formfield['ffticketdetailid'] = $_POST['ticketdetailid'];
    $sqlupdateoi = 'UPDATE ticketdetail
                        SET dbticketpricecharged = :bvticketpricecharged,
                            dbticketdetailseat = :bvticketseat,
                            dbticketnotes = :bvticketnotes
                        WHERE dbticketdetailid = :bvticketdetailid';
    $stmtupdateoi = $db->prepare($sqlupdateoi);
    $stmtupdateoi->bindValue(':bvticketpricecharged', $formfield['ffticketitemprice']);
    $stmtupdateoi->bindValue(':bvticketseat', $formfield['ffticketseat']);
    $stmtupdateoi->bindValue(':bvticketnotes', $formfield['ffticketitemnotes']);
    $stmtupdateoi->bindValue(':bvticketdetailid', $formfield['ffticketdetailid']);
    $stmtupdateoi->execute();
}

$sqlselecto = "SELECT ticketdetail.*, menu.dbmenuitemname, inventory.dbinvamount
                FROM ticketdetail, menu, inventory
                WHERE menu.dbmenuid = ticketdetail.dbmenuid
                AND ticketdetail.dbticketid = :bvticketid
				AND inventory.dbmenuid = menu.dbmenuid";
$resulto = $db->prepare($sqlselecto);
$resulto->bindValue(':bvticketid', $formfield['ffticketid']);
$resulto->execute();

if(($_SESSION['loginpermit'] == 1 && isset($formfield['ffticketid'])) || ($_SESSION['loginpermit'] == 3 && isset($formfield['ffticketid'])) || ($_SESSION['loginpermit'] == 4) && isset($formfield['ffticketid'])) {
    ?>
    <br>
<div class="bg-white p-2 rounded-lg" style="width:98%;margin-left:1%;margin-right:1%">
<fieldset><legend><h2>Enter Items for Ticket Number: <?php echo $formfield['ffticketid'] ; ?></h2></legend>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">

<!--                <div class="container-fluid">-->
<!--                    <div class="row">-->
<!--                        <div class="col">-->
<!--                            <div class="card mt-3 tab-card">-->
<!--                                <div class="card-header tab-card-header d-flex flex-row flex-wrap">-->
<!--                                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">-->
<!---->
<!--                                        --><?php
//                                        $selected = "true";
//                                        $showactive = "active show";
//
//                                        while($rowc = $resultcats->fetch())
//                                        {
//                                            echo '<li class="nav-item">
//                                                  <a class="nav-link font-weight-bold ' . $showactive . '" id="tab'.$rowc['dbcatid'].'"  data-toggle="tab"
//                                                  href="#items' . $rowc['dbcatid'] .'" role="tab"
//                                                  aria-controls="'.$rowc['dbcatid'].'" aria-selected="' . $selected . '">' . $rowc['dbcatname'] . '</a>
//                                                  </li>';
//                                            $selected = "false";
//                                            $showactive = "";
//                                        }
//                                        ?>
<!--                                    </ul>-->
<!--                                </div>-->
<!---->
<!--                                <div class="tab-content" id="myTabContent">-->
<!---->
<!--                                    --><?php
//                                    // get the categories for items
//                                    $selectcats = "SELECT * FROM category";
//                                    $resultcats = $db->prepare($selectcats);
//                                    $resultcats->execute();
//
//
//                                    while($rowc = $resultcats->fetch())
//                                    {
//
//                                        echo '<div class="tab-pane fade p-3 " id="items' . $rowc['dbcatid'] .'" role="tabpanel" aria-labelledby="#tab' . $rowc['dbcatid'].'">';
//                                        echo '<br><h4 class="card-title text-warning font-weight-bold text-uppercase">' . $rowc['dbcatname'] . '</h4>';
//
//                                        echo '<div class="d-flex flex-row flex-wrap">';
//
//                                        $sqlselectp = "SELECT * FROM menu WHERE dbcatid = :bvcatid";
//                                        $resultp = $db->prepare($sqlselectp);
//                                        $resultp->bindValue(':bvcatid', $rowc['dbcatid']);
//                                        $resultp->execute();
//
//                                        while($rowp = $resultp->fetch())
//                                        {
//                                            echo    '<div p-2><form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//                                            echo    '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//                                            echo    '<input type="hidden" name="menuid" value="' . $rowp['dbmenuid'] . '">';
//                                            echo    '<input type="hidden" name="ticketitemprice" value="' . $rowp['dbmenuitemprice'] . '">';
//                                            echo    '<input type="submit" name="OIEnter" class="w-100 btn-sm btn-secondary font-weight-bold" style="padding:15px" value="' . $rowp['dbmenuitemname'] . '">';
//                                            echo    '</form></div>';
//                                        }
//                                        echo '</div></div>';
//
//                                    }
//                                    ?>
<!---->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->



<!--                <div class="container-fluid">-->
<!--                    <div class="row">-->
<!--                        <div class="col">-->
<!--                            <div class="panel with-nav-tabs panel-warning">-->
<!--                                <div class="panel-heading">-->
<!--                                    <ul class="nav nav-tabs">-->
<!--                                        --><?php
//                                            while($rowc = $resultcats->fetch())
//                                            {
//
//                                                echo '<li class="nav-item"><a href="#tab' . $rowc['dbcatid'] . 'warning" class="btn-md btn-warning font-weight-bold m-2" data-toggle="tab">' . $rowc['dbcatname'] . '</a></li>';
//                                            }
//
//                                            // get the categories for items
//                                            $selectcats = "SELECT * FROM category";
//                                            $resultcats = $db->prepare($selectcats);
//                                            $resultcats->execute();
//
//                                            echo '</ul></div>';
//                                            echo '<div class="panel-body">';
//                                            echo '<div class="tab-content">';
//
//                                            while($rowc = $resultcats->fetch())
//                                            {
//
//                                                echo '<div class="tab-pane fade" id="tab' . $rowc['dbcatid'] . 'warning">';
//                                                echo '<div class="border border-dark rounded-lg">';
//                                                echo '<h4 class="text-warning font-weight-bold text-uppercase">' . $rowc['dbcatname'] . '</h4>';
//
//                                                echo '<div class="d-flex flex-row">';
//
//                                                $sqlselectp = "SELECT * FROM menu WHERE dbcatid = :bvcatid";
//                                                $resultp = $db->prepare($sqlselectp);
//                                                $resultp->bindValue(':bvcatid', $rowc['dbcatid']);
//                                                $resultp->execute();
//
//                                                while($rowp = $resultp->fetch())
//                                                {
//                                                    echo    '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//                                                    echo    '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//                                                    echo    '<input type="hidden" name="menuid" value="' . $rowp['dbmenuid'] . '">';
//                                                    echo    '<input type="hidden" name="ticketitemprice" value="' . $rowp['dbmenuitemprice'] . '">';
//                                                    echo    '<input type="submit" name="OIEnter" class="w-100 btn-sm btn-secondary font-weight-bold" style="padding:15px" value="' . $rowp['dbmenuitemname'] . '">';
//                                                    echo    '</form>';
//                                                }
//                                                echo '</div></div>';
//                                                echo '</div>';
//                                            }
//                                            echo '</div></div>';
//                                        ?>
<!--                                    </ul>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->










<!--                --><?php
                while($rowc = $resultcats->fetch())
                {

                    echo '<div class="border border-dark rounded-lg">';
                    echo '<h4 class="text-warning font-weight-bold text-uppercase">' . $rowc['dbcatname'] . '</h4>';

                    echo '<div class="d-flex flex-row flex-wrap">';

                    $sqlselectp = "SELECT * FROM menu, inventory WHERE dbcatid = :bvcatid AND menu.dbmenuid = inventory.dbmenuid";
                    $resultp = $db->prepare($sqlselectp);
                    $resultp->bindValue(':bvcatid', $rowc['dbcatid']);
                    $resultp->execute();

                    while($rowp = $resultp->fetch())
                    {
                        echo    '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                        echo    '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
                        echo    '<input type="hidden" name="menuid" value="' . $rowp['dbmenuid'] . '">';
                        echo    '<input type="hidden" name="ticketitemprice" value="' . $rowp['dbmenuitemprice'] . '">';
						
						
						
                        
						$invamount = $rowp['dbinvamount'];
						
						if($invamount == 0){
							echo    '<input type="submit" name="OIEnter" class="w-100 btn-sm btn-danger font-weight-bold" style="padding:15px; font-family: AvenirLTStd-Roman" value="' . $rowp['dbmenuitemname'] . '" disabled>';

						}else{
					        echo    '<input type="submit" name="OIEnter" class="w-100 btn-sm btn-secondary font-weight-bold" style="padding:15px; font-family: AvenirLTStd-Roman" value="' . $rowp['dbmenuitemname'] . '">';

                        }
						
						echo    '</form>';
                    }
                    echo '</div></div>';
                }
//                ?>



<!--            --><?php
//                while($rowc = $resultcats->fetch())
//                {
//                    echo '<div class="c col-lg-auto  d-lg-inline-block text-center">';
//                    echo '<a href="#'. $rowc['dbcatname'] . 'collapse" data-toggle="collapse" aria-expanded="false" aria-controls="'. $rowc['dbcatname'] . 'collapse" class="btn btn-sq-lg btn-warning font-weight-bold text-white text-uppercase" style="font-family: AvenirLTStd-Roman">
//                        ' . $rowc['dbcatname'] . '  </a>';
//                    //echo '<h4 class="text-warning">' . $rowc['dbcatname'] . '</h4><br>';
//
//                    echo '<div class="collapse" id="'. $rowc['dbcatname'] . 'collapse">';
//                    echo '<div class="card card-body">';
//                    echo '<div class="row">';
//
//                    $sqlselectp = "SELECT * FROM menu WHERE dbcatid = :bvcatid";
//                    $resultp = $db->prepare($sqlselectp);
//                    $resultp->bindValue(':bvcatid', $rowc['dbcatid']);
//                    $resultp->execute();
//
//                    while($rowp = $resultp->fetch())
//                    {
//                        echo    '<div class="col"><form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//                        echo    '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//                        echo    '<input type="hidden" name="menuid" value="' . $rowp['dbmenuid'] . '">';
//                        echo    '<input type="hidden" name="ticketitemprice" value="' . $rowp['dbmenuitemprice'] . '">';
//                        echo    '<h5><input type="submit" name="OIEnter" class="w-100 btn-sq  btn-secondary font-weight-bold text-uppercase" style="font-family: AvenirLTStd-Roman" value="' . $rowp['dbmenuitemname'] . ' - $' . $rowp['dbmenuitemprice'] . '"></h5>';
//                        echo    '</form></div>';
//                    }
//                    echo '</div></div></div></div>';
//                }
//            ?>
            </div>

            <div class="col">

                <table class="table table-responsive-sm w-auto">
                    <tr>
                        <td>
                            <table class="table table-hover text">
                                <thead class="thead-light">
                                <tr>
                                    <th>Item&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>Seat&nbsp;&nbsp;</th>
                                    <th>Notes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $tickettotal = 0;
                                while($rowo = $resulto->fetch())
                                {
                                    $tickettotal = $tickettotal + $rowo['dbticketpricecharged'];

                                    echo '<tr><td>' . $rowo['dbmenuitemname'] . '</td><td>' . $rowo['dbticketpricecharged'] . '</td>';
                                    echo '<td>' . $rowo['dbticketdetailseat'] . '</td><td>' . $rowo['dbticketnotes'] . '</td><td>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                                    echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
                                    echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
                                    echo '<input type="submit" name="NoteEntry" value="Update" class="w-100 btn-sm btn-secondary">';
                                    echo '</form></td><td>';
                                    echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                                    echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
                                    echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
									echo '<input type="hidden" name="menuid" value="' . $rowo['dbmenuid'] . '">';
                                    echo '<input type="submit" name="DeleteItem" value="Delete" class="w-100 btn-sm btn-secondary">';
                                    echo '</form></td></tr>';
                                }
                                ?>
                                <tr>
                                    <th>Total:</th>
                                    <th><?php
                                        setlocale(LC_MONETARY, "en_US");
                                        echo money_format('%(#10n',$tickettotal);
                                        ?>
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                            <?php
                            if(isset($_POST['NoteEntry']))
                            {
                                $sqlselectoi = "SELECT ticketdetail.*, menu.dbmenuitemname
                                    FROM ticketdetail, menu
                                    WHERE menu.dbmenuid = ticketdetail.dbmenuid
                                    AND ticketdetail.dbticketdetailid = :bvticketdetailid";
                                $resultoi = $db->prepare($sqlselectoi);
                                $resultoi->bindValue(':bvticketdetailid', $_POST['ticketdetailid']);
                                $resultoi->execute();
                                $rowoi = $resultoi->fetch();

                                echo '</td><td class="w-50">';
                                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
                                echo '<table class="text-center" style="margin-left:20%">';
                                echo '<tr><td>Price: <input class="form-control form-control-md" type="number" step="0.01" name="newprice" value="' . $rowoi['dbticketpricecharged'] . '"></td></tr>';
                                echo '<tr><td>Seat: <input class="form-control form-control-md" type="number" step="1" name="newseat" value="' . $rowoi['dbticketdetailseat'] . '"></td></tr>';
                                echo '<tr><td>Note: <input class="form-control form-control-md" type="text" maxlength="255" name="newnote" value="' . $rowoi['dbticketnotes'] . '"></td></tr>';
                                echo '<tr><td>';
                                echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
                                echo '<input type="hidden" name="ticketdetailid" value="' . $rowoi['dbticketdetailid'] . '">';
                                echo '<input type="submit" name="UpdateItem" value="Update Item" class="btn btn-primary"></td></tr></table>';
                            }
                            ?>
                        </td></tr>
                </table>

                <br><br>
                <div class="text-center container-fluid">
                    <?php
                    echo '<form action="completeticket.php" method="post">';
                    echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
                    echo '<input type="submit" name="CompleteCart" value="Complete ticket" class="btn btn-warning text-dark">';
                    echo '</form>';
                    ?>
                </div>


            </div>


        </div>
    </div>

</fieldset>
<br><br>
<!--    <table class="table table-responsive-md w-auto">-->
<!--        <tr>-->
<!--        <td>-->
<!--        <table class="table table-hover text">-->
<!--            <thead class="thead-light">-->
<!--            <tr>-->
<!--                <th>Item</th>-->
<!--                <th>Price</th>-->
<!--                <th>Seat</th>-->
<!--                <th>Notes</th>-->
<!--                <th></th>-->
<!--                <th></th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--                <tbody>-->
<!--                --><?php
//                    $tickettotal = 0;
//                    while($rowo = $resulto->fetch())
//                    {
//                        $tickettotal = $tickettotal + $rowo['dbticketpricecharged'];
//
//                        echo '<tr><td>' . $rowo['dbmenuitemname'] . '</td><td>' . $rowo['dbticketpricecharged'] . '</td>';
//                        echo '<td>' . $rowo['dbticketdetailseat'] . '</td><td>' . $rowo['dbticketnotes'] . '</td><td>';
//                        echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//                        echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//                        echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
//                        echo '<input type="submit" name="NoteEntry" value="Update" class="w-100 btn btn-secondary">';
//                        echo '</form></td><td>';
//                        echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//                        echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//                        echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
//                        echo '<input type="submit" name="DeleteItem" value="Delete" class="w-100 btn btn-secondary">';
//                        echo '</form></td></tr>';
//                    }
//                ?>
<!--            <tr>-->
<!--                <th>Total:</th>-->
<!--                <th>--><?php
//                    setlocale(LC_MONETARY, "en_US");
//                    echo money_format('%(#10n',$tickettotal);
//                    ?>
<!--                </th>-->
<!--            </tr>-->
<!--                </tbody>-->
<!--        </table>-->
<!--        --><?php
//            if(isset($_POST['NoteEntry']))
//            {
//                $sqlselectoi = "SELECT ticketdetail.*, menu.dbmenuitemname
//                                    FROM ticketdetail, menu
//                                    WHERE menu.dbmenuid = ticketdetail.dbmenuid
//                                    AND ticketdetail.dbticketdetailid = :bvticketdetailid";
//                $resultoi = $db->prepare($sqlselectoi);
//                $resultoi->bindValue(':bvticketdetailid', $_POST['ticketdetailid']);
//                $resultoi->execute();
//                $rowoi = $resultoi->fetch();
//
//                echo '</td><td class="w-50">';
//                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//                echo '<table class="text-center" style="margin-left:20%">';
//                echo '<tr><td>Price: <input class="form-control form-control-md" type="number" step="0.01" name="newprice" value="' . $rowoi['dbticketpricecharged'] . '"></td></tr>';
//                echo '<tr><td>Seat: <input class="form-control form-control-md" type="number" step="1" name="newseat" value="' . $rowoi['dbticketdetailseat'] . '"></td></tr>';
//                echo '<tr><td>Note: <input class="form-control form-control-md" type="text" maxlength="255" name="newnote" value="' . $rowoi['dbticketnotes'] . '"></td></tr>';
//                echo '<tr><td>';
//                echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//                echo '<input type="hidden" name="ticketdetailid" value="' . $rowoi['dbticketdetailid'] . '">';
//                echo '<input type="submit" name="UpdateItem" value="Update Item" class="btn btn-primary"></td></tr></table>';
//            }
//        ?>
<!--        </td></tr>-->
<!--    </table>-->
<!---->
<!--    <br><br>-->
<!--    <div class="text-center container-fluid">-->
<!--        --><?php
//        echo '<form action="completeticket.php" method="post">';
//        echo '<input type="hidden" name="ticketid" value="' . $formfield['ffticketid'] . '">';
//        echo '<input type="submit" name="CompleteCart" value="Complete ticket" class="btn btn-warning text-dark">';
//        echo '</form>';
//        ?>
<!--    </div>-->

</div>

    <?php
}
include_once 'footer.php';
?>