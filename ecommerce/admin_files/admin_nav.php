<h1>Book Store</h1>
<nav id="login">
  <?php if ($user == "Admin" || $user == "User") { ?>
    <?php if ($user == "Admin") { ?>
      <a href="index.php">Manage Books</a>
    <?php } ?>
    <a href="../logout.php" class="right">Logout</a>
  <?php } else { ?>
    <a href="index.php">Books</a>
    <a href="../register.php" class="right">Register</a>
    <a href="../login.php" class="right">Login</a>
    <a href="index.php" class="right">Admin</a>
  <?php } ?>
</nav>