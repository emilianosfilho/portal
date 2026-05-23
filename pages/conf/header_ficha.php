<header>
  <nav class="navbar navbar-expand-md fixed-top bg-portal">
    <div class="container-fluid">

      <?php if (isset($_SESSION['login'])): ?>
      <a href="index.php" class="ms-5"><img src="<?=@DIR_IMG."logo_header.png"?>" height="40px"></a>
      <?php else: ?>
      <a href="#" class="ms-5"><img src="<?=@DIR_IMG."logo_header.png"?>" height="40px"></a>
      <?php endif; ?>

    </div>
  </nav>
</header>