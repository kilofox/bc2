<?php

/**
 * Abstract controller class for automatic templating.
 *
 * @package    Bootphp
 * @category   Controller
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Kohana_Controller_Template extends Controller
{
    /**
     * @var  View  page template
     */
    public $template = 'template';

    /**
     * @var  boolean  auto render template
     * */
    public $auto_render = true;

    /**
     * Loads the template [View] object.
     */
    public function before()
    {
        parent::before();

        if ($this->auto_render === true) {
            // Load the template
            $this->template = View::factory($this->template);
        }
    }

    /**
     * Assigns the template [View] as the request response.
     */
    public function after()
    {
        if ($this->auto_render === true) {
            $this->response->body($this->template->render());
        }

        parent::after();
    }

}
