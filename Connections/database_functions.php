<?php 
require_once('caliper_helper_functions.php');
require_once('YBDB.php');

//constants
define("PAGE_START_SHOP", "start_shop.php");
define("PAGE_SHOP_LOG", "shop_log.php");
define("PAGE_EDIT_CONTACT", "contact_add_edit.php");
define("PAGE_SELECT_CONTACT", "contact_add_edit_select.php");
define("PAGE_SHOP_LOG_DELETE_VISIT", "shop_log_delete_shopvisitid.php");
define("INDIVIDUAL_HOURS_LOG", "individual_hours_log.php");
define("INDIVIDUAL_HISTORY_LOG", "individual_history_log.php");
define("PAGE_SALE_LOG", "transaction_log.php");
define("PAGE_EDIT_LOCATION", "location_add_edit.php");
define("PAGE_SELECT_LOCATION", "location_add_edit_select.php");

// Set timezone (added by Eli Silverman)
//date_default_timezone_set ( "America/Chicago" );

//This is a general function to generate the contents of a list box based on a MySQL query.  All nessary parameters for the query are passed 
function generate_list($querySQL,$list_value,$list_text, $form_name, $default_value)
{
	global $database_YBDB, $YBDB;
	mysql_select_db($database_YBDB, $YBDB);
	$recordset = mysql_query($querySQL, $YBDB) or die(mysql_error());
	$row_recordset = mysql_fetch_assoc($recordset);
	$totalRows_recordset = mysql_num_rows($recordset);
	$default_delimiter = '';
	
	// if a form name is supplied HTML listbox code is inserted
	if($form_name <> "none"){
		echo "<select name={$form_name} class='yb_standard'>\n";}

	echo "\n";
	do { 
		if( !empty($row_recordset[$list_value]) && $default_value == $row_recordset[$list_value]){ 
			$default_delimiter = 'selected="selected"';
		} else { $default_delimiter = '';}
		echo '<option value="' . $row_recordset[$list_value] . '"' . $default_delimiter .'>' . $row_recordset[$list_text] . '</option>\n';
		} while ($row_recordset = mysql_fetch_assoc($recordset));
 	$rows = mysql_num_rows($recordset);
 	if($rows > 0) {
      mysql_data_seek($recordset, 0);
	  $row_recordset = mysql_fetch_assoc($recordset);
		}
	mysql_free_result($recordset);
	
	// if a form name is supplied HTML listbox code is inserted
	if($form_name <> "none"){echo "</select>";}
}

//This is a general function to generate the contents of a list box based on a MySQL query.  All nessary parameters for the query are passed 
function generate_list_for_no_id($querySQL,$list_value,$list_text, $form_name, $default_value)
{
	global $database_YBDB, $YBDB;
	mysql_select_db($database_YBDB, $YBDB);
	$recordset = mysql_query($querySQL, $YBDB) or die(mysql_error());
	$row_recordset = mysql_fetch_assoc($recordset);
	$totalRows_recordset = mysql_num_rows($recordset);
	$default_delimiter = '';
	
	// if a form name is supplied HTML listbox code is inserted
	if($form_name <> "none"){
		echo "<select name={$form_name} class='yb_standard'>\n";}

	echo "\n";

	do { 

		echo '<option value="' . $row_recordset[$list_text] . '"' . $default_delimiter .'>' . $row_recordset[$list_text] . '</option>\n';

	} while ($row_recordset = mysql_fetch_assoc($recordset));

 	$rows = mysql_num_rows($recordset);
 	if($rows > 0) {
      mysql_data_seek($recordset, 0);
	  $row_recordset = mysql_fetch_assoc($recordset);
		}
	mysql_free_result($recordset);
	
	// if a form name is supplied HTML listbox code is inserted
	if($form_name <> "none"){echo "</select>";}
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_contacts($form_name = "none", $default_value = "", $max_name_length = 20){
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),$max_name_length) AS full_name, contact_id, hidden FROM contacts WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 ORDER BY last_name, first_name, middle_initial";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_cities($form_name = "none", $default_value = "", $max_name_length = 20){
	$querySQL = "SELECT DISTINCT contacts.city FROM contacts WHERE (city <> '') AND hidden <> 1 ORDER BY city";
	$list_value = "contact_id";
	$list_text = "city";
	generate_list_for_no_id($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_states($form_name = "none", $default_value = "", $max_name_length = 20){
	$querySQL = "SELECT DISTINCT contacts.state FROM contacts WHERE (state <> '') AND hidden <> 1 ORDER BY state";
	$list_value = "contact_id";
	$list_text = "state";
	generate_list_for_no_id($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_zips($form_name = "none", $default_value = "", $max_name_length = 20){
	$querySQL = "SELECT DISTINCT contacts.zip FROM contacts WHERE (zip <> '') AND hidden <> 1 ORDER BY zip";
	$list_value = "contact_id";
	$list_text = "zip";
	generate_list_for_no_id($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_CurrentShopUsers($form_name = "none", $default_value = "", $max_name_length = 20){
	$current_shop = current_shop_by_ip();
	$querySQL = "SELECT full_name, shop_hours.contact_id ,hidden FROM shop_hours
LEFT JOIN (SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),$max_name_length) AS full_name, contact_id, hidden FROM contacts) as contacts ON shop_hours.contact_id=contacts.contact_id
WHERE shop_hours.shop_id = $current_shop
ORDER BY full_name;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_staff($form_name = "none", $default_value = "", $max_name_length = 20){
	$querySQL = "SELECT contacts.contact_id, (CONCAT(last_name, ', ', first_name, ' ',middle_initial),40) AS full_name, contacts.phone, contacts.email, hidden FROM contacts
LEFT JOIN shop_hours ON shop_hours.contact_id=contacts.contact_id
WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 AND shop_hours.shop_user_role = 'Staff / Volunteer'
GROUP BY contacts.contact_id
ORDER BY last_name, first_name, middle_initial;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_coordinators($form_name = "none", $default_value = "", $max_name_length = 20){
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),40) AS full_name, contacts.contact_id, hidden, shop_user_role FROM contacts
LEFT JOIN (SELECT contact_id, shop_user_role FROM shop_hours WHERE shop_user_role = 'Coordinator' GROUP BY contact_id) as shop_hours ON shop_hours.contact_id=contacts.contact_id
WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 AND shop_hours.shop_user_role = 'Coordinator'
GROUP BY contacts.contact_id
ORDER BY last_name, first_name, middle_initial;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_current_coordinators($form_name = "none", $default_value = "", $max_name_length = 20){
	$current_shop = current_shop_by_ip();
	$querySQL = "SELECT LEFT(CONCAT(last_name, ', ', first_name, ' ',middle_initial),40) AS full_name, contacts.contact_id, hidden, shop_user_role FROM contacts
LEFT JOIN (SELECT contact_id, shop_user_role FROM shop_hours WHERE shop_user_role = 'Coordinator' AND shop_id = $current_shop GROUP BY contact_id) as shop_hours ON shop_hours.contact_id=contacts.contact_id
WHERE (first_name <> '' OR last_name <> '') AND hidden <> 1 AND shop_hours.shop_user_role = 'Coordinator'
GROUP BY contacts.contact_id
ORDER BY last_name, first_name, middle_initial;";
	$list_value = "contact_id";
	$list_text = "full_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_projects($form_name = "none", $default_value = ""){
	$querySQL = "SELECT project_id FROM projects WHERE active = 1 AND public = 1 ORDER BY project_id";
	$list_value = "project_id";
	$list_text = "project_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_projects_collective($form_name = "none", $default_value = ""){
	$querySQL = "SELECT project_id FROM projects WHERE active = 1 ORDER BY public DESC, project_id";
	$list_value = "project_id";
	$list_text = "project_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_shop_types($form_name = "none", $default_value = ""){
	$querySQL = "SELECT shop_type_id FROM shop_types ORDER BY list_order;";
	$list_value = "shop_type_id";
	$list_text = "shop_type_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_shop_user_roles($form_name = "none", $default_value = ""){
	$querySQL = "SELECT shop_user_role_id FROM shop_user_roles ORDER BY table_order;";
	$list_value = "shop_user_role_id";
	$list_text = "shop_user_role_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_contacts_select_role($form_name = "none", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='Visitor'>Select User Type</option>\n";
	echo "<option value='Visitor'>--------------</option>";
	list_shop_user_roles("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_search_role($form_name = "user_type", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select User Type</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_shop_user_roles("none",$default_value);	
	echo "</select>\n";
}


// Function provides specific MySQL parameters to the function that generates the list box code
function list_shop_locations($form_name = "none", $default_value = ""){
	$querySQL = "SELECT shop_location_id FROM shop_locations WHERE active = 1 ORDER BY shop_location_id;";
	$list_value = "shop_location_id";
	$list_text = "shop_location_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

// Function provides specific MySQL parameters to the function that generates the list box code
function list_transaction_types($form_name = "none", $default_value = ""){
	$querySQL = "SELECT transaction_type_id FROM transaction_types ORDER BY rank;";
	$list_value = "transaction_type_id";
	$list_text = "transaction_type_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_donation_types($form_name = "none", $default_value = ""){
	$querySQL = "SELECT transaction_type_id FROM transaction_types WHERE community_bike = 1;";
	$list_value = "transaction_type_id";
	$list_text = "transaction_type_id";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

function list_donation_locations($form_name = "none", $default_value = ""){
	$querySQL = "SELECT location_name, contact_id FROM contacts WHERE location_type <>\"\" ORDER BY location_name";
	$list_value = "contact_id";
	$list_text = "location_name";
	generate_list($querySQL,$list_value,$list_text,$form_name, $default_value);
}

//function to convert server time to local time.  To be used by all other current date / time requests. 
function local_datetime(){
	$hours_offset = -6;//fall back
	//$hours_offset = -5;//spring forward
	return time() + ($hours_offset * 60 * 60);
					//offset hours; 60 mins; 60secs offset
}

//function converts the current date/time into h:m am format 
function current_datetime(){
	return date("Y-m-d H:i:s",local_datetime());
}

//function converts the current date/time into YYYY-MM-DD am format 
function current_date(){
	return date("Y-m-d",local_datetime());
}

//function converts the current date/time into h:m am format 
function date_to_time($date_in){
	list($date, $time) = preg_split('[ ]', $date_in);
	list($H, $i, $s) = preg_split('[:]', $time);
	$time_out = date("g:i a", mktime($H, $i, $s, 1,1,2000));
	return $time_out;
}

//takes a date in and adds current time if date has changed
function date_update_wo_timestamp($date_in, $database_date){
	list($date, $time) = preg_split('[ ]', $database_date);
	$timestamp_out = (($date == $date_in) ? $database_date : $date_in);
	return $timestamp_out;
}

function date_to_timestamp($date_in){
	list($date, $time) = preg_split('[ ]', $start_time);
	list($Y, $m, $d) = preg_split('[-]', $date);
	list($H, $i, $s) = preg_split('[:]', $time);
	$time_out = mktime($H, $i, $s, $m, $d, $Y);
	return $time_out;
}

//
function datetime_to_time($date_in){
	list($date, $time) = preg_split('[ ]', $date_in);
	list($H, $i, $s) = preg_split('[:]', $time);
	$time_out = date("H:i:s", mktime($H, $i, $s, 1,1,2000));
	return $time_out;
}

//
function datetime_to_date($date_in){
	list($date, $time) = preg_split('[ ]', $date_in);
	list($Y, $m, $d) = preg_split('[-]', $date);
	$date_out = date("Y-m-d", mktime($H, $i, $s, $m,$d,$Y));
	return $date_out;
}

//Function creates list box with times every 15 minutes for the specified number of hours
function list_15min($start_time, $start_offset_min, $form_name, $hours, $display_elapsed_hours, $default_value){
	list($date, $time) = preg_split('[ ]', $start_time);
	list($Y, $m, $d) = preg_split('[-]', $date);
	list($H, $i, $s) = preg_split('[:]', $time);
	//$min_inc is used to round round to nearest 15min
	$min_inc = 15 - intval($i) % 15;
	$start_tim15 = mktime($H, $i, 0, $m,$d,$Y) + $min_inc * 60 + $start_offset_min*60 ;
	//$start_time_am = date("H:i a", mktime($H, $i, $s, 1,1,2000));
	
	echo "<select name=\"$form_name\" class='yb_standard'>";
	if($default_value <> "none"  && $default_value <> "0000-00-00 00:00:00"){
		//if a default value is requested it is displayed at the top of the list
		echo '<option value="' . $default_value . '">' . date_to_time($default_value) . '</option>';
	} 
	if (current_date() == $date) {
		// if current date does not match shop date current date will no be an option
		echo '<option value="current_time">Current Time</option>';
		echo '<option value="current_time">--------------------</option>';
	} 
	for ($j = 0; $j <= $hours*4; $j++) {
		$list_time_15 = $start_tim15 + $j*15*60;
		if ($display_elapsed_hours == 1) {
			$elapsed_hours = " &nbsp;&nbsp;[" . date("G:i",mktime(0, 0, 0, 1,1,2000) + ($j+1)*15*60). " hrs]";
		} else { 
			$elapsed_hours = "";
		}
			
		$list_time_15_return = date("Y-m-d H:i:s", $list_time_15); 
		$list_time_15_display = date("g:i a", $list_time_15). $elapsed_hours;
   		echo "<option value=\"". $list_time_15_return ."\">" . $list_time_15_display . "</option>";
	}
	echo "</select>";
	
}


function list_time($time_list_start, $time, $form_name = "none", $start_offset_min = 0 , $display_elapsed_hours = 0, $default_value = "none", $hours_listed = 8, $et = ""){
	if($time == "0000-00-00 00:00:00" || $default_value <> "none"){
		//create drop down
		//echo list_15min("0000-00-00 01:20:00", 4, "frm_time_out" );
		echo list_15min($time_list_start,$start_offset_min, $form_name, $hours_listed, $display_elapsed_hours, $default_value );
	} else {
		//list time out
		echo date_to_time($time) . "&nbsp;&nbsp;[{$et} hrs]";
	}

}

function sign_out($time_out, $first_name){
	if($time_out == "0000-00-00 00:00:00"){
		echo '<input type="submit" class="btn btn-small btn-primary" name="submit" value="Sign Out: ' . ucwords($first_name) . '" />';
	}
}

//This function corrects the datatype for form submitted variables 
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      if(($theValue == 'current_time') || ($theValue == 'Current Date')){
	  	$theValue = current_datetime();
	  }
	  
	  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function dateandtimein($date, $time){ 
  if ($time <> 'current_time'){
  	$time = $date . ' ' . datetime_to_time($time); 
  }
  return $time;
}

function list_contacts_edit_add($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='new_contact'>Add New Contact</option>\n";
	echo "<option value='new_contact'>--------------</option>";
	list_contacts("none",$default_value);	
	echo "</select>\n";
}


function list_contacts_select_user($form_name = "contact_id", $default_value = "Select User")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select User</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_contacts("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_select_city($form_name = "contact_id", $default_value = "Select City")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value=''>Select City</option>\n";
	echo "<option value=''>--------------</option>";
	list_cities("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_select_state($form_name = "contact_id", $default_value = "Select State")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value=''>Select State</option>\n";
	echo "<option value=''>--------------</option>";
	list_states("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_select_zip($form_name = "contact_id", $default_value = "Select Zip")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value=''>Select Zip</option>\n";
	echo "<option value=''>--------------</option>";
	list_zips("none",$default_value);	
	echo "</select>\n";
}

function list_CurrentShopUsers_select($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select User</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_CurrentShopUsers("none",$default_value);	
	echo "</select>\n";
}

function list_contacts_YBP_project($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='1269'>Yellow Bike Project</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_contacts("none",$default_value);	
	echo "</select>\n";
}

	function list_contacts_coordinators($form_name = "coordinator_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Coordinator</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_coordinators("none",$default_value);	
	echo "</select>\n";
}

	function list_current_coordinators_select($form_name = "coordinator_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Coordinator</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_current_coordinators("none",$default_value);	
	echo "</select>\n";
}

	function list_donation_locations_withheader($form_name = "coordinator_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='no_selection'>Select Location</option>\n";
	echo "<option value='no_selection'>--------------</option>";
	list_donation_locations("none",$default_value);	
	echo "</select>\n";
}

function list_donation_locations_edit_add($form_name = "contact_id", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='new_contact'>Add New Location</option>\n";
	echo "<option value='new_contact'>--------------</option>";
	list_donation_locations("none",$default_value);	
	echo "</select>\n";
}

	function list_transaction_types_withheader($form_name = "transaction_types", $default_value = "")
{
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='all_types'>All Types</option>\n";
	echo "<option value='all_types'>--------------</option>";
	list_transaction_types("none",$default_value);	
	echo "</select>\n";
}

function list_yes_no($form_name = "list_yes_no", $default_value = 0)
{
	if ($default_value == 1){
		$select_yes = 'selected="selected"';
		$select_no = '';
	} else {
		$select_yes = '';
		$select_no = 'selected="selected"';
	}
	
	echo "<select name={$form_name} class='yb_standard'>\n";
	echo "<option value='1'". $select_yes .">Yes</option>\n";
	echo "<option value='0'". $select_no .">No</option>";	
	echo "</select>\n";
}

function max_shop_id(){
	global $database_YBDB, $YBDB;	
    
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset1 = "SELECT max(shop_id) as shop_id FROM shops;";
	$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);
	$totalRows_Recordset1 = mysql_num_rows($Recordset1);
	return $row_Recordset1['shop_id'];
}

function current_shop_by_ip(){
	global $database_YBDB, $YBDB;
	$IP = $_SERVER['REMOTE_ADDR'];
	$current_date = current_date();
	
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset1 = "SELECT shop_id FROM shops WHERE ip_address = '{$IP}' AND date = '{$current_date}' ORDER BY shop_id;";
	$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
	$row_Recordset1 = mysql_fetch_assoc($Recordset1);   
	$totalRows_Recordset1 = mysql_num_rows($Recordset1);
	return $row_Recordset1['shop_id'];
}



?>