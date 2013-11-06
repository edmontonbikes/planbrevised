<?php

if($_GET['contact_id'] == 'new_contact'){
	//adds contact is new_contact is selected
	$insertSQL = sprintf("INSERT INTO contacts (date_created) VALUES (%s)",
						   GetSQLValueString('current_time', "date"));
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
	
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset2 = "SELECT MAX(contact_id) as new_contact_id FROM contacts;";
	$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
	$row_Recordset2 = mysql_fetch_assoc($Recordset2);
	$totalRows_Recordset2 = mysql_num_rows($Recordset2);
	
	$contact_id = $row_Recordset2['new_contact_id'];
	$contact_id_entry = 'new_contact';
	mysql_free_result($Recordset2);
} elseif(isset($_GET['contact_id'])) {
	//else contact_id is assigned from passed value
	$contact_id = $_GET['contact_id'];
	$contact_id_entry = $_GET['contact_id'];
} else {
	$contact_id = -1;
	$contact_id_entry = -1;
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$waiver = $_POST['waiver'];
	if ($waiver == true) { 
		

 $updateSQL = sprintf("UPDATE contacts SET first_name=%s, middle_initial=%s, last_name=%s, email=%s, DOB=%s, receive_newsletter=%s, phone=%s, address1=%s, address2=%s, city=%s, `state`=%s, zip=%s, pass=ENCODE(%s,'yblcatx') WHERE contact_id=%s",
                       GetSQLValueString($_POST['first_name'], "text"),
                       GetSQLValueString($_POST['middle_initial'], "text"),
                       GetSQLValueString($_POST['last_name'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
					   GetSQLValueString($_POST['DOB'], "date"),
					   GetSQLValueString($_POST['list_yes_no'], "int"),
                       GetSQLValueString($_POST['phone'], "text"),
                       GetSQLValueString($_POST['address1'], "text"),
                       GetSQLValueString($_POST['address2'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
					   GetSQLValueString($_POST['password'], "text"),
					   GetSQLValueString($_POST['contact_id'], "int"));

	} else {
		$result = mysql_query("SELECT MAX(contact_id) FROM contacts");
		$row=mysql_fetch_row($result);
		$current_contact=$row[0];
		$page_edit_current_contact = "<a href=contact_add_edit.php?contact_id=".$current_contact.">Back</a>";		
		echo "You must agree to the liability waiver <br /> " . $page_edit_current_contact;
		//print_r($row);
		exit ();
	};
		

  mysql_select_db($database_YBDB, $YBDB);
  $Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
  
  if ($_POST['contact_id_entry']  == 'new_contact'){
  	//navigate back to shop that it came from

	

		//if there is an email address submitted pass this to google groups signup.  Otherwise redirect to shop log.
		//if ((strpos($_POST['email'], '@') > 0) && ($_POST['list_yes_no'] == 1)) {
			//$email = $_POST['email'];
			//$pagegoto = "contact_add_edit_confirmation_iframe.php" . "?shop_id={$shop_id}&new_user_id={$contact_id}&email=$email";
		//} else { 
			$pagegoto = PAGE_SHOP_LOG . "?shop_id={$shop_id}&new_user_id={$contact_id}";
		}
	
		
	header(sprintf("Location: %s", $pagegoto));
  }


?>