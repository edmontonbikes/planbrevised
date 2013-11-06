<?php 
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

$page_edit_contact = PAGE_EDIT_CONTACT; 
$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;


//transaction ID	
if($_GET['trans_id']>0){
	$trans_id = $_GET['trans_id'];
} else {
	$trans_id =-1;}
	
//delete transaction ID	
if($_GET['delete_trans_id']>0){
	$delete_trans_id = $_GET['delete_trans_id'];
} else {
	$delete_trans_id =-1;}
	
//shop_date
if($_GET['trans_date']>0){
	$trans_date = "AND date <= ADDDATE('{$_GET['trans_date']}',1)" ;
} else {
	$datetoday = current_date();
	$trans_date ="AND date <= ADDDATE('{$datetoday}',1)"; 
	$trans_date = "";  }	   
	
//dayname
if($_GET['shop_dayname']=='alldays'){
	$shop_dayname = '';
} elseif(isset($_GET['shop_dayname'])) {
	$shop_dayname = "AND DAYNAME(date) = '" . $_GET['shop_dayname'] . "'";
} else {
	$shop_dayname = '';
}	

//Transaction_type
if($_GET['trans_type']=='all_types'){
	$trans_type = '';
} elseif(isset($_GET['trans_type'])) {
	$trans_type = "AND transaction_log.transaction_type = '" . $_GET['trans_type'] . "'";
} else {
	$trans_type = '';
}	

//record_count
if($_GET['record_count']>0){
	$record_count = $_GET['record_count'];
} else {
	$record_count = 30;}

// This is the recordset for the list of logged transactions	
mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT *,
DATE_FORMAT(date,'%m/%d (%a)') as date_wday,
CONCAT('$',FORMAT(amount,2)) as format_amount,
CONCAT(contacts.last_name, ', ', contacts.first_name, ' ',contacts.middle_initial) AS full_name,
LEFT(IF(community_bike,CONCAT(quantity, ' Bikes: ', location_name),IF(show_soldto_location, CONCAT(location_name,' Donation'), description)),25) as description_with_locations
FROM transaction_log
LEFT JOIN contacts ON transaction_log.sold_to=contacts.contact_id
LEFT JOIN transaction_types ON transaction_log.transaction_type=transaction_types.transaction_type_id
WHERE 1=1 {$trans_date} {$shop_dayname} {$trans_type} ORDER BY transaction_id DESC LIMIT  0, $record_count;";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

//Action on form update
$editFormAction = $_SERVER['PHP_SELF'];

//Form Submit New Transaction===================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormNew")) {

	$trans_type = $_POST['transaction_type']; 
	
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset5 = "SELECT show_startdate FROM transaction_types WHERE transaction_type_id = \"$trans_type\";";
	//echo $query_Recordset5;
	
	$Recordset5 = mysql_query($query_Recordset5, $YBDB) or die(mysql_error());
	$row_Recordset5 = mysql_fetch_assoc($Recordset5);
	$totalRows_Recordset5 = mysql_num_rows($Recordset5);
	$initial_date_startstorage = $row_Recordset5['show_startdate'];
	
	if ($initial_date_startstorage) {
		$date_startstorage = current_datetime();
		$date = "NULL";
	} else {
		$date_startstorage = "NULL";
		$date = current_datetime();
	} //end if
	
	$insertSQL = sprintf("INSERT INTO transaction_log (transaction_type, date_startstorage, date, quantity) VALUES (%s, %s ,%s,%s)",
					   GetSQLValueString($_POST['transaction_type'], "text"),
					   GetSQLValueString($date_startstorage, "date"),
					   GetSQLValueString($date, "date"),
					   GetSQLValueString(1, "int"));
					   
	//echo $insertSQL; 
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());

	// gets newest transaction ID
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset4 = "SELECT MAX(transaction_id) as newtrans FROM transaction_log;";
	$Recordset4 = mysql_query($query_Recordset4, $YBDB) or die(mysql_error());
	$row_Recordset4 = mysql_fetch_assoc($Recordset4);
	$totalRows_Recordset4 = mysql_num_rows($Recordset4);
	$newtrans = $row_Recordset4['newtrans'];  //This field is used to set edit box preferences
	
	$LoadPage = $_SERVER['PHP_SELF'] . "?trans_id={$newtrans}";
	header(sprintf("Location: %s", $LoadPage));
} // end Form Submit New Shop User

//Form Edit Record ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Update")) {
	
	//Error Correction
	$sold_to = (($_POST['sold_to'] == 'no_selection') ? 1268 : $_POST['sold_to'] );
	$sold_by = (($_POST['sold_by'] == 'no_selection') ? 1268 : $_POST['sold_by'] );
	$date_startstorage = date_update_wo_timestamp($_POST['date_startstorage'], $_POST['db_date_startstorage']);
	$date = date_update_wo_timestamp($_POST['date'], $_POST['db_date']);

	$updateSQL = sprintf("UPDATE transaction_log SET transaction_type=%s, date_startstorage=%s, date=%s, amount=%s, quantity=%s, description=%s, sold_to=%s, sold_by=%s WHERE transaction_id=%s",
						   GetSQLValueString($_POST['transaction_type'], "text"),
						   GetSQLValueString($date_startstorage, "date"),
						   GetSQLValueString($date, "date"),
						   GetSQLValueString($_POST['amount'], "double"),
						   GetSQLValueString($_POST['quantity'], "int"),
						   GetSQLValueString($_POST['description'], "text"),
						   GetSQLValueString($sold_to, "int"),
						   GetSQLValueString($sold_by, "int"),
						   GetSQLValueString($_POST['transaction_id'], "int"));
						   //"2006-10-12 18:15:00"
	
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($updateSQL, $YBDB) or die(mysql_error());
	
	$trans_id = $_POST['transaction_id'];
	header(sprintf("Location: %s",$editFormAction . "?trans_id={$trans_id}" ));   //$editFormAction
}

//Form Edit Record Delete ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "FormEdit") && ($_POST["EditSubmit"] == "Delete")) {
	
	$trans_id = $_POST['transaction_id'];
	header(sprintf("Location: %s",$editFormAction . "?delete_trans_id={$trans_id}" ));   //$editFormAction
}

//Form Confirm Delete ===============================================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ConfirmDelete") && ($_POST["DeleteConfirm"] == "Confirm Delete")) {

	$delete_trans_id = $_POST['delete_trans_id'];
	$insertSQL = "DELETE FROM transaction_log WHERE transaction_id = {$delete_trans_id}";
	mysql_select_db($database_YBDB, $YBDB);
	$Result1 = mysql_query($insertSQL, $YBDB) or die(mysql_error());
	
	header(sprintf("Location: %s", PAGE_SALE_LOG ));   //$editFormAction

//Cancel and go back to transaction ================================================================
} elseif ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ConfirmDelete") && ($_POST["DeleteConfirm"] == "Cancel")) { 
	$delete_trans_id = $_POST['delete_trans_id'];
	header(sprintf("Location: %s", PAGE_SALE_LOG . "?trans_id={$delete_trans_id}" ));   //$editFormAction
}

//Change Date     isset($_POST["MM_update"]) =========================================================
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "ChangeDate")) {
  $editFormAction = $_SERVER['PHP_SELF'] . "?trans_date={$_POST['trans_date']}&trans_type={$_POST['trans_type']}&shop_dayname={$_POST['dayname']}&record_count={$_POST['record_count']}";
  header(sprintf("Location: %s",$editFormAction ));   //$editFormAction
}

//This function redirects the Log to the Welcome Page ================================================
function redirect($sec_delay){
$sec_delay = $sec_delay* 1000;
echo <<<EOD
<SCRIPT language="JavaScript"> 
<!--
 function getgoing()
  {
    top.location="http://www.ybdb.austinyellowbike.org/shop_welcome.php";
   }
 
 setTimeout('getgoing()',$sec_delay);
//--> 
</SCRIPT>
EOD;
}
?>

<?php   //end of redirect function ==============

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/YBDB Template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>YBDB</title>
<!-- InstanceBeginEditable name="head" -->
<?php 
//Welcome page redirects
if ($shop_type == 'Open Shop')
	redirect(120); //Always redirect in 60 seconds on this page
	
if (($_GET['welcome'] == 'yes') AND ($shop_type == 'Open Shop'))
	redirect(45); //Redirect in 15 if there is an action
?>
<!-- InstanceEndEditable -->
<link href="css_yb_standard.css" rel="stylesheet" type="text/css" />
</head>


<body class="yb_standard">
<table align="center">
	<tr valign="top">
	  <td height="40" align="right"><a href="shop_log.php">Current Shop</a> | <a href="start_shop.php"> All Shops</a> | <a href="contact_add_edit_select.php">Edit Contact Info</a> | <a href="stats.php">Statistics</a>  | <a href="transaction_log.php">Transaction Log</a> |   <a href="http://www.austinyellowbike.org/" target="_blank">YBP Home</a></td>
	</tr>
	<tr>
	  <td><!-- InstanceBeginEditable name="Body" -->

<table   border="0" cellpadding="1" cellspacing="0">
  <tr>
  	<td align="left" valign="bottom"><?php echo $error_message ?> </td>
  </tr>
  <tr>
    <td>
      <table   border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
	  <tr bordercolor="#CCCCCC" bgcolor="#99CC33">
		<td colspan="7" bgcolor="#99CC33"><div align="center"><strong>Bike and Sale Log </strong></div></td>
		</tr>
		<?php 		// show delete tranaction confirmation =========================================
		if($delete_trans_id <> -1 ) { ?>
			<form method="post" name="FormConfirmDelete" action="<?php echo $editFormAction; ?>">
			<tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
			<td colspan="7"><p><strong>Edit Transaction:
				  <input type="submit" name="DeleteConfirm" value="Confirm Delete" />
				  <input type="submit" name="DeleteConfirm" value="Cancel" />
				  <input type="hidden" name="delete_trans_id" value="<?php echo $delete_trans_id; ?>">
				  <input type="hidden" name="MM_insert" value="ConfirmDelete">
			</strong></p>	      </td>
			</tr>
			</form>
      
	  
	  <?php       //Form to edit preexisting records ================================================
	  } elseif($trans_id <> -1 ) {
	  
	  // Gets data for the transaction being edited
	  mysql_select_db($database_YBDB, $YBDB);
	  $query_Recordset2 = "SELECT *,
DATE_FORMAT(date_startstorage,'%Y-%m-%d') as date_startstorage_day,
DATE_FORMAT(date,'%Y-%m-%d') as date_day,
DATE_FORMAT(DATE_ADD(date_startstorage,INTERVAL 42 DAY),'%W, %M %D') as storage_deadline,
DATEDIFF(DATE_ADD(date_startstorage,INTERVAL 42 DAY),CURRENT_DATE()) as storage_days_left,
FORMAT(amount,2) as format_amount
FROM transaction_log WHERE transaction_id = $trans_id; ";
	  $Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
	  $row_Recordset2 = mysql_fetch_assoc($Recordset2);
	  $totalRows_Recordset2 = mysql_num_rows($Recordset2);
	  $trans_type = $row_Recordset2['transaction_type'];  //This field is used to set edit box preferences
	  
	  // gets prefrences of edit based on Transaction Type
	  mysql_select_db($database_YBDB, $YBDB);
	  $query_Recordset3 = "SELECT * FROM transaction_types WHERE transaction_type_id = \"$trans_type\";";
	  $Recordset3 = mysql_query($query_Recordset3, $YBDB) or die(mysql_error());
	  $row_Recordset3 = mysql_fetch_assoc($Recordset3);
	  $totalRows_Recordset3 = mysql_num_rows($Recordset3);
	  
	  ?>
	  <form method="post" name="FormEdit" action="<?php echo $editFormAction; ?>">
	  
	  <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
	    <td colspan="7" bgcolor="#CCCC33">
	      <table border="0" cellspacing="0" cellpadding="1">
		  	<tr>
		  	  <td colspan="3"><strong>Edit Transaction:
		  	    <input type="submit" name="EditSubmit" value="Update"  >
		  	    <input type="submit" name="EditSubmit" value="Close" >
		  	    <input type="submit" name="EditSubmit" value="Delete"  >
		  	  </strong></td>
		  	  </tr>
		  	
		  	<tr><td width="10">&nbsp;</td>
		  	  <td width="130">Transaction ID: </td>
              <td><?php echo $row_Recordset2['transaction_id']; ?><em><?php echo $row_Recordset3['message_transaction_id']; ?></em></em></td>
		  	</tr>
			<?php ?>
		  	<tr><td>&nbsp;</td><td>Select Type:</td>
		  	  <td><?php list_transaction_types('transaction_type',$row_Recordset2['transaction_type'] ); ?></td>
		  	</tr>
		  	<?php //date_startstorage ==============================================================
			if($row_Recordset3['show_startdate']){?>
			<tr><td>&nbsp;</td>
		  	  <td>Storage Start Date:</td>
		  	  <td><input name="date_startstorage" type="text" id="date_startstorage" value="<?php 
			  echo $row_Recordset2['date_startstorage_day']; ?>" size="10" maxlength="10" />
                <em>YYYY-MM-DD</em></td>
		  	</tr>
			<?php } //end if storage | start of date ================================================
			?>
		  	<tr><td>&nbsp;</td>
		  	  <td><?php echo $row_Recordset3['fieldname_date']; ?>:</td>
		  	  <td><input name="date" type="text" id="date" value="<?php echo $row_Recordset2['date_day']; ?>" size="10" maxlength="10" />
		  	    <em>YYYY-MM-DD
				<SCRIPT>
					function FillDate() { 
						document.FormEdit.date.value = '<?php echo current_date(); ?>' }
				</SCRIPT>
				<input type="button" name="date_fill" value="Fill Current Date" onclick="FillDate()" />
				<br /><?php 
				if ($row_Recordset3['show_startdate']) {  // If there is a start date show storage expiration message.
					echo ($row_Recordset2['date_day'] == "0000-00-00") ? $row_Recordset2['storage_days_left'] . " days of storage remaining.  Bike must be finished by " . $row_Recordset2['storage_deadline'] . "." : "Bike is marked as complete and should no longer be stored in the shop.";
				} ?></em></td>
		  	</tr>
			<?php if($row_Recordset3['show_amount']){ ?>
			<tr><td>&nbsp;</td>
			<td>Amount:</td>
			<td>$ <input name="amount" type="text" id="amount" value="<?php echo $row_Recordset2['format_amount']; ?>" size="6" /></td>
			</tr>
			<?php } // end if show amount
			if($row_Recordset3['community_bike']){ //community bike will allow a quantity to be selected for Yellow Bikes and Kids Bikes?>
		  	<tr>
		  	  <td>&nbsp;</td>
		  	  <td valign="top">Quantity:</td>
		  	  <td><input name="quantity" type="text" id="quantity" value="<?php echo $row_Recordset2['quantity']; ?>" size="3" maxlength="3" /></td>
		  	  </tr>
			<?php } // end if show quanitiy for community bikes
			if($row_Recordset3['show_description']){ ?>
		  	<tr><td>&nbsp;</td>
		  	<td valign="top">Description:</td>
		  	<td><textarea name="description" cols="50" rows="2"><?php echo $row_Recordset2['description']; ?></textarea></td>
		  	</tr>
			<?php } // end if show_description ?>
		  	<tr><td>&nbsp;</td>
		  	<td><?php echo $row_Recordset3['fieldname_soldto']; ?>:</td>
		  	<td><?php
			if($row_Recordset3['show_soldto_location']){
				list_donation_locations_withheader('sold_to', $row_Recordset2['sold_to']); 
				$record_trans_id = $row_Recordset2['transaction_id']; 
				echo " <a href=\"location_add_edit.php?trans_id={$record_trans_id}&contact_id=new_contact\">Create New Location</a> | <a href=\"location_add_edit_select.php?trans_id={$record_trans_id}&contact_id=new_contact\">Edit Existing Location</a>";
			} else {
				list_CurrentShopUsers_select('sold_to', $row_Recordset2['sold_to']);
			}  ?></td>
		  	</tr>
			<tr><td>&nbsp;</td>
			<td><?php echo $row_Recordset3['fieldname_soldby']; ?>:</td>
			<td><?php list_current_coordinators_select('sold_by', $row_Recordset2['sold_by']); ?></td>
			</tr>
	      </table></td>
	  </tr>
		<input type="hidden" name="MM_insert" value="FormEdit">
	  	<input type="hidden" name="transaction_id" value="<?php echo $trans_id; ?>">
		<input type="hidden" name="db_date_startstorage" value="<?php echo $row_Recordset2['date_startstorage']; ?>">
		<input type="hidden" name="db_date" value="<?php echo $row_Recordset2['date']; ?>">
		</form>

	  <?php    // Form to create a tranaction
	  } else { //This section executes if it is not the transaction_id selected NOT FOR EDIT ?>
	  
	  <form method="post" name="FormNew" action="<?php echo $editFormAction; ?>">
	  <tr bordercolor="#CCCCCC" bgcolor="#CCCC33">
	    <td colspan="7"><p><strong>Start New Transaction:</strong><br />&nbsp;&nbsp;&nbsp;&nbsp;Select Type: <?php list_transaction_types('transaction_type','Sale - Used Parts'); ?> 
	        <input type="submit" name="Submit43" value="Create Transaction" />
	      </p>	      </td>
	    </tr>
	  <input type="hidden" name="MM_insert" value="FormNew">
	</form>
	  <?php } // if ?>
	  <tr bordercolor="#CCCCCC" bgcolor="#99CC33">
	    <td><strong>ID</strong></td>
		<td><strong>Date</strong></td>
		<td bgcolor="#99CC33"><strong>Sale Type </strong></td>
		<td><strong>Amount</strong></td>
		<td><strong>Description</strong></td>
		<td><strong>Sold To</strong></td>
		<td><strong>Edit  </strong></td>
	  </tr>
	  <?php while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)) { //do { ?>
	  
	<form method="post" name="FormView_<?php echo $row_Recordset1['transaction_id']; ?>" action="<?php echo $editFormAction; ?>">
	  <tr bordercolor='#CCCCCC' <?php echo ((intval($row_Recordset1['transaction_id']) == intval($trans_id)) ? "bgcolor='#CCCC33'" :  "")?> >
	    <td><?php echo $row_Recordset1['transaction_id']; ?></td>
		<td><?php echo $row_Recordset1['date_wday']; ?></td>
		<td><?php echo $row_Recordset1['transaction_type']; ?></td>
		<td><?php echo $row_Recordset1['format_amount']; ?></td>
		<td><?php echo $row_Recordset1['description_with_locations']; ?></td>
		<td><?php echo $row_Recordset1['full_name']; ?></td>
		<td><?php $record_trans_id = $row_Recordset1['transaction_id']; echo "<a href=\"{$_SERVER['PHP_SELF']}?trans_id={$record_trans_id}\">edit</a>"; ?></td>
	  </tr>
	  <input type="hidden" name="MM_insert" value="FormUpdate">
	  <input type="hidden" name="shop_visit_id" value="<?php echo $row_Recordset1['transaction_id']; ?>">
	</form>
	<?php } //while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); // while Recordset1 ?>
  </table>  </tr>
  <tr>
  	<td height="40" valign="bottom"><form id="form1" name="form1" method="post" action="">
      <p><br />
        Show
        <input name="record_count" type="text" value="30" size="3" maxlength="3" />
        transactions on or before:
        <input name="trans_date" type="text" id="trans_date" value="<?php echo current_date(); ?>" size="10" maxlength="10" />
        (date format YYYY-MM-DD) Day of week:
        <select name="dayname">
          <option value="alldays" selected="selected">All Days</option>
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
          <option value="Sunday">Sunday</option>
        </select>
      </p>
      <p>Type of transaction <?php list_transaction_types_withheader('trans_type', 'all_types'); ?> 
        <input type="submit" name="Submit" value="Add Filter" />
        <input type="hidden" name="MM_insert" value="ChangeDate" />
      </p>
  	</form></td>
  </tr>
</table>


<p>&nbsp;</p>
<!-- InstanceEndEditable --></td>
	</tr>
</table>

</body>
<!-- InstanceEnd --></html><?php
mysql_free_result($Recordset1);
?>