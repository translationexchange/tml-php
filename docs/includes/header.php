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
                    <li <?php active_link("docs/index.php")?>><?php link_to(tr("Documentation & Samples"), "docs/index.php") ?></li>
                    <li <?php active_link("docs/tml.php")?>><?php link_to(tr("TML Interactive Console"), "docs/tml.php") ?></li>
                    <li <?php active_link("docs/editor.php")?>><?php link_to(tr("Blog Translator"), "docs/editor.php") ?></li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <li role="presentation">
                        <?php tml_language_selector_tag(array("role" => "menuitem", "tabindex" => "-1")) ?>
                    </li>

                    <?php if (tml_current_translator() == null) { ?>
                        <li role="presentation"><?php link_to_function('login', 'Tml.Utils.login()') ?></li>
                    <?php } else { ?>
                        <li class="dropdown">
                            <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">
                                <?php if (tml_current_translator()->email == null) { ?>
                                    <?php image_tag('silhouette.gif', array("class" => "img-polaroid", "style" => "width:10px;height:10px;border:1px solid #eee")) ?>
                                <?php } else { ?>
                                    <img src="<?php echo tml_current_translator()->mugshot() ?>" style="width:10px;height:10px;border:1px solid #eee" class="img-polaroid">
                                <?php } ?>
                                <?php echo tml_current_translator()->name ?> <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                                <li role="presentation" class="text-center">
                                    <?php if (tml_current_translator()->email == null) { ?>
                                        <?php image_tag('silhouette.gif', array("class" => "img-circle", "style" => "width:80px;height:80px;border:1px solid #eee")) ?>
                                    <?php } else { ?>
                                        <img src="<?php echo tml_current_translator()->mugshot() ?>" style="width:80px;height:80px;border:1px solid #eee" class="img-circle">
                                    <?php } ?>
                                </li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><?php link_to_function('Toggle Inline Translations', 'Tml.Utils.toggleInlineTranslations()') ?></li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><?php link_to_function('Shortcuts', 'Tml.Utils.openShortcuts()') ?></li>
                                <?php if (\Tml\Config::instance()->isCacheEnabled() && !\Tml\Cache::isReadOnly()) { ?>
                                    <li role="presentation"><?php link_to("Reset Cache (v" . \Tml\Cache::version() . ")", "docs/reset_cache.php") ?></li>
                                <?php } ?>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><?php link_to_function('Log Out', 'Tml.Utils.logout()') ?></li>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
    <?php tml_finish_block_with_options() ?>
</div>

<div class="container">

