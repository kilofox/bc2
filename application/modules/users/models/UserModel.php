<?php

namespace App\modules\users\models;

use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * 用户模型。
 *
 * @package		BootCMS
 * @category	模型
 * @author		Tinsh
 * @copyright	(C) 2005-2015 Kilofox Studio
 */
class UserModel extends Model
{
    private $_values = null;
    private $_loaded = false;
    protected $_tableName = 'users';

    /**
     * 创建并返回一个新的模型对象。
     *
     * @return	对象
     */
    public static function factory($name, $application = 'core')
    {
        return parent::factory($name, $application);
    }

    /**
     * Complete the login for a user by incrementing the logins and saving login timestamp
     *
     * @return void
     */
    public function completeLogin($user)
    {
        DB::update($this->_tableName)->set(['logins' => DB::expr('logins + 1'), 'last_login' => time()])->where('id', '=', $user->id)->execute();
    }

    /**
     * 根据主键加载数据，并返回对象
     * @return	对象
     */
    public function load($id = 0)
    {
        if (is_numeric($id) && $id > 0) {
            $this->_values = DB::table('nodes')->select('*')->where('id', '=', $id)->where('author_id', '=', 1)->first();
            $this->_loaded = true;
        }
        return $this->_values;
    }

    /**
     * 用户列表
     */
    public function userList($itemsPerPage = 10)
    {
        $list = ['data' => null];

        $list = Model::factory('user', 'users')->findAll();
        foreach ($list as &$node) {
            $node->created = \Bootphp\Date::unixToHuman($node->created);
            $node->operation = '<a href="' . $this->baseUrl . 'users/admin/' . $node->id . '/edit">编辑</a>';
        }

        $count = $this->count();

        $pager = \Bootphp\Pagination\Pagination::factory(array(
                    'totalItems' => $count,
                    'itemsPerPage' => $itemsPerPage,
                    'firstPageInUrl' => true,
        ));

        $list['data'] = $list;
        $list['pager'] = $pager->render();
        return $list;
    }

}
