<?php
require_once('pages/conf/function.php');
require_once('pages/conf/controller.php');
foreach ($_SESSION as $key => $value) {
  if ($key <> "login") {
    unset($_SESSION[$key]);
  }
}
?>
<main>

  <div class="album py-5 bg-light">
    <div class="container">

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mt-5">
        
        <div class="col">
          <div class="card shadow-sm">
            <img src="<?=@DIR_IMG."img-cliente-03.jpg"?>">
          </div>
        </div>
        
        <div class="col">
          <div class="card shadow-sm">
            <img src="<?=@DIR_IMG."img-cliente-02.jpg"?>">
          </div>
        </div>
        
        <div class="col">
          <div class="card shadow-sm">
            <img src="<?=@DIR_IMG."img-cliente-01.jpg"?>">
          </div>
        </div>

      </div>
  
    </div>
  </div>

</main>