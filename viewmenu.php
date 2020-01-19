<?php
session_start();
$_SESSION['currentpage'] = "menu";

$pagetitle = "Menu";

require_once 'header.php';
require_once 'connect.php';

//NECESSARY VARIABLES

$categories = 'SELECT * FROM category';
$resultc = $db->prepare($categories);
$resultc->execute();

$menuitems = 'SELECT * FROM menu';
$resultm = $db->prepare($menuitems);
$resultm->execute();


?>

    <br><br>

    <div class="container">
        <?php
        while ($rowc = $resultc->fetch()) {
            echo '<h1 style="font-family: AvenirLTStd-Roman" class="font-italic text-warning">' . $rowc['dbcatname'] . '</h1>';
            while ($rowm = $resultm->fetch()) {
                if ($rowc['dbcatid'] == $rowm['dbcatid']) {
                    echo '<div style="font-family: AvenirLTStd-Roman" class="row text-white border border-white border-left-0 border-right-0 border-bottom-0">';
                    echo '<div class="col-4">'.$rowm['dbmenuitemname'].'</div>';
                    echo '<div class="col-6">'.$rowm['dbmenuitemdescr'].'</div>';
                    echo '<div class="col-2 text-right">'.$rowm['dbmenuitemprice'].'</div></div>';
//                    echo '<tr><td style="width:25%">' . $rowm['dbmenuitemname'] . '</td><td style="width:65%">' . $rowm['dbmenuitemdescr'] . '</td><td style="width:10%">$' . $rowm['dbmenuitemprice'] . '</td></tr>';
                }
            }
            $resultm = $db->prepare($menuitems);
            $resultm->execute();
            echo '<br>';
        }
        ?>
    </div>
<?php

include_once 'footer.php';
?>