<?php
    $toggle = isset($opts['toggle']) ? $opts['toggle'] : "true";
    $toggle_label = isset($opts['toggle_label']) ? $opts['toggle_label'] : "Help Us Translate";
?>

<?php if ($toggle == "true") { ?>
    <div style='margin-top: 5px;' <?php tml_language_dir_attr() ?>>
        <a href='javascript:void(0);' onclick='Tml.Utils.toggleInlineTranslations()'>
            <?php echo $toggle_label; ?>
        </a>
    </div>
<?php } ?>

<div style='margin-top: 5px;' dir='ltr'>
    <a href='http://translationexchange.com' style='font-size:12px;color: #ccc;'>
        Powered by Translation Exchange
    </a>
</div>

