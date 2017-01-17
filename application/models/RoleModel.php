<?php

namespace App\models;

use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * 用户模型。
 *
 * @package	BootCMS
 * @category	模型
 * @author		Tinsh
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class RoleModel extends Model
{
    private $_values = null;
    private $_loaded = false;
    protected $_tableName = 'roles';

    /**
     * 创建并返回一个新的模型对象。
     *
     * @return	对象
     */
    public static function factory($name, $application = 'system')
    {
        return parent::factory($name, $application);
    }

    /**
     * 根据主键加载数据，并返回对象
     * @return	对象
     */
    public function load($id = 0)
    {
        if (is_numeric($id) && $id > 0) {
            $this->_values = $this->where('id', '=', $id)->where('author_id', '=', 1)->first();
            $this->_loaded = true;
        }
        return $this->_values;
    }

    /**
     * 角色列表
     */
    public function roleList($itemsPerPage = 10)
    {
        $list = ['data' => null];

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = $itemsPerPage * ($page - 1);
        $list = $this->limit($itemsPerPage)->offset($offset)->orderBy('id', 'desc')->cached()->findAll();
        foreach ($list as &$node) {
            $node->operation = '<a href="' . $this->baseUrl . 'users/roles/' . $node->id . '/edit">编辑</a>';
        }

        $count = $this->count();

        $pager = \Bootphp\Pagination\Pagination::factory([
                    'totalItems' => $count,
                    'itemsPerPage' => $itemsPerPage,
                    'firstPageInUrl' => true,
        ]);

        $list['data'] = $list;
        $list['pager'] = $pager->render();

        return $list;
    }

}
