<p align="center">
  <img src="https://avatars0.githubusercontent.com/u/1316274?v=3&s=200">
</p>

Tml for PHP
==================

[![Latest Stable Version](https://poser.pugx.org/translationexchange/tml/v/stable)](https://packagist.org/packages/translationexchange/tml)
[![Build Status](https://travis-ci.org/translationexchange/tml-php.svg?branch=master)](https://travis-ci.org/translationexchange/tml-php)
[![Coverage Status](https://coveralls.io/repos/translationexchange/tml-php/badge.svg)](https://coveralls.io/r/translationexchange/tml-php)
[![Latest Unstable Version](https://poser.pugx.org/translationexchange/tml/v/unstable)](https://packagist.org/packages/translationexchange/tml)
[![Dependency Status](https://www.versioneye.com/user/projects/54c9c297de7924f81a00000c/badge.svg?style=flat)](https://www.versioneye.com/user/projects/54c9c297de7924f81a00000c)
[![Total Downloads](https://poser.pugx.org/translationexchange/tml/downloads)](https://packagist.org/packages/translationexchange/tml)
[![License](https://poser.pugx.org/translationexchange/tml/license)](https://packagist.org/packages/translationexchange/tml)

Installation
==================

Tml for PHP can be installed using the composer dependency manager. If you don't already have composer installed on your system, you can get it using the following command:

```sh
$ cd YOUR_APPLICATION_FOLDER
$ curl -s http://getcomposer.org/installer | php
```

Create composer.json in the root folder of your application, and add the following content:

```json
{
    "require": {
        "translationexchange/tml": "dev-master"
    }
}
```

This tells composer that your application requires tml library to be installed.

Now install Tml library by executing the following command:

```sh
$ composer install
```

Composer will automatically create a vendor folder and put the SDK into vendor/tr8n/tr8n-client-sdk directory.

Now you are ready to integrate Tr8n into your application.


Integration
==================

Before you can proceed with the integration, please visit http://translationexchange.com to register your application.

Once you have created a new application, you will be given an application key and a secret. You will need to enter them in the initialization function of the Tr8n SDK.

To make sure you have installed everything correctly, create a sample test file in the root folder of your app and call it index.php

Paste the following content into the file:

```php
<?php require_once(__DIR__ . '/vendor/translationexchange/tml/src/init.php'); ?>
<?php tml_init(array(
    "key" => YOUR_APPLICATION_TOKEN,
    "token" => YOUR_APPLICATION_TOKEN
)); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo tml_current_locale(); ?>">
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <?php tml_scripts(); ?>
</head>
<body>
    <?php tre("Hello World") ?>
</body>
</html>
<?php tml_complete_request() ?>
```


Make sure you replace YOUR_APPLICATION_KEY with the key and YOUR_APPLICATION_TOKEN with the token you copied from translationexchange.com

Now you can open up your browser and navigate to the file:

http://localhost/your_app_path/index.php


If everything was configured correctly, you should see a phrase "Hello World" on your page.

Press the following keys:  Ctrl+Shift+S

You should see a lightbox with Tr8n's default shortcuts. You can configure those shortcuts in the application administration section.

To close the lightbox, click on the top-right corner or simply press the Esc button.

Press Ctrl+Shift+L to switch to a different language.

Now you can press Ctrl+Shift+I to enable inline translations.

When inline translations are enabled you will see translated phrases underlined in green color and not translated phrases with red.

Right-Mouse-Click (or Ctrl+Click on Mac) on any phrase and you will see an inline translator window that will allow you to translate the phrase.

To learn about various integration options and TML features, visit our online interactive documentation:


Links
==================

* Register at TranslationExchange.com: http://translationexchange.com

* Follow TranslationExchange on Twitter: https://twitter.com/translationx

* Connect with TranslationExchange on Facebook: https://www.facebook.com/translationexchange

* If you have any questions or suggestions, contact us: info@translationexchange.com


Copyright and license
==================

Copyright (c) 2015 Translation Exchange, Inc.

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.