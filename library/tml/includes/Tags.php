<?php

use tml\utils\ArrayUtils;

/**
 * Copyright (c) 2015 Translation Exchange, Inc
 *
 *  _______                  _       _   _             ______          _
 * |__   __|                | |     | | (_)           |  ____|        | |
 *    | |_ __ __ _ _ __  ___| | __ _| |_ _  ___  _ __ | |__  __  _____| |__   __ _ _ __   __ _  ___
 *    | | '__/ _` | '_ \/ __| |/ _` | __| |/ _ \| '_ \|  __| \ \/ / __| '_ \ / _` | '_ \ / _` |/ _ \
 *    | | | | (_| | | | \__ \ | (_| | |_| | (_) | | | | |____ >  < (__| | | | (_| | | | | (_| |  __/
 *    |_|_|  \__,_|_| |_|___/_|\__,_|\__|_|\___/|_| |_|______/_/\_\___|_| |_|\__,_|_| |_|\__, |\___|
 *                                                                                        __/ |
 *                                                                                       |___/
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Displays default language selector
 *
 * @param \tml\Language $language
 * @param array $opts
 */
function tml_language_name_tag($language = null, $opts = array()) {
    if ($language == null) $language = tml_current_language();
    if (isset($opts["flag"])) {
        tml_language_flag_tag($language);
        echo " ";
    }
    echo $language->native_name;
}

/**
 * Displays language name
 *
 * @param \tml\Language $language
 */
function tml_language_flag_tag($language = null) {
    if ($language == null) $language = tml_current_language();
    echo "<img src='" . $language->flagUrl() . "' style='margin-right:3px;'>";
}

function tml_language_selector_script_tag($opts = array()) {
    echo "<script>";
    echo "    function tml_change_locale(locale) {";
    echo "      var query_parts = window.location.href.split('#')[0].split('?');";
    echo "      var query = query_parts.length > 1 ? query_parts[1] : null;";
    echo "      var params = {};";
    echo "      if (query) {";
    echo "        var vars = query.split('&');";
    echo "        for (var i = 0; i < vars.length; i++) {";
    echo "          var pair = vars[i].split('=');";
    echo "          params[pair[0]] = pair[1];";
    echo "        }";
    echo "      }";
    echo "      params['locale'] = locale;";
    echo "      query = [];";
    echo "      var keys = Object.keys(params);";
    echo "      for (var i = 0; i < keys.length; i++) {";
    echo "        query.push(encodeURIComponent(keys[i]) + \"=\" + encodeURIComponent(params[keys[i]]));";
    echo "      }";
    echo "      var destination = query_parts[0] + '?' + query.join(\"&\");";
    echo "      window.location = destination;";
    echo "    }";
    echo "  </script>";
}

/**
 * Language selector
 */
function tml_language_selector_tag($opts = array()) {
  $type = isset($opts['type']) ? $opts['type'] : 'default';

  if ($type == 'default') {
    echo "<a href='#' onClick='Tml.UI.LanguageSelector.show()' ";
    echo  ArrayUtils::toHTMLAttributes($opts). " >";
    tml_language_name_tag(tml_current_language(), array("flag" => true));
    echo "</a>";
  } elseif ($type == 'dropdown') {
    $style = isset($opts['style']) ? $opts['style'] : '';
    $class = isset($opts['class']) ? $opts['class'] : '';
    $name = isset($opts['language']) ? $opts['language'] : 'english';

    tml_language_selector_script_tag();

    echo "  <select id='tml_language_selector' onchange='tml_change_locale(this.options[this.selectedIndex].value)' style='$style' class='$class'>";

    $languages = \tml\Config::instance()->application->languages;
    foreach($languages as $lang) {
        echo "<option dir='ltr' value='$lang->locale' " . ($lang->locale == tml_current_language()->locale ? 'selected' : '') . ">";
        if ($name == "native")
            echo $lang->native_name;
        else
            echo $lang->english_name;
        echo "</option>";
    }
    echo "  </select>";
  } elseif ($type == "bootstrap") {
    $element = isset($opts['element']) ? $opts['element'] : 'div';
    $class = isset($opts['class']) ? $opts['class'] : 'dropdown';
    $style = isset($opts['style']) ? $opts['style'] : '';
    $name = isset($opts['language']) ? $opts['language'] : 'english';
    $toggler = isset($opts['toggler']) ? $opts['toggler'] : true;

    tml_language_selector_script_tag();

    echo "<$element class='$class'>";
    echo "  <a href='#' role='button' class='$class-toggle' data-toggle='$class'>\n";
    tml_language_name_tag(tml_current_language(), array("flag" => true));
    echo " <b class='caret'></b></a>";

    echo "<ul class='$class-menu' role='menu'>";

    $languages = \tml\Config::instance()->application->languages;
    foreach($languages as $lang) {
        echo "<li role='presentation'><a href='javascript:void(0);' onclick='tml_change_locale(\"$lang->locale\")'>";
        if ($name == "native")
            echo $lang->native_name;
        else
            echo $lang->english_name;
        echo "</a></li>";
    }

    if ($toggler) {
        echo "<li role='presentation' class='divider'></li>";
        echo "<li role='presentation'><a href='javascript:void(0);' onclick='Tml.Utils.toggleInlineTranslations()'>" . tr("Help Us Translate") . "</a></li>";
    }

    echo "</ul>";
    echo "</$element>";
  }

}


