<?php
session_start();
$_SESSION['currentpage'] = "View Current Items To Be Made";

$pagetitle = "Items To Be Made";

require_once 'header.php';
require_once 'connect.php';

$formfield['ffticketid'] = $_POST['ticketid'];

$tickets = "SELECT * FROM ticket ;";
$result_tickets = $db->prepare($tickets);
$result_tickets->execute();


if(isset($_POST['CloseTicket']))
{
    $sqlupdateo = 'UPDATE ticketdetail
								SET dbticketitemopen = 0
								WHERE dbticketid = :bvticketid';
    $stmtupdateo = $db->prepare($sqlupdateo);
    $stmtupdateo->bindValue(':bvticketid', $formfield['ffticketid']);
    $stmtupdateo->execute();

    $sqlupdateoo = 'UPDATE ticket
								SET dbticketclosed = 1
								WHERE dbticketid = :bvticketid';
    $stmtupdateoo = $db->prepare($sqlupdateoo);
    $stmtupdateoo->bindValue(':bvticketid', $formfield['ffticketid']);
    $stmtupdateoo->execute();
}

if(isset($_POST['CloseItem']))
            {
				$formfield['ffticketdetailid'] = $_POST['ticketdetailid'];
				$formfield['ffmenuid'] = $_POST['menuid'];
				
				
				$sqlupdateoo = 'UPDATE ticketdetail
					SET dbticketitemopen = 0
						WHERE dbticketdetailid = :bvticketdetailid';
				$stmtupdateoo = $db->prepare($sqlupdateoo);
				$stmtupdateoo->bindValue(':bvticketdetailid', $formfield['ffticketdetailid']);
				$stmtupdateoo->execute();
						
				$sqlselector = "SELECT *
                FROM ticketdetail
                WHERE dbticketid = :bvticketid
				AND dbticketitemopen = 1";
				$resultor = $db->prepare($sqlselector);
				$resultor->bindValue(':bvticketid', $formfield['ffticketid']);
				$resultor->execute();
				
				$row_cnt = $resultor->num_rows;
				
				if($row_cnt < 1){
				$sqlupdateoo = 'UPDATE ticket
								SET dbticketclosed = 1
								WHERE dbticketid = :bvticketid';
				$stmtupdateoo = $db->prepare($sqlupdateoo);
				$stmtupdateoo->bindValue(':bvticketid', $formfield['ffticketid']);
				$stmtupdateoo->execute();	
				}
				
				$sqlupdatein = 'UPDATE inventory
					SET dbinvamount = dbinvamount - 1
						WHERE dbmenuid = :bvmenuid';
				$stmtupdatein = $db->prepare($sqlupdatein);
				$stmtupdatein->bindValue(':bvmenuid', $formfield['ffmenuid']);
				$stmtupdatein->execute();
            }
			
//			$sqlselecto = "SELECT ticketdetail.*, menu.dbmenuitemname
//                FROM ticketdetail, menu
//                WHERE menu.dbmenuid = ticketdetail.dbmenuid
//				AND ticketdetail.dbticketitemopen = 1";
//			$resulto = $db->prepare($sqlselecto);
//			$resulto->execute();



if(($_SESSION['loginpermit'] == 1) || ($_SESSION['loginpermit'] == 3) || ($_SESSION['loginpermit'] == 4)) {
    ?>
    <br>
<div class="container-fluid bg-white rounded-lg">
<br>
    <div class="d-flex flex-row flex-wrap align-self-auto">

    <?php while($rowt = $result_tickets->fetch())
    {
        if($rowt['dbticketclosed'] == 0) {

            $ticketid = $rowt['dbticketid'];

            echo '<div class="align-self-auto p-2"><table class="table table-bordered table-responsive-md">
             <thead class="thead-dark">
             <tr>
             <th>Item</th>
             <th>Notes</th>
             <th>Close</th>
             </tr>
             </thead>
             <tbody>
             <tr><td colspan="2"><h5 class="text-warning font-weight-bold">TICKET&nbsp;#' . $rowt['dbticketid'] . '</h5></td>
             <td><form action="' . $_SERVER['PHP_SELF'] . '" method="post">
								<input type="hidden" name="ticketid" value="' . $rowt['dbticketid'] . '">
								<input type="submit" name="CloseTicket" value="Close Ticket" class="w-100 btn btn-secondary">
								</form></td></tr>';

            $ticketdetailss = "SELECT td.*, m.dbmenuitemname FROM ticketdetail td, menu m WHERE dbticketid = :bvticketid AND td.dbmenuid = m.dbmenuid ;";
            $result_details = $db->prepare($ticketdetailss);
            $result_details->bindValue(':bvticketid', $ticketid);
            $result_details->execute();

            while ($rowtd = $result_details->fetch()) {

                if($rowtd['dbticketitemopen'] == 1) {
                    echo '   
                <tr>
                <td> ' . $rowtd['dbmenuitemname'] . '</td>
                <td> ' . $rowtd['dbticketnotes'] . '</td>
                <td><form action="' . $_SERVER['PHP_SELF'] . '" method="post">
								<input type="hidden" name="ticketdetailid" value="' . $rowtd['dbticketdetailid'] . '">
								<input type="hidden" name="menuid" value="' . $rowtd['dbmenuid'] . '">
								<input type="submit" name="CloseItem" value="Close&nbsp;Item" class="w-100 btn btn-secondary">
								</form></td></tr>';
                }
            }
            echo '  </tbody></table></div>';
        }
            

    }?>




    </div>




<!--    <table class="table-responsive-md" align="center">-->
<!--        <tr>-->
<!--        <td>-->
<!--        <table class="table table-hover">-->
<!--            <thead class="thead-light">-->
<!--            <tr>-->
<!--                <th>Item</th>-->
<!--                <th>Notes</th>-->
<!--				<th>Seat</th>-->
<!--                <th>Close Order</th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--                <tbody>-->
<!--                --><?php
//
//					$first = 1;
//					$ticketid;
//
//
//						while($rowo = $resulto->fetch())
//						{
//
//							if($first == 1){
//
//								echo '<tr><td><u>' . $rowo['dbticketid'] . '</u></td></tr>';
//								echo '<tr><td>' . $rowo['dbmenuitemname'] . '</td><td>' . $rowo['dbticketnotes'] . '</td>';
//								echo '<td>' . $rowo['dbticketdetailseat'] . '</td><td>';
//								echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//								echo '<input type="hidden" name="menuid" value="' . $rowo['dbmenuid'] . '">';
//								echo '<input type="hidden" name="ticketid" value="' . $rowo['dbticketid'] . '">';
//								echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
//								echo '<input type="submit" name="CloseItem" value="Close" class="w-100 btn btn-secondary">';
//								echo '</form></td></tr>';
//								$first = $first + 1;
//								$ticketid = $rowo['dbticketid'];
//							}else{
//								if($ticketid == $rowo['dbticketid']){
//								echo '<tr><td>' . $rowo['dbmenuitemname'] . '</td><td>' . $rowo['dbticketnotes'] . '</td>';
//								echo '<td>' . $rowo['dbticketdetailseat'] . '</td><td>';
//								echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//								echo '<input type="hidden" name="ticketid" value="' . $rowo['dbticketid'] . '">';
//								echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
//								echo '<input type="submit" name="CloseItem" value="Close" class="w-100 btn btn-secondary">';
//								echo '</form></td></tr>';
//								}else{
//								echo '<tr><td><u>' . $rowo['dbticketid'] . '</u></td></tr>';
//								echo '<tr><td>' . $rowo['dbmenuitemname'] . '</td><td>' . $rowo['dbticketnotes'] . '</td>';
//								echo '<td>' . $rowo['dbticketdetailseat'] . '</td><td>';
//								echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">';
//								echo '<input type="hidden" name="ticketid" value="' . $rowo['dbticketid'] . '">';
//								echo '<input type="hidden" name="ticketdetailid" value="' . $rowo['dbticketdetailid'] . '">';
//								echo '<input type="submit" name="CloseItem" value="Close" class="w-100 btn btn-secondary">';
//								echo '</form></td></tr>';
//								$ticketid = $rowo['dbticketid'];
//								}
//
//							}
//						}
//
//
//
//                ?>
<!--                </tbody>-->
<!--        </table>-->
<!--        --><?php
//
//        ?>
<!--        </td></tr>-->
<!--    </table>-->





</div>

    <?php
}
include_once 'footer.php';
?>