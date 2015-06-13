<?php require_once(__DIR__ . '/../../library/tml.php'); ?>

<!-- ?php tml_init("500f56dcda6b6b2821905824d0cfbbaa8bb81bdd8edb6da6eded2e717a0e9349", array(
    "host" => "http://127.0.0.1:3000",
    "cache" => array(
        "enabled"   => true,
        "adapter"   => "memcache",
        "host"      => "localhost",
        "port"      => 11211
    ),
    "log" => array(
        "enabled"   => true,
        "severity"  => "debug"
    ),
    "local" => array(
        "base_path" => "/tml"
    )
)); ? -->

<?php tml_init("15228c9bd42a39b6159b90a75c21639f9c458f1bb2cd6db5a7e0c177abc2af16", array(
    "cache" => array(
        "enabled"   => true,
        "adapter"   => "memcache",
        "host"      => "localhost",
        "port"      => 11211
    ),
    "local" => array(
        "base_path" => "/tml"
    )
)); ?>

<?php include('helpers.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo tml_current_language()->locale; ?>" dir="<?php echo tml_current_language()->direction(); ?>">
<head>
  <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php javascript_tag("jquery191.js") ?>
  <?php javascript_tag("bootstrap.js") ?>
  <?php javascript_tag("sh.js") ?>
  <?php stylesheet_tag("bootstrap.css") ?>
  <?php stylesheet_tag("sh.css") ?>
  <?php tml_scripts(); ?>

  <title><?php echo tml_application()->name ?></title>
  <link rel="SHORTCUT ICON" href="<?php echo url_for('docs/assets/img/favicon.ico') ?>"/>
  <style>
    body {
      padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      padding-bottom: 40px;
      background-color: white;
    }
  </style>
</head>

<body>