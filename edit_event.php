<?php require_once "config.php"; 
session_start(); 
$username = $_SESSION['name'];

$editFormAction = $_SERVER['PHP_SELF'];


$events_redirectLoginSuccess = "dashboard.php";
$events_redirectLoginFailed = "index.php";

if(isset($_GET['eid'])) {
    $id = $_GET['eid'];  
    $view_query = "SELECT id, event_name, event_location, event_date, event_obj, event_coordinator, event_number, event_mail, event_budget FROM events_details WHERE id = ?";
    $view = $events_db->prepare($view_query);

    $view->bind_param("i", $id);
    $view->execute();

    $result = $view->get_result();

    if ($result->num_rows > 0) {
        $event_details = $result->fetch_assoc();
    }
  }

if(isset($_POST['submit'])) {
    $co_name = $_POST['co_name'];
    $co_num = $_POST['co_num'];
    $co_email = $_POST['co_email'];
    $event_name = $_POST['event_name'];
    $event_location = $_POST['event_location'];
    $event_date = $_POST['event_date'];
    $event_obj = $_POST['event_obj'];
    $event_bud = $_POST['event_bud'];

    $event_id = $_POST['event_id'];
    $updateQuery = "UPDATE events_details 
                    SET event_name = ?, event_location = ?, event_date = ?, event_obj = ?, event_coordinator = ?, event_number = ?, event_mail = ?, event_budget = ? 
                    WHERE id = ?"; 

    $update = $events_db->prepare($updateQuery);
    $update->bind_param("ssssssssi", $event_name, $event_location, $event_date, $event_obj, $co_name, $co_num, $co_email, $event_bud, $event_id);

    if ($update->execute()) {
        echo "Event updated successfully!";
        header("Location: " . $events_redirectLoginSuccess); 
        exit(); 
    } else {
        echo "Error: " . $update->error;
    }

    $update->close();
    $events_db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="css/style3.css" />
    <link rel="stylesheet" href="css/custom.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
    <style>
  .required:after {
    content:" *";
    color: red;
  }
</style>
  </head>

 <div id="wrapper" style="display:block;">
      <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
          <div class="container-fluid">
            <!-- Page Heading -->
            <div class="mb-4" style="display: inline-block;">
              <h1 class="h3 mb-0 text-warning text-gray-800"></h1>
            </div>
            <br/>
            <br/>
            <br/>
            <br/>
            <div style="float: right;">
                <a href="dashboard.php">
                  <button class="btn btn-danger">
                    <i class="fa fa-arrow-left"></i> Back </button>
                </a>
            </div>
        <br/>
        <div class="row">
              <!-- Area Chart -->
        <div class="col-xl-1 col-md-5 mb-4"> </div>
          <div class="col-xl-10 col-lg-8">
            <div class="card shadow mb-4" >
              <!-- Card Header - Dropdown -->
                <table border="0" cellspacing="0" cellpadding="0" style="width:50%">
              <tbody>
                <tr>
                  <td bgcolor="#004AAD">
                    <h3 class="textsizecss">EDIT EVENT</h3>
                  </td>
              </tr>
             </tbody>
           </table>

            <br/>
            <div class="card-body">
           <form name = "form" id="insert_form_action" action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" onsubmit="return validateEmail()">
   
             <div class="col-md-12">
               <div class="row">
                <div class="col-md-12 mt-3">
               <h4 style="color: black;">Early Notes: </h4>
                </div>

                   <div class="col-md-6">
                <label for="event_name" class="required">Event Name</label>
                <input type="text" name="event_name" class="form-control" id="event_name" placeholder="Event Name" required  value="<?php echo $event_details['event_name']; ?>"/>
              </div>

              <div class="col-md-6">
                <label for="event_location" class="required">Event Location</label>
                <input type="text" name="event_location" class="form-control" id="event_location" placeholder="Event Location" required value="<?php echo $event_details['event_location']; ?>"/>
              </div>

              <div class="col-md-6 mt-3">
                <label for="event_date" class="required">Event Date</label>
                <input type="datetime-local" name="event_date" class="form-control" id="event_date" required value="<?php echo $event_details['event_date']; ?>"/>
              </div>

             <div class="col-md-6 mt-3">
                <label for="event_obj">Event Objective</label>
                <input type="text" name="event_obj" class="form-control" id="event_obj" placeholder="Event Objective" value="<?php echo $event_details['event_obj']; ?>">
              </div>

              <div class="col-md-6 mt-3">
                <label for="event_bud" class="required">Event Budget</label>
                <input type="text" name="event_bud" class="form-control" id="event_bud" placeholder="Event Budget" maxlength="10" required oninput="validatePhoneNumber(this)" value="<?php echo $event_details['event_budget']; ?>"/>
                <input type="hidden" name="event_id" id="event_id" value="<?php echo $_GET['eid']; ?>">
              </div>

                <div class="col-md-12 mt-3">
               <h4 style="color: black;">Early Event Planning:</h4>
                </div>

                 <div class="col-md-6">
                <label for="co_name" class="required">Event Coordinator</label>
                <input type="text" name="co_name" class="form-control"id="co_name" placeholder="Event Coordinator" value="<?php echo $event_details['event_name']; ?>" required/>
              </div>

              <div class="col-md-6">
                <label for="co_num" class="required">Contact Phone Number</label>
                <input type="tel" name="co_num" class="form-control" id="co_num" placeholder="Contact Phone Number" maxlength="10" required oninput="validatePhoneNumber(this)" value="<?php echo $event_details['event_number']; ?>"/>
              </div>

              <div class="col-md-6 mt-3">
                <label for="co_email">Email</label>
                <input type="email" name="co_email" class="form-control" id="co_email" placeholder="Email" value="<?php echo $event_details['event_mail']; ?>"/>
              <span id="email-error" style="color: red; display: none;">Please enter a valid email address.</span>
              </div>
           
              
              <div class="text-center col-md-12 mt-3">
                <button type="submit" name="submit" class="btn btn-warning">Submit</button>
               </div>
               <div class="success-message" id="successMessage"></div>
              </div>
            </div>
          </div>
         
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const form = document.getElementById('insert_form_action');
const action = document.getElementById('action');
  function validatePhoneNumber(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
  }

  function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                errorMessage.style.color = 'red';
                errorMessage.style.fontSize = '17px';

                successMessage.style.display = 'none';
            }

            function showSuccess(message) {
                successMessage.textContent = message;
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';
            }
  function validateEmail() {
        var email = document.getElementById("co_email").value;
        var emailError = document.getElementById("email-error");
        var regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        
        if (regex.test(email)) {
            emailError.style.display = "none";
            return true;
        } else {
            emailError.style.display = "block";
            return false;
        }
    }
</script>
