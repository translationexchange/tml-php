<?php

use Tml\Config;
use Tml\Session;

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

if (Session::isActive()) { ?>
    <script>
        <?php
            $cache_interval =  Config::instance()->configValue("agent.cache", 86400);
            $agent_host = Config::instance()->configValue("agent.host",
                "https://tools.translationexchange.com/agent/stable/agent.min.js"
            );
            $t = time();
            $t = $t - ($t % $cache_interval);
            $agent_host = $agent_host . '?ts=' . $t;
        ?>

        (function() {
            var script = window.document.createElement('script');
            script.setAttribute('id', 'tml-agent');
            script.setAttribute('type', 'application/javascript');
            script.setAttribute('src', '<?php echo $agent_host ?>');
            script.setAttribute('charset', 'UTF-8');
            script.onload = function() {
                Trex.init("<?php echo Session::application()->key ?>", <?php echo json_encode(Config::instance()->configValue("agent", array()), JSON_UNESCAPED_SLASHES) ?>);
            };
            window.document.getElementsByTagName('head')[0].appendChild(script);
        })();
    </script>

<?php } ?>