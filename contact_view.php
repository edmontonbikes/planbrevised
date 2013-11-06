<?php
require_once('header.php'); 

// create recordsets
$query_recordset1 = "SELECT contacts.first_name, contacts.middle_initial, contacts.last_name, contacts.email, contacts.phone, contacts.address1, contacts.address2, contacts.city, contacts.state, contacts.country, contacts.DOB, contacts.zip FROM contacts;";
// define $search
?>

<?php include("include_header.html");
?>

<form method="post" name="search" id="contact_search" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table>
	<tr>
		<td>Name</td>
		<td><?php list_contacts_select_user('contact_id')?>
		</td>
	</tr>
	<tr>
		<td>City</td>
		<td><?php list_contacts_select_city('city')?>
		</td>
	</tr>
	<tr>
		<td>State</td>
		<td><?php list_contacts_select_state('state')?>
		</td>
	</tr>
	<tr>
		<td>Zip Code</td>
		<td><?php list_contacts_select_zip('zip')?>
		</td>
	</tr>
	<tr>
		<td>Min Age</td>
		<td><input type="integer" name="min_age" id="min_age">
				<!-- populate  list -->
				<?php
         //date in mm/dd/yyyy format; or it can be in other formats as well
         //$birthDate = "12/17/1983";
         //explode the date to get month, day and year
         //$birthDate = explode("/", $birthDate);
         //get age from date or birthdate
         //$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[2])-1):(date("Y")-$birthDate[2]));
         //echo "Age is:".$age;
    
				// while 
				?>
			</input>
		</td>
	</tr>
	<tr>
		<td>Max Age</td>
		<td><input type="integer" name="max_age" id="max_age">
			</input>
		</td>
	</tr>
	<tr>
		<td>Type of user</td>
		<td><?php list_contacts_search_role('user_type');?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value ="search" name="search"></input>
		</td>
	</tr>
	<input type="hidden" name="MM_insert" value="RecordSearch">
	</form>
	
<?php 
if (isset($_POST["MM_insert"])) {
	//$contactName = $_POST["name"];
	//$contactCity = $_POST["city"];
	//$contactState = $_POST["state"];
	$contactZip = $_POST["zip"];
	//$contactMinAge = $_POST["min_age"];
	//$contactMaxAge = $_POST["max_age"];
	//$contactUserType = $_POST["user_type"];

	
	
    // define the list of fields
    $fields = array('name', 'city', 'state', 'zip', 'min_age', 'max_age', 'user_type');
    $conditions = array();

    // loop through the defined fields
    foreach( $_POST as $field_name => $val ){
        // if the field is set and not empty
        if( !empty( $val ) && $field_name != "MM_insert" && $field_name != "search"  ) {
/*             console_json( $val , 'not empty'); */
            // create a new condition while escaping the value inputed by the user (SQL Injection)
            $conditions[] = "`$field_name` = '" . mysql_real_escape_string( $val ) . "'";
        }
    }

    // builds the query
    $query = "SELECT (CONCAT(contacts.last_name, ', ', contacts.first_name, ' ', contacts.middle_initial)) AS name, contacts.email, contacts.phone, contacts.address1, contacts.address2, contacts.city, contacts.state, contacts.country, contacts.DOB, contacts.zip FROM contacts";
    // if there are conditions defined
    if(count($conditions) > 0) {
        // append the conditions
        $query .= " WHERE " . implode (' AND ', $conditions); // you can change to 'OR', but I suggest to apply the filters cumulative
    }
    $result = mysql_query($query) or die("Couldn't execute query");
	
	//$q .= " limit $s,$limit";
	echo $contactZip;
	echo "<table>";
	echo "<tr><td>Name</td><td>Email</td><td>Phone</td><td>Address 1</td><td>Address 2</td><td>City</td><td>State</td><td>Country</td><td>Zip</td><td>DOB</td></tr>";
	while ($row= mysql_fetch_array($result)) {
	echo "<tr><td>".$row['name']."</td>
		<td>".$row['email']."</td>
		<td>".$row['phone']."</td>
		<td>".$row['address1']."</td>
		<td>".$row['address2']."</td>
		<td>".$row['city']."</td>
		<td>".$row['state']."</td>
		<td>".$row['country']."</td>
		<td>".$row['zip']."</td>
		<td>".$row['DOB']."</td>
		</tr>";
} // end WHILE
;}
?>
	
<?php include("include_footer.html")
?>