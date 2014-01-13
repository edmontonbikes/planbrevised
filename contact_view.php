<?php 
require_once ('security/password_protect.php');
require_once('header.php'); 
include("include_header.html");

$page_individual_history_log = INDIVIDUAL_HISTORY_LOG;
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
		<td><input type="integer" name="min_age" id="min_age" value="">
			</input>
		</td>
	</tr>
	<tr>
		<td>Max Age</td>
		<td><input type="integer" name="max_age" id="max_age" value="">
			</input>
		</td>
	</tr>
	<tr>
		<td>Type of user</td>
		<td><?php list_contacts_search_role('shop_user_role');?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" class="btn btn-primary" value ="Search" name="search"></input>
		<input type="hidden" name="MM_insert" value="RecordSearch">
		<a href="contact_view.php?logout=1" class="btn btn-disabled btn-small">Logout</a>
	</td></tr>
	</form>
	
<?php 
if (isset($_POST["MM_insert"])) {

 // create conditions array
    $conditions = array();
 console_json( $_POST , 'post');
    // loop through the defined fields
    foreach( $_POST as $field_name => $val ){
        // if the field is set and not empty
        if( !empty( $val ) && $field_name != "MM_insert" && $field_name != "search"  && $field_name != "min_age"  && $field_name != "max_age"  && $val !="no_selection"  ) {
/*             console_json( $val , 'not empty'); */
            // create a new condition while escaping the value inputed by the user (SQL Injection)
            $conditions[] = "`$field_name` = '" . mysql_real_escape_string( $val ) . "'";
        };
    };
	
	//if only one age restriction is input, set the other to a default value
	if( !empty( $_POST["min_age"]) or !empty( $_POST["max_age"])) {
		if( !empty( $_POST["min_age"])) {
			$min_age = $_POST["min_age"];
		} else {
			$min_age = "0";
		};
		if( !empty( $_POST["max_age"])) {
			$max_age = $_POST["max_age"];
		} else {
			$max_age = "199";
		};
	};
	console_json( $min_age , 'min_age');
	console_json( $max_age , 'max_age');

	//if age restrictions are set, create condition
	if( isset( $min_age)) {
		$max_age = ++$max_age; // max age correction
		$dob_condition = "DOB 
            BETWEEN DATE_ADD(CURDATE(), INTERVAL -" . $max_age . " YEAR) 
                AND DATE_ADD(CURDATE(), INTERVAL -" . $min_age . " YEAR)";
	};
	//if both condtional variables are set, append AND to dob_condition
	if( isset( $dob_condition) AND count($conditions) > 0 ) {
        $dob_condition .= " AND "; 
    };

    // builds the query
    $query = "SELECT DISTINCT (CONCAT(contacts.last_name, ', ', contacts.first_name, ' ', contacts.middle_initial)) AS name, contacts.email, contacts.phone, contacts.address1, contacts.address2, contacts.city, contacts.state, contacts.country, contacts.DOB, contacts.zip, contacts.contact_id
	FROM contacts 
	LEFT JOIN shop_hours 
	USING (contact_id)";
	
	//if conditions or dob_condition defined, append WHERE clause
		if( isset( $dob_condition) OR count($conditions) > 0 ) {
        $query .= " WHERE "; 
    }

	// append dob condition if defined
	if( isset( $dob_condition)) {
		$query .= $dob_condition;
	}
    // append conditions if defined
    if( count( $conditions) > 0 ) {
        // append the conditions
        $query .= implode (' AND ', $conditions) . " ORDER BY contacts.last_name ASC"; 
    }
 console_json( $query , 'query');
    $result = mysql_query($query) or die("Couldn't execute query");
	
	echo "<table>";
	echo "<tr><td>#</td><td>Name</td><td>Email</td><td>Phone</td><td>Address 1</td><td>Address 2</td><td>City</td><td>State</td><td>Country</td><td>Zip</td><td>DOB</td></tr>";
	while ($row= mysql_fetch_array($result)) {
	echo "<tr><td>" . ++$a . //number the list
		"</td>
		<td>
		<a href=" .  $page_individual_history_log . "?contact_id=" . $row['contact_id'] . ">" //create contact_id link
		.$row['name']."</a></td>
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