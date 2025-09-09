<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Hotel La Vista </title>
  <link rel="stylesheet" href="index.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <title>User Login</title>
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
    .login-container {
     
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 350px;
      color: white;
      text-align: center;
    }
    .login-container input,
    .login-container button {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      border: none;
      font-size: 14px;
    }
    .login-container button {
      background-color: #6366f1;
      color: white;
      cursor: pointer;
    }
    .login-container button:hover {
      background-color: #080808ff;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h2>User Login</h2>
    <!-- All users go to index.php after clicking Login -->
    <form action="index.php" method="get">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    <p style="color: white;">Don't have an account? <a href="register.php" style="color: gray; text-decoration: underline;">Sign Up</a></p>

    </form>

  </div>

</body>
</html>
