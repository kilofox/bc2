<?php

namespace App\controllers\admin;

use App\controllers\adimin\AdministrationController;
use Bootphp\Model;

/**
 * 后台菜单控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class MenusController extends AdministrationController
{
    /**
     * Before 方法
     */
    public function before()
    {
        $this->routes['<id>/edit'] = 'edit';
        $this->routes['<id>/submenus'] = 'submenus';
        parent::before();
        $this->templatePath = APP_PATH . '/modules/system/views/default/menus/';
        $this->model = Model::factory('menu', 'system');
    }

    /**
     * After 方法
     */
    public function after()
    {
        parent::after();
    }

    /*
     * 默认方法
     */
    public function indexAction()
    {
        if ($this->request->method() == 'POST') {
            $this->creationAction();
        }
        $menus = Model::factory('menu', 'system')->where('parent_id', '=', 0)->orderBy('sort')->findAll();
        $this->assign('nodes', $menus);
        $this->template = 'menus';
    }

    /*
     * 默认方法
     */
    public function submenusAction()
    {
        $parentId = (int) $this->request->param('id');
        $menus = Model::factory('menu', 'system')->where('parent_id', '=', $parentId)->orderBy('sort')->findAll();
        $this->assign('nodes', $menus);
        $this->template = 'menus';
    }

    /**
     * 创建新菜单
     */
    public function creationAction()
    {
        if ($this->request->method() == 'POST') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            try {
                $creation = [
                    'title' => $this->request->post('title'),
                    'parent_id' => $this->request->post('parent_id'),
                    'sort' => $this->request->post('sort'),
                    'application' => $this->request->post('application'),
                    'controller' => $this->request->post('controller'),
                    'action' => $this->request->post('action'),
                    'icon' => $this->request->post('icon')
                ];
                if ($this->model->create($creation)) {
                    $status = 1;
                    $info = '菜单已经创建成功。';
                } else {
                    $status = 5;
                    $info = '菜单创建失败。';
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

    /**
     * 编辑菜单
     */
    public function editAction()
    {
        $itemId = (int) $this->request->param('id');
        $item = $this->model->find($itemId);
        if ($this->request->method() == 'PUT') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            if (!$item) {
                $status = 3;
                $info = '请求的菜单不存在。';
                $this->ajaxReturn($status, $info);
            }
            try {
                $update = [
                    'title' => $this->request->put('title'),
                    'parent_id' => $this->request->put('parent_id'),
                    'sort' => $this->request->put('sort'),
                    'application' => $this->request->put('application'),
                    'controller' => $this->request->put('controller'),
                    'action' => $this->request->put('action'),
                    'icon' => $this->request->put('icon')
                ];
                if ($this->model->update($update, ['id', '=', $item->id])) {
                    $status = 1;
                    $info = '菜单已经更新成功。';
                } else {
                    $status = 5;
                    $info = '菜单没有更新。';
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
        $this->tab = 'menus';
        $this->assign('node', $item);
    }

}