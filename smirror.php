<center><h1> Snamirror Current Status Report</h1></center>


<?php


$con=mysqli_connect("127.0.0.1","netapp","netapp","snaptest");
if (mysqli_connect_errno($con))
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

#$laglarge = mysqli_query($con,"SELECT COUNT(*) as num FROM svlatest where lag>=100");
#$lagmed = mysqli_query($con,"SELECT COUNT(*) as num1 FROM svlatest where lag>=48 AND lag<100");
#$total  = mysqli_query($con,"SELECT COUNT(*) as num2 FROM svlatest ");
#$laglarge1 = mysqli_fetch_array($laglarge);
#$lagmed1 =mysqli_fetch_array($lagmed);
#$total1 =mysqli_fetch_array($total);
#$largelag2 = $laglarge1 ['num'];
#$lagmed2 = $lagmed1 ['num1'];
#$total2 = $total1 ['num2'];

$result = mysqli_query($con,"SELECT * FROM smstatus");

#echo '<h3> Total Number Of Snapvault RelationShips :' .  $total2 . '</h3>';
#echo '<h3> Number Of Lags Between 48 and 100 Hours : ' .  $lagmed2 . '</h3>';
#echo '<h3> Number Of Lags Greater than 100 Hours:' .$largelag2 . '</h3>';


echo "<table border='1'>
 <tr>
 <th>Destination Filer</th>
 <th>Source Path</th>
 <th>Destination Path</th>
 <th>Lag Hrs</th>
 <th>Last Transfer Time Mins</th>
 <th>Last Transfer Size MB</th>
 <th>State</th>
 <th>Status</th>
 <th>Status Fetched At </th>
 </tr>";

 
while($row = mysqli_fetch_array($result))
   {
   echo "<tr>";
   echo "<td>" . $row['dfiler'] . "</td>";
   echo "<td>" . $row['source'] . "</td>";
   echo "<td>" . $row['dpath'] . "</td>";
   echo "<td>" . $row['lag'] . "</td>";
   echo "<td>" . $row['lttime'] . "</td>";
   echo "<td>" . $row['ltsize'] . "</td>";
   echo "<td>" . $row['state'] . "</td>";
   echo "<td>" . $row['status'] . "</td>";
   echo "<td>" . $row['udtime'] . "</td>";
   echo "</tr>";
   }
 echo "</table>";

mysqli_close($con);



 ?>
