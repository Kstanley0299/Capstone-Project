<?php
if(isset($_SESSION['loginid']))
{
    $visible = 1;
?>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-center">
            <a class="navbar-brand" href="index.php"><image class="img-fluid" src="images/white_logo.png" width="100" height="100" alt="Thumbnail image of EZ Cheezy logo"></image></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($_SESSION['currentpage'])) {
                            if($_SESSION['currentpage'] == "index"){echo "active";}} ?>" href="index.php">Home</a>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if(isset($_SESSION['currentpage'])) {
                            if( $_SESSION['currentpage'] == "menu" ||
                                $_SESSION['currentpage'] == "editmenu" ||
                                $_SESSION['currentpage'] == "entermenuitems" ||
                                $_SESSION['currentpage'] == "enternewcat" ||
                                $_SESSION['currentpage'] == "updatecategory" ||
                                $_SESSION['currentpage'] == "enterinventory"){echo "active";}} ?>" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Manage Menu</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="viewmenu.php">View Menu</a>
                            <a class="dropdown-item" href="updatemenu.php">Edit Menu</a>
                            <a class="dropdown-item" href="insertmenuitems.php">New Menu Item</a>
                            <a class="dropdown-item" href="insertcategory.php">Enter/Edit Categories</a>
                            <a class="dropdown-item" href="updateinventory.php">Edit Inventory</a>
                        </div>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if(isset($_SESSION['currentpage'])) {
                            if( $_SESSION['currentpage'] == "viewusers" ||
                                $_SESSION['currentpage'] == "enternewusers" ||
                                $_SESSION['currentpage'] == "insertPermissions" ||
                                $_SESSION['currentpage'] == "viewpermissions" ||
                                $_SESSION['currentpage'] == "updatepermission" ||
                                $_SESSION['currentpage'] == "insertpositions"){echo "active";}} ?>" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Manage Employees</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="selectemployee.php">View Employees</a>
                            <a class="dropdown-item" href="insertemployee.php">New Employee</a>
                            <a class="dropdown-item" href="viewpermissions.php">View Permissions</a>
                            <a class="dropdown-item" href="insertPermissions.php">New Permission</a>
                            <a class="dropdown-item" href="updatepermission.php">Edit Permissions</a>
                            <a class="dropdown-item" href="insertpositions.php">New Position</a>
                        </div>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if(isset($_SESSION['currentpage'])) {
                            if($_SESSION['currentpage'] == "selectcustomer" ||
                                $_SESSION['currentpage'] == "updatecustomer" ||
                                $_SESSION['currentpage'] == "insertcustomers"){echo "active";}} ?>" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Manage Customers</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="selectcustomer.php">View Customers</a>
                            <a class="dropdown-item" href="insertcustomers.php">New Customer</a>
                        </div>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if(isset($_SESSION['currentpage'])) {
                            if($_SESSION['currentpage'] == "selecttickets" ||
                                $_SESSION['currentpage'] == "makescreen" ||
                                $_SESSION['currentpage'] == "enterticket"){echo "active";}} ?>" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Manage Orders</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="selecttickets.php">View Tickets</a>
                            <a class="dropdown-item" href="insertticket_dinein.php">New Dine-In Ticket</a>
                            <a class="dropdown-item" href="insertticket_delivery.php">New Delivery Ticket</a>
                            <a class="dropdown-item" href="makescreen.php">Make Screen</a>
                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php if(isset($_SESSION['currentpage'])) {
                            if($_SESSION['currentpage'] == "schedule"){echo "active";}} ?>" data-toggle="dropdown"
                           href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            Manage Restaurant</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="schedule.php">Update Restaurant Hours</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($_SESSION['currentpage'])) {
                            if($_SESSION['currentpage'] == "logout"){echo "active";}} ?>" href="logout.php">Log Out</a>
                    </li>
                    <li class="nav-item text-white">
                        <?php echo ' [ Welcome, ' . $_SESSION['loginname'] . ' ] '; ?>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
<?php
}
else
{
    $visible = 0;
?>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="index.php"><image class="img-fluid" src="images/white_logo.png" width="150" height="150" alt="Thumbnail image of EZ Cheezy logo"></image></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($_SESSION['currentpage'])) {
                            if($_SESSION['currentpage'] == "login"){echo "active";}} ?>" href="login.php">Log In</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
<?php
}
?>