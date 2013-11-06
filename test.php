<?php 
$year = 5000;
$week = 5;

$string = 'as@fas';
$search = '@';

$new_visits = array(1 => 0,2 => 0,3 => 0,4 => 0,5 => 0,6 => 0,7 => 0); 
$new_visits[1] = 15; 
$new_visits[2] = 15; 
$new_visits[4] = 15; 
$new_visits[1] = 15; 
$new_visits[6] = 20; 

$new_visits = array(1 => 20,2 => 4,3 => 0,4 => 0,5 => 0,6 => 70,7 => 0);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<?php echo strpos($string, $search); ?>

<p>&nbsp;</p>
<table>
<tr valign="bottom">
	<td><?php echo $year; ?></td>
	<td><?php echo $week; ?></td>
	<?php for ($i = 1; $i <= 7; $i++) { ?>
	<td><?php 
	$new_visits[$i] = $i;
	echo $new_visits[$i]; 
	
	?></td>
	<?php } //for ?>
</tr>	
</table>

</body>
</html>
