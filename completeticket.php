<?php
require_once "header.php";
require_once "connect.php";

// Get the posted ticketid
$formfield['ffticketid'] = $_POST['ticketid'];

// update the above ticketid record and set status to closed
$sql_update = "UPDATE ticket SET dbticketclosed = :bvticketclosed WHERE dbticketid = :bvticketid";
$update = $db->prepare($sql_update);
$update->bindValue(':bvticketclosed', 0);
$update->bindValue(':bvticketid', $formfield['ffticketid']);
$update->execute();

// select the ticket details/menu items to be displayed to the screen
$sqlselecto = "SELECT ticketdetail.*, menu.dbmenuitemname
			FROM ticketdetail, menu
			WHERE menu.dbmenuid = ticketdetail.dbmenuid
			AND ticketdetail.dbticketid = :bvticketid";
$resulto = $db->prepare($sqlselecto);
$resulto->bindValue(':bvticketid', $formfield['ffticketid']);
$resulto->execute();

?>
    <div class="text-center container w-50">
        <br>
    <h2 class="text-white">Ticket #<?php echo $formfield['ffticketid']; ?> has been submitted.</h2>
        <br>
    <table class="table table-hover bg-white">
        <thead class="thead-light">
        <tr>
            <th>Item</th>
            <th>Price</th>
            <th>Seat</th>
            <th>Notes</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $tickettotal = 0;
        while($rowo = $resulto->fetch())
        {
            $tickettotal = $tickettotal + $rowo['dbticketpricecharged'];

            echo '<tr><td>' . $rowo['dbmenuitemname'] . '</td><td>' . $rowo['dbticketpricecharged'] . '</td>';
            echo '<td>' . $rowo['dbticketseat'] . '</td><td>' . $rowo['dbticketnotes'] . '</td>';
            echo '</tr>';
        }
        ?>
        <tr class="bg-light"><th></th><th></th><th></th><th></th></tr>
        <tr>
            <th>Subtotal:</th>
            <th><?php
                setlocale(LC_MONETARY, "en_US");
                echo money_format('%(#10n',$tickettotal);
                ?>
            </th>
        </tr>
        <tr>
            <th>Tax:</th>
            <th><?php
                setlocale(LC_MONETARY, "en_US");
                echo money_format('%(#10n',($tickettotal * 0.08));
                ?>
            </th>
        </tr>
        <tr>
            <th>Total:</th>
            <th><?php
                setlocale(LC_MONETARY, "en_US");
                echo money_format('%(#10n',($tickettotal * 1.08));
                ?>
            </th>
        </tr>
        </tbody>
    </table>
</div>
<?php
include_once 'footer.php';
?>