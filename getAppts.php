<?php


$getInput = (string) $_GET['q'];

$userID = intval(substr($getInput, 0, strpos($getInput, 'e')));
$parseDate = substr($getInput, strpos($getInput, 'e') + 1);
$date = substr($parseDate, 0, 4) . "-" . substr($parseDate, 4, 2) . "-" . substr($parseDate, 6);
$date = str_replace(' ', '', $date);


$link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
if(mysqli_connect_error()){
    die("Database connection failed");
}


$sql= "SELECT apptTime FROM `Appointment` WHERE sID = " . $userID . " AND apptDate = '" . $date . "'";


$result = mysqli_query($link, $sql);
if (mysqli_num_rows($result) > 0){
    
echo "<ul class='list-group' id='appointments'>";

while($row = mysqli_fetch_array($result)) {

    echo "<li>" . mysqli_real_escape_string($link, $row['apptTime']) . "</li>";

}

echo "</ul>";


} else {
    echo "Your selected doctor/technician has no appointments for your selected day. You may choose any appropriate time!";
}
















?>