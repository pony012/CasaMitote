<?php
	include_once 'header.php';
?>

<!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <h2>Heading</h2>
          <p><?php print_r(BaseCtrl::getUser(9810,"testRoot")); ?></p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div>
        <div class="col-md-4">
          <h2>Heading</h2>
          <p><?php print_r(BaseCtrl::getUser(1000,"testGerente")); ?></p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Heading</h2>
          <p><?php print_r(BaseCtrl::getUser(1000,"testRoot")); ?></p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div>
      </div>

      <hr>

<?php
    include_once 'footer.php';
?>