<?php
session_start();
//IF SESSION DOES NOT EXIST, REDIRECT BACK TO LOGIN PAGE
if(!$_SESSION['email']){
    header("Location: index.php");
}
//Connect to Database
$link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
if(mysqli_connect_error()){
    die("Connection to database failed.");
}
//Look for user
$search_query = "SELECT * FROM `Patient` WHERE email = '".mysqli_real_escape_string($link, $_SESSION['email'])."' LIMIT 1";

if($result = mysqli_query($link, $search_query)){

    $row = mysqli_fetch_array($result);

}else{
    echo "User not found! Please contact technical support.";
}


//Appointment Booking
if(array_key_exists("book-button", $_POST)){

    $error = "";
    //Constructing error message if any field is missing
    if(!$_POST['time-input']){
        $error .= "Please enter an appropriate time for your appointment! <br>";
    }
    if(!$_POST['staff-list']){
        $error .= "Please select one of the following staff members! <br>";
    }
    if(!$_POST['date-input']){
        $error .= "Please provide a date for your appointment! <br>";
    }
    //NO ERRORS DETECTED!

    $sIDstr = (string) $_POST['staff-list'];

    $staffID = substr($sIDstr, 0, strpos($sIDstr, ','));

    $cur_patient_query = "SELECT pID from `Patient` WHERE email = '".mysqli_real_escape_string($link, $_SESSION['email'])."' LIMIT 1";



    $cur_patient_result = mysqli_query($link, $cur_patient_query);

    $row_res = "";

    if (mysqli_num_rows($cur_patient_result) == 1){
        $row_res = mysqli_fetch_array($cur_patient_result);


        $check_time_query = "SELECT apptTime FROM `Appointment` WHERE `apptTime` = " . $_POST['time-input'];

        if (mysqli_query($link, $check_time_query)){
            echo "<script> alert('Your selected time is already taken! Please try choosing a different time.')</script>";
        } else {

            $insert_query = "INSERT INTO `Appointment` (`apptDate`, `pID`, `apptTime`, `sID`)
                                    VALUES ('".mysqli_real_escape_string($link, $_POST["date-input"])."', '".mysqli_real_escape_string($link, $row_res["pID"])."', '".mysqli_real_escape_string($link, $_POST["time-input"])."', '".mysqli_real_escape_string($link, $staffID)."')";


            
            
            
            

            //INSERTION SUCCESFFUL
            if (mysqli_query($link, $insert_query)){

                echo "<p> appointment successfully created! <p>";


            } else {
                echo "<p>There was a problem setting your appointment up. Please try again!</p>";
            }







            $find_aid = "SELECT aID from `Appointment` WHERE apptDate = '" . mysqli_real_escape_string($link, $_POST["date-input"]) . "' AND apptTime = '" . mysqli_real_escape_string($link, $_POST["time-input"]) . "' AND sID = '" . mysqli_real_escape_string($link, $staffID) . "' AND pID = '" . mysqli_real_escape_string($link, $row_res["pID"]) . "' LIMIT 1";




            if ($res_find = mysqli_query($link, $find_aid)){
                
                $res = mysqli_fetch_array($res_find);
                
                $insert_query_bill = "INSERT INTO `Bill` (`cost`, `pID`, `sID`, `aID`)
                                    VALUES ('". "150" ."', '".mysqli_real_escape_string($link, $row_res["pID"])."', '".mysqli_real_escape_string($link, $staffID)."', '".$res["aID"]."')";

                if (!mysqli_query($link, $insert_query_bill)){
                    echo "<p> Bill could not be loaded. Try again. <p>";
                }
            }



        }

    }
}


if(array_key_exists("appt-cancel", $_POST)){
    $del_query = "DELETE FROM `Appointment` WHERE `aID` = '".$_POST['del-id']."' LIMIT 1";
    if(!mysqli_query($link, $del_query)){
        echo "Deletion of appointment unsuccesful!";
    }
}




if (array_key_exists("save-patient", $_POST)){
    $query_i = "UPDATE `Patient` SET name = '".$_POST['edit-name']."', 
    emergencyContact = '".$_POST['edit-emerg']."',
    phoneNumber = '".$_POST['edit-pnum']."'
    WHERE pID = '".$row['pID']."' LIMIT 1";

    if(mysqli_query($link, $query_i)){
        header("Location: patient_dashboard.php");
    } 

}




if (array_key_exists("deleteAcc", $_POST)){


    $search_query = "SELECT * FROM `Patient` WHERE `email` = '".mysqli_real_escape_string($link, $_SESSION['email'])."' LIMIT 1";

    if($result = mysqli_query($link, $search_query)){

        $row = mysqli_fetch_array($result);



        $delete_appts_query = "DELETE FROM `Appointment` WHERE `pID` = ". $row['pID'] . "";

        $delete_patient_query = "DELETE FROM `Patient` WHERE `pID` = ". $row['pID'] . "";


        if (mysqli_query($link, $delete_appts_query) and mysqli_query($link, $delete_patient_query)){
            header("Location: index.php");
        }

    }else{
        echo "User not found! Please contact technical support or try again.";
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

        </style>


    </head>
    <body>
        <!-- Booking buttons -->
        <h1 style="text-align: center; margin-right: 3%; margin-top: 1%;">Welcome to your patient dashboard! 


            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#seeFinancialSummary" >
                Your Financial Summary
            </button>


            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#bookApptModal" style="float: right;">
                Book an Appointment/Lab Test!
            </button>


        </h1>



        <div class="modal fade" id="seeFinancialSummary" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Your Financial Summary</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method = "post">                      

                            <?php
                            $id = $row['pID'];
                            $totalPrice = 0;

                            $searchApp = "SELECT * FROM `Appointment`WHERE `pID` = '".$id."' AND `apptDate`<='".date("Y-m-d")."'  ORDER BY `apptDate` ASC LIMIT 5";
                            $resulta = mysqli_query($link, $searchApp);
                            ?>

                            <table width="400", cellpadding=5 callspacing=5 border=1>
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                </tr>
                                <?php while($rows = mysqli_fetch_array($resulta)): ?>
                                <tr>
                                    <!--  check whether the appointment is with doc or technician if doc, isDoc is true -->
                                    <?php
                                    $docTable = "SELECT * FROM `Doctor` WHERE sID = '".$rows['sID']."'";
                                    $otherTable = "SELECT * FROM `Technician` WHERE sID = '".$rows['sID']."'";

                                    $docResult = mysqli_query($link, $docTable);
                                    $otherResult = mysqli_query($link, $otherTable);
                                    if(mysqli_num_rows($docResult)>0){
                                        //IS DOC
                                        $docData = mysqli_fetch_array($docResult);
                                        $isDoc = true;
                                    }else{
                                        //IS TECHNICIAN
                                        $otherData = mysqli_fetch_array($otherResult);
                                        $isDoc = false;

                                    }   ?>

                                    <td><?php if($isDoc){
    echo "Doctor ";
    echo $rows['sID'];}
                                        else{
                                            echo "Technician ";
                                            echo $rows['sID'];}
                                        ?></td>

                                    <td><?php echo $rows['apptDate']." at ".$rows['apptTime']; ?></td>
                                    <td><?php if($isDoc)
{$totalPrice += 150;
 echo "150";}else
{  echo "50";
 $totalPrice += 50;}
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <tr> 
                                    <td> Total </td>
                                    <td>       </td>
                                    <td> <?php echo $totalPrice; ?> </td>
                                </tr>
                            </table>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Go back</button>
                    </div>
                </div>
            </div>
        </div>







        <!-- Appointment booking -->
        <div class="modal fade" id="bookApptModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Select an Appointment/Lab Test</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method = "post">

                            <div class='form-group'>
                                <label for='doctor-type' id='appt-type'>Appointment Type:</label>
                                <select name="staff-type" onchange="displayStaff(this.value); displayAppts();" required>
                                    <option value = "" name="none-selected">Select a Staff:</option>
                                    <option value = "doct-sel" name="doct-selected">Doctor</option>
                                    <option value = "tech-sel" name ="other-selected">Technician</option>
                                </select>
                            </div>

                            <div class="form-group">

                                <label for='doctor-selection' id='doct-select'>Select one from the following:</label>
                                <select onchange="displayAppts()" id="staff-list" class="form-control" name="staff-list">
                                    <!-- The list of staff gets shown here -->
                                </select>

                            </div>


                            <div class='form-group'>
                                <label for='date-input' id='appt-date'>Appointment Date:</label>
                                <input type='date' name = 'date-input' id='date-input' onchange="displayAppts()" required>
                            </div>


                            <p>The following appointment times are taken for the day you have chosen. Please select any time between 9am and 5pm where the time does not coincide with the shown times below: </p>

                            <div id="staff-appts">
                                <!-- this is where appointments show up using php and ajax -->
                            </div>




                            <br>

                            <div class='form-group'>
                                <label for='date-input' id='appt-time'>Choose an appropriate time (9AM to 5PM):</label>
                                <input name = 'time-input' id="time-input" type="time" min="09:00" max="17:00" step="1800"required>
                            </div>


                            <button type="submit" class="btn btn-primary" id="loginButton" name = "book-button" disabled>Book Me!</button>

                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>




        <!-- Appointment display -->
        <div class="card-deck">
            <div class="card border-dark" id = "appt-card">
                <img class="card-img-top" src="img/appt.jpg" alt="Card image cap" style="width: 100%; height: 18rem;">
                <div class="card-body" style="height: 100%;" >
                    <h5 class="card-title">Your Appointments</h5>
                    
                    <ul class="list-group list-group-flush" style="height: 100%;"> 
                        <?php
                        $id = $row['pID'];

                        $searchApp = "SELECT * FROM `Appointment` WHERE `pID` = '".$id."' ORDER BY `apptDate` ASC LIMIT 5";
                        $result = mysqli_query($link, $searchApp);
                        while($approw = mysqli_fetch_array($result)){
                            $searchDoc = "SELECT * FROM `Staff` WHERE `sID` = '".$approw['sID']."' LIMIT 1";
                            $dresult   = mysqli_fetch_array(mysqli_query($link, $searchDoc));
                            echo "<li class='list-group-item'>
                                    <p>Doctor/Technician Name:  ".$dresult['name']." </p>
                                    <p>Date:         ".$approw['apptDate']." </p>
                                    <p>Time:         ".$approw['apptTime']." </p>
                                    <p>Doctor email: ".$dresult['email']." </p>
                                    <p>Room:         ".$dresult['room']."</p><br>
                                    <form method = 'post'>
                                    <input type = 'hidden'
                                    value = '".$approw['aID']."' name = 'del-id'>
                                    <button type='submit' class='btn btn-primary' data-toggle='modal' id = '".$approw['aID']."' name = 'appt-cancel' style='float: right;' value = 'delete'>Cancel</button>
                                    </form>
                                    </li>";
                        }


                        ?>


                    </ul>

                </div>

            </div>

            <div class="card border-dark">
                <img class="card-img-top" src="img/info3.png" alt="Card image cap" style="width: 100%; height: 19rem;">
                <div class="card-body">


                    <h5 class="card-title">Info <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal" style = "float: right;" >Edit </button>


                    </h5> 


                    <div id="editModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title"> </h4>
                                </div>
                                <div class="modal-body">

                                    <form method = "post">

                                        Name: <br>
                                        <input type="text" name="edit-name" value= <?php echo $row['name'];?>><br><br>

                                        Phone Number:<br>
                                        <input type="text" name="edit-pnum" maxlength="10" value=<?php echo $row['phoneNumber'];?>>
                                        <br><br>


                                        Emergency Contact:<br>
                                        <input type="text" name="edit-emerg" maxlength="10" value=<?php echo $row['emergencyContact'];?>><br><br>

                                        Age: 
                                        <?php echo $row['age'];?> <br><br>

                                        Email:
                                        <?php echo $row['email'];?><br><br>

                                        Sex: 
                                        <?php echo $row['sex'];?><br><br>

                                        Patient ID:
                                        <?php echo $row['pID']?>
                                        <br><br>


                                        <button type="submit" class="btn btn-primary" id = "editInfo" name = "save-patient"> Save</button>
                                    </form>


                                </div>

                            </div>

                        </div>
                    </div>





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

                    <label>Emergency Contact: </label>
                    <?php
                    echo $row['emergencyContact'];
                    ?>

                    <br>

                    <label>Age: </label>
                    <?php
                    echo $row['age'];
                    ?>

                    <br>

                    <label>Sex: </label>
                    <?php
                    echo $row['sex'];
                    ?>

                    <br>


                    <form method="post">
                        <button type="submit" class="btn btn-danger btn-sm" name="deleteAcc" id="delete-acc" style="float: right;">
                            Deactivate your account!
                        </button>  

                    </form>



                </div>

            </div>
            <div class="card border-dark">
                <img class="card-img-top" src="img/prescription.jpg" alt="Card image cap" style="width: 100%; height: 19rem;">
                <div class="card-body">
                    <h5 class="card-title">Prescriptions</h5>
                    <p class="card-text">
                        <?php
                        $searchApp1 = "SELECT * FROM `Appointment` WHERE `pID` = '".$id."' AND `prescription` != '' ORDER BY `apptDate` ASC LIMIT 5";
                        $result1 = mysqli_query($link, $searchApp1);
                        while($approw1 = mysqli_fetch_array($result1)){
                            $searchDoc1 = "SELECT * FROM `Staff` WHERE `sID` = '".$approw1['sID']."' LIMIT 1";
                            $dresult1   = mysqli_fetch_array(mysqli_query($link, $searchDoc1));
                            echo     "<b>Prescribed by   : </b>Dr. ".$dresult1['name']."<br>
                                          <b>Name of Medicine: </b>".$approw1['prescription']."<br>
                                          <i><b>Dosage Details  : </b></i>".$approw1['dosage']."<br>
                                          <hr>";
                        }




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
            $(function(){
                var dtToday = new Date();

                var month = dtToday.getMonth() + 1;
                var day = dtToday.getDate();
                var year = dtToday.getFullYear();

                if(month < 10)
                    month = '0' + month.toString();
                if(day < 10)
                    day = '0' + day.toString();

                var minDate = year + '-' + month + '-' + day;    
                $('#date-input').attr('min', minDate);
            });





            function displayStaff(str){
                if (str == "") {
                    document.getElementById("staff-list").innerHTML = "";
                    return;
                } else { 
                    runAjax("staff-list");

                    xmlhttp.open("GET","getUser.php?q="+str, true);
                    xmlhttp.send();
                }

            }



            function displayAppts(){


                var selectedStaff = getSelectedStaff().toString().trim();

                if (selectedStaff != "0"){
                    var id = selectedStaff.substring(0, selectedStaff.indexOf(','));

                    var date_input = document.getElementById('date-input').value.toString().trim();

                    var yyyy = "";
                    var mm = "";
                    var dd = "";

                    if (date_input != ""){
                        var yyyy = date_input.substring(0, 4);
                        var mm = date_input.substring(5, 7);
                        var dd = date_input.substring(8);

                        runAjax("staff-appts");

                        xmlhttp.open("GET","getAppts.php?q="+id+"e"+yyyy+mm+dd, true);
                        xmlhttp.send();
                    } else {
                        document.getElementById("staff-appts").innerHTML = "";
                        return;
                    }


                }


            }



            function getSelectedStaff(){
                var list = document.getElementById("staff-list");
                var selected = 0; 
                if (list.options[list.selectedIndex] != undefined){
                    selected = list.options[list.selectedIndex].text;
                }

                return selected;
            }



            function runAjax(givenId){
                if (window.XMLHttpRequest) {
                    // code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                } else {
                    // code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById(givenId).innerHTML = this.responseText;
                    }
                };
            }


  
            $('#time-input').change(function(){

                var selectedTime = $('#time-input').val();
                console.log(selectedTime);
                var appropriateTime = true;

                console.log($('#staff-appts').text());

                $("#appointments li").each(function() {
                    var time = $(this).text();

                    if(time == selectedTime){

                        console.log("this value is: " + time);
                        appropriateTime = false;
                        return;
                    } 

                });


                if (appropriateTime == true){
                    $('#loginButton').prop('disabled', false);
                }

                else {

                    $('#loginButton').prop('disabled', true);

                }

            });




        </script>


    </body>
</html>
