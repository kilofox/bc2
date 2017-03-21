<?php

namespace App\Model;

/**
 * 菜单模型。
 *
 * @package	BootCMS
 * @category	模型
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2017 Kilofox Studio
 */
class MenuModel extends \Bootphp\ORM\ORM
{
    protected $tableName = 'system_menus';

    /**
     * 系统菜单
     */
    public function menu($current = '')
    {
        $menus = $this->orderBy('sort')->findAll();

        $menu = ['tabs' => [], 'default' => 0];
        $defaultId = 0;
        $subMenu = [];
        foreach ($menus as $node) {
            if ($node->parent_id == 0) {
                $menu['tabs'][$node->id] = $node;
                $menu['tabs'][$node->id]->apps[] = $node->application;
                $menu['tabs'][$node->id]->subMenu = [];
                if ($node->controller == $current)
                    $menu['default'] = $node->id;
            } else {
                $subMenu[] = $node;
            }
        }
        foreach ($subMenu as $node) {
            if (isset($menu['tabs'][$node->parent_id])) {
                $menu['tabs'][$node->parent_id]->apps[] = $node->application;
                $menu['tabs'][$node->parent_id]->subMenu[] = $node;
            }
        }

        return $menu;
    }

}
