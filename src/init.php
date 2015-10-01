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

/** Enabble Following lines in Debug mode only  **/
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/**
 * Require files in a specific order
 */
$files = array(
    "Tml/Utils",
    "Tml/Base.php",
    "Tml",
    "Tml/Tokens",
    "Tml/Tokenizers",
    "Tml/RulesEngine",
    "Tml/Decorators/Base.php",
    "Tml/Decorators",
    "Tml/Cache/Base.php",
    "Tml/Cache",
    "Tml/Includes/Tags.php"
);

foreach($files as $dir) {
    $path = dirname(__FILE__)."/".$dir;
    if (is_dir($path)) {
        foreach (scandir($path) as $filename) {
            $file = $path . "/" . $filename;
            if (is_file($file)) {
                require_once $file;
            }
        }
    } else {
        require_once $path;
    }
}

use Tml\Application;
use Tml\Config;
use Tml\Logger;
use Tml\TmlException;
use Tml\Translator;
use Tml\Utils\ArrayUtils;
use Tml\Utils\BrowserUtils;
use Tml\Tokenizers\DomTokenizer;
use Tml\Utils\StringUtils;

/**
 * Initializes the TML library
 *
 * @param null $token
 * @param array $options
 * @return bool
 */
function tml_init($options = array()) {
    global $tml_page_t0;
    $tml_page_t0 = microtime(true);

    $key = isset($options["key"]) ? $options["key"] : Config::instance()->configValue("application.key");
    $token = isset($options["token"]) ? $options["token"] : Config::instance()->configValue("application.token");
    $host = isset($options["host"]) ? $options["host"] : Config::instance()->configValue("application.host");

    foreach(array("cache", "log", "local", "agent") as $type) {
        if (isset($options[$type]))
            Config::instance()->updateConfig($type, $options[$type]);
    }

    $locale = null;
    $translator = null;
    $cookie_params = null;

    // create application instance, but don't initialize it yet
    $application = new Application(array("name" => "", "key" => $key, "access_token" => $token, "host" => $host));
    Config::instance()->application = $application;

    // get cookie name
    $cookie_name = "trex_" . $key;

//    var_dump($cookie_name);

    // check if cookie is set
    if (isset($_COOKIE[$cookie_name])) {
        $cookie_params = Config::instance()->decode($_COOKIE[$cookie_name], $token);
        $locale = $cookie_params['locale'];
        if (isset($cookie_params['translator'])) {
            $translator = new Translator(array_merge($cookie_params["translator"], array('application' => Config::instance()->application)));
        }
    }

    if (!$cookie_params) $cookie_params = array();

    // options locale always takes over
    if (isset($options["locale"])) {
        $locale = $options["locale"];
    } else if (isset($_GET["locale"])) {
        $locale =  $_GET["locale"];
        $cookie_params["locale"] = $_GET["locale"];
        setcookie($cookie_name, Config::instance()->encode($cookie_params, $token), null, "/");
    }

    // use default browser locale(s)
    if (!$locale && isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        $locale = BrowserUtils::acceptedLocales($_SERVER['HTTP_ACCEPT_LANGUAGE']);

    // use our default locale
    if (!$locale)
        $locale = Config::instance()->default_locale;

    $source = null;
    if (isset($_SERVER["REQUEST_URI"])) {
        $source = $_SERVER["REQUEST_URI"];
        $source = explode("#", $source);
        $source = $source[0];
        $source = explode("?", $source);
        $source = $source[0];
        $source = str_replace('.php', '', $source);
        $source = preg_replace('/\/$/', '', $source);
    }

    if (!$source || $source == '' || $source == '/')
        $source = "index";

    Config::instance()->current_translator = $translator;
    Config::instance()->current_source = $source;
    Config::instance()->current_locale = $locale;

//    var_dump($cookie_params);

    try {
        $application->fetch();
    } catch (\Exception $e) {
        Logger::instance()->error("Application failed to initialize: " . $e);
    }

    $locale = $application->supportedLocale($locale);

//    var_dump($application);

    if (Config::instance()->isEnabled()) {
        $current_language = $application->language($locale);
    } else {
        $current_language = $application->language(Config::instance()->default_locale);
    }

    Config::instance()->current_language = $current_language;

    return true;
}

/**
 * @param array $options
 */
function tml_complete_request($options = array()) {
    Config::instance()->completeRequest($options);
    global $tml_page_t0;
    $milliseconds = round(microtime(true) - $tml_page_t0,3)*1000;
    Logger::instance()->info("Page loaded in " . $milliseconds . " milliseconds");
}

/**
 * Includes Tml JavaScript library
 */
function tml_scripts() {
    if (Config::instance()->configValue("agent.type", "agent") == "agent")
        include(__DIR__ . '/Tml/Includes/AgentScripts.php');
    else
        include(__DIR__ . '/Tml/Includes/ToolsScripts.php');
}

/**
 * Includes Tml footer scripts
 */
function tml_footer() {
  include(__DIR__ . '/Tml/Includes/FooterScripts.php');
}

/**
 * @return \Tml\Application
 */
function tml_application() {
    return Config::instance()->application;
}

/**
 * @return \Tml\Language
 */
function tml_current_locale() {
    return Config::instance()->current_language->locale;
}

/**
 * @return \Tml\Language
 */
function tml_current_language() {
    return Config::instance()->current_language;
}

/**
 * @return string
 */
function tml_current_language_direction() {
    return tml_current_language()->direction();
}

/**
 * @return \Tml\Translator
 */
function tml_current_translator() {
    return Config::instance()->current_translator;
}

/**
 * Opens the source block
 *
 * @param string $name
 */
function tml_begin_source($name) {
    tml_begin_block_with_options(array("source" => $name));
}

/**
 * Closes the source block
 */
function tml_finish_source() {
    Config::instance()->finishBlockWithOptions();
}

/**
 * @param array $options
 */
function tml_begin_block_with_options($options = array()) {
    Config::instance()->beginBlockWithOptions($options);
}

/**
 * @return null
 */
function tml_finish_block_with_options() {
    return Config::instance()->finishBlockWithOptions();
}

/**
 * There are three ways to call this method:
 *
 * 1. tr($label, $description = "", $tokens = array(), options = array())
 * 2. tr($label, $tokens = array(), $options = array())
 * 3. tr($params = array("label" => label, "description" => "", "tokens" => array(), "options" => array()))
 *
 * @param string $label
 * @param string $description
 * @param array $tokens
 * @param array $options
 * @throws Exception
 * @return mixed
 */
function tr($label, $description = "", $tokens = array(), $options = array()) {
    $params = ArrayUtils::normalizeTmlParameters($label, $description, $tokens, $options);

    try {
        // Translate individual sentences
        if (isset($params["options"]['split'])) {
            $sentences = StringUtils::splitSentences($params["label"]);
            foreach($sentences as $sentence) {
                $params["label"] = str_replace($sentence, tml_current_language()->translate($sentence, $params["description"], $params["tokens"], $params["options"]), $params["label"]);
            }
            return $label;
        }

        // Remove html and translate the content
        if (isset($params["options"]["strip"])) {
            $stripped_label = str_replace(array("\r\n", "\n"), '', strip_tags(trim($params["label"])));
            $translation = tml_current_language()->translate($stripped_label, $params["description"], $params["tokens"], $params["options"]);
            $label = str_replace($stripped_label, $translation, $params["label"]);
            return $label;
        }

        return tml_current_language()->translate($params["label"], $params["description"], $params["tokens"], $params["options"]);
    } catch(TmlException $ex) {
        Logger::instance()->error("Failed to translate " . $params["label"] . ": " . $ex);
        return $label;
    } catch(\Exception $ex) {
        Logger::instance()->error("ERROR: Failed to translate " . $params["label"] . ": " . $ex);
        throw $ex;
    }
}

/**
 * Translates a label and prints it to the page
 *
 * @param string $label
 * @param string $description
 * @param array $tokens
 * @param array $options
 */
function tre($label, $description = "", $tokens = array(), $options = array()) {
    echo tr($label, $description, $tokens, $options);
}

/**
 * Translates a label while suppressing its decorations
 * The method is useful for translating alt tags, list options, etc...
 *
 * @param string $label
 * @param string $description
 * @param array $tokens
 * @param array $options
 * @return mixed
 */
function trl($label, $description = "", $tokens = array(), $options = array()) {
    $params = ArrayUtils::normalizeTmlParameters($label, $description, $tokens, $options);
    $params["options"]["skip_decorations"] = true;
	return tr($params);
}

/**
 * Same as trl, but with printing it to the page
 *
 * @param string $label
 * @param string $description
 * @param array $tokens
 * @param array $options
 */
function trle($label, $description = "", $tokens = array(), $options = array()) {
    echo trl($label, $description, $tokens, $options);
}

/**
 * Translates a block of html, converts it to TML, translates it and then converts it back to HTML
 *
 * @param $label
 * @param string $description
 * @param array $tokens
 * @param array $options
 * @return array
 */
function trh($label, $description = "", $tokens = array(), $options = array()) {
    $params = ArrayUtils::normalizeTmlParameters($label, $description, $tokens, $options);

    $html = trim($params["label"]);
    $ht = new DomTokenizer($html, array(), $params["options"]);
    return $ht->translate();
}

/**
 * Translates a block of html, converts it to TML, translates it and then converts it back to HTML
 *
 * @param $label
 * @param string $description
 * @param array $tokens
 * @param array $options
 */
function trhe($label, $description = "", $tokens = array(), $options = array()) {
    $params = ArrayUtils::normalizeTmlParameters($label, $description, $tokens, $options);
    echo trh($params);
}
