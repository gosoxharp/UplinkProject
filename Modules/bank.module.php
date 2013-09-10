<?php
$digest = md5("Modules/bank.module.php");
if($ModulesArray['Bank']['Digest'] != $digest) {  die(); }
if($iptype == "Bank")
{
$GetBankInfo = mysql_query("SELECT * FROM Banks WHERE Name = '$name'");
$GBI = mysql_fetch_array($GetBankInfo);
$cost = $GBI['Cost'];
if(!$action)
{
echo "<center>Welcome to $name bank!<br/>We have a wide selection of options for you.<br/><br/>";
echo ClickLink("<a href='?", "&action", "&action=register'>Account Registration</a>");
echo "<br/><br/>";
echo ClickLink("<a href='?", "&action", "&action=manage'>Manage Existing Account</a>");
echo "<br/><br/>";
echo ClickLink("<a href='?", "&action", "&action=admin'>Administration</a>");
}

if($action == "register")
{
$register = $_REQUEST['register'];
if(!$register)
{
echo "<center>Welcome to ".$name." bank!<br/>We have a wide selection of options for you.<br/><br/>Would you like to open an account with us?<br/><br/>";
echo "The cost to register at the ".$name." bank is " . $cost . "<br/>";
echo ClickLink("<a href='?", "&register", "&register=yes'>Yes</a>");
echo " or ";
echo ClickLink("<a href='?", "&action", "'>No</a>");
} else {
if($register == "complete")
{
$CheckUserAccount = mysql_query("SELECT * FROM UserBank WHERE BankIP = '$oip' AND UserID = '$id' ") or die();
$CUA = mysql_fetch_array($CheckUserAccount);
if($CUA['ID']) { echo "<center>Sorry you already have an account at ".$name." bank<br/>Your account is ".$CUA['Account']; }
else {
$BankAccount = rand(1000000000, 9999999999999);
$CheckAccount = mysql_query("SELECT * FROM UserBank WHERE Account = '$BankAccount' ");
$CA = mysql_fetch_array($CheckAccount);
if($CA['ID']) { echo "Sorry there was an error. Please try again. ";
ClickLink("?", "&register", "&register");
} else {
$AddAccount = mysql_query("INSERT INTO UserBank (BankIP, UserID, Account, Amount) VALUES ('$oip', '$id', '$BankAccount', '0.00') ");
}
}
} else {
$linktouse = ClickLink("?", "&register", "&register=complete");
echo "<center>Please wait one moment while we ready your account.<script>setInterval(function(){location.href='$linktouse';}, 5000);</script>";

}
}
}
if($action == "manage")
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


<table border='0'>
<tr><th>Known Accounts</th></tr>
";
$getAccounts = mysql_query("SELECT * FROM UserBank WHERE BankIP = '$oip' and UserID = '$ID'" );
$UA = mysql_fetch_array($
}
}


?>
