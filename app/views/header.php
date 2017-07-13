<!DOCTYPE html>
<html lang="zh-tw">
<head>
  <title><?php echo $title ?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/select2.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/kendo.common-material.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/kendo.material.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/kendo.material.mobile.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/main.css"/> 
  <!--<link rel="stylesheet" type="text/css" href="/public/css/kendo.custom.css"/>-->
  <!-- Javascript -->
  <script src="/public/js/jquery.min.js"></script>
  <script src="/public/js/angular.min.js"></script>
  <script src="/public/js/bootstrap.min.js"></script>
  <script src="/public/js/select2.min.js"></script>
  <script src="/public/js/skel.min.js"></script>
  <script src="/public/js/util.js"></script>
  <!--[if lte IE 8]><script src="/public/js/ie/respond.min.js"></script><![endif]-->
  <script src="/public/js/main.js"></script>
  <script src="/public/js/kendo.all.min.js"></script>
  <script src="/public/js/shim.js"></script>
  <script src="/public/js/jszip.js"></script>
  <script src="/public/js/xlsx.js"></script>
  <script src="/public/js/exceplus-2.5.min.js"></script>
  <script src="/public/js/webtoolkit.base64.js"></script>
  <script src="/public/js/moment-with-locales.min.js"></script>
  <script src='https://www.google.com/recaptcha/api.js'></script>
  <!-- mojo -->
  <link rel="stylesheet" type="text/css" href="/public/css/mojo.css"/>
  <script>
    var mojo = {
      ver: '1.7.13',
      errmsg: '',
      cache: {},
      data: {},
      grid: {},
      reg: {
        'username': /^[a-zA-Z0-9]{2,50}$/,
        'userpass': /^[a-zA-Z0-9!@#$%^&*`~\-_=+\\|;:'",<.>\/?\[{\]}]{4,80}$/,
        'email': /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
        'float31': /^[\d]{1,3}(\.\d)?$/,
        'float51': /^[\d]{1,5}(\.\d)?$/,
        'float111': /^[\d]{1,11}(\.\d)?$/,
        'int11': /^[\d]{1,11}$/,
        'string255': /^(.){1,255}$/,
        'country_code': /[a-z0-9][\d]{2}/i
      },
      refs: {},
    };
  </script>
  <script src="/public/js/mojo.js?2017071302"></script>
</head>
<body data-mojo="<?php echo (isset($_SESSION['admin'])? $_SESSION['admin']['session'] : (isset($_SESSION['agent'])? $_SESSION['agent']['session'] : ''));?>" data-error="<?php echo (isset($error_code)? $error_code : '') ;?>">
<?php 
    if (isset($topbar)) {
        echo $topbar;
    }
    if (isset( $sidebar )) {
        echo '<aside id="sidebar">';
        echo $sidebar;
        echo '</aside>';
    }   
?>
<main id="main" class="<?php echo (isset($_SESSION['username'])? $_SESSION['username'] : 'login') ;?>">
  <div class="inner">
    <div id="dialog"></div>
    <article class="box">
      <header id="header">
      <?php
          if (isset( $header )) {
              echo $header;
          }      
      ?>
      </header>
