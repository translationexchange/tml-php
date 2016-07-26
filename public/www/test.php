<?php include('includes/head.php'); ?>

<div class="container">

<?php $user = array("gender" => "female", "name" => "Michael") ?>

<?php tre("<link>{user}</link> tagged {user | himself, herself} in <bold>{count || photo}</bold>.", array("user" => array($user, $user["name"]), "count" => 1, "link" => array("href" => "http://www.yahoo.com"), "bold" => '<span style="color: red">{$0}</span>')) ?>

</div>

<?php include('includes/foot.php'); ?>