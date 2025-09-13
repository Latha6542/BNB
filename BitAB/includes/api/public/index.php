<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FundFlow - Budget Explorer</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>FundFlow — Budget Explorer</h1>
    <p>Track flows from budget → departments → projects → vendors. Each transaction has a traceable ID.</p>
  </header>

  <main>
    <div class="card">
      <h3>Recent Transactions</h3>
      <p>No transactions yet. <a href="login.php" class="btn">Login</a> to manage budgets.</p>
    </div>

    <div class="card">
      <h3>About FundFlow</h3>
      <p>FundFlow helps admins and departments easily track and manage budgets, ensuring transparency and accountability.</p>
    </div>
  </main>

  <footer>
    &copy; <?php echo date("Y"); ?> FundFlow. All rights reserved.
  </footer>
</body>
</html>
