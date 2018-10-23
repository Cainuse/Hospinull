<?php
//Database Connect
$link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
if(mysqli_connect_error()){
    die("Database connection failed");
}


?>


<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">


        <style type="text/css">

            body{
                background-color: skyblue;
            }

        </style>



    </head>
    <body>
        <h1 style="text-align: center;">Welcome to the Statistics dashboard! </h1>



        <div class="card-deck">
            <div class="card border-dark" id = "appt-card">
                <img class="card-img-top" src="img/appt.jpg" alt="Card image cap" style="width: 100%; height: 25rem;">
                <div class="card-body">
                    <h5 class="card-title">All lab techs who have had sessions with all appointments</h5>

                    <?php
                    $techAppt = "SELECT * FROM `Technician` t, `Staff` s WHERE t.sID = s.sID AND NOT EXISTS (SELECT `aID` FROM `Appointment` a1 WHERE `aID` NOT IN (SELECT `aID` FROM `Appointment` a2 WHERE a2.sID=t.sID))";
                    if($result = mysqli_query($link, $techAppt)){

                        while($row = mysqli_fetch_array($result)){
                            echo "<p>Name: ".$row['name']."</p>
                                      <p>Specification: ".$row['spec']."</p>
                                      <p>Equipment: ".$row['equipment']."</p><hr>";

                        }
                    }else{
                        echo "<p>Selecting Technician failed.<p>";
                    }

                    ?>

                </div>

            </div>
            <div class="card border-dark">
                <img class="card-img-top" src="img/stats.png" alt="Card image cap" style="width: 100%; height: 25rem;">
                <div class="card-body">
                    <h5 class="card-title">Stats</h5>

                    <!-- total number of appointments given the user selects doctor or technician: this is count aggregation -->
                    <!-- average number of appointments per doctor/technician on today: this is average aggregation -->

                    <?php

                    // Simple aggregations

                    $count_query = "SELECT COUNT(aID) as count FROM `Appointment` WHERE apptDate = '" . date("Y-m-d") . "'";
                    $avg_query = "SELECT AVG(age) as avg FROM `Patient`";


                    if ($result_count = mysqli_query($link, $count_query)){
                        $row = mysqli_fetch_array($result_count);

                        echo "<p>Today's total number of appointments: " . $row['count'] . "<p>";
                    } else {
                        echo "<p>Counting Appointments failed.<p>";
                    }

                    if ($avg_count = mysqli_query($link, $avg_query)){
                        $row2 = mysqli_fetch_array($avg_count);

                        echo "<p>The average age of all patients is: " . $row2['avg'] . "<p>";
                    } else {
                        echo "<p>Computing average age failed.<p>";
                    }




                    // aggregations group-by

                    $min_avg = "SELECT MIN(avg) as minAvg, sID FROM (SELECT AVG(P.age) 'avg', A.sID FROM Patient P, Appointment A WHERE P.pID = A.pID GROUP BY A.sID) as alias1";
                    $max_avg = "SELECT MAX(avg) as maxAvg, sID FROM (SELECT AVG(P.age) 'avg', A.sID FROM Patient P, Appointment A WHERE P.pID = A.pID GROUP BY A.sID) as alias2";


                    if ($res_min = mysqli_query($link, $min_avg)){
                        $row = mysqli_fetch_array($res_min);

                        echo "<label>Minimum patient age average of appointments grouped by Staff: </label>
                            <p>staff id: ". $row['sID'] . ", Average: " . $row['minAvg'] . "</p>";

                    } else {

                        echo "<p>Computing minimum patient age average of appointments grouped by Staff failed.<p>";
                    }

                    if ($res_max = mysqli_query($link, $max_avg)){
                        $row = mysqli_fetch_array($res_max);

                        echo "<p>Maximum patient age average of appointments grouped by Staff: </p>
                            <p>staff id: ". $row['sID'] . ", Average: " . $row['maxAvg'] . "</p>";
                    } else {
                        echo "<p>Computing maximum patient age average of appointments grouped by Staff failed.<p>";
                    }




                    $min_count_appt = "SELECT MIN(apptcount) as minApptCount, sID FROM (SELECT COUNT(aID)'apptcount', sID FROM Appointment A GROUP BY sID) as alias1";
                    $max_count_appt = "SELECT MAX(apptcount) as maxApptCount, sID FROM (SELECT COUNT(aID)'apptcount', sID FROM Appointment A GROUP BY sID) as alias2";


                    if ($res_min = mysqli_query($link, $min_count_appt)){
                        $row = mysqli_fetch_array($res_min);

                        echo "<p>Minimum count of appointments per Staff: <p>
                        <p>staff id: ". $row['sID'] . ", Min Count: " . $row['minApptCount'] . "</p>";

                    } else {

                        echo "<p>Computing minimum number of appointments per Staff failed.<p>";
                    }

                    if ($res_max = mysqli_query($link, $max_count_appt)){
                        $row = mysqli_fetch_array($res_max);

                        echo "<p>Maximum count of appointments per Staff: <p>
                        <p>staff id: ". $row['sID'] . ", Min Count: " . $row['maxApptCount'] . "</p>";
                    } else {
                        echo "<p>Computing maximum number of appointments per Staff failed.<p>";
                    }


                    ?>



                </div>

            </div>

        </div>






        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>


        <script type="text/javascript">

        </script>

    </body>
</html>
