<?php
session_start();


if (!isset($_SESSION['user'])) {
    
    if (isset($_COOKIE['remember_user'])) {
        $users_file = '../backend/users.json';
        if (file_exists($users_file)) {
            $users = json_decode(file_get_contents($users_file), true) ?? [];
            foreach ($users as $user) {
                if ($user['username'] === $_COOKIE['remember_user']) {
                    $_SESSION['user'] = ['username' => $user['username'], 'email' => $user['email']];
                    break;
                }
            }
        }
    }

    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

$current_user = $_SESSION['user'];

// Load products
$products_file = '../backend/products.json';
$products = [];
if (file_exists($products_file)) {
    $products = json_decode(file_get_contents($products_file), true) ?? [];
}

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie('remember_user', '', time() - 3600, '/');
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Products</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0f0e0c;
    --surface: #1a1916;
    --surface2: #201e1a;
    --border: #2e2c28;
    --gold: #c9a84c;
    --gold-light: #e8c96d;
    --text: #e8e4dc;
    --muted: #7a756a;
    --radius: 6px;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    background-image:
      radial-gradient(ellipse 80% 40% at 50% 0%, rgba(201,168,76,0.06) 0%, transparent 60%);
  }

  /* NAV */
  nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 48px;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: rgba(15,14,12,0.9);
    backdrop-filter: blur(10px);
    z-index: 10;
  }

  .nav-brand {
    font-family: 'Playfair Display', serif;
    font-size: 20px;
    color: var(--gold);
    letter-spacing: 0.04em;
  }

  .nav-user {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 14px;
    color: var(--muted);
  }

  .nav-user strong { color: var(--text); }

  .logout-btn {
    background: transparent;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    color: var(--muted);
    padding: 7px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    transition: border-color 0.2s, color 0.2s;
  }

  .logout-btn:hover { border-color: var(--gold); color: var(--gold); }

  /* MAIN */
  main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 56px 32px;
  }

  .page-header {
    text-align: center;
    margin-bottom: 52px;
  }

  .page-title {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: var(--text);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .page-title span { color: var(--gold); }

  .page-sub {
    font-size: 13px;
    color: var(--muted);
    letter-spacing: 0.08em;
    text-transform: uppercase;
  }

  /* GRID */
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 28px;
  }

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.25s, border-color 0.25s, box-shadow 0.25s;
    animation: fadeUp 0.5s ease both;
  }

  .card:hover {
    transform: translateY(-4px);
    border-color: var(--gold);
    box-shadow: 0 12px 40px rgba(201,168,76,0.1);
  }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .card:nth-child(2) { animation-delay: 0.1s; }
  .card:nth-child(3) { animation-delay: 0.2s; }
  .card:nth-child(4) { animation-delay: 0.3s; }

  .card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
    background: var(--surface2);
  }

  .card-img-placeholder {
    width: 100%;
    height: 200px;
    background: var(--surface2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 13px;
  }

  .card-body { padding: 22px 24px 24px; }

  .card-name {
    font-family: 'Playfair Display', serif;
    font-size: 18px;
    margin-bottom: 6px;
    color: var(--text);
  }

  .card-price {
    font-size: 22px;
    font-weight: 500;
    color: var(--gold);
    margin-bottom: 8px;
  }

  .card-category {
    font-size: 12px;
    color: var(--muted);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 18px;
  }

  .card-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--gold);
    text-decoration: none;
    font-weight: 500;
    letter-spacing: 0.04em;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s;
  }

  .card-link:hover { border-color: var(--gold); }
  .card-link::after { content: '→'; }

  .empty {
    text-align: center;
    color: var(--muted);
    padding: 80px 0;
    font-size: 16px;
  }
</style>
</head>
<body>

<nav>
  <div class="nav-brand">Shop</div>
  <div class="nav-user">
    Welcome, <strong><?= htmlspecialchars($current_user['username']) ?></strong>
    <a href="?logout=1" class="logout-btn">Logout</a>
  </div>
</nav>

<main>
  <div class="page-header">
    <div class="page-sub">Browse our collection</div>
    <div class="page-title">All <span>Products</span></div>
  </div>

  <?php if (empty($products)): ?>
  <div class="empty">No products available yet.</div>
  <?php else: ?>
  <div class="grid">
    <?php foreach ($products as $p): ?>
    <div class="card">
      <?php if (!empty($p['image'])): ?>
        <img class="card-img" src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
      <?php else: ?>
        <div class="card-img-placeholder">No image</div>
      <?php endif; ?>
      <div class="card-body">
        <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
        <div class="card-price"><?= number_format($p['price'], 1) ?></div>
        <div class="card-category">Category: <?= htmlspecialchars($p['category']) ?></div>
        <a href="#" class="card-link">View Details</a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>

</body>
</html>
