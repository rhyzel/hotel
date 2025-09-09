<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>User Registration</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #1e293b;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-container {
     
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 400px;
      color: white;
    }
    .register-container h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
    }
    .register-container label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
    }
    .register-container input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
    }
    .register-container input:focus {
      outline: 2px solid #6366f1;
    }
    .register-container button {
      width: 100%;
      padding: 10px;
      background-color: #6366f1;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    .register-container button:hover {
      background-color: #4f46e5;
    }
    .register-container p {
      text-align: center;
      font-size: 13px;
      margin-top: 12px;
    }
    .register-container a {
      color: #93c5fd;
      text-decoration: none;
    }
    .register-container a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2>Create Account</h2>
    <form action="register.php" method="POST">
      <label for="fullname">Full Name</label>
      <input type="text" id="fullname" name="fullname" required>

      <label for="username">Username</label>
      <input type="text" id="username" name="username" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" 
             required minlength="6" 
             title="Password must be at least 6 characters">

      <label for="confirm">Confirm Password</label>
      <input type="password" id="confirm" name="confirm" 
             required minlength="6" 
             title="Please re-enter the same password">

      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>

</body>
</html>
