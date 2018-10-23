<?php
date_default_timezone_set('America/Vancouver');
session_start();
//IF SESSION DOES NOT EXIST, REDIRECT BACK TO LOGIN PAGE
if(!$_SESSION['semail']){
    header("Location: index.php");
}
//Connect to Database
$link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
if(mysqli_connect_error()){
    die("Connection to database failed.");
}
//Look for user
$search_query = "SELECT * FROM `Staff` WHERE email = '".mysqli_real_escape_string($link, $_SESSION['semail'])."' LIMIT 1";
$isDoc = false;
if($result = mysqli_query($link, $search_query)){

    $row = mysqli_fetch_array($result);

    $docTable = "SELECT * FROM `Doctor` WHERE sID = '".$row['sID']."'";
    $otherTable = "SELECT * FROM `Technician` WHERE sID = '".$row['sID']."'";

    $docResult = mysqli_query($link, $docTable);
    $otherResult = mysqli_query($link, $otherTable);
    if(mysqli_num_rows($docResult)>0){
        //IS DOC
        $docData = mysqli_fetch_array($docResult);
        $isDoc = true;
    }else{
        //IS TECHNICIAN
        $otherData = mysqli_fetch_array($otherResult);
    }



}else{
    echo "User not found! Please contact technical support.";
}


// If Doctor Issued prescription
if(array_key_exists("prescript-id", $_POST)){
    $insert_prescript = "UPDATE `Appointment` SET `prescription` = '".$_POST['med-name']."' , `dosage` = '".$_POST['dosage-desc']."' WHERE `aID` = '".$_POST['prescript-id']."'";

    if(!mysqli_query($link, $insert_prescript)){
        echo "Insert prescription failed.";
    }
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


        <style>


            #appt-card{
                height: 100%;
            }

            #dosage-desc{
                height : 100px;

            }

        </style>



    </head>
    <body>
        <h1 style="text-align: center; margin-right: 3%; margin-top: 1%;">Welcome to your Staff dashboard! <button type="submit" class="btn btn-primary float-right" id="statsButton">Check Stats!</button>
        </h1>


        <div class="modal fade" id="bookApptModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Prescribe Medicine/Assign Lab test</h5>
                    </div>
                    <div class="modal-body">

                        <form method = "post" id = 'presciption-form'>

                            <div class="form-group">
                                <label for="medicine" id="med">Type of medicine/Lab test: </label>
                                <input type="text" maxlength="64" name = "med-name" id="med-name" required>

                            </div>
                            <div class="form-group">
                                <label for="treatment" id="tr">Treatment Description/ Dosage Description: </label>
                                <textarea rows="4" maxlength="144" cols="42" form = 'presciption-form' id = 'dosage-desc' name = 'dosage-desc' required></textarea>

                            </div>


                            <div class="modal-footer">

                                <input type = 'hidden' id = 'prescript-id' name = 'prescript-id'>
                                <button type="submit" class="btn btn-primary" id="loginButton" name = "prescript-submit">Confirm</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="card-deck">
            <div class="card border-dark" id = "appt-card">
                <img class="card-img-top" src="img/appt.jpg" alt="Card image cap" style="width: 100%; height: 18rem;">
                <div class="card-body" style="height: 100%;" >
                    <h5 class="card-title">Upcoming Appointments</h5>

                    <ul class="list-group list-group-flush" style="height: 100%;"> 
                        <?php
                        $id = $row['sID'];

                        $searchApp = "SELECT * FROM `Appointment`WHERE `sID` = '".$id."' AND `apptDate`>'".date("Y-m-d")."'  ORDER BY `apptDate` ASC LIMIT 5";
                        $result = mysqli_query($link, $searchApp);
                        while($approw = mysqli_fetch_array($result)){
                            $searchPat = "SELECT * FROM `Patient` WHERE `pID` = '".$approw['pID']."' LIMIT 1";
                            $presult   = mysqli_fetch_array(mysqli_query($link, $searchPat));
                            echo "<li class='list-group-item'>
                                    <p>Patient Name: ".$presult['name']." </p>
                                    <p>Date:         ".$approw['apptDate']." </p>
                                    <p>Time:         ".$approw['apptTime']." </p>
                                    <p>Patient Phone Number: ".$presult['phoneNumber']." </p>
                                    <p>Sex:       ".$presult['sex']." </p>
                                    </li>";
                        }


                        ?>


                    </ul>

                </div>

            </div>
            <div class="card border-dark" style="height: 100%;">
                <img class="card-img-top" src="img/appt.jpg" alt="Card image cap" style="width: 100%; height: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Previous Appointments</h5>
                    <p class="card-text">

                    <ul class="list-group list-group-flush"> 
                        <?php
                        $id = $row['sID'];

                        $searchApp = "SELECT * FROM `Appointment`WHERE `sID` = '".$id."' AND `apptDate`<='".date("Y-m-d")."'  ORDER BY `apptDate` ASC LIMIT 5";
                        $result = mysqli_query($link, $searchApp);
                        while($approw = mysqli_fetch_array($result)){
                            $searchPat = "SELECT * FROM `Patient` WHERE `pID` = '".$approw['pID']."' LIMIT 1";
                            $presult   = mysqli_fetch_array(mysqli_query($link, $searchPat));
                            echo "<li class='list-group-item'>
                                    <p>Patient Name: ".$presult['name']." </p>
                                    <p>Date:         ".$approw['apptDate']." </p>
                                    <p>Time:         ".$approw['apptTime']." </p>
                                    <p>Patient Phone Number: ".$presult['phoneNumber']." </p>

                                    <p><button type='button' class='btn btn-primary' onclick='fn(this.id)' id = '".$approw['aID']."' data-toggle='modal' data-target='#bookApptModal' style='float: left;'>
                                        Prescribe Medicine
                                        </button></p>
                                    </li>";
                        }


                        ?>


                    </ul>

                </div>

            </div>
            <!--User Info-->
            <div class="card border-dark">
                <img class="card-img-top" src="img/info3.png" alt="Card image cap" style="width: 100%; height: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Info</h5>

                    <label>Name: </label>
                    <?php
                    echo $row['name'];
                    ?>

                    <br>

                    <label>Phone Number: </label>
                    <?php
                    echo $row['phoneNumber'];
                    ?>

                    <br>

                    <label>Email: </label>
                    <?php
                    echo $row['email'];
                    ?>
                    <br>

                    <label>Specialization: </label>
                    <?php

                    if($isDoc){
                        echo $docData['dSpec'];
                    }else{
                        echo $otherData['spec'];
                    } 
                    ?>

                    <br>

                    <?php
                    if($isDoc){

                        echo "<label>Education: </label> " .$docData['Edu']. "<br>";   
                    } else {
                        echo "<label>Equipment: </label> " .$otherData['equipment']. "<br>";
                    }

                    ?>
                    <label>Hospital ID: </label>
                    <?php
                    echo $row['hID'];
                    ?>

                    <br>

                </div>

            </div>
            <!--Prescription-->
            <div class="card border-dark">
                <img class="card-img-top" src="img/room.jpg" alt="Card image cap" style="width: 100%; height: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Room Information</h5>
                    <p class="card-text">
                        <?php
                        echo "Your room is ".$row['room'];     

                        ?>
                    </p>
                </div>

            </div>
        </div>


        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>

        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>


        <script type="text/javascript">

            function fn(aid){        
                document.getElementById('prescript-id').value=aid;
            }


            document.getElementById("statsButton").onclick = function () {
                location.href = "administrator_dashboard.php";
            };



        </script>

    </body>
</html>
