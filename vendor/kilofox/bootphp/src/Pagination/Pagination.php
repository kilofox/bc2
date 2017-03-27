<?php

namespace Bootphp;

/**
 * Pagination links generator.
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class Pagination
{
    /**
     * Merged configuration settings.
     *
     * @var array
     */
    protected $config = [
        'currentPage' => ['source' => 'query_string', 'key' => 'page'],
        'totalItems' => 0,
        'itemsPerPage' => 10,
        'view' => 'pagination/basic',
        'autoHide' => false,
        'firstPageInUrl' => false,
    ];

    /**
     * Current page number.
     *
     * @var integer
     */
    protected $currentPage;

    /**
     * Total item count.
     *
     * @var integer
     */
    protected $totalItems;

    /**
     * How many items to show per page.
     *
     * @var integer
     */
    protected $itemsPerPage;

    /**
     * Total page count.
     *
     * @var integer
     */
    protected $totalPages;

    /**
     * Item offset for the first item displayed on the current page.
     *
     * @var integer
     */
    protected $currentFirstItem;

    /**
     * Item offset for the last item displayed on the current page.
     *
     * @var integer
     */
    protected $currentLastItem;

    /**
     * Previous page number. FALSE if the current page is the first one.
     *
     * @var integer
     */
    protected $previousPage;

    /**
     * Next page number. FALSE if the current page is the last one.
     *
     * @var integer
     */
    protected $nextPage;

    /**
     * First page number. FALSE if the current page is the first one.
     *
     * @var integer
     */
    protected $firstPage;

    /**
     * Last page number. FALSE if the current page is the last one.
     *
     * @var integer
     */
    protected $lastPage;

    /**
     * Query offset
     *
     * @var integer
     */
    protected $offset;

    /**
     * Creates a new Pagination object.
     *
     * @param   array   $config Configuration
     * @return  Pagination
     */
    public static function factory(array $config = [])
    {
        return new self($config);
    }

    /**
     * Creates a new Pagination object.
     *
     * @param   array   $config Configuration
     * @return  void
     */
    public function __construct(array $config = [])
    {
        // Overwrite system defaults with application defaults
        $this->config = $this->config_group() + $this->config;
        // Pagination setup
        $this->setup($config);
    }

    /**
     * Retrieves a pagination config group from the config file. One config group can
     * refer to another as its parent, which will be recursively loaded.
     *
     * @param   string  $group  Pagination config group. "default" if none given.
     * @return  array   Config settings
     */
    public function configGroup($group = 'default')
    {
        // Load the pagination config file
        $config_file = Core::$config('pagination');
        // Initialize the $config array
        $config['group'] = (string) $group;
        // Recursively load requested config groups
        while (isset($config['group']) && isset($config_file->$config['group'])) {
            // Temporarily store config group name
            $group = $config['group'];
            unset($config['group']);
            // Add config group values, not overwriting existing keys
            $config += $config_file->$group;
        }
        // Get rid of possible stray config group names
        unset($config['group']);
        // Return the merged config group settings
        return $config;
    }

    /**
     * Loads configuration settings into the object and (re)calculates pagination if needed.
     * Allows you to update config settings after a Pagination object has been constructed.
     *
     * @param   array   $config Configuration
     * @return  object  Pagination
     */
    public function setup(array $config = [])
    {
        if (isset($config['group'])) {
            // Recursively load requested config groups
            $config += $this->config_group($config['group']);
        }

        // Overwrite the current config settings
        $this->config = $config + $this->config;

        // Only (re)calculate pagination when needed
        if ($this->currentPage === null || isset($config['currentPage']) || isset($config['totalItems']) || isset($config['itemsPerPage'])) {
            // Retrieve the current page number
            if (!empty($this->config['currentPage']['page'])) {
                // The current page number has been set manually
                $this->currentPage = (int) $this->config['currentPage']['page'];
            } else {
                switch ($this->config['currentPage']['source']) {
                    case 'query_string':
                        $this->currentPage = isset($_GET[$this->config['currentPage']['key']]) ? (int) $_GET[$this->config['currentPage']['key']] : 1;
                        break;
                    case 'route':
                        $this->currentPage = (int) Request::current()->param($this->config['currentPage']['key'], 1);
                        break;
                }
            }
            // Calculate and clean all pagination variables
            $this->totalItems = (int) max(0, $this->config['totalItems']);
            $this->itemsPerPage = (int) max(1, $this->config['itemsPerPage']);
            $this->totalPages = (int) ceil($this->totalItems / $this->itemsPerPage);
            $this->currentPage = (int) min(max(1, $this->currentPage), max(1, $this->totalPages));
            $this->currentFirstItem = (int) min((($this->currentPage - 1) * $this->itemsPerPage) + 1, $this->totalItems);
            $this->currentLastItem = (int) min($this->currentFirstItem + $this->itemsPerPage - 1, $this->totalItems);
            $this->previousPage = $this->currentPage > 1 ? $this->currentPage - 1 : false;
            $this->nextPage = ($this->currentPage < $this->totalPages) ? $this->currentPage + 1 : false;
            $this->firstPage = ($this->currentPage === 1) ? false : 1;
            $this->lastPage = ($this->currentPage >= $this->totalPages) ? false : $this->totalPages;
            $this->offset = (int) (($this->currentPage - 1) * $this->itemsPerPage);
        }

        return $this;
    }

    /**
     * Generates the full URL for a certain page.
     *
     * @param   integer Page number
     * @return  string  Page URL
     */
    public function url($page = 1)
    {
        // Clean the page number
        $page = max(1, (int) $page);
        // No page number in URLs to first page
        if ($page === 1 && !$this->config['firstPageInUrl']) {
            $page = null;
        }
        switch ($this->config['currentPage']['source']) {
            case 'query_string':
                return URL::site(Request::current()->uri()) . URL::query([$this->config['currentPage']['key'] => $page]);
            case 'route':
                return URL::site(Request::current()->uri([$this->config['currentPage']['key'] => $page])) . URL::query();
        }
        return '#';
    }

    /**
     * Checks whether the given page number exists.
     *
     * @param   integer Page number
     * @return  boolean
     */
    public function validPage($page)
    {
        // Page number has to be a clean integer
        if (!Valid::digit($page))
            return false;
        return $page > 0 && $page <= $this->totalPages;
    }

    /**
     * Renders the pagination links.
     *
     * @param   mixed   $view   String of the view to use, or a View object
     * @return  string  Pagination output (HTML)
     */
    public function render($view = null)
    {
        // Automatically hide pagination whenever it is superfluous
        if ($this->config['autoHide'] === false && $this->totalPages <= 1)
            return '';
        if ($view === null) {
            // Use the view from config
            $view = $this->config['view'];
        }
        if (!$view instanceof View) {
            // Load the view file
            $view = View::factory($view);
        }
        // Pass on the whole Pagination object
        return $view->set(get_object_vars($this))->set('page', $this)->render();
    }

    /**
     * Renders the pagination links.
     *
     * @return  string  Pagination output (HTML)
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            BootphpException::handler($e);

            return '';
        }
    }

    /**
     * Returns a Pagination property.
     *
     * @param   string  Property name
     * @return  mixed   Pagination property. NULL if not found.
     */
    public function __get($key)
    {
        return isset($this->$key) ? $this->$key : null;
    }

    /**
     * Updates a single config setting, and recalculates pagination if needed.
     *
     * @param   string  Config key
     * @param   mixed   Config value
     * @return  void
     */
    public function __set($key, $value)
    {
        $this->setup([$key => $value]);
    }

}
