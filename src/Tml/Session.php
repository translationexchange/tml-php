<?php

/**
 * Copyright (c) 2016 Translation Exchange, Inc
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

namespace Tml;

use Tml\Utils\ArrayUtils;
use Tml\Utils\BrowserUtils;
use Tml\Tokenizers\DomTokenizer;
use Tml\Utils\StringUtils;

class Session {

    /**
     * @var Application
     */
    public $application;

    /**
     * @var mixed
     */
    public $current_user;

    /**
     * @var string
     */
    public $current_locale;

    /**
     * @var Language
     */
    public $current_language;

    /**
     * @var Translator
     */
    public $current_translator;

    /**
     * @var Source
     */
    public $current_source;

    /**
     * @var array
     */
    public $block_options;

    /**
     * @return Session
     */
    public static function instance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new Session();
        }
        return $inst;
    }

    /**
     *
     */
    function __construct() {
        $this->application = null;
        $this->current_locale = null;
        $this->current_language = null;
        $this->current_user = null;
        $this->current_translator = null;
        $this->current_source = null;
        $this->block_options = array();
    }

    /**
     * Returns access token
     *
     * @return string
     */
    public function accessToken() {
        return $this->application->access_token;
    }

    /**
     * Checks if keys should be sent to the server
     *
     * @return bool
     */
    public function isKeyRegistrationEnabled() {
        if (!Config::instance()->isCacheEnabled())
            return true;

        if ($this->isInlineTranslationModeEnabled())
            return true;

        return false;
    }

    /**
     * @param array $options
     */

    public function completeRequest(/** @noinspection PhpUnusedParameterInspection */ $options = array()) {
        if (!isset($this->application)) return;
        $this->application->submitMissingKeys();
    }

    /**
     * @param array $options
     */
    public function beginBlockWithOptions($options = array()) {
        array_push($this->block_options, $options);
    }

    /**
     * @param $key
     * @return null
     */
    public function getBlockOption($key) {
        if (count($this->block_options) == 0) return null;
        $current_options = $this->block_options[count($this->block_options)-1];
        if (!array_key_exists($key, $current_options)) return null;
        return $current_options[$key];
    }

    /**
     * @return null
     */
    public function finishBlockWithOptions() {
        if (count($this->block_options) == 0) return null;
        array_pop($this->block_options);
    }

    /**
     * @return bool
     */
    public function isInlineTranslationModeEnabled() {
        return ($this->current_translator && $this->current_translator->isInlineModeEnabled());
    }

    /**
     * Initializes the TML request
     *
     * @param array $options
     * @return bool
     */
    static function init($options = array()) {
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
        self::instance()->application = $application;

        // get cookie name
        $cookie_name = "trex_" . $key;

        // var_dump($cookie_name);

        // check if cookie is set
        if (isset($_COOKIE[$cookie_name])) {
            $cookie_params = Config::instance()->decode($_COOKIE[$cookie_name]);
            $locale = $cookie_params['locale'];
            if (isset($cookie_params['translator'])) {
                $translator = new Translator(array_merge($cookie_params["translator"], array('application' => Session::instance()->application)));
            }
        }

        if (!$cookie_params) $cookie_params = array();

        // options locale always takes over
        if (isset($options["locale"])) {
            $locale = $options["locale"];
        } else if (isset($_GET["locale"])) {
            $locale =  $_GET["locale"];
            $cookie_params["locale"] = $_GET["locale"];
            setcookie($cookie_name, Config::instance()->encode($cookie_params), null, "/");
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

        Session::instance()->current_translator = $translator;
        Session::instance()->current_source = $source;
        Session::instance()->current_locale = $locale;

//    var_dump($cookie_params);

        try {
            $application->fetch();
        } catch (\Exception $e) {
            Logger::instance()->error("Application failed to initialize: " . $e);
        }

        $locale = $application->supportedLocale($locale);

//    var_dump($application);

        if (self::isActive()) {
            $current_language = $application->language($locale);
        } else {
            $current_language = $application->language(Config::instance()->default_locale);
        }

        Session::instance()->current_language = $current_language;

        return true;
    }

    /**
     * @param array $options
     */
    static function finalize($options = array()) {
        Session::instance()->completeRequest($options);
        global $tml_page_t0;
        $milliseconds = round(microtime(true) - $tml_page_t0,3)*1000;
        Logger::instance()->info("Page loaded in " . $milliseconds . " milliseconds");
    }

    /**
     * @return \Tml\Application
     */
    static function application() {
        return self::instance()->application;
    }

    /**
     * @return \Tml\Language
     */
    static function currentLocale() {
        return self::instance()->current_language->locale;
    }

    /**
     * @return string
     */
    static function currentSource() {
        return self::instance()->current_source;
    }

    /**
     * @return \Tml\Language
     */
    static function currentLanguage() {
        return self::instance()->current_language;
    }

    /**
     * @return string
     */
    static function currentLanguageDirection() {
        return self::currentLanguage()->direction();
    }

    /**
     * @return \Tml\Translator
     */
    static function currentTranslator() {
        return self::instance()->current_translator;
    }

    /**
     * Opens the source block
     *
     * @param string $name
     */
    static function beginSource($name) {
        self::beginBlockWithOptions(array("source" => $name));
    }

    /**
     * Closes the source block
     */
    static function finishSource() {
        self::instance()->finishBlockWithOptions();
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
     * @return mixed
     * @throws \Exception
     */
    static function tr($label, $description = "", $tokens = array(), $options = array()) {
        $params = ArrayUtils::normalizeTmlParameters($label, $description, $tokens, $options);

        try {
            // Translate individual sentences
            if (isset($params["options"]['split'])) {
                $sentences = StringUtils::splitSentences($params["label"]);
                foreach($sentences as $sentence) {
                    $params["label"] = str_replace($sentence, self::currentLanguage()->translate($sentence, $params["description"], $params["tokens"], $params["options"]), $params["label"]);
                }
                return $label;
            }

            // Remove html and translate the content
            if (isset($params["options"]["strip"])) {
                $stripped_label = str_replace(array("\r\n", "\n"), '', strip_tags(trim($params["label"])));
                $translation = self::currentLanguage()->translate($stripped_label, $params["description"], $params["tokens"], $params["options"]);
                $label = str_replace($stripped_label, $translation, $params["label"]);
                return $label;
            }

            return self::currentLanguage()->translate($params["label"], $params["description"], $params["tokens"], $params["options"]);
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
    static function tre($label, $description = "", $tokens = array(), $options = array()) {
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
    static function trl($label, $description = "", $tokens = array(), $options = array()) {
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
    static function trle($label, $description = "", $tokens = array(), $options = array()) {
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
    static function trh($label, $description = "", $tokens = array(), $options = array()) {
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
    static function trhe($label, $description = "", $tokens = array(), $options = array()) {
        $params = ArrayUtils::normalizeTmlParameters($label, $description, $tokens, $options);
        echo trh($params);
    }

    /**
     * @param $key
     * @return null
     */
    static function blockOption($key) {
        return self::instance()->getBlockOption($key);
    }

    /**
     * @return bool
     */
    static function isActive() {
        return (self::instance()->application != null && self::instance()->application->key != null);
    }

    /**
     * @return bool
     */
    static function isInactive() {
        return !self::isActive();
    }
}
