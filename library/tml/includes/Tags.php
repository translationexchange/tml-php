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
    $toggle = isset($opts['toggle']) ? $opts['toggle'] : true;
    $toggle_label = isset($opts['toggle_label']) ? $opts['toggle_label'] : "Help Us Translate";

    tml_language_selector_script_tag();

    echo "<$element class='$class' style='$style'>";
    echo "  <a href='#' role='button' class='$class-toggle' data-toggle='$class'>\n";
    tml_language_name_tag(tml_current_language(), array("flag" => true));
    echo "</a>";

    echo "<ul class='$class-menu' role='menu'>";

    $languages = \tml\Config::instance()->application->languages;
    foreach($languages as $lang) {
        echo "<li role='presentation'><a href='javascript:void(0);' onclick='tml_change_locale(\"$lang->locale\")'>";
        tml_language_name_tag($lang, array("flag" => true));
        echo "</a></li>";
    }

    if ($toggle) {
        echo "<li role='presentation' class='divider'></li>";
        echo "<li role='presentation'><a href='javascript:void(0);' onclick='Tml.Utils.toggleInlineTranslations()'>" . tr($toggle_label) . "</a></li>";
    }

    echo "<li role='presentation' class='divider'></li>";

    echo "<div style='font-size:8px;color:#ccc;text-align: center'>";
    echo "<a href='http://translationexchange.com'>";
    echo "<img style='padding: 10px;border: 0px;background-repeat: no-repeat;background-size: 14px 17px;";
    echo "background-image:url(data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAAEMAAABQCAYAAABCiMhGAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNS4xIE1hY2ludG9zaCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCQTg4MTEyOEU0NkIxMUUzODhCMEJEOUNDRDQ0QkU0MiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCQTg4MTEyOUU0NkIxMUUzODhCMEJEOUNDRDQ0QkU0MiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkJBODgxMTI2RTQ2QjExRTM4OEIwQkQ5Q0NENDRCRTQyIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkJBODgxMTI3RTQ2QjExRTM4OEIwQkQ5Q0NENDRCRTQyIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+mszKSAAAAqFJREFUeNrsnE1IFVEYhr+53QiKEIkgpCBwJSH9SITkqkVELXJZ0iYIqRaBCZWgoWKikRBI1KVNmyg3gVKQ0CZE+9kHQYukaGP2I6iEP1ffc+dc7iwK5s5Mx3PPvC88zGxm5s4zc85853JmvNardwTxwC1wGuyWdGQGvARduaH23wUJkJHFcgoclnTmKzgIIT8yWOlNsQiVPeB+8c74hmWNMNXqzqimh0JqlIxVeigkr2Tk6aGQtQwdlEIZlEEZlEEZEZONuf0YmASLYNNGPRI1e8FFsHUjZNwAg5Zd3GHwHuw02Uw+WShCZRr0m+4zPljc9D+alpG1WMYW0zJsfgplxMGTYp1BGZRBGZRBGZRBGZRBGZRBGZRBGQxlUAZlUAZlUAZlUAZlUAZlUAZlUIbDMmyeYr3KO6OUJdMyGiyWcdS0DPV+ykMLRRwD16JuHGc60gV9Fd6KP/Vxs/hTEDvBbIT9nQLNEdp8XkpTH0/GMRl3bladJpi7EWQcB89dfLSW24EdAeOsM0T2gQkWXX4bf6P7mlTL2KFFbE97Ob4NvAO70j42UcdTbyHU2jo28Qwe7zXYb2nl6mXE3DzwF6DJ5kGrkjFv4EBP4laHBjKjZDz7zwfJgTOWi3iVG2qfVTKui//ZhMTaXmD9Hmi1XMSf4m9UMubAIfAY/Epg58t62QcuWyxhQfx37A7grvgcHKipgdU5UPWX538LuBlh8Deu+4oVS2X8BN8h4p+j1jlNMOW+5lQ8+QmpsIQpusr5vsYV8EUqNElWoKojHpYKTlIyusFtqfAkIWMA9IgDiStD/cXXIY4kjowHoE0cSlQZj8AlcSxRZDwF58XBhJGxFlgfBWfF0YSR4QXK62ZxOGFk1OtR7QlxPGFkjIBGSUHWBRgAlJNpO4bVinwAAAAASUVORK5CYII=);'/>";
    echo "<br>";
    echo "Powered By Translation Exchange";
    echo "</a>";
    echo "</div>";

    echo "</ul>";
    echo "</$element>";
  }

}


