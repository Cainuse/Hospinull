<?php
//START SESSION
session_start();


$error = "";


//Patient Login
if(array_key_exists("log-submit", $_POST)){
    $link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
    if(mysqli_connect_error()){
        die("Database connection failed");
    }

    $error = "";
    $search_query= "SELECT * FROM `Patient` WHERE email = '".mysqli_real_escape_string($link, $_POST['log-email'])."' LIMIT 1";
    $result = mysqli_query($link, $search_query);

    //User is found
    if(mysqli_num_rows($result)>0){
        $row = mysqli_fetch_array($result);

        //password match!
        if($_POST['log-pass'] == $row['password']){
            $_SESSION['email'] = mysqli_real_escape_string($link, $_POST["log-email"]);
            header("Location: patient_dashboard.php");
            //password unmatch!
        }else{
            $error .= "Password is incorrect.";
            echo $error;
        }

        //User is not found
    }else{
        $error .= "User not found!";
        echo "Error: User not found!";
    }
}

//Staff Login
if(array_key_exists("slog-submit", $_POST)){
    $link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
    if(mysqli_connect_error()){
        die("Database connection failed");
    }


    $error = "";
    $search_query= "SELECT * FROM `Staff` WHERE email = '".mysqli_real_escape_string($link, $_POST['slog-email'])."' LIMIT 1";

    $result = mysqli_query($link, $search_query);

    //User is found
    if(mysqli_num_rows($result)>0){
        $row = mysqli_fetch_array($result);

        //password match!
        if($_POST['slog-pass'] == $row['password']){
            $_SESSION['semail'] = mysqli_real_escape_string($link, $_POST["slog-email"]);
            header("Location: staff_dashboard.php");
            //password unmatch!
        }else{
            $error .= "Password is incorrect.";
            echo $error;

        }


        //User is not found
    }else{
        $error .= "User not found!";
        echo "Error: User not found!";
    }
} 

//Patient Registration
if(array_key_exists("patient-submit", $_POST)){
    $link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
    if(mysqli_connect_error()){
        die("Database connection failed");
    }


    $error = "";
    //Constructing error message if any field is missing
    if(!$_POST['name']){
        $error .= "Please enter your name! <br>";
    }
    if(!$_POST['emergphone']){
        $error .= "Please provide an emergency phone number! <br>";
    }
    if(!$_POST['phone']){
        $error .= "Please provide your phone number! <br>";
    }
    if(!$_POST['age']){
        $error .= "Please enter your age! <br>";
    }
    if(!$_POST['sex']){
        $error .= "Please provide your sex! <br>";
    }
    if(!$_POST['email']){
        $error .= "Please enter your email address! <br>";
    }
    if(!$_POST['password']){
        $error .= "Please enter your password! <br>";
    }
    if($error!=""){
        $error = "<p><b>The following information are missing: </b></p>".$error;
    }
    //NO ERRORS DETECTED!
    else{
        $check_email_query = "SELECT * FROM `Patient` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";
        $check_email_result = mysqli_query($link, $check_email_query);     

        //Check to see if email address is in use
        if(mysqli_num_rows($check_email_result)>0){
            $error .= "Email Address has been taken. <br>";
            echo "$error";
        }
        //email address available
        else{   
            $insert_query = "INSERT INTO `Patient` (`name`, `emergencyContact`, `phoneNumber`, `age`, `sex`, `email`, `password`)
                                    VALUES ('".mysqli_real_escape_string($link, $_POST["name"])."', '".mysqli_real_escape_string($link, $_POST["emergphone"])."', '".mysqli_real_escape_string($link, $_POST["phone"])."', '".mysqli_real_escape_string($link, $_POST["age"])."', '".mysqli_real_escape_string($link, $_POST["sex"])."', '".mysqli_real_escape_string($link, $_POST["email"])."', '".mysqli_real_escape_string($link, $_POST["password"])."')";

            //INSERTION SUCCESFFUL
            if (mysqli_query($link, $insert_query)){
//                $hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
//
//                $update_pass = "UPDATE `Patient` SET `password` = '". $hash ."' WHERE `pID` = '".mysqli_insert_id($link)."'"; 

                $_SESSION['email'] = mysqli_real_escape_string($link, $_POST["email"]);
                header("Location: patient_dashboard.php");
//                
//                if(mysqli_query($link, $update_pass)){
//                    
//                    header("Location: patient_dashboard.php");
//                }else{
//                    echo "There was a problem encrypting your password! <br>";
//                }
//
//                

            } else {
                echo "<p>There was a problem signing you up. Please try again later!</p>";
            }
        }


    }

}
//************************************************************************************\\
//Staff Registration
if(array_key_exists("staff-submit", $_POST)){
    $link = mysqli_connect("shareddb-h.hosting.stackcp.net", "hospinull-3331d05b", "8ieahgusuz", "hospinull-3331d05b");
    if(mysqli_connect_error()){
        die("Database connection failed");
    }

    $check_semail_query = "SELECT * FROM `Staff` WHERE email = '".mysqli_real_escape_string($link, $_POST['semail'])."' LIMIT 1";
    $check_semail_result = mysqli_query($link, $check_semail_query);     


    $roomNum = rand(0, 9999);



    //Check to see if email address is in use
    if(mysqli_num_rows($check_semail_result)>0){
        $error .= "Email Address has been taken. <br>";
        echo "$error";
    }
    //email address available
    else{   
        $insert_s_query = "INSERT INTO `Staff` (`name`, `phoneNumber`, `email`, `password`, `hID`, `room`)
                                VALUES ('".mysqli_real_escape_string($link, $_POST["sname"])."', '".mysqli_real_escape_string($link, $_POST["sphone"])."', '".mysqli_real_escape_string($link, $_POST["semail"])."', '". $_POST["spassword"] ."', '".mysqli_real_escape_string($link, $_POST["shid"])."', '". $roomNum . "')";

        //STAFF INSERTION SUCCESSFUL
        if (mysqli_query($link, $insert_s_query)){
            if($_POST['prof']=="Doctor"){
                $insert_staff = "INSERT INTO `Doctor` (`sID`, `dSpec`, `Edu`)
                                                            VALUES ('".mysqli_insert_id($link)."', '".mysqli_real_escape_string($link, $_POST["spec"])."', '". mysqli_real_escape_string($link, $_POST["eduorequ"]) . "')";
                if(mysqli_query($link, $insert_staff)){
                    $_SESSION['semail'] = mysqli_real_escape_string($link, $_POST["semail"]);
                    $_SESSION['prof'] = "Doctor";
                    header("Location: staff_dashboard.php");
                }
                else{
                    echo "WARNING! STAFF REGISTRATION SUCESSFUL BUT SUBCLASS (DOCTOR) REGISTRATION FAILED.";
                }

            }else{
                $insert_staff = "INSERT INTO `Technician` (`sID`, `spec`, `equipment`)
                                                            VALUES ('".mysqli_insert_id($link)."', '".mysqli_real_escape_string($link, $_POST["spec"])."', '". mysqli_real_escape_string($link, $_POST["eduorequ"]) . "')";
                if(mysqli_query($link, $insert_staff)){
                    $_SESSION['semail'] = mysqli_real_escape_string($link, $_POST["semail"]);
                    $_SESSION['prof'] = "Other";
                    header("Location: staff_dashboard.php");
                }
                else{
                    echo "WARNING! STAFF REGISTRATION SUCESSFUL BUT SUBCLASS (TECHNICIAN) REGISTRATION FAILED.";
                }
            }
        } else {
            echo "<p>There was a problem signing you up. Please try again later!</p>";
        }
    }

   
}


?>


<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>The Null Hospital</title>

        <!-- Bootstrap core CSS -->
        <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom fonts for this template -->
        <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Lato:100,100i,300,300i,400,400i,700,700i,900,900i" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/one-page-wonder.css" rel="stylesheet">

        <style>

            .btn-xl{
                font-size: 50px;
                border-radius: 7%;
                margin: 5%;
            }

        </style>

    </head>

    <body>

        <!-- Navigation -->


        <nav class="navbar navbar-expand-lg natorvbar-dark navbar-custom">
            <div class="container">
                <h3>Welcome to the NULL Hospital system!</h3>
            </div>
        </nav>

        <!-- modal 1 for staff registration button -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Hey there!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">


                        <form method="post">
                            <div class="form-group">
                                <label for="exampleInputEmail2">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" name = "slog-email" required>

                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name = "slog-pass"required>
                            </div>


                            <button type="submit" class="btn btn-secondary" name="slog-submit" style="margin-top: 7px;">Login</button><br>

                            <label style="margin-top: 8px;">New around here? Sign up below!</label><br>


                            <div class="dropdown-divider"></div>

                        </form>


                        <form method="post">

                            <div class="form-group" id="fullName">
                                <label for="fullName" class="userPassLabel">Full Name</label>
                                <input name="sname" type="text" pattern="(?n:(^(?(?![^,]+?,)((?<first>[A-Z][a-z]*?) )?((?<second>[A-Z][a-z]*?) )?((?<third>[A-Z][a-z]*?) )?)(?<last>[A-Z](('|[a-z]{1,2})[A-Z])?[a-z]+))(?(?=,)(, (?<first>[A-Z][a-z]*?))?( (?<second>[A-Z][a-z]*?))?( (?<third>[A-Z][a-z]*?))?)$)" class="form-control" id="staffName" aria-describedby="emailHelp" placeholder="Enter Your full name" required>
                            </div>
                            <div class="form-group" id="usernameDiv">
                                <label for="userName" class="userPassLabel">Email</label>
                                <input name="semail" type="email" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" aria-describedby="emailHelp" placeholder="Enter Your email address" required>
                            </div>
                            <div class="form-group" id="phoneNum">
                                <label for="phoneNum" class="userPassLabel">Hospital ID</label>
                                <input name="shid" type="number" maxlength="5" class="form-control" placeholder="E.g. 15" required>
                            </div>
                            <div class="form-group" id="phoneNum">
                                <label for="phoneNum" class="userPassLabel">Phone Number</label>
                                <input name="sphone" type="tel" min="0" minlength="10" maxlength="10" pattern="\d*" class="form-control" placeholder="E.g. 6040000000" required>
                            </div> 
                            <div class="form-group" id="nameDiv">
                                <label for="fullName" class="userPassLabel">Specialty</label>
                                <input name="spec" type="text" class="form-control" id="staffSpecialty" placeholder="E.g. Spinal Cord" required>
                            </div>
                            <div class="form-group">
                                <label for="prof">Type</label>
                                <select class="form-control" onchange="displayForSelected(this.value)" id="staffType" name = "prof" required>
                                    <option value="d">Doctor</option>
                                    <option value="t">Technician</option>
                                </select>
                            </div>

                            <div class="form-group" id="eduOrEqu">
                                <label for="fullName" id="eduorequlabel" class="userPassLabel">Education</label>
                                <input name="eduorequ" type="text" maxlength="50" class="form-control" id="edeq">
                            </div>

                            <div class="form-group">
                                <label for="password" class="userPassLabel">Password</label> 
                                <input name="spassword" type="password" class="form-control" id="password" placeholder="Password" required>
                            </div>
                            <button type="submit" class="btn btn-primary" id="loginButton" name = "staff-submit">Register Me!</button>
                        </form>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div> 

        <!-- modal 2 for patient registration button -->
        <div class="modal fade" id="loginModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Hey there!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form method="post">
                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" class="form-control" id="exampleInputEmail1" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" aria-describedby="emailHelp" placeholder="Enter email" name = "log-email"required>

                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name = "log-pass" required>
                            </div>


                            <button type="submit" class="btn btn-secondary" name = "log-submit" style="margin-top: 7px;">Login</button><br>



                            <div class="dropdown-divider"></div>

                        </form>


                        <label>New around here? Sign up below!</label><br>

                        <form method="post">

                            <div class="form-group" id="fullName">
                                <label for="fullName" class="userPassLabel">Full Name</label>
                                <input name="name" type="text" pattern="(?n:(^(?(?![^,]+?,)((?<first>[A-Z][a-z]*?) )?((?<second>[A-Z][a-z]*?) )?((?<third>[A-Z][a-z]*?) )?)(?<last>[A-Z](('|[a-z]{1,2})[A-Z])?[a-z]+))(?(?=,)(, (?<first>[A-Z][a-z]*?))?( (?<second>[A-Z][a-z]*?))?( (?<third>[A-Z][a-z]*?))?)$)"  class="form-control" id="patientName" aria-describedby="emailHelp" placeholder="Enter Your full name">
                            </div>
                            <div class="form-group" id="usernameDiv">
                                <label for="userName" class="userPassLabel">Email</label>
                                <input name="email" type="email" class="form-control" aria-describedby="emailHelp" placeholder="Enter Your email address">
                            </div>
                            <div class="form-group" id="phoneNum">
                                <label for="phoneNum" class="userPassLabel">Phone Number</label>
                                <input name="phone" type="tel" maxlength="10" minlength="10" pattern="\d*" class="form-control" placeholder="E.g. 6040000000">
                            </div>
                            <div class="form-group" id="emerg-phoneNum">
                                <label for="emergphoneNum" class="userPassLabel">Emergency Phone Number</label>
                                <input name="emergphone" type="tel" maxlength="10" minlength="10" pattern="\d*" class="form-control" placeholder="E.g. 6040000000">
                            </div>
                            <div class="form-group" id="nameDiv">
                                <label for="fullName" class="userPassLabel">Age</label>
                                <input name="age" type="number" min="0" max="120" maxlength="3" class="form-control" id="userAge" placeholder="Enter Your Age">
                            </div>
                            <div class="form-group">
                                <label for="userSex">Sex</label>
                                <select class="form-control" id="userSex" name = "sex">
                                    <option>Male</option>
                                    <option>Female</option>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password" class="userPassLabel">Password</label>
                                <input name="password" type="password" class="form-control" id="password" placeholder="Password">
                            </div>
                            <button type="submit" class="btn btn-primary" id="loginButton" name = "patient-submit">Register Me!</button>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>



        <header class="masthead text-center text-white">



            <div class="masthead-content">

                <!-- LOGIN TABLE CONTAINER -->

                <div class="col-lg-20">
                    <button type="button" class="btn btn-dark btn-xl" data-toggle="modal" data-target="#loginModal">
                        STAFF
                    </button>

                    <button type="button" class="btn btn-dark btn-xl" data-toggle="modal" data-target="#loginModal2" >
                        PATIENT
                    </button>
                </div>



            </div>
            <div class="bg-circle-1 bg-circle" ></div>
            <div class="bg-circle-2 bg-circle"></div>
            <div class="bg-circle-3 bg-circle"></div>
            <div class="bg-circle-4 bg-circle"></div>
        </header>

        <section>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <div class="p-5">
                            <img class="img-fluid rounded-circle" src="img/doctor2.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="p-5">
                            <h2 class="display-4">About our Doctors...</h2>
                            <p>Our doctors typically work very long hours and have to be available for emergencies. These hours are spent seeing patients in an office-based setting, running tests as well as interpreting them, prescribing medicine or treatments, doing rounds in the hospital, making notes on patient's physical conditions, advising patients on how to stay healthy and talking to them about further treatment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="p-5">
                            <img class="img-fluid rounded-circle" src="img/technician2.jpg" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <h2 class="display-4">About our Technicians...</h2>
                            <p>Healthcare technicians provide two levels of care, direct and indirect. Often, technicians are trained and qualified to complete specialty tasks and this varies depending on clinic needs. Healthcare technicians provide a key role in patient care and cleanliness of hospital units.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Footer -->
        <footer class="py-5 bg-black">
            <div class="container">
                <p class="m-0 text-center text-white small">Copyright &copy; NULLTeam.com</p>
            </div>
            <!-- /.container -->
        </footer>

        <!-- Bootstrap core JavaScript -->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>







        <script type="text/javascript">



            function displayForSelected(val){

                if (val == "d"){
                    document.getElementById("eduorequlabel").innerHTML = "Education";

                } else {
                    document.getElementById("eduorequlabel").innerHTML = "Equipment";
                }

            }




        </script>











    </body>

</html>
