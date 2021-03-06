<?php

namespace App\modules\system\controllers;

use App\controllers\admin\AdministrationController;
use Bootphp\Model;

/**
 * 后台首页控制器。
 *
 * @package	BootCMS
 * @category	控制器
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class SettingsController extends AdministrationController
{
    /**
     * Before 方法
     */
    public function before()
    {
        parent::before();
        $this->templatePath = APP_PATH . '/modules/system/views/default/settings/';
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
        if ($this->request->isAjax()) {
            $status = 0;
            $info = '您没有足够的权限进行此项操作。';
            //	$this->accessLevel = Admin::minimumLevel('setting_general');
            //	if ( $this->user->role_id >= $this->accessLevel )
            //	{
            $siteId = intval($this->request->post('sid'));
            $mSite = Model::factory('site', 'system');
            $site = $mSite->find($siteId);
            if (!$site) {
                $status = 3;
                $info = '请求的站点不存在。';
                $this->ajaxReturn($status, $info);
            }
            try {
                $update = [
                    'site_title' => $this->request->post('site_title'),
                    'site_description' => $this->request->post('site_description'),
                    'meta_keywords' => $this->request->post('meta_keywords'),
                    'meta_description' => $this->request->post('meta_description'),
                    'admin_email' => $this->request->post('admin_email'),
                    'company' => $this->request->post('company'),
                    'phone' => $this->request->post('phone'),
                    'address' => $this->request->post('address'),
                    'date_format' => $this->request->post('date_format'),
                    'timezone' => $this->request->post('timezone'),
                ];
                if ($mSite->update($update, ['id', '=', $site->id])) {
                    $status = 1;
                    $info = '网站信息已经更新成功。';
                } else {
                    $status = 5;
                    $info = '网站信息没有更新。';
                }
            } catch (Validation_Exception $e) {
                $errors = $e->errors('models');
                foreach ($errors as $ev) {
                    $status = 4;
                    $info = $ev;
                    break;
                }
            }
            //}
            $this->ajaxReturn($status, $info);
        }

        // 加载站点信息
        $site = Model::factory('site', 'system')->find(1);
        $this->assign('site', $site);
    }

}
