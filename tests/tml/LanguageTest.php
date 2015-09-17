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

namespace Tml;

require_once(__DIR__."/../BaseTest.php");

class LanguageTest extends \BaseTest
{

    protected $app, $russian, $english;
    protected $male, $female, $unknown;

    protected function setUp() {
        $this->app = new Application(self::loadJSON('application.json'));
        Config::instance()->application = $this->app;
        $this->english = $this->app->addLanguage(new Language(self::loadJSON('languages/en.json')));
        $this->russian = $this->app->addLanguage(new Language(self::loadJSON('languages/ru.json')));
        $this->male = new \User("Michael", "male");
        $this->female = new \User("Anna", "female");
        $this->unknown = new \User("Anna", "unknown");

        Config::instance()->beginBlockWithOptions(array("dry" => true));
    }

    protected function tearDown() {
        Config::instance()->finishBlockWithOptions();
    }

    public function testLoadingLanguage() {
        $this->assertEquals('en', $this->english->locale);
        $this->assertEquals('English', $this->english->english_name);
        $this->assertEquals('English', $this->english->native_name);
        $this->assertEquals(array("list", "date", "number", "gender", "genders"), array_keys($this->english->contexts));
        $this->assertEquals(array("times", "plural", "ordinal", "ord", "singular", "pos"), array_keys($this->english->cases));
    }

    public function testDefaultTranslationsWithNoTokens() {
        // No Tokens
        $this->assertEquals('Hello World',
            $this->english->translate("Hello World")
        );

        $this->assertEquals('Hello World',
            $this->english->translate("Hello World", 'Greeting')
        );
    }

    public function testDefaultTranslationsWithBasicTokens() {
        // Basic Tokens
        $this->assertEquals('You have 0 messages',
            $this->english->translate('You have {count} messages', '', array('count' => 0))
        );

        $this->assertEquals('Hello World',
            $this->english->translate('Hello {world}', '', array('world' => 'World'))
        );

        $this->assertEquals('Hello Michael',
            $this->english->translate('Hello {user}', '', array('user' => $this->male))
        );
    }

    public function testDefaultTranslationsWithDecorationTokens() {
        // Decoration Tokens
        $this->assertEquals('Hello <strong>World</strong>',
            $this->english->translate('Hello [strong: World]', '', array('strong' => function($txt){
                return "<strong>$txt</strong>";
            }))
        );

        $this->assertEquals('Hello <strong>Michael</strong>',
            $this->english->translate('Hello [strong: {user}]', '', array(
                    'strong' => function($txt){return "<strong>$txt</strong>";},
                    'user' => $this->male
                )
            )
        );

        $this->assertEquals('Hello <strong>World</strong>',
            $this->english->translate('Hello [strong: World]', '', array('strong' => '<strong>{$0}</strong>'))
        );

        $this->assertEquals('Hello <strong>World</strong>',
            $this->english->translate('Hello [strong: {world}]', '', array('world' => 'World', 'strong' => '<strong>{$0}</strong>'))
        );

        $this->assertEquals('This is <strong>Pretty <italic>Cool</italic>!</strong>',
            $this->english->translate('This is [strong: {pretty} [italic: {cool}]!]', '', array(
                'pretty' => 'Pretty',
                'cool' => 'Cool',
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );
    }

    public function testDefaultTranslationsWithTransformTokens() {
        $this->assertEquals('Michael, you have 10 messages',
            $this->english->translate('{user}, you have {count} messages', '', array('user'=>$this->male, 'count'=>10))
        );

        $this->assertEquals('Michael, you have 1 message',
            $this->english->translate('{user}, you have {count||message}', '', array('user'=>$this->male, 'count'=>1))
        );

        $this->assertEquals('Michael, you have 5 messages',
            $this->english->translate('{user}, you have {count||message}', '', array('user'=>$this->male, 'count'=>5))
        );

        $this->assertEquals('Michael, you have 5 messages',
            $this->english->translate('{user}, you have {count||message,messages}', '', array('user'=>$this->male, 'count'=>5))
        );

        $this->assertEquals('Michael, you have 5 messages',
            $this->english->translate('{user}, you have {count|| one: message, other: messages}', '', array('user'=>$this->male, 'count'=>5))
        );

        $this->assertEquals('1 person',
            $this->english->translate('{count||person}', '', array('count'=>1))
        );

        $this->assertEquals('2 people',
            $this->english->translate('{count||person}', '', array('count'=>2))
        );

        $this->assertEquals('people',
            $this->english->translate('{count|person}', '', array('count'=>2))
        );

        $this->assertEquals('people',
            $this->english->translate('{count|one:person, other:people}', '', array('count'=>2))
        );

        $this->assertEquals('people',
            $this->english->translate('{count | one: person, other: people}', '', array('count'=>2))
        );

        $this->assertEquals('Michael, you have <strong>5 messages</strong>',
            $this->english->translate('{user}, you have [strong: {count||message}]', '', array(
                'user'=>$this->male,
                'strong' => '<strong>{$0}</strong>',
                'count'=>5
            ))
        );

        $this->assertEquals('Michael, you have <strong>5 <italic>messages</italic></strong>',
            $this->english->translate('{user}, you have [strong: {count} [italic: {count|message}]]', '', array(
                'user'=>$this->male,
                'count'=>5,
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );

        // Gender transform tokens
        $michael = new \User("Michael", "male");
        $anna = new \User("Anna", "female");

        $this->assertEquals('He likes this',
            $this->english->translate('{user|He,She} likes this', '', array(
                'user' => $michael
            ))
        );

        $this->assertEquals('She likes this',
            $this->english->translate('{user|He,She} likes this', '', array(
                'user' => $anna
            ))
        );

        $this->assertEquals('Born on:',
            $this->english->translate('{user|Born on}:', '', array(
                'user' => $michael
            ))
        );

        $this->assertEquals('Born on:',
            $this->english->translate('{user|Born on}:', '', array(
                'user' => $anna
            ))
        );
    }

    public function testForeignTranslationsWithNoTokens() {

        self::cacheTranslations($this->app, 'Hello World', '', array("ru" => array(
            array("label" => "Привет Мир")
        )));

        // No Tokens
        $this->assertEquals('Привет Мир',
            $this->russian->translate("Hello World")
        );

        self::cacheTranslations($this->app, 'Invite', 'Action to invite', array("ru" => array(
            array("label" => "Пригласить")
        )));

        self::cacheTranslations($this->app, 'Invite', 'An invitation', array("ru" => array(
            array("label" => "Приглашение")
        )));

        $this->assertEquals('Пригласить',
            $this->russian->translate('Invite', 'Action to invite')
        );

        $this->assertEquals('Приглашение',
            $this->russian->translate('Invite', 'An invitation')
        );

        $this->assertEquals('Invite',
            $this->russian->translate('Invite', 'Something non-existent')
        );
    }

    public function testForeignTranslationsWithBasicTokens() {
        self::cacheTranslations($this->app, 'Hello {world}', '', array("ru" => array(
            array("label" => "Привет {world}")
        )));

        $this->assertEquals('Привет Мир',
            $this->russian->translate('Hello {world}', '', array('world' => 'Мир'))
        );

        $user = new \User("Михаил");

        self::cacheTranslations($this->app, 'Hello {user}', '', array("ru" => array(
            array("label" => "Привет {user}")
        )));

        $this->assertEquals('Привет Михаил',
            $this->russian->translate('Hello {user}', '', array('user' => $user))
        );
    }


    public function testForeignTranslationsWithDecorationTokens() {
        self::cacheTranslations($this->app, 'Hello [strong: World]', '', array("ru" => array(
            array("label" => "Привет [strong: Мир]")
        )));

        // Decoration Tokens
        $this->assertEquals('Привет <strong>Мир</strong>',
            $this->russian->translate('Hello [strong: World]', '', array('strong' => function($txt){
                return "<strong>$txt</strong>";
            }))
        );

        $this->assertEquals('Привет <strong>Мир</strong>',
            $this->russian->translate('Hello [strong: World]', '', array('strong' => '<strong>{$0}</strong>'))
        );

        self::cacheTranslations($this->app, 'Hello [strong: {world}]', '', array("ru" => array(
            array("label" => "Привет [strong: {world}]")
        )));

        $this->assertEquals('Привет <strong>Мир</strong>',
            $this->russian->translate('Hello [strong: {world}]', '', array('world' => 'Мир', 'strong' => '<strong>{$0}</strong>'))
        );

        self::cacheTranslations($this->app, 'This is [strong: pretty [italic: cool]!]', '', array("ru" => array(
            array("label" => "Это [strong: довольно [italic: круто]!]")
        )));

        $this->assertEquals('Это <strong>довольно <italic>круто</italic>!</strong>',
            $this->russian->translate('This is [strong: pretty [italic: cool]!]', '', array(
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );
    }


    public function testForeignLanguageAsDefaultLanguage() {
        Config::instance()->beginBlockWithOptions(array("locale" => 'ru'));

        $user = new \User("Михаил");

        $this->assertEquals('Михаил, у вас есть 5 сообщений',
            $this->russian->translate('{user}, у вас есть {count|| one: сообщение, few: сообщения, other: сообщений}', '', array('user'=>$user, 'count'=>5))
        );

        $this->assertEquals('Михаил, у вас есть 1 сообщение',
            $this->russian->translate('{user}, у вас есть {count|| one: сообщение, few: сообщения, other: сообщений}', '', array('user'=>$user, 'count'=>1))
        );

        $this->assertEquals('Михаил, у вас есть 2 сообщения',
            $this->russian->translate('{user}, у вас есть {count|| one: сообщение, few: сообщения, other: сообщений}', '', array('user'=>$user, 'count'=>2))
        );

        $michael = new \User("Михаил", "male");
        $anna = new \User("Анна", "female");

        $this->assertEquals('Ему это нравится',
            $this->russian->translate('{user|Ему, Ей} это нравится', '', array(
                'user' => $michael
            ))
        );

        $this->assertEquals('Ей это нравится',
            $this->russian->translate('{user|Ему, Ей} это нравится', '', array(
                'user' => $anna
            ))
        );

        $this->assertEquals('Родился:',
            $this->russian->translate('{user| male: Родился, female: Родилась}:', '', array(
                'user' => $michael
            ))
        );

        $this->assertEquals('Родилась:',
            $this->russian->translate('{user| male: Родился, female: Родилась}:', '', array(
                'user' => $anna
            ))
        );

        $this->assertEquals('Родилась:',
            $this->russian->translate('{user| Родился, Родилась}:', '', array(
                'user' => $anna
            ))
        );

        Config::instance()->finishBlockWithOptions();
    }

    public function testForeignTranslationsWithTransformTokens() {
        $user = new \User("Михаил");

        self::cacheTranslations($this->app, '{user}, you have {count||message}', '', array("ru" => array(
            array("label" => "{user}, у вас есть {count} сообщение", "context" => array("count" => array(
                "number" => "one"
            ))),
            array("label" => "{user}, у вас есть {count} сообщения", "context" => array("count" => array(
                "number" => "few")
            )),
            array("label" => "{user}, у вас есть {count} сообщений", "context" => array("count" => array(
                "number" => "other"
            )))
        )));

        // Numeric transform Tokens
        $this->assertEquals('Михаил, у вас есть 1 сообщение',
            $this->russian->translate('{user}, you have {count||message}', '', array('user'=>$user, 'count'=>1))
        );
        $this->assertEquals('Михаил, у вас есть 2 сообщения',
            $this->russian->translate('{user}, you have {count||message}', '', array('user'=>$user, 'count'=>2))
        );
        $this->assertEquals('Михаил, у вас есть 5 сообщений',
            $this->russian->translate('{user}, you have {count||message}', '', array('user'=>$user, 'count'=>5))
        );

        self::cacheTranslations($this->app, '{count||person}', '', array("ru" => array(
            array("label" => "{count} человек", "context" => array("count" => array(
                "number" => "one"
            ))),
            array("label" => "{count} человека", "context" => array("count" => array(
                "number" => "few"
            ))),
            array("label" => "{count} человек", "context" => array("count" => array(
                "number" => "other"
            )))
        )));

        $this->assertEquals('1 человек',
            $this->russian->translate('{count||person}', '', array('count'=>1))
        );

        $this->assertEquals('2 человека',
            $this->russian->translate('{count||person}', '', array('count'=>2))
        );

        $this->assertEquals('3 человека',
            $this->russian->translate('{count||person}', '', array('count'=>3))
        );

        $this->assertEquals('10 человек',
            $this->russian->translate('{count||person}', '', array('count'=>10))
        );

        self::cacheTranslations($this->app, '{count|person}', '', array("ru" => array(
            array("label" => "человек", "context" => array("count" => array(
                "number" => "one"
            ))),
            array("label" => "люди", "context" => array("count" => array(
                "number" => "few"
            ))),
            array("label" => "люди", "context" => array("count" => array(
                "number" => "other"
            )))
        )));

        $this->assertEquals('люди',
            $this->russian->translate('{count|person}', '', array('count'=>2))
        );

        self::cacheTranslations($this->app, '{count||message}', '', array("ru" => array(
            array("label" => "{count||one: сообщение, few: сообщения, many: сообщений}"),
        )));

        $this->assertEquals('2 сообщения',
            $this->russian->translate('{count||message}', '', array('count'=>2))
        );

        self::cacheTranslations($this->app, '{user}, you have [strong: {count||message}]', '', array("ru" => array(
            array("label" => "{user}, у вас есть [strong: {count||one: сообщение, few: сообщения, other: сообщений}]"),
        )));

        $this->assertEquals('Михаил, у вас есть <strong>1 сообщение</strong>',
            $this->russian->translate('{user}, you have [strong: {count||message}]', '', array(
                'user'      =>  $user,
                'strong'    => '<strong>{$0}</strong>',
                'count'     => 1
            ))
        );

        $this->assertEquals('Михаил, у вас есть <strong>5 сообщений</strong>',
            $this->russian->translate('{user}, you have [strong: {count||message}]', '', array(
                'user'=>$user,
                'strong' => '<strong>{$0}</strong>',
                'count'=>5
            ))
        );

        self::cacheTranslations($this->app, '{user}, you have [strong: {count} [italic: {count|message}]]', '', array("ru" => array(
            array("label" => "{user}, у вас есть [strong: {count} [italic: {count|one: сообщение, few: сообщения, other: сообщений}]]"),
        )));

        $this->assertEquals('Михаил, у вас есть <strong>1 <italic>сообщение</italic></strong>',
            $this->russian->translate('{user}, you have [strong: {count} [italic: {count|message}]]', '', array(
                'user'=>$user,
                'count'=>1,
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );

        $this->assertEquals('Михаил, у вас есть <strong>5 <italic>сообщений</italic></strong>',
            $this->russian->translate('{user}, you have [strong: {count} [italic: {count|message}]]', '', array(
                'user'=>$user,
                'count'=>5,
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );

        Config::instance()->beginBlockWithOptions(array("force_decorations" => true, "dry" => true));

        $this->assertEquals("<tml:label class='tml_translatable tml_translated' data-translation_key='1b165fc36a5fb5d7b1f1f45a7ff0f7fd' data-target_locale='ru'><tml:token class='tml_token tml_token_data' data-name='user'>Михаил</tml:token>, у вас есть <strong><tml:token class='tml_token tml_token_data' data-name='count'>5</tml:token> <italic>сообщений</italic></strong></tml:label>",
            $this->russian->translate('{user}, you have [strong: {count} [italic: {count|message}]]', '', array(
                'user'=>$user,
                'count'=>5,
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );

        $this->assertEquals("<tml:label class='tml_translatable tml_not_translated' data-translation_key='f8d414c8f884cdd3c41f15bd13d26690' data-target_locale='ru'><tml:token class='tml_token tml_token_data' data-name='user'>Михаил</tml:token>, you now have <strong><tml:token class='tml_token tml_token_data' data-name='count'>5</tml:token> <italic><tml:case class='tml_language_case' data-locale='en' data-rule='eyJrZXl3b3JkIjoicGx1cmFsIiwibGFuZ3VhZ2VfbmFtZSI6IkVuZ2xpc2giLCJsYXRpbl9uYW1lIjoiUGx1cmFsIiwibmF0aXZlX25hbWUiOm51bGwsImNvbmRpdGlvbnMiOiIobWF0Y2ggJ1wvJFwvJyBAdmFsdWUpIiwib3BlcmF0aW9ucyI6IihyZXBsYWNlICdcLyRcLycgJ3MnIEB2YWx1ZSkiLCJvcmlnaW5hbCI6Im1lc3NhZ2UiLCJ0cmFuc2Zvcm1lZCI6Im1lc3NhZ2VzIn0%3D'>messages</tml:case></italic></strong></tml:label>",
            $this->russian->translate('{user}, you now have [strong: {count} [italic: {count|message}]]', '', array(
                'user'=>$user,
                'count'=>5,
                'strong' => '<strong>{$0}</strong>',
                'italic' => '<italic>{$0}</italic>'
            ))
        );

        Config::instance()->finishBlockWithOptions();

        // Gender transform tokens
        $michael = new \User("Michael", "male");
        $anna = new \User("Anna", "female");

        self::cacheTranslations($this->app, '{user|He,She} likes this', '', array("ru" => array(
            array("label" => "{user|Ему, Ей} это нравится"),
        )));

        $this->assertEquals('Ему это нравится',
            $this->russian->translate('{user|He,She} likes this', '', array(
                'user' => $michael
            ))
        );

        $this->assertEquals('Ей это нравится',
            $this->russian->translate('{user|He,She} likes this', '', array(
                'user' => $anna
            ))
        );

    }

    public function testDefaultLanguageCases() {
        $this->assertEquals('This is your 1st warning',
            $this->english->translate('This is your {count::ord} warning', '', array('count' => 1))
        );

        $this->assertEquals('This is your 2nd warning',
            $this->english->translate('This is your {count::ord} warning', '', array('count' => 2))
        );

        $this->assertEquals('This is your 3rd warning',
            $this->english->translate('This is your {count::ord} warning', '', array('count' => 3))
        );

        $this->assertEquals('This is your 4th warning',
            $this->english->translate('This is your {count::ord} warning', '', array('count' => 4))
        );

        $this->assertEquals('This is your first warning',
            $this->english->translate('This is your {count::ordinal} warning', '', array('count' => 1))
        );

        $this->assertEquals('This is your second warning',
            $this->english->translate('This is your {count::ordinal} warning', '', array('count' => 2))
        );

        $this->assertEquals('This is your third warning',
            $this->english->translate('This is your {count::ordinal} warning', '', array('count' => 3))
        );

        $this->assertEquals('This is your 4 warning',
            $this->english->translate('This is your {count::ordinal} warning', '', array('count' => 4))
        );

        $this->assertEquals('This is your 4th warning',
            $this->english->translate('This is your {count::ordinal::ord} warning', '', array('count' => 4))
        );

        $this->assertEquals('This has already happened once before.',
            $this->english->translate('This has already happened {count::times} before.', '', array('count' => 1))
        );

        $this->assertEquals('This has already happened twice before.',
            $this->english->translate('This has already happened {count::times} before.', '', array('count' => 2))
        );

        $this->assertEquals('This has already happened 3 times before.',
            $this->english->translate('This has already happened {count::times} before.', '', array('count' => 3))
        );

        $michael = new \User("Michael", "male");
        $this->assertEquals("This is Michael's message",
            $this->english->translate('This is {user::pos} message', '', array('user' => $michael))
        );
    }

    public function testTranslatedLanguageCases() {
        $michael = new \User("Михаил", "male");
        $anna = new \User("Анна", "female");

        self::cacheTranslations($this->app, '{actor} sent {target} a present.', '', array("ru" => array(
            array("label" => "{actor||прислал,прислала} подарок {target::dat}."),
        )));

        $this->assertEquals('Анна прислала подарок Михаилу.',
            $this->russian->translate('{actor} sent {target} a present.', '', array('actor' => $anna, 'target' => $michael))
        );

        $this->assertEquals('Михаил прислал подарок Анне.',
            $this->russian->translate('{actor} sent {target} a present.', '', array('target' => $anna, 'actor' => $michael))
        );

        self::cacheTranslations($this->app, '{actor} is thinking about {target}.', '', array("ru" => array(
            array("label" => "{actor} думает {target::pre::about}."),
        )));

        $this->assertEquals('Михаил думает об Анне.',
            $this->russian->translate('{actor} is thinking about {target}.', '', array('target' => $anna, 'actor' => $michael))
        );

//        $this->assertEquals('Анна думает о Михаиле.',
//            $this->russian->translate('{actor} is thinking about {target}.', '', array('actor' => $anna, 'target' => $michael))
//        );

    }
}