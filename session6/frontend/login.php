<?php
session_start();

// If already logged in, redirect to products
if (isset($_SESSION['user'])) {
    header('Location: product.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Load users from simple file-based storage
    $users_file = '../backend/users.json';
    $users = [];
    if (file_exists($users_file)) {
        $users = json_decode(file_get_contents($users_file), true) ?? [];
    }

    $found = false;
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $found = true;
            $_SESSION['user'] = [
                'username' => $user['username'],
                'email'    => $user['email'],
            ];
            // Also set a cookie (30 days)
            setcookie('remember_user', $user['username'], time() + 60 * 60 * 24 * 30, '/');
            header('Location: product.php');
            exit;
        }
    }

    if (!$found) {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0f0e0c;
    --surface: #1a1916;
    --border: #2e2c28;
    --gold: #c9a84c;
    --gold-light: #e8c96d;
    --text: #e8e4dc;
    --muted: #7a756a;
    --error: #e05454;
    --radius: 4px;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-image:
      radial-gradient(ellipse 60% 40% at 70% 20%, rgba(201,168,76,0.08) 0%, transparent 60%),
      radial-gradient(ellipse 50% 30% at 20% 80%, rgba(201,168,76,0.05) 0%, transparent 50%);
  }

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 48px 44px;
    width: 100%;
    max-width: 420px;
    animation: fadeUp 0.5s ease both;
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .logo {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: var(--gold);
    letter-spacing: 0.04em;
    margin-bottom: 8px;
  }

  .subtitle {
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 36px;
    letter-spacing: 0.03em;
  }

  .field { margin-bottom: 20px; }

  label {
    display: block;
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 8px;
  }

  input[type=text], input[type=password], input[type=email] {
    width: 100%;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 11px 14px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--text);
    transition: border-color 0.2s;
    outline: none;
  }

  input:focus {
    border-color: var(--gold);
  }

  input::placeholder { color: var(--muted); }

  .error {
    background: rgba(224,84,84,0.1);
    border: 1px solid rgba(224,84,84,0.3);
    color: var(--error);
    padding: 10px 14px;
    border-radius: var(--radius);
    font-size: 13px;
    margin-bottom: 20px;
  }

  .btn {
    width: 100%;
    background: var(--gold);
    color: #0f0e0c;
    border: none;
    border-radius: var(--radius);
    padding: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    margin-top: 8px;
  }

  .btn:hover { background: var(--gold-light); }
  .btn:active { transform: scale(0.99); }

  .footer {
    text-align: center;
    margin-top: 28px;
    font-size: 13px;
    color: var(--muted);
  }

  .footer a {
    color: var(--gold);
    text-decoration: none;
    font-weight: 500;
  }

  .footer a:hover { text-decoration: underline; }

  .divider {
    height: 1px;
    background: var(--border);
    margin: 32px 0;
  }
</style>
</head>
<body>
<div class="card">
  <div class="logo">Login form</div>
  <div class="subtitle">Sign in to continue</div>

  <?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="field">
      <label>Username</label>
      <input type="text" name="username" placeholder="Enter Username" required autocomplete="username">
    </div>
    <div class="field">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter password" required autocomplete="current-password">
    </div>
    <button type="submit" class="btn">Login</button>
  </form>

  <div class="divider"></div>
  <div class="footer">Click here to <a href="register.php">Register</a></div>
</div>
</body>
</html>
