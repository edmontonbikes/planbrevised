<?php
require_once('header.php'); 

if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id = current_shop_by_ip();
}

switch ($_GET['error']) {
case 'new_error_message':	//this is a sample error message.  insert error case here		
   $error_message = '';
   break;
default:
   $error_message = 'Enter or Update Contact Information - </span><span class="yb_standard"> Plan B uses this information solely to support the project and it is kept entirely private.  When we apply for grants it helps us to know a little bit about our shop users.  <p>Thanks for supporting The New Orleans Community Bike Project. </p> </span><span class="yb_heading3red">';
   break;
}

$page_shop_log = PAGE_SHOP_LOG . "?shop_id=$shop_id";


require_once('process_contact_info.php');

$editFormAction = $_SERVER['PHP_SELF'] . "?contact_id={$contact_id}&shop_id={$shop_id}";

// Split apart PHP_SELF
$URLparts = explode('/', $_SERVER['PHP_SELF']);
$domainBase = $URLparts[1];

$editFormAction = "/{$domainBase}/" . PAGE_SHOP_LOG . "?contact_id={$contact_id}&shop_id={$shop_id}";

// If there is no "/plan-b", use this line below
// $editFormAction = "/" . PAGE_SHOP_LOG . "?contact_id={$contact_id}&shop_id={$shop_id}";


//}


mysql_select_db($database_YBDB, $YBDB);
$query_Recordset1 = "SELECT *, DECODE(pass,'yblcatx') AS passdecode FROM contacts WHERE contact_id = $contact_id";
$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>

<?php include("include_header.html"); ?>

<table>
  <tr valign="top">
    <td class="well" align="left"><span class="yb_heading3red"><?php echo $error_message; ?></span></td>
	  </tr>
<?php 
	if (isset($_POST["MM_insert"])) {
 	 echo '<tr valign="center"><td align="center"><span class="yb_heading3red">CONTACT EDIT SUCCESSFUL.<a href="shop_log.php">Please log in.</a></span></td></tr>';
  	};
?>
  <tr>
    <td align="center">
      
      <form id="contact-info-form" method="post" name="form1" action="<?php echo $editFormAction; ?>">
        <table width="500" border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
          <tr valign="baseline">
            <td width="200" align="right" nowrap>Contact_id:</td>
			    <td><?php echo $row_Recordset1['contact_id']; ?></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">First_name:</td>
			    <td><input type="text" name="first_name" value="<?php echo $row_Recordset1['first_name']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Middle_initial:</td>
			    <td><input name="middle_initial" type="text" value="<?php echo $row_Recordset1['middle_initial']; ?>" size="1" maxlength="1"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Last_name:</td>
			    <td><input type="text" name="last_name" value="<?php echo $row_Recordset1['last_name']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Email:</td>
			    <td><input type="text" name="email" value="<?php echo $row_Recordset1['email']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Date of Birth: </td>
			    <td><input type="text" name="DOB" value="<?php echo $row_Recordset1['DOB']; ?>" size="10" /> 
			      (YYYY-MM-DD) </td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Phone:</td>
			    <td><input type="text" name="phone" value="<?php echo $row_Recordset1['phone']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Address1:</td>
			    <td><input type="text" name="address1" value="<?php echo $row_Recordset1['address1']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">Address2:</td>
			    <td><input type="text" name="address2" value="<?php echo $row_Recordset1['address2']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">City:</td>
			    <td><input type="text" name="city" value="<?php echo $row_Recordset1['city']; ?>" size="32"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">State:</td>
			    <td><input name="state" type="text" value="<?php echo $row_Recordset1['state']; ?>" size="2" maxlength="2"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">ZIP:</td>
			    <td><input type="text" name="zip" value="<?php echo $row_Recordset1['zip']; ?>" size="5"></td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">New Password:</td>
			    <td><input name="password" type="password" id="password" value="<?php echo $row_Recordset1['passdecode']; ?>" size="32">
			      <br />
			      Your password keeps others from viewing your personal information. </td>
			    </tr>
	  <tr valign="baseline">
	    <td nowrap align="right">Waiver of Liability (shown below)</td>
			    <td><input id="waiver-input" name="waiver" type="checkbox">I Agree</td>
			    </tr>
          <tr valign="baseline">
            <td nowrap align="right">&nbsp;</td>
			    <td><input id="contact-info-submit" class="btn btn-large" type="submit" value="Update Contact Info"></td>
		        </tr>
          </table>
		    <input type="hidden" name="list_yes_no" value="0">  <!--This overides the option not to be invited to the newsletter list.-->
        <input type="hidden" name="MM_insert" value="form1">
        <input type="hidden" name="contact_id" value="<?php echo $row_Recordset1['contact_id']; ?>">
        <input type="hidden" name="contact_id_entry" value="<?php echo $contact_id_entry; ?>">
        </form>	  </td>
	  </tr>
</table>
<p>&nbsp;</p>
<div class="well">
  <span class="yb_heading3red">Waiver of Liability</span>
  <br />
  <br />
  <p>I, and my heirs, in consideration of my participation in the New Orleans Community 
  Bike Project's Open Workshop hereby release the New Orleans Community Bike Project,
  its officers, employees and agents, and any other people officially connected with this 
  organization, from any and all liability for damage to or loss of personal
  property, sickness, or injury from whatever source, legal entanglements, imprisonment, 
  death, or loss of money, which might occur while participating in said event/activity/class.
  Specifically, I release The New Orleans Community Bike Project from any liability or 
  responsibility for my personal well-being, condition of tools and equipment provided 
  and produced thereof, including, but not limited to, bicycles and modes of transportation 
  produced by participants. The New Orleans Community Bike Project is a working, 
  mechanical environment and I am aware of the risks of participation. I hereby state 
  that I am in sufficient physical condition to accept a rigorous level of physical 
  activity and exertion, as is sometimes the case when working in a mechanical environment. 
  I understand that participation in this program is strickly voluntary and I 
  freely chose to participate. I understand that The New Orleans Community Bike Project 
  does not provide medical coverage for me. I verify that I will be responsible 
  for any medical costs I incur as a result of my participation.</p>
</div>

<?php include("include_footer.html"); ?>

<?php
mysql_free_result($Recordset1);
?>
