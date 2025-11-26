
<?php
include("db_connect.php");
$message = "";


if (isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    
    $check = "SELECT * FROM manager WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "User already exists! Please try logging in.";
    } else {
        $sql = "INSERT INTO manager (fullname, email, username, password)
                VALUES ('$fullname', '$email', '$username', '$password')";
        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful! You can now log in.";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}


if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check_user = "SELECT * FROM manager WHERE username='$username'";
    $user_result = mysqli_query($conn, $check_user);

    if (mysqli_num_rows($user_result) == 0) {
        $message = "User not found! Please register first.";
    } else {
        $sql = "SELECT * FROM manager WHERE username='$username' AND password='$password'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
           
            header("Location: enterbook.php");
            exit(); 
        } else {
            $message = "Incorrect password! Please try again.";
        }
    }
}


if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    $sql = "SELECT * FROM manager WHERE username='$username' AND password='$old_password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $update_sql = "UPDATE manager SET password='$new_password' WHERE username='$username'";
        if (mysqli_query($conn, $update_sql)) {
            $message = "Password updated successfully!";
        } else {
            $message = "Error updating password: " . mysqli_error($conn);
        }
    } else {
        $message = "Invalid username or old password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Portal</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <div class="container">
    <h2>Library Manager Portal</h2>
    <div class="tab-buttons">
      <button class="tab-btn active" id="loginBtn">Login</button>
      <button class="tab-btn" id="registerBtn">Register</button>
      <button class="tab-btn" id="updateBtn">Update Password</button>
    </div>

    <?php if (!empty($message)) : ?>
      <p style="color: yellow; font-weight: 500; margin-bottom: 10px;"><?php echo $message; ?></p>
    <?php endif; ?>

    <div class="form-box">
     
      <form id="loginForm" class="form active" method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
      </form>

    
      <form id="registerForm" class="form" method="POST" action="">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
      </form>

      
      <form id="updateForm" class="form" method="POST" action="">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="old_password" placeholder="Enter Old Password" required>
        <input type="password" name="new_password" placeholder="Enter New Password" required>
        <button type="submit" name="update">Update Password</button>
      </form>
    </div>
  </div>

  <script>
    const loginBtn = document.getElementById("loginBtn");
    const registerBtn = document.getElementById("registerBtn");
    const updateBtn = document.getElementById("updateBtn");
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    const updateForm = document.getElementById("updateForm");

    loginBtn.addEventListener("click", () => {
      loginBtn.classList.add("active");
      registerBtn.classList.remove("active");
      updateBtn.classList.remove("active");
      loginForm.classList.add("active");
      registerForm.classList.remove("active");
      updateForm.classList.remove("active");
    });

    registerBtn.addEventListener("click", () => {
      registerBtn.classList.add("active");
      loginBtn.classList.remove("active");
      updateBtn.classList.remove("active");
      registerForm.classList.add("active");
      loginForm.classList.remove("active");
      updateForm.classList.remove("active");
    });

    updateBtn.addEventListener("click", () => {
      updateBtn.classList.add("active");
      loginBtn.classList.remove("active");
      registerBtn.classList.remove("active");
      updateForm.classList.add("active");
      loginForm.classList.remove("active");
      registerForm.classList.remove("active");
    });
  </script>
</body>
</html>
