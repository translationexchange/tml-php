<h2><?php tre("Installation") ?></h2>

<p>
    <?php tre("Tml Client SDK for PHP can be installed using the composer dependency manager. If you don't already have composer installed on your system, you can get it using the following command:") ?>
</p>

<pre><code class="language-bash">$ cd YOUR_APPLICATION_FOLDER
$ curl -s http://getcomposer.org/installer | php
</code></pre>

<p>
    <?php tre("Create {file} in the root folder of your application, and add the following content:", array("file" => "composer.json")) ?>
</p>

<pre><code class="language-javascript">{
  "require": {
    "translationexchange/tml": "dev-master"
  }
}
</code></pre>

<p>
    <?php tre("This tells composer that your application requires tml-client-sdk library to be installed.") ?>
</p>

<p>
    <?php tre("Now install Tml SDK library by executing the following command:") ?>
</p>

<pre><code class="language-bash">$ php composer.phar install</code></pre>

<p>
    <?php tre("Composer will automatically create a vendor folder and put the SDK into {path} directory.", array("path" => "vendor/tml/tml-client-sdk")) ?>
</p>

<p>
    <?php tre("Now you are ready to integrate Tml into your application.") ?>
</p>





