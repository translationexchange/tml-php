<?php phpinfo() ?>

<?php tre("You have [bold: {count || message}]", array("count" => 1, "bold" => '<a href="http://www.google.com">{$0}</a>')) ?>

<br><br>

<?php tre("You have {count || message}", array("count" => 2)) ?>

<br><br>

<?php tre("You have {count || message}", array("count" => 5)) ?>
