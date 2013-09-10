<?php
session_start();
//session_destroy();
//print_r($_SESSION);
$qstring = $_SERVER['QUERY_STRING'];
if(!$id) { $id = 1; }
$oip = $_REQUEST['ip'];
$softid = $_REQUEST['softid'];
$action = $_REQUEST['action'];
$con=mysql_connect('localhost','uplink','uplink') or die(mysql_error());
mysql_select_db('uplink',$con);

function ClickLink($pre, $var, $aft)
{
$ex = @explode($var, $_SERVER['QUERY_STRING']);
//print_r($ex);
if(@reset($ex) != "")
{
return $pre.@reset($ex).$aft;
} else {
$ex2 = explode("&", @end($ex));
return $pre.@end($ex2).$aft;
}
}

function RunningSofts($id, $runningaction)
{

if($runningaction == "getsofts")
{
//echo "test";
$query = mysql_query("SELECT * FROM UserSoftware WHERE UserID='$id' AND Running='1' ") or die;
while($results = mysql_fetch_array($query))
{
	$name = $results['Name'];
	$version = $results['Version'];
	$softid = $results['SoftID'];
	echo ClickLink("<tr><td>$name</td><td>$version</td><td><a href='?", "&action", "&softid=$softid&action=close'>X</a></td></tr>");
}

} // end of getsofts

if($runningaction == "runsofts")
{
$query = mysql_query("UPDATE UserSoftware Set Running='1' WHERE UserID='$id' AND SoftID='$softid' ") or die;
} //end of runsofts

if($runningaction == "stopsofts")
{
$query = mysql_query("UPDATE UserSoftware Set Running='0' WHERE UserID='$id' AND SoftID='$softid' ") or die;
} //end of stopsofts

}

function GetSoftware($id, $type)
{

$query = mysql_query("SELECT * FROM UserSoftware WHERE UserId='$id' AND Type='$type' ") or die;
while($results = mysql_fetch_array($query))
{
$name = $results['Name'];
$version = $results['Version'];
$softid = $results['SoftID'];
echo ClickLink("<a href='?", "&softid", "&softid=$softid'>$name</a> [$version]");
}
}

$GetModules = mysql_query("SELECT * FROM Modules");
while($GM = mysql_fetch_array($GetModules))
{
$ModuleName = $GM['Name'];
$ModulesArray["$ModuleName"]['ID'] = $GM['ID'];
$ModulesArray["$ModuleName"]['File'] = $GM['File'];
$ModulesArray["$ModuleName"]['Digest'] = $GM['Digest'];

}
echo "
<!doctype html>
<html>
	<head>
		<title>Example</title>
		<style type=\"text/css\">
			* { margin: 0; padding: 0; }
			#menu { position: fixed; bottom: 4px; right: 70%; }
			#menu li { position: relative; float: left; list-style: none; margin-right: .5em; border-bottom: 1px solid #222; }
			#menu li:hover > .sub { display: block !important; }
			#menu .sub { display: none; position: absolute; bottom: 100%; left: 0; }
			#menu .sub .sub2 { display: none; position: absolute; bottom: 0%; left: 110%; }
			#menu .sub li { display: block; width: 100%; border: 0; border-right: 1px solid #222; margin-bottom: .5em; white-space: pre; }
		</style>

		<script type=\"text/javascript\">
			(function() {
				if (!document.body || !document.body.firstChild)
					return setTimeout(arguments.callee, 1);

				var submenus = document.getElementsByClassName('sub');
				for (var i=0, sub=submenus[0]; i<submenus.length; sub=submenus[++i]) {
					(function(sub) {
					 	sub.parentNode.onmouseover = function(e) {
							if (e.target == this)
								sub.style.display = (sub.style.display == 'block') ? 'none' : 'block';

							(function(s) {
								for (var i=0, sub=submenus[0]; i<submenus.length; sub=submenus[++i]) {
									if (s !== sub) sub.style.display = 'none';
								}
							})(sub);
						};
					})(sub);
				}
			})();

			(function() {
				if (!document.body || !document.body.firstChild)
					return setTimeout(arguments.callee, 1);

				var sub2menus = document.getElementsByClassName('sub2');
				for (var i=0, sub2=sub2menus[0]; i<sub2menus.length; sub2=sub2menus[++i]) {
					(function(sub2) {
					 	sub2.parentNode.onmouseover = function(e) {
							if (e.target == this)
								sub2.style.display = (sub2.style.display == 'block') ? 'none' : 'block';

							(function(s) {
								for (var i=0, sub2=sub2menus[0]; i<sub2menus.length; sub2=sub2menus[++i]) {
									if (s !== sub2) sub2.style.display = 'none';
								}
							})(sub2);
						};
					})(sub2);
				}
			})();
		</script>
	</head>
<center>";
/* to get user ip and user hw info */
$query = mysql_query("SELECT * FROM IpList WHERE UID = '$id'") or die();
$fetch = mysql_fetch_array($query);
$useraccount = $fetch['Account'];
$userip = $fetch['IP'];
echo "<div style='position: absolute; float: left;'>&nbsp;&nbsp;&nbsp;[$useraccount@$userip]</div>
<center><div><table>";

RunningSofts($id, "getsofts");
echo "</table></div></center><br/><br/>";
/* finish of user info */


if(!$oip)
{
echo "<table border='1' >";
$query = mysql_query("SELECT * FROM UserIpList WHERE UserID='$id'") or die;
while($results = mysql_fetch_array($query))
{
$name = $results['Name'];
$ip = $results['IP'];
$linktouse = ClickLink("<a href='?", "&ip", "&ip=$ip'>$name [$ip]</a>");
echo "<tr><td>$linktouse</td></tr>";
}

echo "</table>
	</center>
";
} 

if($oip)
{
$query = mysql_query("SELECT * FROM IpList WHERE IP='$oip' ") or die();
$row = mysql_fetch_array($query);
if($row)
{
$name = $row['Name'];
$account = $row['Account'];
$password = $row['Password'];
$iptype = $row['Type'];
if($iptype == "User")
{
echo "
<center>
<h2>$name</h2>[$oip]
<br/><br/><br/>
<table border='1'>
<tr><td>Username: <input type='text' name='LAcc' style='float:right;' id='LAcc' readonly='true'/></td></tr>
<tr><td>Password: <input type='password' name='LPass' style='float:right;' id='LPass' readonly='true'/></td></tr>
<tr><td><input type='submit' value='Login' style='float:right;' /></td></tr>
</table>
";
}

if($iptype == "Bank")
{
include "Modules/bank.module.php";

}
}
}
if($softid)
{
$query2 = mysql_query("SELECT * FROM UserSoftware WHERE SoftID='$softid' ") or die();
$result = mysql_fetch_array($query2);
$name = $result['Name'];
$type = $result['Type'];
$version = $result['Version'];
if($result)
{
if($type == "crackers")
{
RunningSofts($name, "yes", $version, $softid);
echo "
<br/><br/>
<center>
<table border='1'>
<tr><th colspan='3'><h3>$name [$version]</h3></th></tr>
<tr><td>
<table border='0'>
<tr><td>Username:</td>";
$account2 = str_split($account);
$password2 = str_split($password);
$caps = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
foreach($account2 as $akey=>$avalue)
{
if(!in_array($avalue, $caps))
{
echo "<td id='a$akey'>&nbsp;</td>";
}
else {
echo "<td id='a$akey'>&nbsp;&nbsp;</td>";
}
if($action == "crack")
{
$atime = ($akey / $version) * 1000;
echo "\n<script>setInterval(function(){document.getElementById('a$akey').innerHTML = '$avalue';}, $atime);</script>\n";
}
}
echo "</tr></table></td><td><table border='0'><tr><td>Password:</td>";
foreach($password2 as $pkey=>$pvalue)
{
if(!in_array($pvalue, $caps))
{
echo "<td id='p$pkey'>&nbsp;</td>";
}
else {
echo "<td id='p$pkey'>&nbsp;&nbsp;</td>";
}
if($action == "crack")
{
$ptime = ($pkey / $version) * 1500;
echo "<script>setInterval(function(){document.getElementById('p$pkey').innerHTML = '$pvalue';}, $ptime);</script>\n";
}

}
if($action == "crack")
{
echo "<script>setInterval(function(){document.getElementById('LAcc').value='$account';}, $atime);</script>";
echo "<script>setInterval(function(){document.getElementById('LPass').value='$password';}, $ptime);</script>";
}
echo "</tr>
</table>
</td><td>";
echo ClickLink("<a href='?", "&action", "&action=crack'>Crack</a>");
echo "</td></tr>
</table>
";
} // end cracker software
if($type == "security")
{
if(($name == "TraceTracker") && ($action != "close"))
{
$clicklink = ClickLink("<a href='?", "&softid", "&softid=$softid&action=close'>X</a>");
echo "<table border='1' style='position: absolute; bottom: 0%; left: 85%;'><tr><th>$name [$version] $clicklink</th></tr><tr><td id='$name'>&nbsp;</td></tr></table>";
if((!$_SESSION["$name"]) && (!$_SESSION["$name"]['version']))
{
RunningSofts($name, "yes", $version, $softid);
}
}
}

}
if($action == "close")
{
RunningSofts($name, "no", $version, $softid);
}
}




echo "
<ul id=\"menu\">
			<li>Programs
				<ul class=\"sub\">"; 


echo "<table border='1'>";
//echo "<tr><th>File Utilities</th></tr>";
echo "<li>File Utilities<ul class='sub2'>";
GetSoftware($id, 'utilities');
echo "</ul></li>";
echo "<li>Hardware Drivers<ul class='sub2'>";
GetSoftware($id, 'drivers');
echo "</ul></li>";
echo "<li>Crackers<ul class='sub2'>";
GetSoftware($id, 'crackers');
echo "</ul></li>";
echo "<li>Security<ul class='sub2'>";
GetSoftware($id, 'security');
echo "</ul></li>";

echo "</table>";
$cpu = $fetch['CPU'];
$ram = $fetch['RAM'];
if($ram > 1024) { $ram = $ram / 1024; $ram = $ram." Gb"; } else { $ram = $ram." Mb"; }
$conn = $fetch['Connection'];
if($conn > 1024) { $conn = $conn / 1024; $conn = $conn." Mb/s"; } else { $conn = $conn." Kb/s"; }
$hdd = $fetch['HardDrive'];
if($hdd > 1024) { $hdd = $hdd / 1024; $hdd = $hdd." Tb"; } else { $hdd = $hdd." Gb"; }
echo "</ul>		
	</li>

			<li>Hardware
				<ul class=\"sub\">
<table border='1'>
<tr><td><nobr>Cpu: $cpu Ghz</nobr></td></tr>
<tr><td><nobr>Ram: $ram </nobr></td></tr>
<tr><td><nobr>Connection: $conn </nobr></td></tr>
<tr><td><nobr>Harddrive: $hdd </nobr></td></tr>
</table>


				</ul>
			</li>

			<li>Files
<ul class='sub'>
<li>lol1<ul class='sub2'><table border='1'><tr><td>lol1</td></tr><tr><td>lol2</td></tr></table></ul></li>
<li>lol3<ul class='sub2'>lol4</ul></li>
<li>lol5<ul class='sub2'>lol6</ul></li>
</ul>
</li>
			<li>Status</li>
			<li>Finicial
				<ul class=\"sub\">
				<table border='1'><tr><td>Bank</td><td>Account</td><td>Amount</td></tr>";

$query = mysql_query("SELECT * FROM UserBank WHERE UserID='$id' ") or die();
while($results = mysql_fetch_array($query))
{
$bankip = $results['BankIP'];
$account = $results['Account'];
$amount = $results['Amount'];
echo "<tr><td><a href='?ip=$bankip&account=$account'>$bankip</a></td><td>$account</td><td>$amount</td></tr>";

}
echo "</table></ul></li>";
echo "
				
			<li>Email
				<ul class=\"sub\"> 

					<table border='1'>
 
						<tr><td>Email</td></tr>
						<tr><td>To:<br/><center><input type='text' size='24' /></center></td></tr> 
						<tr><td>Message:<br /><textarea></textarea></td></tr> 
					</table>
				</ul>
			</li>
		</ul>

	

</html>";

?>



