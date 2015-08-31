<?php require_once(__DIR__ . '/../../src/init.php'); ?>

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

<?php tml_init(array(
    "key"   => "c5d1005ced6df79cd2f7e78410134a68ec5546812013518a02402cfba1797eba",
    "token" => "04165d184608f9e2ada04b35d995a39703031aba4b76627dfa4164318b21a7d9",
    "host"  => "http://localhost:3000",
    "cache" => array(
        "enabled"   => false,
        "adapter"   => "memcache",
        "host"      => "localhost",
        "port"      => 11211
    )
)); ?>

<?php include('helpers.php'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo tml_current_locale(); ?>" dir="<?php echo tml_current_language_direction() ?>">
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
  <link rel="SHORTCUT ICON" href="<?php echo url_for('assets/img/favicon.ico') ?>"/>
  <style>
    body {
      padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      padding-bottom: 40px;
      background-color: white;
    }
  </style>
</head>

<body>