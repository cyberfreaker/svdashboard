<center><h1> Snapvault Update Current Status Report</h1></center>
<table cellpadding="0" cellspacing="5">
<tr><td>
<form action="lag.php" method="post">
<input name="button" type="submit" value="Show Me Only Lag" style="width:180px;height:30px;display:inline" /> 
</form> 
</td><td>
<form action="vsize.php" method="post">
<input  name="button" type="submit" value="Volume Size Report" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="aggrsize.php" method="post">
<input name="button" type="submit" value="Aggregate Size Report" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="smirror.php" method="post">
<input name="button" type="submit" value="Snapmirror Status" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="./asw/index.php" method="post">
<input name="button" type="submit" value="ASW Vault Status" style="width:180px;height:30px;display:inline" />
</form>
</td></tr></table>


<?php


$con=mysqli_connect("127.0.0.1","netapp","netapp","snaptest");
if (mysqli_connect_errno($con))
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

$laglarge = mysqli_query($con,"SELECT COUNT(*) as num FROM svlatest where lag>=100");
$lagmed = mysqli_query($con,"SELECT COUNT(*) as num1 FROM svlatest where lag>=48 AND lag<100");
$total  = mysqli_query($con,"SELECT COUNT(*) as num2 FROM svlatest ");
$laglarge1 = mysqli_fetch_array($laglarge);
$lagmed1 =mysqli_fetch_array($lagmed);
$total1 =mysqli_fetch_array($total);
$largelag2 = $laglarge1 ['num'];
$lagmed2 = $lagmed1 ['num1'];
$total2 = $total1 ['num2'];

//$result = mysqli_query($con,"SELECT * FROM svlatest");

echo '<h3> Total Number Of Snapvault RelationShips :' .  $total2 . '</h3>';
echo '<h3> Number Of Lags Between 48 and 100 Hours : ' .  $lagmed2 . '</h3>';
echo '<h3> Number Of Lags Greater than 100 Hours:' .$largelag2 . '</h3>';


echo '<table border=1>
 <tr>
 <th><a href="?orderBy=dfiler?">Destination Filer</a></th>
 <th>Destination Path</th>
 <th><a href = "?orderBy=sfiler">Source Filer</a></th>
 <th>Source Path</th>
 <th>State</th>
 <th>Status</th>
 <th><a href = "?orderBy=lag">Lag Hours </a></th>
 <th>Last Transfer Type</th>
 <th>Status Fetched At </th>
 </tr>';
$orderBy = array('dfiler','sfiler','lag');
$order = 'dfiler';
if (isset($_GET['orderBy']) && in_array($_GET['orderBy'], $orderBy)) {
    $order = $_GET['orderBy'];
}
if($order =='lag'){
$order = 'lag * 1 DESC';
}
$result = mysqli_query($con,"SELECT * FROM svlatest ORDER BY $order");
while($row = mysqli_fetch_array($result))
   {
   echo "<tr>";
   echo "<td>" . $row['dfiler'] . "</td>";
   echo "<td>" . $row['dpath'] . "</td>";
   echo "<td>" . $row['sfiler'] . "</td>";
   echo "<td>" . $row['spath'] . "</td>";
   $cstate=$row['state'];
   $unins='uninitialized';
  if ($cstate==$unins)
     $ccolor='#FFD700';
  else
     $ccolor='#FFFFFF'; 
   echo "<td bgcolor=$ccolor>" . $row['state'] . "</td>";
   echo "<td>" . $row['status'] . "</td>";
   $clag=$row['lag'];
   if ($clag > "48")
     $bcolor='#DC143C';
   else
     $bcolor='#32CD32';
   echo "<td bgcolor=$bcolor>" . $clag . "</td>";
   echo "<td>" . $row['ltrans'] . "</td>";
   echo "<td>" . $row['udtime'] . "</td>";
   echo "</tr>";
   }
 echo "</table>";

mysqli_close($con);



 ?>
<h3>Legend: </h3>
<table cellpadding="2" cellspacing="2">
<tr><td width=15 bgcolor=#DC143C></td>
<td> Snapvault Destination Volumes Lagging More Than 48 Hours </td></tr>
<tr><td width=15 bgcolor=#32CD32></td>
<td> Snapvault Destination Volumes Lagging Less Than 47 Hours</td></tr>
</table>
