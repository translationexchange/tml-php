<?php

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

/**
 * Tml related links
 *
 * @param string $dest
 * @param null $title
 * @param array $opts
 */
function tml_link_to($dest, $title = null, $opts = array()) {
    $path = null;
    $function = "";
    switch ($dest) {
        case 'app_phrases':
            if ($title == null) $title = "Phrases";
            $path = "/tml/app/phrases/index";
            break;
        case 'app_settings':
            if ($title == null) $title = "Settings";
            $path = "/tml/app/settings/index";
            break;
        case 'app_translations':
            if ($title == null) $title = "Translations";
            $path = "/tml/app/translations/index";
            break;
        case 'app_translators':
            if ($title == null) $title = "Translators";
            $path = "/tml/app/translators/index";
            break;
        case 'assignments':
            if ($title == null) $title = "Assignments";
            $path = "/tml/translator/assignments";
            break;
        case 'notifications':
            if ($title == null) $title = "Notifications";
            $path = "/tml/translator/notifications";
            break;
        case 'following':
            if ($title == null) $title = "Following";
            $path = "/tml/translator/following";
            break;
        case 'preferences':
            if ($title == null) $title = "Preferences";
            $path = "/tml/translator/preferences";
            break;
        case 'help':
            if ($title == null) $title = "Help";
            $path = "/tml/help";
            break;
        case 'discussions':
            if ($title == null) $title = "Discussions";
            $path = "/tml/app/forum";
            break;
        case 'awards':
            if ($title == null) $title = "Awards";
            $path = "/tml/app/awards";
            break;
        case 'phrases':
            if ($title == null) $title = "Phrases";
            $path = "/tml/app/phrases";
            break;
        case 'translations':
            if ($title == null) $title = "Translations";
            $path = "/tml/app/translations";
            break;
        case 'toggle_inline':
            if ($title == null) $title = "Toggle inline mode";
            $function = "Tml.UI.LanguageSelector.toggleInlineTranslations();";
            break;
        case 'notifications_popup':
            if ($title == null) $title = "Notifications";
            $function = "Tml.UI.Lightbox.show('/tml/translator/notifications/lb_notifications', {width:600});";
            break;
        case 'shortcuts_popup':
            if ($title == null) $title = "Shortcuts";
            $function = "Tml.UI.Lightbox.show('/tml/help/lb_shortcuts', {width:400});";
            break;
        case 'login':
            if ($title == null) $title = "Login";
            $function = "Tml.UI.Lightbox.show('/login/index?mode=lightbox', {width:550, height:500});";
            break;
        case 'logout':
            if ($title == null) $title = "Logout";
            $function = "Tml.UI.Lightbox.show('/login/out?mode=lightbox', {width:400});";
            break;
    }

    if ($path != null) {
        $path = tml\Config::instance()->application->host . $path;
        echo '<a ' . \tml\Utils\ArrayUtils::toHTMLAttributes($opts) . ' href="' . $path . '">' . tr($title) . '</a>';
        return;
    }

    if ($function != null) {
        echo '<a ' . \tml\Utils\ArrayUtils::toHTMLAttributes($opts) . ' href="#" onClick="' . $function . '">' . tr($title) . '</a>';
        return;
    }

    echo "Invalid tml link key";
}

/**
 * Translator login
 *
 * @return string
 */
function tml_login_url() {
    return tml\Config::instance()->application->host . '/login';
}

/**
 * Translator signup
 *
 * @return string
 */
function tml_signup_url() {
    return tml\Config::instance()->application->host . '/signup';
}
