<?php require_once(__DIR__ . '/../library/Tml.php'); ?>
<?php tml_init(); ?>

<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />

<?php
$path = dirname(__FILE__)."/../tests/fixtures/html/examples";
$selected_sample = $_GET["sample"];
$selected_file_path = $path.'/'.$selected_sample.'.html';
$content = file_get_contents($selected_file_path);

$options = array();
$options["debug"] = ($_GET["debug_tml"] == 1);
$options["split_sentences"] = ($_GET["split"]==1);
$options["data_tokens.special"] = ($_GET["special_tokens"] == 1);
$options["data_tokens.numeric"] = ($_GET["numeric_tokens"] == 1);
?>

<?php tml_scripts(); ?>

<?php tml_begin_block_with_options(array("source" => "examples/" . $selected_sample)) ?>

<?php echo trh($content, "", array(), $options) ?>

<?php tml_finish_block_with_options() ?>

<?php tml_complete_request() ?>