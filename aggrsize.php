<center><h1> Snapvault Filer Destination Aggregate Capacity Report</h1>
<table cellpadding="0" cellspacing="5">
<tr><td>
<form action="lag.php" method="post">
<input name="button" type="submit" value="Snapvault Lag Report" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="vsize.php" method="post">
<input  name="button" type="submit" value="Volume Size Report" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="index.php" method="post">
<input name="button" type="submit" value="Snapvault Update Complete Report" style="width:230px;height:30px;display:inline" />
</form>
</td></tr></table>
<br>
<table cellpadding="2" cellspacing="2">
<tr><td width=15 bgcolor=#DC143C></td>
<td> Aggegate FreeSpace less than 15%</td></tr>
<tr><td width=15 bgcolor=#32CD32></td>
<td> Aggregate FreeSpace Greater Than Or Equal to 16%</td></tr>
</table>

<br>
</center>

<?php

$con=mysqli_connect("127.0.0.1","netapp","netapp","snaptest");
if (mysqli_connect_errno($con))
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
$const = 1024;
$result = mysqli_query($con,"SELECT * FROM aggrsize ORDER BY percentage");
$total_count = mysqli_query($con,"SELECT COUNT(*) as num FROM aggrsize");
$total_exroot = mysqli_query($con,"SELECT COUNT(*) as num1 FROM aggrsize WHERE aggregate != 'root' AND aggregate != 'root_aggr'");
$less_space = mysqli_query($con,"SELECT COUNT(*) as num2 FROM aggrsize WHERE aggregate != 'root' AND aggregate != 'root_aggr' AND percentage <= '15' ");
$total_gb = mysqli_query($con,"SELECT sum(total) as totgb FROM aggrsize WHERE aggregate != 'root' AND aggregate != 'root_aggr' ");
$used_gb = mysqli_query($con,"SELECT sum(used) as usedgb FROM aggrsize WHERE aggregate != 'root' AND aggregate != 'root_aggr' ");
$count = mysqli_fetch_array($total_count);
$count1 = mysqli_fetch_array($total_exroot);
$count2 = mysqli_fetch_array($less_space);
$gb1 = mysqli_fetch_array($total_gb);
$gb2 = mysqli_fetch_array($used_gb);
$total= $count ['num'];
$texroot= $count1 ['num1'];
$less = $count2 ['num2'];
$totalgb =$gb1 ['totgb'];
$usedgbt =$gb2 ['usedgb'];
$totaltb =round($totalgb/$const);
$usedtb =round($usedgbt/$const);
echo "<center>";
echo "Total Number Of Aggregates:  $total ";
echo "<br>";
echo "Total Excluding root Aggregates :  $texroot";
echo "<br>";
echo "\nNumber Of aggregates With Less Than 15% Free Space : $less";
echo "<br>";
echo "Total space in Aggregates excluding Root Aggregates In TB : $totaltb";
echo "<br>";
echo "Total used Space for volumes in TB : $usedtb";
echo "<br>";


echo "<table border='1'>
 <tr>
 <th> Filer</th>
 <th> Aggregate</th>
 <th> Total Size GB </th>
 <th> Used Size Gb</th>
 <th> Free Size GB</th>
 <th> Free % </th>
 <th> Status Fetched At </th>
 </tr>";

 
while($row = mysqli_fetch_array($result))
   {
   echo "<tr>";
   echo "<td>" . $row['filer'] . "</td>";
   echo "<td>" . $row['aggregate'] . "</td>";
   echo "<td>" . $row['total'] . "</td>";
   echo "<td>" . $row['used'] . "</td>";
   echo "<td>" . $row['free'] . "</td>";
   $perc=$row['percentage'];
   if ($perc < "15")
     $bcolor='#DC143C';
   else
     $bcolor='#32CD32';

   echo "<td bgcolor=$bcolor>" . $row['percentage'] . "</td>";
   echo "<td>" . $row['udtime'] . "</td>";
   echo "</tr>";
   }
 echo "</table>";

echo "</center>";
mysqli_close($con);



 ?>
