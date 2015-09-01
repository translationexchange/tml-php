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

namespace Tml\Tokens;

use Tml\Config;

require_once(__DIR__."/../../BaseTest.php");

class DecorationTokenizerTest extends \BaseTest {

    public function testParsing() {
        $dt = new DecorationTokenizer("Hello World");
        $this->assertEquals(array("[tml]", "Hello World", "[/tml]"), $dt->fragments);
        $this->assertEquals(array("tml", "Hello World"), $dt->parse());

        $dt = new DecorationTokenizer("[bold: Hello World]");
        $this->assertEquals(array("[tml]", "[bold:", " Hello World", "]", "[/tml]"), $dt->fragments);
        $this->assertEquals(array("tml", array("bold", "Hello World")), $dt->parse());

        // broken
        $dt = new DecorationTokenizer("[bold: Hello World");
        $this->assertEquals(array("[tml]", "[bold:", " Hello World", "[/tml]"), $dt->fragments);
        $this->assertEquals(array("tml", array("bold", "Hello World")), $dt->parse());

        $dt = new DecorationTokenizer("[bold: Hello [strong: World]]");
        $this->assertEquals(array("[tml]", "[bold:", " Hello ", "[strong:", " World", "]", "]", "[/tml]"), $dt->fragments);
        $this->assertEquals(array("tml", array("bold", "Hello ", array("strong", "World"))), $dt->parse());

        // broken
        $dt = new DecorationTokenizer("[bold: Hello [strong: World]");
        $this->assertEquals(array("[tml]", "[bold:", " Hello ", "[strong:", " World", "]", "[/tml]"), $dt->fragments);
        $this->assertEquals(array("tml", array("bold", "Hello ", array("strong", "World"))), $dt->parse());

        // numbers
        $dt = new DecorationTokenizer("[bold1: Hello [strong22: World]]");
        $this->assertEquals(array("[tml]", "[bold1:", " Hello ", "[strong22:", " World", "]", "]", "[/tml]"), $dt->fragments);
        $this->assertEquals(array("tml", array("bold1", "Hello ", array("strong22", "World"))), $dt->parse());

        $dt = new DecorationTokenizer("[bold: Hello, [strong: how] [weak: are] you?]");
        $this->assertEquals(
            array("[tml]", "[bold:", " Hello, ", "[strong:", " how", "]", " ", "[weak:", " are", "]", " you?", "]", "[/tml]"),
            $dt->fragments
        );
        $this->assertEquals(
            array("tml", array("bold", "Hello, ", array("strong", "how"), " ", array("weak", "are"), " you?")),
            $dt->parse()
        );

        $dt = new DecorationTokenizer("[bold: Hello, [strong: how [weak: are] you?]");
        $this->assertEquals(
            array("[tml]", "[bold:", " Hello, ", "[strong:", " how ", "[weak:", " are", "]", " you?", "]", "[/tml]"),
            $dt->fragments
        );
        $this->assertEquals(
            array("tml", array("bold", "Hello, ", array("strong", "how ", array("weak", "are"), " you?"))),
            $dt->parse()
        );

        $dt = new DecorationTokenizer("[link: you have [italic: [bold: {count}] messages] [light: in your mailbox]]");
        $this->assertEquals(
            array("[tml]", "[link:", " you have ", "[italic:", " ", "[bold:", " {count}", "]", " messages", "]", " ", "[light:", " in your mailbox", "]", "]", "[/tml]"),
            $dt->fragments
        );
        $this->assertEquals(
            array("tml", array("link", "you have ", array("italic", "", array("bold", "{count}"), " messages"), " ", array("light", "in your mailbox"))),
            $dt->parse()
        );

        $dt = new DecorationTokenizer("[link] you have [italic: [bold: {count}] messages] [light: in your mailbox] [/link]");
        $this->assertEquals(
            array("[tml]", "[link]", " you have ", "[italic:", " ", "[bold:", " {count}", "]", " messages", "]", " ", "[light:", " in your mailbox", "]", " ", "[/link]", "[/tml]"),
            $dt->fragments
        );
        $this->assertEquals(
            array("tml", array("link", " you have ", array("italic", "", array("bold", "{count}"), " messages"), " ", array("light", "in your mailbox"), " ")),
            $dt->parse()
        );
    }

    public function testSubstitution() {
        $dt = new DecorationTokenizer("[bold: Hello World]");
        $this->assertEquals("<strong>Hello World</strong>", $dt->substitute());

        $dt = new DecorationTokenizer("[bold]Hello World[/bold]");
        $this->assertEquals("<strong>Hello World</strong>", $dt->substitute());

        $dt = new DecorationTokenizer("[bold] Hello World [/bold]");
        $this->assertEquals("<strong> Hello World </strong>", $dt->substitute());

        $dt = new DecorationTokenizer("[p: Hello World]", array("p" => '<p>{$0}</p>'));
        $this->assertEquals("<p>Hello World</p>", $dt->substitute());

        $dt = new DecorationTokenizer("<p>Hello World</p>", array("p" => '<p>{$0}</p>'));
        $this->assertEquals(array('[tml]', '<p>', 'Hello World', '</p>', '[/tml]'), $dt->fragments);
        $this->assertEquals("<p>Hello World</p>", $dt->substitute());

        $dt = new DecorationTokenizer("<link>Hello World</link>", array("link" => '<a href="test.com">{$0}</a>'));
        $this->assertEquals('<a href="test.com">Hello World</a>', $dt->substitute());

        $dt = new DecorationTokenizer("<link>Hello <strong>World</strong></link>", array("link" => '<a href="test.com">{$0}</a>'));
        $this->assertEquals('<a href="test.com">Hello <strong>World</strong></a>', $dt->substitute());

        $dt = new DecorationTokenizer("<link>Hello [indent: World]</link>", array("link" => '<a href="test.com">{$0}</a>', "indent" => '<strong>{$0}</strong>'));
        $this->assertEquals('<a href="test.com">Hello <strong>World</strong></a>', $dt->substitute());

        $dt = new DecorationTokenizer("[p: Hello World]", array("p" => function($v){return "<p>$v</p>";}));
        $this->assertEquals("<p>Hello World</p>", $dt->substitute());

        $dt = new DecorationTokenizer("[p]Hello World[/p]", array("p" => function($v){return "<p>$v</p>";}));
        $this->assertEquals("<p>Hello World</p>", $dt->substitute());

        $dt = new DecorationTokenizer("[link: you have 5 messages]", array("link" => function($v){return "<a href='http://mail.google.com'>$v</a>";}));
        $this->assertEquals("<a href='http://mail.google.com'>you have 5 messages</a>", $dt->substitute());

        $dt = new DecorationTokenizer("[link: you have {count||message}]", array("link" => function($v){return "<a href='http://mail.google.com'>$v</a>";}));
        $this->assertEquals("<a href='http://mail.google.com'>you have {count||message}</a>", $dt->substitute());

        $dt = new DecorationTokenizer("[link]you have 5 messages[/link]", array("link" => '<a href="http://mail.google.com">{$0}</a>'));
        $this->assertEquals('<a href="http://mail.google.com">you have 5 messages</a>', $dt->substitute());

        $dt = new DecorationTokenizer("[link]you have {count||message}[/link]", array("link" => '<a href="http://mail.google.com">{$0}</a>'));
        $this->assertEquals('<a href="http://mail.google.com">you have {count||message}</a>', $dt->substitute());

        $dt = new DecorationTokenizer("[link]you have [bold: {count||message}][/link]", array("link" => '<a href="http://mail.google.com">{$0}</a>'));
        $this->assertEquals('<a href="http://mail.google.com">you have <strong>{count||message}</strong></a>', $dt->substitute());

        $dt = new DecorationTokenizer("[link]you have [bold: [italic: {count}] {count||message}][/link]", array("link" => '<a href="http://mail.google.com">{$0}</a>'));
        $this->assertEquals('<a href="http://mail.google.com">you have <strong><i>{count}</i> {count||message}</strong></a>', $dt->substitute());

        $dt = new DecorationTokenizer("[p] This document will provide you with some examples of how to use TML for internationalizing your application. The same document is present with every Tml Client SDK to ensure that all samples work the same. [/p]", array("p" => '<p>{$0}</p>'));
        $this->assertEquals('<p> This document will provide you with some examples of how to use TML for internationalizing your application. The same document is present with every Tml Client SDK to ensure that all samples work the same. </p>', $dt->substitute());


        Config::instance()->setDefaultToken("super", '<super>{$0}</super>', 'decoration');
        $dt = new DecorationTokenizer("[super: Hello World]");
        $this->assertEquals("<super>Hello World</super>", $dt->substitute());

    }

}