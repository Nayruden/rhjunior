<?php
error_reporting( E_ALL );
require_once( './defines.php' );
require_once( './rss.class.php' );

$myfeed = new RSSFeed();
$myfeed->setChannel( 'RHJunior Webcomics',
                     'http://rhjunior.com/', 
                     'Updates on RHJunior Webcomics' );

$myfeed->addItem( 'TOTQ',
                  'http://www.rhjunior.com/totq/00534.html',
                  htmlspecialchars( '<img src="http://www.rhjunior.com/totq/Images/00538.png"/>' ) );

echo $myfeed->buildRSS();
?>