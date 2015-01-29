<?php require_once(__DIR__ . '/../library/tml.php'); ?>
<?php tml_init(); ?>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />

<?php

$label = isset($_REQUEST["tml_label"]) ? $_REQUEST["tml_label"] : "";
$context = isset($_REQUEST["tml_context"]) ? $_REQUEST["tml_context"] : "";

$tokens = isset($_REQUEST["tml_tokens"]) ? $_REQUEST["tml_tokens"] : "{}";
$tokens = json_decode($tokens, true);

$options = isset($_REQUEST["tml_options"]) ? $_REQUEST["tml_options"] : "{}";
$options = json_decode($options, true);

?>

<?php tml_scripts(); ?>

<?php tml_begin_block_with_options(array("source" => "/examples/interactive_tml")) ?>

<div style="padding:15px;">
    <?php if ($label != "") { ?>
        <?php tre($label, $context, $tokens, $options) ?>
    <?php } ?>
</div>

<?php tml_finish_block_with_options() ?>

<?php tml_complete_request() ?>