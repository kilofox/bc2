<?php

namespace App\modules\index\controllers;

class IndexController extends \Bootphp\Controller
{
    public function indexAction()
    {
        $isCard = \Bootphp\Text\Text::autoLinkUrls('www.kilofox.net');
        echo $isCard;
        echo \Bootphp\I18n::get('hello, world!');
        echo '<br/>';
        echo \Bootphp\I18n::get('hello, world!', null, 'file', 'zh-cn');
    }

}
