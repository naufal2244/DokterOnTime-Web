<?php
require_once('../config/autoload.php');
require_once('./includes/path.inc.php');
include('../helper/select_helper.php');
ob_start(); // Mulai output buffering

// Cek login
if ($_SESSION["loggedin"] != 1) {
    header("Location: register.php");
    exit();
}

$sess_email = $_SESSION["sess_clinicadminemail"];
$result1 = mysqli_query($conn, "SELECT * FROM clinic_manager WHERE clinicadmin_email = '" . $sess_email . "' ");
$row1 = mysqli_fetch_assoc($result1);
$clinic_id = $row1["clinic_id"];


$result = mysqli_query($conn, "SELECT * FROM clinics WHERE clinic_id = '" . $clinic_id . "' ");
$row = mysqli_fetch_assoc($result);

// Tangani POST request setelah semua pemrosesan di atas
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = "0";
    $clinic_name = escape_input($_POST["inputClinicName"]);

    $weekopen = escape_input($_POST["inputOpensHourWeek"]);
    $weekclose = escape_input($_POST["inputCloseHourWeek"]);
    $satopen = escape_input($_POST["inputOpensHourSat"]);
    $satclose = escape_input($_POST["inputCloseHourSat"]);
    $sunopen = escape_input($_POST["inputOpensHourSun"]);
    $sunclose = escape_input($_POST["inputCloseHourSun"]);

    $contact = escape_input($_POST["inputContact"]);
    $email = escape_input($_POST["inputEmailAddress"]);
    $url = escape_input($_POST["inputURL"]);
    $address = escape_input($_POST["inputAddress"]);
    $city = escape_input($_POST["inputCity"]);

    if (!empty($_POST['inputState'])) {
        $state = $_POST['inputState'];
    } else {
        $state = "";
    }
    $zipcode = escape_input($_POST["inputZipCode"]);

    // Check Email Valid
    $clinicstmt = $conn->prepare("SELECT * FROM clinics WHERE clinic_email = ?");
    $clinicstmt->bind_param("s", $email);
    $clinicstmt->execute();
    $clinicresult = $clinicstmt->get_result();

    if ($clinicresult->num_rows != 0) {
        echo "<script>Swal.fire({title: 'Error!', text: 'Email Already Exist', type: 'error', confirmButtonText: 'Try Again'})</script>";
        exit();
    }

    $updatestmt = $conn->prepare("UPDATE clinics SET clinic_name = ?, clinic_email = ?, clinic_url = ?, clinic_contact = ?, clinic_address = ?, clinic_city = ?, clinic_state = ?, clinic_zipcode = ?, clinic_status = ? WHERE clinic_id = ?");
    $updatestmt->bind_param("ssssssssss", $clinic_name, $email, $url, $contact, $address, $city, $state, $zipcode, $status, $clinic_id);

    if ($updatestmt->execute()) {
        // Insert or update business hours for Monday to Friday
        for ($day_id = 1; $day_id <= 5; $day_id++) {
            $businessstmt = $conn->prepare("INSERT INTO business_hour (clinic_id, days_id, open_time, close_time) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE open_time = VALUES(open_time), close_time = VALUES(close_time)");
            $businessstmt->bind_param("iiss", $clinic_id, $day_id, $weekopen, $weekclose);
            $businessstmt->execute();
        }

        // Insert or update business hours for Saturday
        $day_id = 6;
        $businessstmt = $conn->prepare("INSERT INTO business_hour (clinic_id, days_id, open_time, close_time) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE open_time = VALUES(open_time), close_time = VALUES(close_time)");
        $businessstmt->bind_param("iiss", $clinic_id, $day_id, $satopen, $satclose);
        $businessstmt->execute();

        // Insert or update business hours for Sunday
        $day_id = 7;
        $businessstmt = $conn->prepare("INSERT INTO business_hour (clinic_id, days_id, open_time, close_time) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE open_time = VALUES(open_time), close_time = VALUES(close_time)");
        $businessstmt->bind_param("iiss", $clinic_id, $day_id, $sunopen, $sunclose);
        $businessstmt->execute();

        ob_end_clean(); // Pastikan buffer output kosong sebelum header
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $updatestmt->error;
    }

    $clinicstmt->close();
    $businessstmt->close();
    $updatestmt->close();
    mysqli_close($conn);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include CSS_PATH; ?>
    <link rel="stylesheet" href="../assets/css/clinic/style.css">
</head>
<body>
    <div class="container">
        <div class="title text-center mt-5">
            <h3><a href="login.php"><?php echo $BRAND_NAME; ?></a></h3>
        </div>
        <form name="registerForm" id="registerForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <ul class="timeline mb-5" id="timeline">
                <!-- Timeline Steps -->
                <li class="li">
                    <div class="timestamp">
                        <span class="frame">Step 1</span>
                    </div>
                    <div class="status">
                        <h4>Detail</h4>
                    </div>
                </li>
                <li class="li">
                    <div class="timestamp">
                        <span class="frame">Step 2</span>
                    </div>
                    <div class="status">
                        <h4>Contact</h4>
                    </div>
                </li>
                <li class="li">
                    <div class="timestamp">
                        <span class="frame">Step 3</span>
                    </div>
                    <div class="status">
                        <h4>Location</h4>
                    </div>
                </li>
            </ul>

            <div class="register-wrap">
                <!-- Details -->
                <div class="tab">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputClinicName">Clinic Name</label>
                            <input type="text" name="inputClinicName" class="form-control input" id="inputClinicName" placeholder="Enter Name">
                        </div>
                    </div>
                    <label for="inputBusinessHour">Business Hour</label>
                    <div class="mb-3">
                        <small class="text-muted">When you're closed on a certain day, just leave the hours blank.</small>
                        
                    </div>
                    <div class="form-group row">
                        <label for="inputBusinessHourWeek" class="col-sm-2 col-form-label text-right">Monday - Friday</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control timepicker" name="inputOpensHourWeek">
                        </div><span>--</span>
                        <div class="col-sm-4">
                            <input type="text" class="form-control timepicker" name="inputCloseHourWeek">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputBusinessHourSat" class="col-sm-2 col-form-label text-right">Saturday</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control timepicker" name="inputOpensHourSat">
                        </div><span>--</span>
                        <div class="col-sm-4">
                            <input type="text" class="form-control timepicker" name="inputCloseHourSat">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputBusinessHourSun" class="col-sm-2 col-form-label text-right">Sunday</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control timepicker" name="inputOpensHourSun">
                        </div><span>--</span>
                        <div class="col-sm-4">
                            <input type="text" class="form-control timepicker" name="inputCloseHourSun">
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="tab">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputContact">Contact Number</label>
                            <input type="text" name="inputContact" class="form-control input" id="inputContact" placeholder="Enter Phone Number">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputEmailAddress">Email Address*</label>
                            <input type="text" name="inputEmailAddress" class="form-control input" id="inputEmailAddress" placeholder="Enter Email Address">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="inputURL">URL Link</label>
                            <input type="text" name="inputURL" class="form-control input" id="inputURL" placeholder="Enter URL">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="tab">
                    <div class="form-group">
                        <label for="inputAddress">Address</label>
                        <input type="text" name="inputAddress" class="form-control input" id="inputAddress" onfocus="geolocate()" oninput="map_marker()" placeholder="1234 Main St">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="inputCity">City</label>
                            <input type="text" name="inputCity" class="form-control input" id="inputCity" oninput="map_marker()">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inputState">State</label>
                            <select name="inputState" id="inputState" class="form-control input" onblur="map_marker()">
                                <option value="" selected disabled>Choose</option>
                                <?php foreach ($select_state as $state_value) {
                                    echo '<option value="' . $state_value . '">' . $state_value . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="inputZipCode">Zip Code</label>
                            <input type="text" name="inputZipCode" class="form-control input" id="inputZipCode" oninput="map_marker()">
                        </div>
                    </div>
                    <div class="form-group map-container">
                        <script>
                        function map_marker()
                            {
                                var street = document.getElementById("inputAddress").value;
                                var city = document.getElementById("inputCity").value;
                                var state = document.getElementById("inputState").value;
                                var country = "Malaysia";
                                var zipcode = document.getElementById("inputZipCode").value;
                                var address = ""+city+" "+state+" "+country+"";
                                var q = encodeURIComponent(address);
                                document.getElementById("map").innerHTML = "<iframe width='100%' height='450' frameborder='0' style='border:0' src='https://www.google.com/maps/embed/v1/place?key=AIzaSyAGx-OjyNn10KsJ_OsE7cl2_qxg6mNBZyI&q="+street+","+city+","+state+","+zipcode+"+Malaysia' allowfullscreen></iframe>";
                            }
                        </script>
                        <div id="map"></div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div class="row">
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-block" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-primary btn-block" id="nextBtn" onclick="nextPrev(1)">Next</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php include JS_PATH; ?>
    <script>
        $('#upload').change(function() {
            $('#preview').html("");
            var totalFile = document.getElementById("inputGroupFileImage").files.length;

            for (var i=0;i<totalFile;i++) {
                $('#preview').append("<img src='"+URL.createObjectURL(event.target.files[i])+"'>");
            }
        });
    </script>
    <script>
        var currentTab = 0; // Current tab is set to be the first tab (0)
        showTab(currentTab); // Display the current tab

        function showTab(n) {
            // This function will display the specified tab of the form ...
            var x = document.getElementsByClassName("tab");
            x[n].style.display = "block";
            // ... and fix the Previous/Next buttons:
            if (n == 0) {
                document.getElementById("prevBtn").style.display = "none";
            } else {
                document.getElementById("prevBtn").style.display = "inline";
            }
            if (n == (x.length - 1)) {
                document.getElementById("nextBtn").innerHTML = "Submit";
            } else {
                document.getElementById("nextBtn").innerHTML = "Next";
            }
            // ... and run a function that displays the correct step indicator:
            fixStepIndicator(n)
        }

        function nextPrev(n) {
            // This function will figure out which tab to display
            var x = document.getElementsByClassName("tab");
            // Exit the function if any field in the current tab is invalid:
            if (n == 1 && !validateForm()) return false;
            // Hide the current tab:
            x[currentTab].style.display = "none";
            // Increase or decrease the current tab by 1:
            currentTab = currentTab + n;
            // if you have reached the end of the form... :
            if (currentTab >= x.length) {
                //...the form gets submitted:
                document.getElementById("registerForm").submit();
                return false;
            }
            // Otherwise, display the correct tab:
            showTab(currentTab);
        }

        function validateForm() {
            // This function deals with validation of the form fields
            var x, y, i, valid = true;
            x = document.getElementsByClassName("tab");
            y = x[currentTab].getElementsByClassName("input")
            // A loop that checks every input field in the current tab:
            for (i = 0; i < y.length; i++) {
                // If a field is empty...
                if (y[i].value == "") {
                    // add an "invalid" class to the field:
                    y[i].className += " invalid";
                    // and set the current valid status to false:
                    valid = false;
                }
            }
            // If the valid status is true, mark the step as finished and valid:
            if (valid) {
                document.getElementsByClassName("li")[currentTab].className += " complete";
            }
            return valid; // return the valid status
        }

        function fixStepIndicator(n) {
            // This function removes the "active" class of all steps...
            var i, x = document.getElementsByClassName("li");
            for (i = 0; i < x.length; i++) {
                x[i].className = x[i].className.replace(" active", "");
            }
            //... and adds the "active" class to the current step:
            x[n].className += " active";
        }
    </script>
    <script>
        $(function () {
            $('.timepicker').datetimepicker({
                format: 'HH:mm'
            });
        });
    </script>

    <script>
    var placeSearch, autocomplete;
    var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
    };

    function initAutocomplete() {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */
            (document.getElementById('autocomplete')), {
                types: ['geocode']
            });

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
    }

    function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        for (var component in componentForm) {
            document.getElementById(component).value = '';
            document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(addressType).value = val;
            }
        }
    }

    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCfDjL3bKUl1fLdby_vhWimMejbVecejpc&libraries=places&callback=initAutocomplete" async defer></script>

</body>
</html>
