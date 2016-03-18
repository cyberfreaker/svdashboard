<form action="lag.php" method="post">
<input type="hidden" name="lag" value="<?=$var1?>">
<input name="button" type="submit" value="Show Me Only Lag" style="width:180px;height:30px" />
</form>



<?php
$user = netapp;
$password = netapp;
$database = snaptest;

  // Connect to the database
        $con = mysql_connect("localhost",$user,$password) or die ('Could not connect: ' . mysql_error());
        mysql_select_db($database, $con);

    // Create the form, post to the same file
    echo "<form method='post' action='combo.php'>";

    // Form a query to populate the combo-box
    $query = "SELECT DISTINCT dfiler FROM svlatest;";

    // Successful query?
    if($result = mysql_query($query))  {

      // If there are results returned, prepare combo-box
      if($success = mysql_num_rows($result) > 0) {
        // Start combo-box
        echo "<select name='item'>\n";
        echo "<option>-- Destination Filer --</option>\n";

        // For each item in the results...
        while ($row = mysql_fetch_array($result))
          // Add a new option to the combo-box
          echo "<option value='$row[dfiler]'>$row[dfiler]</option>\n";

        // End the combo-box
        echo "</select>\n";
      }
      // No results found in the database
      else { echo "No results found."; }
    }
    // Error in the database
    else { echo "Failed to connect to database."; }

    // Add a submit button to the form
    echo "<input type='submit' value='Submit' /></form>";

?>





<?php


$con=mysqli_connect("127.0.0.1","netapp","netapp","snaptest");
if (mysqli_connect_errno($con))
   {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

$result = mysqli_query($con,"SELECT * FROM svlatest");


echo "<table border='1'>
 <tr>
 <th>Destination Filer</th>
 <th>Destination Path</th>
 <th>Source Filer</th>
 <th>Source Path</th>
 <th>State</th>
 <th>Status</th>
 <th>Lag Hours</th>
 <th>Last Transfer Type</th>
 <th>Status Fetched At </th>
 </tr>";


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
