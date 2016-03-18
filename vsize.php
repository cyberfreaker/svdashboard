<h1> Snapvault Destination Volume Capacity Report</h1>
<table cellpadding="0" cellspacing="5">
<tr><td>
<form action="lag.php" method="post">
<input name="button" type="submit" value="Snapvault Lag Report" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="aggrsize.php" method="post">
<input  name="button" type="submit" value="Aggregate Capacity Report" style="width:180px;height:30px;display:inline" />
</form>
</td><td>
<form action="index.php" method="post">
<input name="button" type="submit" value="Snapvault Status Report" style="width:180px;height:30px;display:inline" />
</form>
</td></tr></table>


<table cellpadding="2" cellspacing="2">
<tr><td width=15 bgcolor=#DC143C></td>
<td> Volume FreeSpace less than 15%</td></tr>
<tr><td width=15 bgcolor=#32CD32></td>
<td> Volume FreeSpace Greater Than Or Equal to 16%</td></tr>
</table>

<?php


$con=mysqli_connect("127.0.0.1","netapp","netapp","snaptest");
if (mysqli_connect_errno($con))
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

$result = mysqli_query($con,"SELECT * FROM svvsize");


echo "<table border='1'>
 <tr>
 <th> Filer</th>
 <th> Volume</th>
 <th> Total Size GB </th>
 <th>Used Size Gb</th>
 <th>Free Size GB</th>
 <th>Free % </th>
 <th>Inodes Used Percentage</th>
 <th>Status Fetched At </th>
 </tr>";

 
while($row = mysqli_fetch_array($result))
   {
   echo "<tr>";
   echo "<td>" . $row['filer'] . "</td>";
   echo "<td>" . $row['volume'] . "</td>";
   echo "<td>" . $row['total'] . "</td>";
   echo "<td>" . $row['used'] . "</td>";
   echo "<td>" . $row['free'] . "</td>";
   $perc=$row['percentage'];
   if ($perc < "10")
     $bcolor='#DC143C';
   else
     $bcolor='#32CD32';

   echo "<td bgcolor=$bcolor>" . $row['percentage'] . "</td>";
  $inodep=$row['inodeused'];
  if ($inodep > "85")
     $bcolor='#DC143C';
   else
     $bcolor='#32CD32';

   echo "<td bgcolor=$bcolor>" . $row['inodeused'] . "</td>";
   echo "<td>" . $row['udtime'] . "</td>";
   echo "</tr>";
   }
 echo "</table>";

mysqli_close($con);



 ?>
