<?php 
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php'); 

function stand_check_in($stand_num){
	global $database_YBDB, $YBDB;
	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset1 = "SELECT  contacts.first_name, contacts.last_name, stands.stand_id FROM contacts 
	LEFT JOIN shop_hours ON contacts.contact_id=shop_hours.contact_id
	LEFT JOIN stands ON shop_hours.contact_id=stands.contact_id
	WHERE shop_hours.shop_id = $shop_id AND stands.contact_id IS NULL";
	$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
	$num_results1 = mysql_num_rows($Recordset1);

	mysql_select_db($database_YBDB, $YBDB);
	$query_Recordset2 = "SELECT stands.contact_id, contacts.first_name, contacts.last_name FROM stands
	LEFT JOIN contacts ON stands.contact_id=contacts.contact_id
	WHERE stands.stand_id=$stand_num";
	$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
	$row_Recordset2 = mysql_fetch_assoc($Recordset2);

	if(isset($row_Recordset2['contact_id'])){
		echo $row_Recordset2['first_name']." ".$row_Recordset2['last_name'];
	} else {
		echo "<select>";
		for ($i=0; $i<$num_results1; $i++){
			$row_Recordset1 = mysql_fetch_assoc($Recordset1);
			echo "<option>".stripslashes($row_Recordset1['last_name']).",".stripslashes($row_Recordset1['first_name'])."</option>";
		};
		echo "</select>";
	};
}


mysql_select_db($database_YBDB, $YBDB);

if($_GET['shop_id']>0){
	$shop_id = $_GET['shop_id'];
} else {
	$shop_id = current_shop_by_ip();
	if (isset($shop_id)) {
		//$shop_id stays the same
	} else {
		$gotopage = PAGE_START_SHOP . "?error=no_shop"; 
		header(sprintf("Location: %s",$gotopage ));
	};
};


//$query_Recordset1 = "SELECT  contacts.first_name, contacts.last_name, stands.stand_id FROM contacts 
//LEFT JOIN shop_hours ON contacts.contact_id=shop_hours.contact_id
//LEFT JOIN stands ON shop_hours.contact_id=stands.contact_id
//WHERE shop_hours.shop_id = $shop_id AND stands.contact_id IS NULL";
//$Recordset1 = mysql_query($query_Recordset1, $YBDB) or die(mysql_error());
//$num_results1 = mysql_num_rows($Recordset1);

//$query_Recordset2 = "SELECT stands.contact_id, contacts.first_name, contacts.last_name FROM stands
//LEFT JOIN contacts ON stands.contact_id=contacts.contact_id
//WHERE stands.stand_id='1'";
//$Recordset2 = mysql_query($query_Recordset2, $YBDB) or die(mysql_error());
//$row_Recordset2 = mysql_fetch_assoc($Recordset2);


?>

<?php include("include_header.html"); ?>
<?php print $num_results ?>
<table   border="1" cellpadding="1" cellspacing="0" bordercolor="#CCCCCC">
<tr>
	<td>Stand 1</td>
	<td>Stand 2</td>
	<td>Stand 3</td>
	<td>Stand 4</td>
	<td>Stand 5</td>
</tr>
<tr height="30">
	<td>
		<?php
		stand_check_in(1);
//		if(isset($row_Recordset2['contact_id'])){
//			echo $row_Recordset2['first_name']." ".$row_Recordset2['last_name'];
//		} else {
//		echo "<select>";
//			for ($i=0; $i<$num_results1; $i++)
//			{$row_Recordset1 = mysql_fetch_assoc($Recordset1);
//			echo "<option>".stripslashes($row_Recordset1['last_name']).",".stripslashes($row_Recordset1['first_name'])."</option>";
//			};
//		echo "</select>";
//		};
		?>
	</td>
	<td><select>
		<?php
		echo "<option>".list_CurrentShopUsers()."</option>";
		?>
	</td>
	<td></td>
	<td></td>
	<td></td>
</tr>
<tr>
	<td>Stand 9</td>
	<td>Stand 8</td>
	<td>Stand 7</td>
	<td>Stand 6</td>
</tr>
<tr height="30">
	<td></td>
	<td></td>
	<td></td>
	<td></td>
</tr>
</table>



<?php include("include_footer.html"); ?>