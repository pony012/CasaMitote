<?php
	include_once 'functions.php';
	print_r(BaseCtrl::getUser(9810,"testRoot"));
	print_r(BaseCtrl::getUser(1000,"testGerente"));
	print_r(BaseCtrl::getUser(1000,"testRoot"));
?>