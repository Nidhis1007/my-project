<?php require_once "config.php"; 
session_start(); 
$username = $_SESSION['name'];


$Login_query = "SELECT id, event_name, event_location, event_date, event_obj, event_coordinator, event_number, event_mail, event_budget FROM events_details";

$stmt = $events_db->prepare($Login_query);

$stmt->execute();


// Get result
$result = $stmt->get_result();


$events_redirectLoginSuccess = "dashboard.php";
if(isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    $deleteQuery = "DELETE FROM events_details WHERE id = ?"; 

    $delete = $events_db->prepare($deleteQuery);
    $delete->bind_param("i", $event_id);

    if ($delete->execute()) {
        echo "Deleted successfully!";
        header("Location: " . $events_redirectLoginSuccess); 
        exit(); 
    } else {
        echo "Error: " . $delete->error;
    }

    $delete->close();
    $events_db->close();

}

if(isset($_GET['logout']) && $_GET['logout'] == 1) {
session_unset();
session_destroy();
header("Location: index.php");
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

    <link rel="stylesheet" href="css/style2.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
  </head>
  <style type="text/css">

    .submit-btn {
            text-decoration: none;
            color: #004AAD; 
            padding: 10px; 
            border: 0px solid black;
            border-radius: 5px;
    }

    .submit-btn:hover {
            color: white;
            background-color: #004AAD;
    }

    .modal {
        display: none; 
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0); 
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%; 
    }

    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-btn:hover,
    .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    input[type="text"], input[type="date"], textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
    }

    button[type="submit"] {
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
    }
    a.dashboard_css {
    text-decoration: none;
   }

  </style>
<script type="text/javascript">
  function deleteEvent(id)
    {
        var retVal = confirm("Are you sure to delete Events?");
        if (retVal === true){
            window.location.href = "dashboard.php?delete=" + id;
        }
    }
</script>
  <body>
    <section class="container" style="max-width:1200px">
    <div style="display: inline-flex;margin-left: 80%;">
        <a href="dashboard.php?logout=1" style="text-decoration: none;" class="submit-btn">Logout</a>
      </div>
        <form name="login_form" action="" method="POST">
            <div class="signup">
                <h2 class="form-title" id="signup"><span>Hi <?php echo strtoupper($username); ?>, Welcome Back! 👋</span></h2>
                </br>
                <button class="submit-btn btn-warning"><a href="add_event.php" class="submit-btn">Click Here to Add Events</a></button>
              </br>
            </div>
        </form>
    </br></br>
            <ul>
                <li style="color: red;list-style-type: none;"></li>
            </ul>

        <table id="example" class="table table-striped table-bordered" style="border-collapse: collapse;text-align: center;width:100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Event Name</th>
                    <th width="10%">Location</th>
                    <th width="10%">Date time:</th>
                    <th width="10%">Contact Number</th>
                    <th width="10%">Contact Mail</th>
                    <th width="10%">Edit</th>
                    <th width="10%">Delete</th>
                </tr>
            </thead>
            <tbody>
        <?php
        if ($result->num_rows > 0) {
            $rowCount = 1;
            while ($user = $result->fetch_assoc()) { ?>
                <tr>
                <td><?php echo $rowCount++; ?></td>
                <td><?php echo htmlspecialchars($user['event_name']); ?></td>
                <td><?php echo htmlspecialchars($user['event_location']); ?></td>
                <td><?php echo htmlspecialchars($user['event_date']); ?></td>
                <td><?php echo htmlspecialchars($user['event_number']); ?></td>
                <td><?php echo htmlspecialchars($user['event_mail']); ?></td>
                <td><a href='edit_event.php?eid=<?php echo $user['id']; ?>' class='btn btn-warning'>Edit</a></td>
                
                <td><a onclick='deleteEvent(<?php echo $user['id']; ?>)' href='#' class='btn btn-danger'>Delete</a></td>
                </tr> 
            <?php 
            }
        } else {
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>No records found.</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td></td>";  
            echo "<td></td>";
        }
        ?>
    </tbody>
        </table>
    </section>
  </body>

</html>
<script>
    $(document).ready(function() {
            var hCols = [3, 4];
            $('#example').DataTable({
                "dom": "<'row'<'col-sm-4'B><'col-sm-2'l><'col-sm-6'p<br/>i>>" + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12'p<br/>i>>",
                "paging": true,
                "autoWidth": true,
                "columnDefs": [{
                    "visible": false,
                    "targets": hCols
                }],
                "buttons": [{
                    extend: 'colvis',
                    collectionLayout: 'three-column',
                    text: function() {
                        var totCols = $('#example thead th').length;
                        var hiddenCols = hCols.length;
                        var shownCols = totCols - hiddenCols;
                        return 'Columns (' + shownCols + ' of ' + totCols + ')';
                    },
                    prefixButtons: [{
                        extend: 'colvisGroup',
                        text: 'Show all',
                        show: ':hidden'
                    }, {
                        extend: 'colvisRestore',
                        text: 'Restore'
                    }]
                },
                ,oLanguage: {
                oPaginate: {
                sNext: '<span class="pagination-default">&#x276f;</span>',
                sPrevious: '<span class="pagination-default">&#x276e;</span>'
            }
        }
                    ,"initComplete": function(settings, json) {
                        $('#example').on('column-visibility.dt', function(e, settings, column, state) {
                            var visCols = $('#example thead tr:first th').length;
                            //Below: The minus 2 because of the 2 extra buttons Show all and Restore
                            var tblCols = $('.dt-button-collection li[aria-controls=example] a').length - 2;
                            $('.buttons-colvis[aria-controls=example] span').html('Columns (' + visCols + ' of ' + tblCols + ')');
                            e.stopPropagation();
                        });
                    }
                });
            });
</script>
<script>
$(document).ready(function() {
    $('#example').DataTable({
        searching: true
    });
});

</script>