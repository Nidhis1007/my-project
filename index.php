<?php require_once "config.php"; ?>
<?php
if (!isset($_SESSION)) {
   session_start();
}

$_SESSION["id"]=NULL;
$_SESSION["name"]=NULL;
$_SESSION["email"]=NULL;
$_SESSION['last_activity'] =NULL;
unset($_SESSION["name"]);
unset($_SESSION["email"]);
unset($_SESSION["id"]);
unset($_SESSION["last_activity"]);


if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 3600) {
session_unset();
session_destroy();
header("Location: index.php");
}

$_SESSION['last_activity'] = time();

$events_redirectLoginSuccess = "dashboard.php";
$events_redirectLoginFailed = "index.php";


//Login Section 
if (isset($_POST['action']) && $_POST['action'] == "login") {
   
  $email=$_POST['email'];
  $pass=$_POST['password'];

  $Login_query = "SELECT id, name, email, pass, phone_number FROM events_users WHERE email = ?";

  $stmt = $events_db->prepare($Login_query);

  $stmt->bind_param("s", $email); 
  $stmt->execute();

  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();

      $pass = trim($pass); 
      $storedHash = trim($user['pass']);
    
        if (password_verify($pass, $storedHash)) {
            $_SESSION["id"] = $user['id'];
            $_SESSION["name"] = $user['name'];
            $_SESSION["email"] = $user['email'];
            header("Location: " . $events_redirectLoginSuccess);
            exit;
        } else {
            header("Location: " . $events_redirectLoginFailed);
            exit;
        }
  } else {
         header("Location: " . $events_redirectLoginFailed);
         exit;
  }

    $stmt->close();

    $events_db->close();

    }


//Save Section
    if (isset($_POST['action']) && $_POST['action'] == "save") {
        $uname=$_POST['name'];
        $email=$_POST['email'];
        $pass=$_POST['password'];
        $cpassword = $_POST['cpassword'];
        $phonenumber=$_POST['phonenumber'];

        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        $insertQuery ="INSERT into events_users(name, email, pass, phone_number) VALUES (?, ?, ?, ?)";

        $stmt = $events_db->prepare($insertQuery);

        $stmt->bind_param("ssss", $uname, $email, $hashed_password, $phone_number);

        if ($stmt->execute()) {
            echo "User registered successfully!";
            header("Location: ". $events_redirectLoginFailed);
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $events_db->close();

    }


//ALl Event details

    $Login_query = "SELECT event_name, event_location FROM events_details";

    $stmt = $events_db->prepare($Login_query);

    $stmt->execute();
    
    $result = $stmt->get_result();


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<style type="text/css">
    .event-card {
    background-color: #f8f9fa; 
    border: 1px solid #ddd; 
    border-radius: 8px; 
    padding: 15px;
    margin: 10px 0; 
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    }

    .event-info {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .event-name {
        font-size: 20px;
        color: #007bff;
        margin-right: 10px;
    }

    .event-location {
        font-size: 16px;
        color: #555;
    }

    .location-label {
        font-weight: bold;
        color: #333;
    }

    .location {
        color: #007bff; 
        font-style: italic;
    }

    .event-number {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 50%;
        margin-right: 10px;
        font-weight: bold;
    }
    .scroll-container {
        max-height: 400px; 
        width: 103%;              
        max-width: 1000px;      
        overflow-x: auto;       
        margin: 0 auto;       
        white-space: nowrap;   
        border: 1px solid #ccc; 
        border-radius: 15px;   
        padding: 10px;         
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .content {
        display: inline-block; 
        width: 200px;           
        margin: 10px;        
        background-color: lightblue; 
        text-align: center;
        border-radius: 10px;
        padding: 20px;         
    }
    .error-message{
        color:red;
    }
</style>
<body>

<div class="auth-container">
    <div class="auth-sidebar">
        <div>
            <h2>Welcome Back!</h2>
            <div class="scroll-container">
                <?php if ($result->num_rows > 0) { 
                    $rowCount = 1; 
                    while ($user = $result->fetch_assoc()) { ?>
                        <div class="event-card">
                            <div class="event-info">
                                <span class="event-name"><?php echo htmlspecialchars($user['event_name']); ?></span>
                            </div>
                            <div class="event-location">
                                <span class="location-label">Location:</span>
                                <span class="location"><?php echo htmlspecialchars($user['event_location']); ?></span>
                            </div>
                        </div>
                    <?php } 
                } else {
                    echo "No events found.";
                } ?>
            </div>
        </div>
    </div>

    <div class="auth-main">
        <form class="auth-form" id="authForm" method="POST">
            <div class="form-header">
                <h1>Sign In</h1>
            </div>
            <br/>

            <div class="form-group" id="uname" style="display:none;">
                <i class="fas fa-user input-icon"></i>
                <input type="name" class="form-control" name="name" id="name" placeholder="Name" required>
            </div>

            <div class="form-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email address" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock input-icon" style="top:39%"></i>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                <i class="far fa-eye password-toggle" style="top:39%" id="passwordToggle"></i>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="strength-text" id="strengthText"></div>
            </div>

            <div class="form-group" id="ucpassword" style="display:none;">
                <i class="fas fa-lock input-icon" style="top:39%"></i>
                <input type="cpassword" class="form-control" name="cpassword" id="cpassword" placeholder="Confirm Password" required>
                <i class="far fa-eye password-toggle" style="top:39%" id="passwordToggle"></i>
                <div class="password-strength">
                    <div class="password-strength-bar" id="cstrengthBar"></div>
                </div>
                <div class="strength-text" id="cstrengthText"></div>
            </div>

            <div class="form-group" id="phone" style="display:none;">
                <i class="fa fa-phone input-icon"></i>
                <input type="tel" class="form-control" name="phonenumber" id="phonenumber" placeholder="Phone Number" maxlength="10" required oninput="validatePhoneNumber(this)">
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span>Sign In</span>
                <div class="loader">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <input type="hidden" name="action" id="action" value="">
            </button>

            <div class="error-message" id="errorMessage"></div>
        
            <div class="success-message" id="successMessage"></div>

            <div class="switch-form">
                Don't have an account<a href="#" onclick="toggleForm()"> Sign Up</a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
<script>
    const submitBtn = document.getElementById('submitBtn');
    const action = document.getElementById('action');
    const header1 = document.querySelector('.form-header h1');

    submitBtn.addEventListener('click', () => {
            const uisSignUp = header1.textContent === 'Sign In';
            if(uisSignUp){
                action.value = "login";

            } else{
                action.value = "save";
            }
            document.getElementById("authForm").submit();
    });

    /*const passwordInput = document.getElementById('password');

    passwordInput.addEventListener('click', () => {
    console.log(header1.textContent);
        if (header1.textContent != 'Sign In') {
                    const passwordTooltip = document.createElement('div');
                        passwordTooltip.className = 'password-tooltip';
                        passwordTooltip.style.cssText = `
                        position: absolute;
                        background: white;
                        padding: 10px;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                        display: none;
                        z-index: 100;
                        width: 200px;
                        font-size: 0.9em;
                        color: #6b7280;
                    `;
                    passwordTooltip.innerHTML = `
                    Password must contain:<br>
                    - At least 8 characters<br>
                    - Upper & lowercase letters<br>
                    - Numbers<br>
                    - Special characters
                `;

                passwordInput.parentElement.appendChild(passwordTooltip);

                passwordInput.addEventListener('focus', () => {
                passwordTooltip.style.display = 'block';
            });

            passwordInput.addEventListener('blur', () => {
                passwordTooltip.style.display = 'none';
            });
        }
    });*/


document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('authForm');
            const passwordInput = document.getElementById('password');
            const cpasswordInput = document.getElementById('cpassword');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const cstrengthBar = document.getElementById('cstrengthBar');
            const cstrengthText = document.getElementById('cstrengthText');
            const passwordToggle = document.getElementById('passwordToggle');
            const submitBtn = document.getElementById('submitBtn');
            const uheader = document.querySelector('.form-header h1');
            const uisSignUp = uheader.textContent === 'Sign Up';
            const ucpassword = document.getElementById('ucpassword');
            const uname = document.getElementById('uname');
            const phone = document.getElementById('phone');
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("cpassword").value;
            var errorMessage = document.getElementById("errorMessage");


            passwordToggle.addEventListener('click', () => {
                const type = passwordInput.type === 'password' ? 'text' : 'password';
                passwordInput.type = type;
                passwordToggle.className = `far ${type === 'password' ? 'fa-eye' : 'fa-eye-slash'} password-toggle`;
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const email = document.getElementById('email').value;
                const password = passwordInput.value;

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    showError('Please enter a valid email address');
                    return;
                }

                if (password.length < 8) {
                    showError('Password must be at least 8 characters long');
                    return;
                }

                if (confirmPassword !== "" && password !== confirmPassword) {
                    errorMessage.textContent = "Passwords do not match!";
                    errorMessage.style.display = "block";
                    return false;
                }else{
                    errorMessage.style.display = "none"; 
                    //action.value = "save";
                    document.getElementById("authForm").submit();
    
                    submitBtn.disabled = true;
                    submitBtn.querySelector('span').style.opacity = '0';
                    submitBtn.querySelector('.loader').style.display = 'block';

                }
                });
            

            window.toggleForm = () => {
                const header = document.querySelector('.form-header h1');
                const switchText = document.querySelector('.switch-form');
                const submitBtn = document.querySelector('.submit-btn span');
                const isSignUp = header.textContent === 'Sign In';

                document.querySelector('.auth-form').classList.add('fade-in');
                
                if (isSignUp) {
                    header.textContent = 'Sign Up';
                    submitBtn.textContent = 'Create Account';
                    switchText.innerHTML = 'Already have an account? <a href="#" onclick="toggleForm()">Sign In</a>';
                    ucpassword.style.display = 'block';
                    uname.style.display = 'block';
                    phone.style.display = 'block';

                    passwordInput.addEventListener('input', () => {
                        const password = passwordInput.value;
                        let strength = 0;
                        let message = '';

                        if (password.length >= 8) strength += 25;
                        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
                        if (password.match(/\d/)) strength += 25;
                        if (password.match(/[^a-zA-Z\d]/)) strength += 25;

                        strengthBar.style.width = `${strength}%`;
                        
                        if (strength <= 25) {
                            strengthBar.style.backgroundColor = '#ef4444';
                            message = 'Weak';
                        } else if (strength <= 50) {
                            strengthBar.style.backgroundColor = '#f97316';
                            message = 'Fair';
                        } else if (strength <= 75) {
                            strengthBar.style.backgroundColor = '#22c55e';
                            message = 'Good';
                        } else {
                            strengthBar.style.backgroundColor = '#15803d';
                            message = 'Strong';
                        }

                        strengthText.textContent = `Password Strength: ${message}`;
                    });

                    cpasswordInput.addEventListener('input', () => { 
                        const cpassword = cpasswordInput.value;
                        let strength = 0;
                        let message = '';

                        if (cpassword.length >= 8) strength += 25;
                        if (cpassword.match(/[a-z]/) && cpassword.match(/[A-Z]/)) strength += 25;
                        if (cpassword.match(/\d/)) strength += 25;
                        if (cpassword.match(/[^a-zA-Z\d]/)) strength += 25;

                        cstrengthBar.style.width = `${strength}%`;
                        
                        if (strength <= 25) {
                            cstrengthBar.style.backgroundColor = '#ef4444';
                            message = 'Weak';
                        } else if (strength <= 50) {
                            cstrengthBar.style.backgroundColor = '#f97316';
                            message = 'Fair';
                        } else if (strength <= 75) {
                            cstrengthBar.style.backgroundColor = '#22c55e';
                            message = 'Good';
                        } else {
                            cstrengthBar.style.backgroundColor = '#15803d';
                            message = 'Strong';
                        }

                        cstrengthText.textContent = `Password Strength: ${message}`;
                    });

                } else {
                    header.textContent = 'Sign In';
                    submitBtn.textContent = 'Sign In';
                    switchText.innerHTML = 'Don\'t have an account? <a href="#" onclick="toggleForm()">Sign Up</a>';
                    ucpassword.style.display = 'none';
                    uname.style.display = 'none';
                    phone.style.display = 'none';
                }

                form.reset();
                strengthBar.style.width = '0';
                strengthText.textContent = '';
                errorMessage.style.display = 'none';
                successMessage.style.display = 'none';
            };
                 
            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                successMessage.style.display = 'none';
                form.classList.add('shake');
                setTimeout(() => form.classList.remove('shake'), 400);
            }

            function showSuccess(message) {
                successMessage.textContent = message;
                successMessage.style.display = 'block';
                errorMessage.style.display = 'none';
            }

        });

</script>
<script>
  function validatePhoneNumber(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
  }
</script>

</body>
</html>
