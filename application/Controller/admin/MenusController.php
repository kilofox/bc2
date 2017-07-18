<?php

namespace App\Controller\admin;

use App\Controller\admin\AdministrationController;
use Bootphp\ORM\ORM;
use Bootphp\I18n;
use Bootphp\URL;
use Bootphp\Validation\ValidationException;

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
        parent::before();

        $this->model = ORM::factory('menu');
        \Bootphp\I18n::lang('zh-cn');
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
        if ($this->request->method() === 'POST') {
            $this->creationAction();
        }

        $menus = $this->model->where('parent_id', '=', 0)->orderBy('sort')->findAll();
        foreach ($menus as &$node) {
            $node->operation = '<a href="' . $this->baseUrl . 'admin/menus/' . $node->id . '/edit">Edit</a>';
        }
        $pager = \Bootphp\Pagination\Pagination::factory();

        $list = [
            'data' => $menus,
            'pager' => $pager->render('admin/pagination/metro')
        ];

        $this->keyList = [
            'id' => [
                'alias' => I18n::get('ID')
            ],
            'title' => [
                'alias' => I18n::get('Title')
            ],
            'parent_id' => [
                'alias' => I18n::get('Category')
            ],
            'sort' => [
                'alias' => I18n::get('Order')
            ],
            'operation' => [
                'alias' => I18n::get('Operation')
            ]
        ];

        $this->commonList($list);
    }

    /*
     * 默认方法
     */
    public function submenusAction()
    {
        $parentId = (int) $this->request->getAttribute('id');
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
            } catch (ValidationException $e) {
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
        $itemId = (int) $this->request->getParam('id');
        $item = $this->model->find($itemId);
        if ($this->request->method() === 'PUT') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            if (!$item) {
                $status = 3;
                $info = '请求的菜单不存在。';
                $this->ajaxReturn($status, $info);
            }
            try {
                $this->model->title = $this->request->data('title');
                $this->model->parent_id = $this->request->data('parent_id');
                $this->model->sort = $this->request->data('sort');
               // $this->model->application = $this->request->data('application');
              //  $this->model->controller = $this->request->data('controller');
               // $this->model->action = $this->request->data('action');
              //  $this->model->icon = $this->request->data('icon');
                if ($this->model->update()) {
                    $status = 1;
                    $info = '菜单已经更新成功。';
                } else {
                    $status = 5;
                    $info = '菜单没有更新。';
                }
            } catch (ValidationException $e) {
                $errors = $e->errors('models');
                foreach ($errors as $ev) {
                    $status = 4;
                    $info = $ev;
                    break;
                }
            }
            exit(json_encode(array('status' => $status, 'info' => $info, 'data' => '')));
            $this->ajaxReturn($status, $info);
        }

        $this->keyList = [
            'id' => [
                'alias' => I18n::get('ID')
            ],
            'title' => [
                'alias' => I18n::get('Title')
            ],
            'parent_id' => [
                'alias' => I18n::get('Category')
            ],
            'sort' => [
                'alias' => I18n::get('Order')
            ]
        ];

        $this->updateUrl = URL::site('admin/menus/' . $itemId . '/edit');

        $this->commonEdit($item);
    }

}
