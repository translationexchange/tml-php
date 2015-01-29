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
