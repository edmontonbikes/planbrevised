<?php 
require_once('Connections/database_functions.php');
require_once('Connections/YBDB.php');

//if latest contact entry is empty, rewrite it. If not, create new.
$result = mysql_query("SELECT contact_id, first_name, last_name FROM contacts ORDER BY contact_id desc LIMIT 0,1");
@$row=mysql_fetch_row($result);
$contact_id=$row[0];
if(!empty($row[1]) && !empty($row[2]))
	{
	$contact_id = 'new_contact';
	};

?>

<?php include("include_header.html"); ?>
      <p><span class="yb_heading2">Welcome to "Plan B", the New Orleans Community Bike Project </span></p>
      <p>Here are a few things to know about using the shop:</p>
      <ul>
        <li><span class="yb_heading3red">This is your Community Bike Shop.</span> We provide a space for the people of New Orleans to work on bikes and learn bike mechanics skills.</li>
      </ul>
      <ul>
        <li>The New Orleans Community Bike Project is a 501(c)3 non-profit organization  <span class="yb_heading3red">entirely supported by volunteer time, part donations, and monetary  donations. <u>Please give generously</u></span>. </li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">We expect that you treat the shop and everyone in it with respect</span>, and leave the shop in a cleaner state than you found it. </li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">Please consider making a cash donation for personal use of the shop</span> in addition to paying for parts. </li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">Donations go towards</span> new shop tools and supplies as well as other operating expenses.</li>
      </ul>
      <ul>
        <li><span class="yb_heading3red">To get started,</span> just sign-in and  talk to one of the shop staff. <span class="yb_heading3red">Make sure to sign-out</span> when you are done. </li>
      </ul>
      <table height="40" border="1" align="center" cellpadding="1" cellspacing="0">
      <tr align="center">
        <td width="187"><span class="style8"><span class="style9"><a href="<?php echo PAGE_EDIT_CONTACT; ?>?contact_id=<?php print $contact_id ?>">First Time User</a></span> <br />
        </span><span class="yb_standardCENTERred">Fill out intial information </span></td>
        <td width="195"><span class="style8"><span class="style9"><a href="shop_log.php">Sign In</a> to get started</span><br /> 
          </span><span class="yb_standardCENTERred">Talk to a mechanic</span></td>
        <td width="203"><span class="style8"><span class="style9"><a href="shop_log.php">Sign Out</a> before leaving</span><br /> 
          </span><span class="yb_standardCENTERred">Workspace cleaned up?</span></td>
      <!--  <td width="155"><span class="style8"><span class="style9"><a href="survey.php"> Take Our Survey!</a></span><br />
        </span><span class="yb_standardCENTERred">How are  we doing?</span></td> -->
      </tr>
    </table>
    <p><br />
<!--
      <span class="yb_pagetitle">Learn More</span>:<br />
        <span class="yb_heading3red">NOCBP Info:   </span><a href="http://www.bikeproject.org" target="_blank">Plan B Home Page</a> | <a href="http://www.austinyellowbike.org/yb_about_ybp.htm" target="_blank">About YBP</a> | <a href="http://www.austinyellowbike.org/yb_calendar.html" target="_blank">Shop Schedule </a> | <a href="http://www.austinyellowbike.org/yb_newsletters.htm" target="_blank">Monthly Newsletter</a> | <a href="http://www.austinyellowbike.org/yb_services.htm" target="_blank">Shop Services</a><br />
	<span class="yb_heading3red">Giving Back:</span> <a href="http://www.austinyellowbike.org/yb_participate.htm" target="_blank">Volunteering at YBP</a> | <a href="http://www.austinyellowbike.org/yb_services.htm" target="_blank">Volunteer Shops</a> | <a href="http://www.austinyellowbike.org/yb_projects.htm" target="_blank">Projects</a> | <a href="http://www.austinyellowbike.org/yb_bikes.htm" target="_blank">Earn-A-Bike</a> | <a href="http://www.austinyellowbike.org/yb_donate.htm" target="_blank">Donating Online</a><br />
    -->
	</p>
   
<?php

// Don't include footer on welcome page. Redundant.
//include("include_footer.html");

?>
