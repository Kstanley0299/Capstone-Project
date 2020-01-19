<?php
session_start();
$_SESSION['currentpage'] = "backindex";

$pagetitle = "Welcome";

require_once 'header.php';
require_once 'connect.php';



if($_SESSION['loginpermit'] == 1) {
?>
<br><br>

    <div class="container text-white text-center">
        <h3 style="font-family: AvenirLTStd-Roman">Quick Access</h3>
        <br>
        <div class="row">
            <div class="col-lg-12">
                <p>
                    <a href="insertticket_dinein.php" class="btn btn-sq-lg btn-warning font-weight-bold text-white text-uppercase" style="font-family: AvenirLTStd-Roman">
                        Dine-In Order
                    </a>
                    <a href="insertticket_delivery.php" class="btn btn-sq-lg btn-success font-weight-bold text-white text-uppercase" style="font-family: AvenirLTStd-Roman">
                        Delivery Order
                    </a>
                    <a href="insertticket.php" class="btn btn-sq-lg btn-primary font-weight-bold text-white text-uppercase" style="font-family: AvenirLTStd-Roman">
                        Pick-Up Order
                    </a>
                    <a href="insertcustomers.php" class="btn btn-sq-lg btn-info font-weight-bold text-white text-uppercase" style="font-family: AvenirLTStd-Roman">
                        New Customer
                    </a>
                </p>
            </div>
        </div>
    </div>





<?php
}
require_once 'footer.php';
?>