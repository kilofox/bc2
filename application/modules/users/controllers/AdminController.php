<?php

namespace App\modules\users\controllers;

use App\modules\system\controllers\AdministrationController;
use App\modules\users\models\UserModel;
use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * 后台用户控制器。
 *
 * @package	BootCMS
 * @category	Controller
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class AdminController extends AdministrationController
{
    /**
     * Before 方法
     */
    public function before()
    {
        $this->routes['<id>/edit'] = 'edit';
        parent::before();
    }

    /**
     * After 方法
     */
    public function after()
    {
        parent::after();
    }

    /*
     * 用户列表
     */
    public function indexAction()
    {
        $this->title = '用户';
        $this->keyList = [
            'id' => ['alias' => 'ID'],
            'nickname' => ['alias' => '昵称', 'align' => 'center'],
            'email' => ['alias' => 'E-mail'],
            'created' => ['alias' => '注册时间'],
            'operation' => ['alias' => '操作'],
        ];
        // 用户列表
        $list = Model::factory('user', 'users')->userList(3);
        $this->commonList($list);
    }

    /**
     * 编辑用户
     */
    public function editAction()
    {
        $userId = intval($this->request->param('id'));
        $oUser = Model::factory('user', 'users');
        $user = $oUser->find($userId);
        if ($this->request->method() === 'PUT') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            if (!$user) {
                $status = 3;
                $info = '请求的用户不存在。';
                $this->ajaxReturn($status, $info);
            }
            try {
                $update = [
                    'nickname' => $this->request->put('nickname'),
                    'email' => $this->request->put('email'),
                    'address' => $this->request->put('address'),
                ];
                if ($oUser->update($update, ['id', '=', $user->id])) {
                    $status = 1;
                    $info = '用户已经更新成功。';
                } else {
                    $status = 5;
                    $info = '用户没有更新。';
                }
            } catch (Validation_Exception $e) {
                $errors = $e->errors('models');
                foreach ($errors as $ev) {
                    $status = 4;
                    $info = $ev;
                    break;
                }
            }
            $this->ajaxReturn($status, $info);
        }

        $this->title = '编辑用户';
        $this->updateUrl = '/users/admin/' . $user->id . '/edit';
        $this->keyList = [
            'id' => ['alias' => 'ID', 'disabled' => true],
            'nickname' => ['alias' => '昵称', 'align' => 'center'],
            'email' => ['alias' => 'E-mail'],
            'address' => ['alias' => '地址', 'tag' => 'textarea']
        ];
        $this->commonEdit($user);
    }

}
