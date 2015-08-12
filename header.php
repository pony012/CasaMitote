<?php
    include_once 'functions.php';
    session_start();
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css">
        <!--<link rel="stylesheet" href="css/main.css">-->

    </head>
    <body>
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script src="functions.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Casa Mitote</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
<?php
    if(isset($_SESSION['data']['User']['idUsuario'])){
?> 
            <form class="navbar-form navbar-right" role="form" action="signout.php" method="POST">
                <label class="label label-primary"><?php echo $_SESSION['data']['User']['nombres']?> | <?php echo $_SESSION['data']['User']['idUsuario']?></label>
                <button type="submit" class="btn btn-success">Salir</button>
            </form>
            <div class="navbar-form navbar-right">
                <a href="cuentasPasadas.php" class="btn btn-primary">Cuentas Pasadas</a>
            </div>
            <div class="navbar-form navbar-right">
                <a href="cuenta.php" class="btn btn-primary">Cuentas Activas</a>
            </div>
<?php
    }else{
?>
            <form class="navbar-form navbar-right" role="form" action="signin.php" method="POST">
                <div class="form-group">
                  <input type="text" placeholder="Nombre" class="form-control" name="user">
                </div>
                <div class="form-group">
                  <input type="password" placeholder="Password" class="form-control" name="pass">
                </div>
                <button type="submit" class="btn btn-success">Entrar</button>
            </form>
<?php
    }
?>
          
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <!--
    <div class="jumbotron">
      <div class="container">
        <h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called a jumbotron and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more &raquo;</a></p>
      </div>
    </div>
    -->
    <div class="container-fluid">
    <!-- End Header-->