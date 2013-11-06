<?php
require_once('Connections/YBDB.php');
require_once('Connections/database_functions.php');

?>

<?php include("include_header.html");
?>

<table>
	<tr>
		<td>Name</td>
		<td>Phone</td>
		<td>Email</td>
	</tr>
	<tr>
		<td><?php list_staff()
		?>
		</td>
	</tr>
</table>

<?php list_staff();
?>
<?php include("include_footer.html");
?>