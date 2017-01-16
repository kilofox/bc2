<?php

namespace App\modules\articles\controllers;

use App\modules\system\controllers\AdministrationController;
use App\modules\articles\models\UserModel;
use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * 后台文章分类控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright (C) 2005-2016 Kilofox Studio
 */
class CategoriesController extends AdministrationController
{
    /**
     * Before 方法
     */
    public function before()
    {
        $this->routes['<id>/edit'] = 'edit';
        $this->routes['<id>/delete'] = 'delete';
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
     * 文章分类列表
     */
    public function indexAction()
    {
        $this->title = '文章分类';
        $this->keyList = [
            'id' => ['alias' => 'ID'],
            'title' => ['alias' => '昵称'],
            'description' => ['alias' => '描述'],
            'operation' => ['alias' => '操作'],
        ];
        // 文章分类列表
        $list = Model::factory('category', 'articles')->categoryList(3);
        $this->commonList($list);
    }

    /**
     * 创建一个新的文章分类
     */
    public function createAction()
    {
        if ($this->request->method() === 'POST') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            $model = Model::factory('category', 'articles');
            try {
                $create = [
                    'name' => $this->request->post('name', ''),
                    'description' => $this->request->post('description', ''),
                ];
                if ($model->create($create)) {
                    $status = 1;
                    $info = '新用户创建成功。';
                } else {
                    $status = 5;
                    $info = '新用户创建失败。';
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
        $this->title = '创建文章分类';
    }

    /**
     * 编辑文章分类
     */
    public function editAction()
    {
        $itemId = intval($this->request->param('id'));
        $model = Model::factory('category', 'articles');
        $item = $model->find($itemId);
        if ($this->request->method() === 'PUT') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            if (!$item) {
                $status = 3;
                $info = '请求的文章分类不存在。';
                $this->ajaxReturn($status, $info);
            }
            try {
                $update = [
                    'name' => $this->request->put('name'),
                    'description' => $this->request->put('description'),
                ];
                if ($model->update($update, ['id', '=', $item->id])) {
                    $status = 1;
                    $info = '文章分类已经更新成功。';
                } else {
                    $status = 5;
                    $info = '文章分类没有更新。';
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
        $this->title = '编辑文章分类';
        $this->keyList = [
            'id' => ['alias' => 'ID', 'disabled' => true],
            'title' => ['alias' => '名称', 'align' => 'center'],
            'description' => ['alias' => '描述', 'tag' => 'textarea']
        ];
        $this->updateUrl = $this->baseUrl . 'articles/roles/' . $item->id . '/edit';
        $this->commonEdit($item);
    }

    /**
     * 删除文章分类
     */
    public function deleteAction()
    {
        if ($this->request->method() === 'DELETE') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            $itemId = intval($this->request->param('id'));
            $model = Model::factory('category', 'articles');
            $item = $model->find($itemId);
            if (!$item) {
                $status = 3;
                $info = '请求的文章分类不存在。';
                $this->ajaxReturn($status, $info);
            }
            if ($item->id <= 5) {
                $status = 3;
                $info = '系统内置文章分类不可删除。';
                $this->ajaxReturn($status, $info);
            }
            try {
                if ($model->delete(['id', '=', $item->id])) {
                    $status = 1;
                    $info = '文章分类已经删除。';
                } else {
                    $status = 5;
                    $info = '文章分类删除失败。';
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
    }

}
