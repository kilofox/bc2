<?php

namespace App\Model;

use Bootphp\Model;

/**
 * 节点模型。
 *
 * @package	BootCMS
 * @category	模型
 * @author		Tinsh
 @copyright  (C) 2005-2017 Kilofox Studio
 */
class SiteModel extends Model
{
    protected $_tableName = 'sites';

    /**
     * 创建并返回一个新的模型对象。
     *
     * @return	对象
     */
    public static function factory($name, $application = 'system')
    {
        return parent::factory($name, $application);
    }

}
