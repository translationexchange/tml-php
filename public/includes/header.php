<?php include('head.php'); ?>

<div class="navbar navbar-fixed-top">
    <?php tml_begin_block_with_options(array("source" => "header")) ?>
    <div class="navbar-inner">
        <div class="container">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <?php link_to(tr("Tml For PHP"), "index.php", array("class" => "brand")) ?>

            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li <?php active_link("docs.php")?>><?php link_to(tr("Documentation & Samples"), "docs.php") ?></li>
                    <li <?php active_link("tml.php")?>><?php link_to(tr("TML Interactive Console"), "tml.php") ?></li>
                    <li <?php active_link("editor.php")?>><?php link_to(tr("Blog Translator"), "editor.php") ?></li>
                </ul>
                <div class="nav navbar-nav pull-right navbar-text">
                    <?php tml_language_selector_tag("popup", array("toggle" => true)) ?>
                </div>
            </div><!--/.nav-collapse -->
        </div>
    </div>
    <?php tml_finish_block_with_options() ?>
</div>


<div class="container">


