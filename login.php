<?php
session_start();
$_SESSION['currentpage'] = "login";

$email_err = "";
$password_err = "";
$pagetitle = "Login Confirmation";
require_once 'header.php';
require_once 'connect.php';

if(isset($_SESSION['loginid']))
{
    echo '<br><div class="alert alert-warning" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">';
    echo '<strong>Error!</strong> You are already logged in.';
    echo '</div><br>';

    include_once 'footer.php';
    exit();
}

$showform = 1;

if(isset($_POST['submit']))
{
    $formfield['ffemail'] = strtolower(trim($_POST['email']));
    $formfield['ffpassword'] = trim($_POST['password']);

    if(empty($formfield['ffemail'])) { $email_err = "Email is missing";}
    if(empty($formfield['ffpassword'])) { $password_err = "Password is missing";}

    if($email_err == "" && $password_err == "")
    {
        try
        {
            $sql = 'SELECT * FROM employees WHERE dbempemail = :bvemail';
            $s = $db->prepare($sql);
            $s->bindValue(':bvemail', $formfield['ffemail']);
            $s->execute();
            $count = $s->rowCount();
        }
        catch(PDOException $e)
        {
            echo "Error!! " . $e->getMessage();
            exit();
        }

        if($count < 1)
        {
            echo '<br><div class="alert alert-danger" style="width:40%; margin: 0 30% 0 30%; text-align:center" role="alert">';
            echo '<strong>Error!</strong> E-mail or password is incorrect.';
            echo '</div>';
        }
        else
        {
            $row = $s->fetch();
            $confirmeduname = $row['dbempemail'];
            $confirmedpw = $row['dbemppass'];

            if(password_verify($formfield['ffpassword'], $confirmedpw))
            {
                $_SESSION['loginid'] = $row['dbempid'];
                $_SESSION['loginname'] = $row['dbempfname'];
                $_SESSION['loginpermit'] = $row['dbpositionid'];
                $showform = 0;
                echo '<br><div class="alert alert-success" style="width:40%; margin: 0 30% 0 30%; text-align:center role="alert">Logged in successfully.</div><br>';
                echo '<div class="text-center"><a href="index.php">Continue</a></div>';
                echo "<br>";

                echo '<script type="text/javascript">';
                echo 'function Redirect(){';
                echo '  window.location="index.php";';
                echo '}';
                echo '  setTimeout("Redirect()", 500);';
                echo '</script>';


            }
            else
            {
                echo '<br><div class="alert alert-danger" style="width:40%; margin: 0 30% 0 30%; text-align:center" role="alert">';
                echo '<strong>Error!</strong> E-mail or password is incorrect.  line 72';
                echo '</div>';
            }
        }
    }
}
if($showform == 1) {
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card card-body bg-light mt-5">
                    <h4>You are not logged in. Please log in</h4>
                    <form method="post" action="login.php" name="loginForm" id="loginForm">
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" name="email" id="email"
                                   class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>"/>
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password"
                                   class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"/>
                            <span class="invalid-feedback"><?php echo $password_err; ?></span>
                        </div>
                        <div class="form-row">
                            <div class="col text-center">
                                <input type="submit" value="Submit" id="submit" name="submit" class="btn btn-secondary">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
    </div>
    <?php
}
include_once 'footer.php';
?>