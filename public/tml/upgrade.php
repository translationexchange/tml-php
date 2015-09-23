<?php include('../includes/head.php'); ?>

<?php

if (isset($_REQUEST["access_token"]) && $_REQUEST["access_token"] == tml_application()->access_token) {
    \Tml\Cache::invalidateVersion();

?>

    Cache has been upgraded!

<?php } else { ?>

    Failed to upgrade cache.

<?php } ?>

<?php include('../includes/foot.php'); ?>
