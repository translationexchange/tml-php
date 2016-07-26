<a name="integration"></a>
<?php trhe("

    <h2>Integration</h2>
    <p>
      Before you can proceed with the integration, you need to <a href='https://dashboard.translationexchange.com'>register a new application on TranslationExchange.com</a> and copy the key and the secret for the app.
      You will need to enter them in the initialization function of the Tml SDK.
    </p>

    <h4>Include Tml Header</h4>
    <p>
        In order to use Tml SDK in your application, you need to include the main Tml script in the header of your app:
    </p>

"); ?>

<pre><code class="language-php">&lt;?php require_once(__DIR__ . '/vendor/translationexchange/tml/src/init.php'); ?&gt;
</code></pre>

<?php trhe("
    <h4>Initialize Tml SDK</h4>
    <p>
      To initialize the SDK, call the initialization function with the key and secret of your application:
    </p>
"); ?>

<pre><code class="language-php">&lt;?php tml_init(YOUR_APPLICATION_TOKEN); ?&gt;
</code></pre>

<?php trhe("
    <h4>Include Tml JavaScript</h4>

    <p>
      Tml scripts provide Javascript and Stylesheet necessary for enabling inline translation features as well as cross domain communication with the Tml service.
    </p>

    <p>
      To include Tml scripts, add the following line into the head section of your php file:
    </p>
"); ?>

<pre><code class="language-php">&lt;?php tml_scripts(); ?&gt;
</code></pre>

<?php trhe("
    <p>
      This directive loads all necessary classes and provides a set of tags/includes you can use throughout your application. For instance, you now can use Tml shortcuts by pressing CTRL+SHIFT+S, language selector - CTRL+SHIFT+L, etc...
    </p>

    <h4>Complete Page Request</h4>

    <p>
      When Tml is run in translation mode, it automatically registers new translation keys with TranslationExchange service. In order for Tml to know when to send the new keys to the service it must know when the request is completed.
    </p>

    <p>
      Add the following line as the last statement in your application:
    </p>
"); ?>

<pre><code class="language-php">&lt;?php tml_complete_request() ?&gt;
</code></pre>

<?php trhe("
    <h4>Full Page Example</h4>

    <p>
      Here is a full example of the source code with the above directives:
    </p>
"); ?>

<pre><code class="language-php" style="font-size:10px">&lt;?php require_once(__DIR__ . '/vendor/translationexchange/tml/src/init.php'); ?&gt;

&lt;?php tml_init("YOUR_APPLICATION_TOKEN") ?&gt;

&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"&gt;

&lt;html xmlns="http://www.w3.org/1999/xhtml" lang="&lt;?php echo tml_current_locale() ?&gt;"&gt;

&lt;head&gt;
  &lt;meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" /&gt;
  &lt;?php tml_scripts() ?&gt;
&lt;/head&gt;

&lt;body&gt;
  &lt;?php tre("Hello World") ?&gt;
&lt;/body&gt;

&lt;/html&gt;

&lt;?php tml_complete_request() ?&gt;
</code></pre>

<p>
    <?php tre("Make sure you replace {token} with the token you copied from TranslationExchange", array("token"=>"YOUR_APPLICATION_TOKEN")) ?>
</p>

<p>
  Now you can open up your browser and navigate to the file you created. If everything was configured correctly, you should see a phrase "Hello World" on your page.
</p>

<p>
  You should see a lightbox with Tml's default shortcuts. You can configure those shortcuts in the application administration section.
</p>

<p>
  Press Ctrl+Shift+L to switch to a different language.
</p>

<p>
  Now you can press Ctrl+Shift+I to enable inline translations.
</p>

<p>
  When inline translations are enabled you will see translated phrases underlined in green color and not translated phrases with red.
</p>

<p>
  Right-Mouse-Click (or Ctrl+Click on Mac) on any phrase and you will see an inline translator window that will allow you to translate the phrase.
</p>


