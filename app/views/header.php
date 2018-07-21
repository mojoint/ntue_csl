<!DOCTYPE html>
<html lang="zh-tw">
<head>
  <title><?php echo $title ?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <meta name="robots" content="noindex,nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="/public/css/jquery-ui.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/bootstrap.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/select2.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/kendo.common-material.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/kendo.material.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/kendo.material.mobile.min.css"/> 
  <link rel="stylesheet" type="text/css" href="/public/css/main.css"/> 
  <!--<link rel="stylesheet" type="text/css" href="/public/css/kendo.custom.css"/>-->
  <!-- Javascript -->
  <script src="/public/js/jquery.min.js"></script>
  <script src="/public/js/jquery-ui.min.js"></script>
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
  <script src="/public/js/Blob.js"></script>
  <script src="/public/js/FileSaver.js"></script>
  <script src="/public/js/JQueryDatePickerTW.js"></script>
  <!--  <script src="/public/js/exceplus-2.5.js"></script> -->
  <script src="/public/js/webtoolkit.base64.js"></script>
  <script src="/public/js/moment-with-locales.min.js"></script>
  <script src="/public/js/tinymce/tinymce.min.js"></script>
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
        'username': /^[a-zA-Z0-9_]{3,50}$/,
        'userpass': /^[a-zA-Z0-9!@#$%^&*`~\-_=+]{3,80}$/,
        'email': /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/,
        'float32': /^(\d){1,3}(\.(\d){1,2})?$/,
        'float51': /^(-)?(\d){1,5}(\.\d)?$/,
        'float111': /^(\d){1,11}(\.\d)?$/,
        'int11': /^(\d){1,11}$/,
        'phone': /^(\d){6,8}$/,
        'tel': /^(\d){3,4}-(\d){3,4}$/,
        'string255': /^(.){1,255}$/,
        'country_code': /[a-z0-9][\d]{2}/i,
        'zipcode': /^(\d){3,5}$/
      },
      refs: {},
      tags: {}
    };

    /* ie indexof fix */
    // Production steps of ECMA-262, Edition 5, 15.4.4.14
    // Reference: http://es5.github.io/#x15.4.4.14
    if (!Array.prototype.indexOf) {
      Array.prototype.indexOf = function(searchElement, fromIndex) {
        var k;
        // 1. Let o be the result of calling ToObject passing
        //    the this value as the argument.
        if (this == null) {
          throw new TypeError('"this" is null or not defined');
        }
    
        var o = Object(this);
    
        // 2. Let lenValue be the result of calling the Get
        //    internal method of o with the argument "length".
        // 3. Let len be ToUint32(lenValue).
        var len = o.length >>> 0;
    
        // 4. If len is 0, return -1.
        if (len === 0) {
          return -1;
        }
    
        // 5. If argument fromIndex was passed let n be
        //    ToInteger(fromIndex); else let n be 0.
        var n = fromIndex | 0;
    
        // 6. If n >= len, return -1.
        if (n >= len) {
          return -1;
        }
    
        // 7. If n >= 0, then Let k be n.
        // 8. Else, n<0, Let k be len - abs(n).
        //    If k is less than 0, then let k be 0.
        k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);
    
        // 9. Repeat, while k < len
        while (k < len) {
          // a. Let Pk be ToString(k).
          //   This is implicit for LHS operands of the in operator
          // b. Let kPresent be the result of calling the
          //    HasProperty internal method of o with argument Pk.
          //   This step can be combined with c
          // c. If kPresent is true, then
          //    i.  Let elementK be the result of calling the Get
          //        internal method of o with the argument ToString(k).
          //   ii.  Let same be the result of applying the
          //        Strict Equality Comparison Algorithm to
          //        searchElement and elementK.
          //  iii.  If same is true, return k.
          if (k in o && o[k] === searchElement) {
            return k;
          }
          k++;
        }
        return -1;
      };
    }
    
    function mojocallback( token ) {
      if (token) {
        $('#form-login').submit();
      }
    };
  </script>
  <script src="/public/js/mojo.combo.v18.7.19.js"></script>
  <script src='https://www.google.com/recaptcha/api.js'></script>
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
