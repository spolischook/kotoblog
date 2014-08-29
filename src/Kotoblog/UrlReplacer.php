<?php

namespace Kotoblog;

class UrlReplacer
{
    static public $urlRedirect = array(
        '/category/wordpress' => '/tags/wordpress',
        '/news/function.preg-replace-callback' => '/',
        '/ubuntu/printer-canon-lbp-na-ubuntu-1010.html/comment-page-1' => '/ubuntu/printer-canon-lbp-na-ubuntu-1010.html',
        '/news/podbor-parolya-k-joomla-adminke.html/comment-page-1' => '/news/podbor-parolya-k-joomla-adminke.html',
        '/news/podbor-parolya-k-joomla-adminke.html/feed' => '/news/podbor-parolya-k-joomla-adminke.html',
        '/wordpress/nestandartnye-shrifty-na-sajte-font-html-css.html/comment-page-1' => '/wordpress/nestandartnye-shrifty-na-sajte-font-html-css.html',
        '/news/ustanovka-composer-v-ubuntu-12-04.html/comment-page-1' => '/news/ustanovka-composer-v-ubuntu-12-04.html',
        '/foto/eti-smeshnye-koty.html/comment-page-1' => '/foto/eti-smeshnye-koty.html',
        '/php/php-razbit-stroku-na-podstroki-zadannoj-dliny.html/comment-page-1' => '/php/php-razbit-stroku-na-podstroki-zadannoj-dliny.html',
        '/news/resheno-2002-nevozmozhno-podklyuchitsya-k-serveru-mysql.html/comment-page-1' => '/news/resheno-2002-nevozmozhno-podklyuchitsya-k-serveru-mysql.html',
        '/ubuntu/xampp-ubuntu-1010-nastrojka-virtualnyx-xostov.html/comment-page-1' => '/ubuntu/xampp-ubuntu-1010-nastrojka-virtualnyx-xostov.html',
        '/wp-content/uploads/2010/12/2510945.jpg' => '/',
        '/ubuntu/xampp-ubuntu-1010-nastrojka-virtualnyx-xostov.html' => '/news/xampp-ubuntu-1010-nastrojka-virtualnyx-xostov.html'
    );

    static public $urlMap = array(
        '/articles/work-with-doctrine-annotation-reader' => '/news/work-with-doctrine-annotation-reader.html',
        '/articles/eti-smieshnyie-koty' => '/foto/eti-smeshnye-koty.html',
        '/articles/curl-php5-ubuntu-pravil-naia-ustanovka' => '/php/curl-php5-ubuntu-pravilnaya-ustanovka.html',
        '/articles/denwer-i-kodirovka-utf-8' => '/php/denwer-i-kodirovka-utf-8.html',
        '/articles/ustanovka-composer-v-ubuntu-12-04' => '/news/ustanovka-composer-v-ubuntu-12-04.html',
        '/articles/ustanovka-curl-ubuntu-12-04' => '/news/curl-ubuntu-12-04.html',
        '/articles/shablon-vyvoda-spiska-tovarov-katieghorii-prestashop' => '/php/shablon-vyvoda-spiska-tovarov-kategorii-prestashop.html',
        '/articles/zapusk-php-skripta-po-cron-v-ubuntu-10-10' => '/php/zapusk-php-skripta-po-cron-v-ubuntu-1010.html',
        '/articles/sudo-npm-command-not-found-ubuntu' => '/ubuntu/sudo-npm-command-not-found-ubuntu.html',
        '/articles/biesshovnyi-fon-v-gimp' => '/dizajn/besshovnyj-fon-v-gimp.html',
        '/articles/smieshnyie-foto' => '/foto/smeshnye-foto.html',
        '/articles/php-razbit-stroku-na-podstroki-zadannoi-dliny' => '/php/php-razbit-stroku-na-podstroki-zadannoj-dliny.html',
        '/articles/phpunit-ubuntu-12-04' => '/news/phpunit-ubuntu-12-04.html',
        '/articles/koty-v-iskusstvie' => '/foto/koty-v-iskusstve.html',
        '/articles/behat-mink-symfony-2-1' => '/php/behat-mink-symfony-2-1.html',
        '/articles/zdorov-ie-kota-samoie-ghlavnoie' => '/zdorove/zdorove-kota-samoe-glavnoe.html',
        '/articles/niestandartnyie-shrifty-na-saitie-font-html-css' => '/wordpress/nestandartnye-shrifty-na-sajte-font-html-css.html',
        '/articles/podbor-parolia-k-joomla-adminkie' => '/news/podbor-parolya-k-joomla-adminke.html',
        '/articles/setup-environment-for-symfony2-nastroika-okruzhieniia-dlia-symfony2-na-ubuntu-12-04' => '/news/setup-environment-for-symfony2-nastrojka-okruzheniya-dlya-symfony2-na-ubuntu-12-04.html',
        '/articles/www-data-php-mail-i-ssmtp-niepravil-nyi-from-v-pis-makh' => '/news/www-data-php-mail-i-ssmtp-nepravilnyj-from-v-pismax.html',
        '/articles/novoghodniie-koty' => '/foto/novogodnie-koty.html',
        '/articles/printier-canon-lbp-na-ubuntu-10-10' => '/ubuntu/printer-canon-lbp-na-ubuntu-1010.html',
        '/articles/xampp-ubuntu-10-10-nastroika-virtual-nykh-khostov' => '/news/xampp-ubuntu-1010-nastrojka-virtualnyx-xostov.html',
        '/articles/rieshieno-2002-nievozmozhno-podkliuchit-sia-k-siervieru-mysql' => '/news/resheno-2002-nevozmozhno-podklyuchitsya-k-serveru-mysql.html',
//        '/news/doctrinecachebundle-apc.html' => '',
        '/articles/global-nyie-pieriemiennyie-twig-symfony2' => '/php/globalnye-peremennye-twig-symfony2.html',
        '/articles/mongodb-symfony2-travis-ci' => '/news/mongodb-symfony2-travis-ci.html',
    );
}
