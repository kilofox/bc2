<?php

namespace App\modules\articles\controllers;

use App\modules\system\controllers\AdministrationController;
use App\modules\articles\models\ArticleModel;
use Bootphp\Model;
use Bootphp\Database\DB;

/**
 * 后台文章控制器。
 *
 * @package BootCMS
 * @category 控制器
 * @author Tinsh
 * @copyright (C) 2005-2015 Kilofox Studio
 */
class AdminController extends AdministrationController
{
    /**
     * Before 方法
     */
    public function before()
    {
        $this->routes['category/<id>/edit'] = 'categoryEdit';
        $this->routes['category/edit'] = 'categoryEdit';
        $this->routes['category/create'] = 'categoryCreate';
        $this->routes['category/<id>/delete'] = 'categoryDelete';
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

    /**
     * 文章列表
     */
    public function indexAction()
    {
        $this->articlesAction();
    }

    /**
     * 文章列表
     */
    public function articlesAction()
    {
        $this->title = '文章';
        $this->keyList = [
            'id' => ['alias' => 'ID'],
            'title' => ['alias' => '标题'],
            'created' => ['alias' => '创建时间'],
            'status' => ['alias' => '状态'],
            'operation' => ['alias' => '操作'],
        ];

        // 用户列表
        $list = Model::factory('article', 'articles')->alias('a')->select('a.*')->join(['users', 'u'], 'left')->on('a.author_id', '=', 'u.id')->where('a.status', '=', 1)->orderBy('id', 'desc')->findPage(3);

        foreach ($list['data'] as &$node) {
            $node->created = \Bootphp\Date::unixToHuman($node->created);
            switch ($node->status) {
                case 0:
                    $node->status = '垃圾筒';
                    break;
                case 1:
                    $node->status = '已发布';
                    break;
                case 2:
                    $node->status = '草稿';
                    break;
                case 3:
                    $node->status = '待审核';
                    break;
            }
            $node->operation = '';
        }

        $this->commonList($list);
    }

    /**
     * 编辑文章
     */
    public function editAction()
    {
        $articleId = intval($this->request->param('id'));
        $oArticle = Model::factory('article', 'articles');
        $article = $oArticle->find($articleId);
        if ($this->request->method() === 'PUT') {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            if (!$article) {
                $status = 3;
                $info = '请求的文章不存在。';
                $this->ajaxReturn($status, $info);
            }
            try {
                $update = [
                    'title' => $this->request->param('title'),
                    'content' => $this->request->param('content'),
                    'keywords' => $this->request->param('keywords'),
                    'descript' => $this->request->param('descript'),
                ];
                if ($oArticle->update($update, ['id', '=', $article->id])) {
                    $status = 1;
                    $info = '文章已经更新成功。';
                } else {
                    $status = 5;
                    $info = '文章没有更新。';
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
        $this->tab = 'articles';
        $this->assign('node', $article);
    }

}
