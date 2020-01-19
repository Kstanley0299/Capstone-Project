<?php
	$pagetitle = "Insert User Info";
	require_once 'header.php';
	
	$errormsg = "";
	$showform = 1;
	
	require_once "connect.php";

	$sqlposition = 'select position.*, permission.* from position, permission';
	$resultup = $db->prepare($sqlposition);
	$resultup->execute();

		if( isset($_POST['thesubmit']) )
		{
			echo '<p>The form was submitted.</p>';

			//Data Cleansing
			$formfield['fffname'] = trim($_POST['BBfname']);
			$formfield['fflname'] = trim($_POST['BBlname']);
			$formfield['ffdate'] = date('Y-m-d', strtotime($_POST['BBdate']));
			$formfield['ffposition'] = $_POST['BBposition'];
			$formfield['ffpermission'] = $_POST['BBpermission'];
			$formfield['ffemail'] = trim(strtolower($_POST['BBemail']));
			$formfield['ffpass'] = trim($_POST['BBpass']);
			$formfield['ffpass2'] = trim($_POST['BBpass2']);
			
			/*  ****************************************************************************
     		CHECK FOR EMPTY FIELDS
    		Complete the lines below for any REQIURED form fields only.
			Do not do for optional fields.
    		**************************************************************************** */
			if(empty($formfield['fffname'])){$errormsg .= "<p>Your name is empty.</p>";}
			if(empty($formfield['fflname'])){$errormsg .= "<p>Your name is empty.</p>";}
			if($formfield['ffdate'] == ''){$errormsg .= "<p>Your date is empty.</p>";}
				if (!isset($formfield['ffposition']) || empty($formfield['ffposition']))
			{
				$errormsg .= "<p>Your user permission is empty.</p>";
			}
			if(empty($formfield['ffemail'])){$errormsg .= "<p>Your email is empty.</p>";}
			if(empty($formfield['ffpass'])){$errormsg .= "<p>Your password is empty.</p>";}
			if(empty($formfield['ffpass2'])){$errormsg .= "<p>Your confirm password is empty.</p>";}
			if($formfield['ffdate'] == ''){$errormsg .= "<p>Your date is empty.</p>";}
			
			if (!isset($formfield['ffposition']) || empty($formfield['ffposition']))
			{
				$errormsg .= "<p>Your user permission is empty.</p>";
			}
     		//CHECK FOR MATCHING PASSWORDS
			if($formfield['ffpass'] != $formfield['ffpass2'])
			{
				$errormsg .= "<p>Your passwords do not match.</p>";
			}
			
     		//VALIDATE THE EMAIL
    		if (!filter_var($formfield['ffemail'], FILTER_VALIDATE_EMAIL))
			{
				$errormsg .= "<p>Your email is not valid.</p>";
			}

			/*  ****************************************************************************
			DISPLAY ERRORS
			If we have concatenated the error message with details, then let the user know
			**************************************************************************** */
			if($errormsg != "")
			{
				echo "<div class='error'><p>THERE ARE ERRORS!</p>";
				echo $errormsg;
				echo "</div>";
			}
			else
			{
				$options = [
					'cost' => 12,
					'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
				];
				$encpass = password_hash($formfield['ffpass'], PASSWORD_BCRYPT, $options);

				try
				{
					//enter data into database
					$sqlinsert = 'INSERT INTO employees (dbempfname, dbemplname, dbemphiredate, dbpositionid,dbpermissionid, dbempemail, dbemppass)
								  VALUES (:bvfname, :bvlname, :bvdate, :bvposition, :bvpermission, :bvemail, :bvpass)';
					$stmtinsert = $db->prepare($sqlinsert);
					$stmtinsert->bindvalue(':bvfname', $formfield['fffname']);
					$stmtinsert->bindvalue(':bvlname', $formfield['fflname']);
					$stmtinsert->bindvalue(':bvdate', $formfield['ffdate']);
					$stmtinsert->bindvalue(':bvposition', $formfield['ffposition']);
					$stmtinsert->bindvalue(':bvpermission', $formfield['ffpermission']);
					$stmtinsert->bindvalue(':bvemail', $formfield['ffemail']);
					$stmtinsert->bindvalue(':bvpass', $encpass);
					$stmtinsert->execute();
					echo "<div class='success'><p>There are no errors.  Thank you.</p></div>";
				}//try
				catch(PDOException $e)
				{
					echo 'ERROR!!!' .$e->getMessage();
					exit();
				}
			}//else statement end
		}//if isset submit

	$sqlselect = "SELECT employees.*, position.dbpositionid, 
							FROM employees, position
							where employees.dbpositionid = position.dbpositionid";

	$result = $db-> query($sqlselect);

if($_SESSION['loginpermit'] == 1 && $visible == 1)
	{
	?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="BBform">
			<fieldset><legend>Insert Personal Information</legend>
				<table border>
					<tr>
						<th><label for="BBfname">First Name:</label></th>
						<td><input type="text" name="BBfname" id="BBfname" size="10" value="<?php if( isset($formfield['fffname'])){echo $formfield['fffname'];}?>"/></td>
					</tr>
					<tr>
						<th><label for="BBlname">Last Name:</label></th>
						<td><input type="text" name="BBlname" id="BBlname" size="10" value="<?php if( isset($formfield['fflname'])){echo $formfield['fflname'];}?>"/></td>
					</tr>
					<tr>
						<th><label for="BBdate">Hire Date:</label></th>
						<td><input type = "date" name="BBdate" id="BBdate" value ="<?php if( isset($formfield['ffdate'])){echo $formfield['ffdate'];}?>" /></td>
					</tr>
										<tr>
						<th><label>Position:</label></th>
						<td><select name="BBposition" id="BBposition">
						<option value = "">Please Select a Position</option>
						<?php while ($rowup = $resultup->fetch() )
							{
							if ($rowup['dbpositionid'] == $formfield['ffposition'])
								{$checker = 'selected';}
							else {$checker = '';}
							echo '<option value="'. $rowup['dbpositionid'] . '" ' . $checker . '>' . $rowup['dbpositiondescr'] . '</option>';
							}
						?>
						</select>
						</td>
					</tr>
					<tr>
						<th><label>Permission level:</label></th>
						<td><select name="BBpermission" id="BBpermission">
						<option value = "">Please Select a Position</option>
						<?php while ($rowup = $resultup->fetch() )
							{
							if ($rowup['dbpermissionid'] == $formfield['ffpermission'])
								{$checker = 'selected';}
							else {$checker = '';}
							echo '<option value="'. $rowup['dbpermissionid'] . '" ' . $checker . '>' . $rowup['dbpermissionlevel'] . '</option>';
							}
						?>
						</select>
						</td>
					</tr>
					<tr>
						<th><label for="BBemail">Email:</label></th>
						<td><input type="text" name="BBemail" id="BBemail" value="<?php if( isset($formfield['ffemail'])){echo $formfield['ffemail'];}?>" /></td>
					</tr>
					<tr>
						<th><label for="BBpass">Password:</label></th>
						<td><input type="password" name="BBpass" id="BBpass" value="<?php if( isset($formfield['ffpass'])){echo $formfield['ffpass'];}?>" /></td>
					</tr>
					<tr>
						<th><label for="BBpass2">Confirm Password:</label></th>
						<td><input type="password" name="BBpass2" id="BBpass2" value="<?php if( isset($formfield['ffpass2'])){echo $formfield['ffpass'];}?>" /></td>
					</tr>
				<input type="submit" name = "thesubmit" value="Enter">
			</fieldset>
		</form> 
<?php
	}
include_once 'footer.php';
?>