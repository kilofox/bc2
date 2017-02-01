<?php

namespace Bootphp\ORM;

use Bootphp\Database\Database;
use Bootphp\Database\DB;
use Bootphp\BootphpException;

/**
 * [Object Relational Mapping][ref-orm] (ORM) is a method of abstracting
 * database access to standard PHP calls. All table rows are represented as
 * model objects, with object properties representing row data. ORM in Bootphp
 * generally follows the [Active Record][ref-act] pattern.
 *
 * [ref-orm]: http://wikipedia.org/wiki/Object-relational_mapping
 * [ref-act]: http://wikipedia.org/wiki/Active_record
 *
 * @package    Bootphp/ORM
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class ORM extends \Bootphp\Model
{
    /**
     * Initialization storage for ORM models.
     * @var array
     */
    protected static $initCache = [];

    /**
     * Creates and returns a new model.
     * Model name must be passed with its' original casing, e.g.
     *
     *    $model = ORM::factory('User_Token');
     *
     * @chainable
     * @param   string  $model  Model name
     * @param   mixed   $id     Parameter for find()
     * @return  ORM
     */
    public static function factory($model, $id = null)
    {
        // Set class name
        $model = 'App\\models\\' . ucfirst($model) . 'Model';

        return new $model($id);
    }

    /**
     * "Has one" relationships
     * @var array
     */
    protected $hasOne = [];

    /**
     * "Belongs to" relationships
     * @var array
     */
    protected $belongsTo = [];

    /**
     * "Has many" relationships
     * @var array
     */
    protected $hasMany = [];

    /**
     * Relationships that should always be joined
     * @var array
     */
    protected $loadWith = [];

    /**
     * Validation object created before saving/updating
     * @var Validation
     */
    protected $_validation = null;

    /**
     * Current object
     * @var array
     */
    protected $_object = [];

    /**
     * @var array
     */
    protected $changed = [];

    /**
     * @var array
     */
    protected $originalValues = [];

    /**
     * @var array
     */
    protected $_related = [];

    /**
     * @var bool
     */
    protected $_valid = false;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var bool
     */
    protected $saved = false;

    /**
     * @var array
     */
    protected $sorting;

    /**
     * Foreign key suffix
     * @var string
     */
    protected $foreignKeySuffix = '_id';

    /**
     * Model name
     * @var string
     */
    protected $_object_name;

    /**
     * Table name
     * @var string
     */
    protected $tableName;

    /**
     * Table columns
     * @var array
     */
    protected $tableColumns;

    /**
     * Auto-update columns for updates
     * @var string
     */
    protected $_updated_column = null;

    /**
     * Auto-update columns for creation
     * @var string
     */
    protected $_created_column = null;

    /**
     * Table primary key
     * @var string
     */
    protected $_primary_key = 'id';

    /**
     * Primary key value
     * @var mixed
     */
    protected $primaryKeyValue;

    /**
     * Database Object
     * @var Database
     */
    protected $_db = null;

    /**
     * Database config group
     * @var String
     */
    protected $dbGroup = null;

    /**
     * Database methods applied
     * @var array
     */
    protected $_db_applied = [];

    /**
     * Database methods pending
     * @var array
     */
    protected $dbPending = [];

    /**
     * Database query builder
     * @var Database_Query_Builder_Select
     */
    protected $dbBuilder;

    /**
     * With calls already applied
     * @var array
     */
    protected $_with_applied = [];

    /**
     * Data to be loaded into the model from a database call cast
     * @var array
     */
    protected $_cast_data = [];

    /**
     * Constructs a new model and loads a record if given.
     *
     * @param   mixed   $id   Parameter for find or object to load
     */
    public function __construct($id = null)
    {
        $this->initialize();

        if ($id !== null) {
            if (is_array($id)) {
                foreach ($id as $column => $value) {
                    // Passing an array of column => values
                    $this->where($column, '=', $value);
                }

                $this->find();
            } else {
                // Passing the primary key
                $this->where($this->_primary_key, '=', $id)->find();
            }
        } elseif (!empty($this->_cast_data)) {
            // Load preloaded data from a database call cast
            $this->loadValues($this->_cast_data);

            $this->_cast_data = [];
        }
    }

    /**
     * Prepares the model database connection, determines the table name, and
     * loads column information.
     *
     * @return void
     */
    protected function initialize()
    {
        // Set the object name if none predefined
        if (empty($this->_object_name)) {
            $this->_object_name = strtolower(basename(get_class($this), 'Model'));
        }

        // Check if this model has already been initialized
        if (!$init = (isset(self::$initCache[$this->_object_name]) ? self::$initCache[$this->_object_name] : false)) {
            $init = [
                'belongsTo' => [],
                'hasOne' => [],
                'hasMany' => [],
            ];

            if (!is_object($this->_db)) {
                // Get database instance
                $init['_db'] = Database::instance($this->dbGroup);
            }

            if (empty($this->tableName)) {
                // Table name is the same as the object name
                $init['tableName'] = $this->_object_name;
            }

            $defaults = [];

            foreach ($this->belongsTo as $alias => $details) {
                if (!isset($details['model'])) {
                    $defaults['model'] = ucwords($alias);
                }

                $defaults['foreign_key'] = $alias . $this->foreignKeySuffix;

                $init['belongsTo'][$alias] = array_merge($defaults, $details);
            }

            foreach ($this->hasOne as $alias => $details) {
                if (!isset($details['model'])) {
                    $defaults['model'] = ucwords($alias);
                }

                $defaults['foreign_key'] = $this->_object_name . $this->foreignKeySuffix;

                $init['hasOne'][$alias] = array_merge($defaults, $details);
            }

            foreach ($this->hasMany as $alias => $details) {
                if (!isset($details['model'])) {
                    $defaults['model'] = ucwords($alias);
                }

                $defaults['foreign_key'] = $this->_object_name . $this->foreignKeySuffix;
                $defaults['through'] = null;

                if (!isset($details['far_key'])) {
                    $defaults['far_key'] = $alias . $this->foreignKeySuffix;
                }

                $init['hasMany'][$alias] = array_merge($defaults, $details);
            }

            self::$initCache[$this->_object_name] = $init;
        }

        // Assign initialized properties to the current object
        foreach ($init as $property => $value) {
            $this->{$property} = $value;
        }

        // Load column information
        $this->tableColumns = $this->_db->listColumns($this->tableName);

        // Clear initial model state
        $this->clear();
    }

    /**
     * Initializes validation rules, and labels.
     *
     * @return void
     */
    protected function _validation()
    {
        // Build the validation object with its rules
        $this->_validation = \Bootphp\Validation::factory($this->_object)
                ->bind(':model', $this)
                ->bind(':original_values', $this->originalValues)
                ->bind(':changed', $this->changed);

        foreach ($this->rules() as $field => $rules) {
            $this->_validation->rules($field, $rules);
        }

        // Use column names by default for labels
        $columns = array_keys($this->tableColumns);

        // Merge user-defined labels
        $labels = array_merge(array_combine($columns, $columns), $this->labels());

        foreach ($labels as $field => $label) {
            $this->_validation->label($field, $label);
        }
    }

    /**
     * Unloads the current object and clears the status.
     *
     * @chainable
     * @return ORM
     */
    public function clear()
    {
        // Replace the object and reset the object status
        $this->_object = $this->changed = $this->_related = $this->originalValues = [];

        // Create an array with all the columns set to null
        $values = array_combine(array_keys($this->tableColumns), array_fill(0, count($this->tableColumns), null));

        // Replace the current object with an empty one
        $this->loadValues($values);

        // Reset primary key
        $this->primaryKeyValue = null;

        // Reset the loaded state
        $this->loaded = false;

        $this->reset();

        return $this;
    }

    /**
     * Checks if object data is set.
     *
     * @param   string  $column Column name
     * @return  boolean
     */
    public function __isset($column)
    {
        return isset($this->_object[$column]) ||
                isset($this->_related[$column]) ||
                isset($this->hasOne[$column]) ||
                isset($this->belongsTo[$column]) ||
                isset($this->hasMany[$column]);
    }

    /**
     * Unsets object data.
     *
     * @param   string  $column Column name
     * @return  void
     */
    public function __unset($column)
    {
        unset($this->_object[$column], $this->changed[$column], $this->_related[$column]);
    }

    /**
     * Check whether the model data has been modified.
     * If $field is specified, checks whether that field was modified.
     *
     * @param   string  $field  Field to check for changes
     * @return  boolean Whether or not the field has changed
     */
    public function changed($field = null)
    {
        return $field === null ? $this->changed : isset($this->changed[$field]);
    }

    /**
     * Handles retrieval of all model values, relationships, and metadata.
     * [!!] This should not be overridden.
     *
     * @param   string  $column Column name
     * @return  mixed
     */
    public function __get($column)
    {
        return $this->get($column);
    }

    /**
     * Handles getting of column.
     *
     * @param   string  $column  Column name
     * @throws  BootphpException
     * @return  mixed
     */
    public function get($column)
    {
        if (array_key_exists($column, $this->_object)) {
            return $this->_object[$column];
        } elseif (isset($this->_related[$column])) {
            // Return related model that has already been fetched
            return $this->_related[$column];
        } elseif (isset($this->belongsTo[$column])) {
            $model = $this->related($column);

            // Use this model's column and foreign model's primary key
            $col = $model->_primary_key;
            $val = $this->_object[$this->belongsTo[$column]['foreign_key']];

            // Make sure we don't run WHERE "AUTO_INCREMENT column" = null queries. This would
            // return the last inserted record instead of an empty result.
            // See: http://mysql.localhost.net.ar/doc/refman/5.1/en/server-session-variables.html#sysvar_sql_auto_is_null
            if ($val !== null) {
                $model->where($col, '=', $val)->find();
            }

            return $this->_related[$column] = $model;
        } elseif (isset($this->hasOne[$column])) {
            $model = $this->related($column);

            // Use this model's primary key value and foreign model's column
            $col = $this->hasOne[$column]['foreign_key'];
            $val = $this->pk();

            $model->where($col, '=', $val)->find();

            return $this->_related[$column] = $model;
        } elseif (isset($this->hasMany[$column])) {
            $model = self::factory($this->hasMany[$column]['model']);

            if (isset($this->hasMany[$column]['through'])) {
                // Grab has_many "through" relationship table
                $through = $this->hasMany[$column]['through'];

                // Join on through model's target foreign key (far_key) and target model's primary key
                $join_col1 = $through . '.' . $this->hasMany[$column]['far_key'];
                $join_col2 = $model->tableName . '.' . $model->_primary_key;

                $model->join($through)->on($join_col1, '=', $join_col2);

                // Through table's source foreign key (foreign_key) should be this model's primary key
                $col = $through . '.' . $this->hasMany[$column]['foreign_key'];
                $val = $this->pk();
            } else {
                // Simple has_many relationship, search where target model's foreign key is this model's primary key
                $col = $this->hasMany[$column]['foreign_key'];
                $val = $this->pk();
            }

            return $model->where($col, '=', $val);
        } else {
            throw new BootphpException('The ' . $column . ' property does not exist in the ' . get_class($this) . ' class');
        }
    }

    /**
     * Base set method.
     * [!!] This should not be overridden.
     *
     * @param  string $column  Column name
     * @param  mixed  $value   Column value
     * @return void
     */
    public function __set($column, $value)
    {
        $this->set($column, $value);
    }

    /**
     * Handles setting of columns.
     * Override this method to add custom set behavior.
     *
     * @param   string  $column Column name
     * @param   mixed   $value  Column value
     * @throws  BootphpException
     * @return  ORM
     */
    public function set($column, $value)
    {
        if (!isset($this->_object_name)) {
            // Object not yet constructed, so we're loading data from a database call cast
            $this->_cast_data[$column] = $value;

            return $this;
        }

        if (array_key_exists($column, $this->_object)) {
            // Filter the data
            $value = $this->runFilter($column, $value);

            // See if the data really changed
            if ($value !== $this->_object[$column]) {
                $this->_object[$column] = $value;

                // Data has changed
                $this->changed[$column] = $column;

                // Object is no longer saved or valid
                $this->saved = $this->_valid = false;
            }
        } elseif (isset($this->belongsTo[$column])) {
            // Update related object itself
            $this->_related[$column] = $value;

            // Update the foreign key of this model
            $this->_object[$this->belongsTo[$column]['foreign_key']] = ($value instanceof ORM) ? $value->pk() : null;

            $this->changed[$column] = $this->belongsTo[$column]['foreign_key'];
        } else {
            throw new BootphpException('The ' . $column . ' property does not exist in the ' . get_class($this) . ' class');
        }

        return $this;
    }

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param   array   $values     Array of column => val
     * @param   array   $expected   Array of keys to take from $values
     * @return  ORM
     */
    public function values(array $values, array $expected = null)
    {
        // Default to expecting everything except the primary key
        if ($expected === null) {
            $expected = array_keys($this->_object);

            // Don't set the primary key by default
            unset($values[$this->_primary_key]);
        }

        foreach ($expected as $key => $column) {
            if (is_string($key)) {
                // isset() fails when the value is null (we want it to pass)
                if (!array_key_exists($key, $values))
                    continue;

                // Try to set values to a related model
                $this->{$key}->values($values[$key], $column);
            } else {
                // isset() fails when the value is null (we want it to pass)
                if (!array_key_exists($column, $values))
                    continue;

                // Update the column, respects __set()
                $this->$column = $values[$column];
            }
        }

        return $this;
    }

    /**
     * Returns the values of this object as an array, including any related one-one
     * models that have already been loaded using with()
     *
     * @return array
     */
    public function asArray()
    {
        $object = [];

        foreach ($this->_object as $column => $value) {
            // Call __get for any user processing
            $object[$column] = $this->__get($column);
        }

        foreach ($this->_related as $column => $model) {
            // Include any related objects that are already loaded
            $object[$column] = $model->as_array();
        }

        return $object;
    }

    /**
     * Binds another one-to-one object to this model.  One-to-one objects
     * can be nested using 'object1:object2' syntax
     *
     * @param  string $target_path Target model to bind to
     * @return ORM
     */
    public function with($target_path)
    {
        if (isset($this->_with_applied[$target_path])) {
            // Don't join anything already joined
            return $this;
        }

        // Split object parts
        $aliases = explode(':', $target_path);
        $target = $this;
        foreach ($aliases as $alias) {
            // Go down the line of objects to find the given target
            $parent = $target;
            $target = $parent->related($alias);

            if (!$target) {
                // Can't find related object
                return $this;
            }
        }

        // Target alias is at the end
        $target_alias = $alias;

        // Pop-off top alias to get the parent path (user:photo:tag becomes user:photo - the parent table prefix)
        array_pop($aliases);
        $parent_path = implode(':', $aliases);

        if (empty($parent_path)) {
            // Use this table name itself for the parent path
            $parent_path = $this->_object_name;
        } else {
            if (!isset($this->_with_applied[$parent_path])) {
                // If the parent path hasn't been joined yet, do it first (otherwise LEFT JOINs fail)
                $this->with($parent_path);
            }
        }

        // Add to with_applied to prevent duplicate joins
        $this->_with_applied[$target_path] = true;

        // Use the keys of the empty object to determine the columns
        foreach (array_keys($target->_object) as $column) {
            $name = $target_path . '.' . $column;
            $alias = $target_path . ':' . $column;

            // Add the prefix so that load_result can determine the relationship
            $this->select([$name, $alias]);
        }

        if (isset($parent->belongsTo[$target_alias])) {
            // Parent belongs_to target, use target's primary key and parent's foreign key
            $join_col1 = $target_path . '.' . $target->_primary_key;
            $join_col2 = $parent_path . '.' . $parent->belongsTo[$target_alias]['foreign_key'];
        } else {
            // Parent has_one target, use parent's primary key as target's foreign key
            $join_col1 = $parent_path . '.' . $parent->_primary_key;
            $join_col2 = $target_path . '.' . $parent->hasOne[$target_alias]['foreign_key'];
        }

        // Join the related object into the result
        $this->join([$target->tableName, $target_path], 'LEFT')->on($join_col1, '=', $join_col2);

        return $this;
    }

    /**
     * Initializes the Database Builder to given query type
     *
     * @param  integer $type Type of Database query
     * @return ORM
     */
    protected function build($type)
    {
        // Construct new builder object based on query type
        switch ($type) {
            case 'select':
                $this->dbBuilder = DB::select();
                break;
            case 'update':
                $this->dbBuilder = DB::update([$this->tableName, $this->_object_name]);
                break;
            case 'delete':
                // Cannot use an alias for DELETE queries
                $this->dbBuilder = DB::delete($this->tableName);
        }

        // Process pending database method calls
        foreach ($this->dbPending as $method) {
            $name = $method['name'];
            $args = $method['args'];

            $this->_db_applied[$name] = $name;

            call_user_func_array([$this->dbBuilder, $name], $args);
        }

        return $this;
    }

    /**
     * Finds and loads a single database row into the object.
     *
     * @chainable
     * @throws BootphpException
     * @return ORM
     */
    public function find()
    {
        if ($this->loaded)
            throw new BootphpException('Method find() cannot be called on loaded objects');

        if (!empty($this->loadWith)) {
            foreach ($this->loadWith as $alias) {
                // Bind auto relationships
                $this->with($alias);
            }
        }

        $this->build('select');

        return $this->loadResult(false);
    }

    /**
     * Finds multiple database rows and returns an iterator of the rows found.
     *
     * @throws BootphpException
     * @return Database_Result
     */
    public function findAll()
    {
        if ($this->loaded)
            throw new BootphpException('Method find_all() cannot be called on loaded objects');

        if (!empty($this->loadWith)) {
            foreach ($this->loadWith as $alias) {
                // Bind auto relationships
                $this->with($alias);
            }
        }

        $this->build('select');

        return $this->loadResult(true);
    }

    /**
     * Loads a database result, either as a new record for this model, or as an
     * iterator for multiple rows.
     *
     * @chainable
     * @param  bool $multiple Return an iterator or load a single row
     * @return ORM|Database_Result
     */
    protected function loadResult($multiple = false)
    {
        $this->dbBuilder->from($this->tableName);

        if ($multiple === false) {
            // Only fetch 1 record
            $this->dbBuilder->limit(1);
        }

        // Select all columns by default
        if ($this->hasMany || $this->belongsTo) {
            $this->dbBuilder->select();
        } else {
            $this->dbBuilder->select($this->tableName . '.*');
        }

        if (!isset($this->_db_applied['order_by']) && !empty($this->sorting)) {
            foreach ($this->sorting as $column => $direction) {
                if (strpos($column, '.') === false) {
                    // Sorting column for use in JOINs
                    $column = $this->_object_name . '.' . $column;
                }

                $this->dbBuilder->order_by($column, $direction);
            }
        }

        if ($multiple === true) {
            // Return database iterator casting to this object type
            $result = $this->dbBuilder->as_object(get_class($this))->execute($this->_db);

            $this->reset();

            return $result;
        } else {
            // Load the result as an associative array
            $result = $this->dbBuilder->as_assoc()->execute($this->_db);

            $this->reset();

            if ($result->count() === 1) {
                // Load object values
                $this->loadValues($result->current());
            } else {
                // Clear the object, nothing was found
                $this->clear();
            }

            return $this;
        }
    }

    /**
     * Loads an array of values into into the current object.
     *
     * @chainable
     * @param  array $values Values to load
     * @return ORM
     */
    protected function loadValues(array $values)
    {
        if (array_key_exists($this->_primary_key, $values)) {
            if ($values[$this->_primary_key] !== null) {
                // Flag as loaded and valid
                $this->loaded = $this->_valid = true;

                // Store primary key
                $this->primaryKeyValue = $values[$this->_primary_key];
            } else {
                // Not loaded or valid
                $this->loaded = $this->_valid = false;
            }
        }

        // Related objects
        $related = [];

        foreach ($values as $column => $value) {
            if (strpos($column, ':') === false) {
                // Load the value to this model
                $this->_object[$column] = $value;
            } else {
                // Column belongs to a related model
                list ($prefix, $column) = explode(':', $column, 2);

                $related[$prefix][$column] = $value;
            }
        }

        if (!empty($related)) {
            foreach ($related as $object => $values) {
                // Load the related objects with the values in the result
                $this->related($object)->loadValues($values);
            }
        }

        if ($this->loaded) {
            // Store the object in its original state
            $this->originalValues = $this->_object;
        }

        return $this;
    }

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Filters a value for a specific column
     *
     * @param  string $field  The column name
     * @param  string $value  The value to filter
     * @return string
     */
    protected function runFilter($field, $value)
    {
        $filters = $this->filters();

        // Get the filters for this column
        $wildcards = empty($filters[true]) ? [] : $filters[true];

        // Merge in the wildcards
        $filters = empty($filters[$field]) ? $wildcards : array_merge($wildcards, $filters[$field]);

        // Bind the field name and model so they can be used in the filter method
        $_bound = array
            (
            ':field' => $field,
            ':model' => $this,
        );

        foreach ($filters as $array) {
            // Value needs to be bound inside the loop so we are always using the
            // version that was modified by the filters that already ran
            $_bound[':value'] = $value;

            // Filters are defined as array($filter, $params)
            $filter = $array[0];
            $params = Arr::get($array, 1, [':value']);

            foreach ($params as $key => $param) {
                if (is_string($param) && array_key_exists($param, $_bound)) {
                    // Replace with bound value
                    $params[$key] = $_bound[$param];
                }
            }

            if (is_array($filter) || !is_string($filter)) {
                // This is either a callback as an array or a lambda
                $value = call_user_func_array($filter, $params);
            } elseif (strpos($filter, '::') === false) {
                // Use a function call
                $function = new \ReflectionFunction($filter);

                // Call $function($this[$field], $param, ...) with Reflection
                $value = $function->invokeArgs($params);
            } else {
                // Split the class and method of the rule
                list($class, $method) = explode('::', $filter, 2);

                // Use a static method call
                $method = new ReflectionMethod($class, $method);

                // Call $Class::$method($this[$field], $param, ...) with Reflection
                $value = $method->invokeArgs(null, $params);
            }
        }

        return $value;
    }

    /**
     * Filter definitions for validation
     *
     * @return array
     */
    public function filters()
    {
        return [];
    }

    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
        return [];
    }

    /**
     * Validates the current model's data
     *
     * @param  Validation $extra_validation Validation object
     * @throws ORM_Validation_Exception
     * @return ORM
     */
    public function check(Validation $extra_validation = null)
    {
        // Determine if any external validation failed
        $extra_errors = ($extra_validation && !$extra_validation->check());

        // Always build a new validation object
        $this->_validation();

        $array = $this->_validation;

        if (($this->_valid = $array->check()) === false || $extra_errors) {
            $exception = new ORM_Validation_Exception($this->_object_name, $array);

            if ($extra_errors) {
                // Merge any possible errors from the external object
                $exception->add_object('_external', $extra_validation);
            }
            throw $exception;
        }

        return $this;
    }

    /**
     * Insert a new object to the database.
     *
     * @param   Validation  $validation Validation object
     * @throws  BootphpException
     * @return  ORM
     */
    public function create(Validation $validation = null)
    {
        if ($this->loaded)
            throw new BootphpException('Cannot create ' . $this->_object_name . ' model because it is already loaded.');

        // Require model validation before saving
        if (!$this->_valid || $validation) {
            $this->check($validation);
        }

        $data = [];
        foreach ($this->changed as $column) {
            // Generate list of column => values
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_created_column)) {
            // Fill the created column
            $column = $this->_created_column['column'];
            $format = $this->_created_column['format'];

            $data[$column] = $this->_object[$column] = ($format === true) ? time() : date($format);
        }

        $result = DB::insert($this->tableName)
                ->columns(array_keys($data))
                ->values(array_values($data))
                ->execute($this->_db);

        if (!array_key_exists($this->_primary_key, $data)) {
            // Load the insert id as the primary key if it was left out
            $this->_object[$this->_primary_key] = $this->primaryKeyValue = $result[0];
        } else {
            $this->primaryKeyValue = $this->_object[$this->_primary_key];
        }

        // Object is now loaded and saved
        $this->loaded = $this->saved = true;

        // All changes have been saved
        $this->changed = [];
        $this->originalValues = $this->_object;

        return $this;
    }

    /**
     * Updates a single record or multiple records.
     *
     * @chainable
     * @param   Validation  $validation Validation object
     * @throws  BootphpException
     * @return  ORM
     */
    public function update(Validation $validation = null)
    {
        if (!$this->loaded)
            throw new BootphpException('Cannot update ' . $this->_object_name . ' model because it is not loaded.');

        // Run validation if the model isn't valid or we have additional validation rules.
        if (!$this->_valid || $validation) {
            $this->check($validation);
        }

        if (empty($this->changed)) {
            // Nothing to update
            return $this;
        }

        $data = [];
        foreach ($this->changed as $column) {
            // Compile changed data
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_updated_column)) {
            // Fill the updated column
            $column = $this->_updated_column['column'];
            $format = $this->_updated_column['format'];

            $data[$column] = $this->_object[$column] = ($format === true) ? time() : date($format);
        }

        // Use primary key value
        $id = $this->pk();

        // Update a single record
        DB::update($this->tableName)
                ->set($data)
                ->where($this->_primary_key, '=', $id)
                ->execute($this->_db);

        if (isset($data[$this->_primary_key])) {
            // Primary key was changed, reflect it
            $this->primaryKeyValue = $data[$this->_primary_key];
        }

        // Object has been saved
        $this->saved = true;

        // All changes have been saved
        $this->changed = [];
        $this->originalValues = $this->_object;

        return $this;
    }

    /**
     * Updates or creates the record depending on loaded().
     *
     * @chainable
     * @param   Validation  $validation Validation object
     * @return  ORM
     */
    public function save(Validation $validation = null)
    {
        return $this->loaded() ? $this->update($validation) : $this->create($validation);
    }

    /**
     * Deletes a single record while ignoring relationships.
     *
     * @chainable
     * @throws  BootphpException
     * @return  ORM
     */
    public function delete()
    {
        if (!$this->loaded)
            throw new BootphpException('Cannot delete ' . $this->_object_name . ' model because it is not loaded.');

        // Use primary key value
        $id = $this->pk();

        // Delete the object
        DB::delete($this->tableName)
                ->where($this->_primary_key, '=', $id)
                ->execute($this->_db);

        return $this->clear();
    }

    /**
     * Tests if this object has a relationship to a different model,
     * or an array of different models. When providing far keys, the number
     * of relations must equal the number of keys.
     *
     *
     *     // Check if $model has the login role
     *     $model->has('roles', ORM::factory('role', ['name' => 'login']));
     *     // Check for the login role if you know the roles.id is 5
     *     $model->has('roles', 5);
     *     // Check for all of the following roles
     *     $model->has('roles', [1, 2, 3, 4]);
     *     // Check if $model has any roles
     *     $model->has('roles')
     *
     * @param  string  $alias    Alias of the has_many "through" relationship
     * @param  mixed   $farKeys Related model, primary key, or an array of primary keys
     * @return boolean
     */
    public function has($alias, $farKeys = null)
    {
        $count = $this->count_relations($alias, $farKeys);
        if ($farKeys === null) {
            return (bool) $count;
        } else {
            return $count === count($farKeys);
        }
    }

    /**
     * Tests if this object has a relationship to a different model,
     * or an array of different models. When providing far keys, this function
     * only checks that at least one of the relationships is satisfied.
     *
     *     // Check if $model has the login role
     *     $model->has('roles', ORM::factory('role', ['name' => 'login']));
     *     // Check for the login role if you know the roles.id is 5
     *     $model->has('roles', 5);
     *     // Check for any of the following roles
     *     $model->has('roles', [1, 2, 3, 4]);
     *     // Check if $model has any roles
     *     $model->has('roles')
     *
     * @param   string  $alias      Alias of the has_many "through" relationship
     * @param   mixed   $farKeys   Related model, primary key, or an array of primary keys
     * @return  boolean
     */
    public function has_any($alias, $farKeys = null)
    {
        return (bool) $this->count_relations($alias, $farKeys);
    }

    /**
     * Returns the number of relationships
     *
     *     // Counts the number of times the login role is attached to $model
     *     $model->count_relations('roles', ORM::factory('role', ['name' => 'login']));
     *     // Counts the number of times role 5 is attached to $model
     *     $model->count_relations('roles', 5);
     *     // Counts the number of times any of roles 1, 2, 3, or 4 are attached to
     *     // $model
     *     $model->count_relations('roles', [1, 2, 3, 4]);
     *     // Counts the number roles attached to $model
     *     $model->count_relations('roles')
     *
     * @param  string  $alias    Alias of the has_many "through" relationship
     * @param  mixed   $farKeys Related model, primary key, or an array of primary keys
     * @return integer
     */
    public function count_relations($alias, $farKeys = null)
    {
        if ($farKeys === null) {
            return (int) DB::select([DB::expr('COUNT(*)'), 'records_found'])
                            ->from($this->hasMany[$alias]['through'])
                            ->where($this->hasMany[$alias]['foreign_key'], '=', $this->pk())
                            ->execute($this->_db)->get('records_found');
        }

        $farKeys = ($farKeys instanceof ORM) ? $farKeys->pk() : $farKeys;

        // We need an array to simplify the logic
        $farKeys = (array) $farKeys;

        // Nothing to check if the model isn't loaded or we don't have any far_keys
        if (!$farKeys || !$this->loaded)
            return 0;

        $count = (int) DB::select([DB::expr('COUNT(*)'), 'records_found'])
                        ->from($this->hasMany[$alias]['through'])
                        ->where($this->hasMany[$alias]['foreign_key'], '=', $this->pk())
                        ->where($this->hasMany[$alias]['far_key'], 'IN', $farKeys)
                        ->execute($this->_db)->get('records_found');

        // Rows found need to match the rows searched
        return (int) $count;
    }

    /**
     * Adds a new relationship to between this model and another.
     *
     *     // Add the login role using a model instance
     *     $model->add('roles', ORM::factory('role', ['name' => 'login']));
     *     // Add the login role if you know the roles.id is 5
     *     $model->add('roles', 5);
     *     // Add multiple roles (for example, from checkboxes on a form)
     *     $model->add('roles', [1, 2, 3, 4]);
     *
     * @param  string  $alias    Alias of the has_many "through" relationship
     * @param  mixed   $farKeys Related model, primary key, or an array of primary keys
     * @return ORM
     */
    public function add($alias, $farKeys)
    {
        $farKeys = ($farKeys instanceof ORM) ? $farKeys->pk() : $farKeys;

        $columns = [$this->hasMany[$alias]['foreign_key'], $this->hasMany[$alias]['far_key']];
        $foreign_key = $this->pk();

        $query = DB::insert($this->hasMany[$alias]['through'], $columns);

        foreach ((array) $farKeys as $key) {
            $query->values([$foreign_key, $key]);
        }

        $query->execute($this->_db);

        return $this;
    }

    /**
     * Removes a relationship between this model and another.
     *
     *     // Remove a role using a model instance
     *     $model->remove('roles', ORM::factory('role', ['name' => 'login']));
     *     // Remove the role knowing the primary key
     *     $model->remove('roles', 5);
     *     // Remove multiple roles (for example, from checkboxes on a form)
     *     $model->remove('roles', [1, 2, 3, 4]);
     *     // Remove all related roles
     *     $model->remove('roles');
     *
     * @param  string $alias    Alias of the has_many "through" relationship
     * @param  mixed  $farKeys Related model, primary key, or an array of primary keys
     * @return ORM
     */
    public function remove($alias, $farKeys = null)
    {
        $farKeys = ($farKeys instanceof ORM) ? $farKeys->pk() : $farKeys;

        $query = DB::delete($this->hasMany[$alias]['through'])
                ->where($this->hasMany[$alias]['foreign_key'], '=', $this->pk());

        if ($farKeys !== null) {
            // Remove all the relationships in the array
            $query->where($this->hasMany[$alias]['far_key'], 'IN', (array) $farKeys);
        }

        $query->execute($this->_db);

        return $this;
    }

    /**
     * Count the number of records in the table.
     *
     * @return integer
     */
    public function countAll()
    {
        $selects = [];

        foreach ($this->dbPending as $key => $method) {
            if ($method['name'] == 'select') {
                // Ignore any selected columns for now
                $selects[$key] = $method;
                unset($this->dbPending[$key]);
            }
        }

        if (!empty($this->loadWith)) {
            foreach ($this->loadWith as $alias) {
                // Bind relationship
                $this->with($alias);
            }
        }

        $this->build('select');

        $records = $this->dbBuilder->from([$this->tableName, $this->_object_name])
                ->select([DB::expr('COUNT(' . $this->_db->quote_column($this->_object_name . '.' . $this->_primary_key) . ')'), 'records_found'])
                ->execute($this->_db)
                ->get('records_found');

        // Add back in selected columns
        $this->dbPending += $selects;

        $this->reset();

        // Return the total number of records in a table
        return (int) $records;
    }

    /**
     * Returns an ORM model for the given one-one related alias
     *
     * @param  string $alias Alias name
     * @return ORM
     */
    protected function related($alias)
    {
        if (isset($this->_related[$alias])) {
            return $this->_related[$alias];
        } elseif (isset($this->hasOne[$alias])) {
            return $this->_related[$alias] = self::factory($this->hasOne[$alias]['model']);
        } elseif (isset($this->belongsTo[$alias])) {
            return $this->_related[$alias] = self::factory($this->belongsTo[$alias]['model']);
        } else {
            return false;
        }
    }

    /**
     * Returns the value of the primary key.
     *
     * @return mixed Primary key
     */
    public function pk()
    {
        return $this->primaryKeyValue;
    }

    /**
     * Returns last executed query
     *
     * @return string
     */
    public function lastQuery()
    {
        return $this->_db->last_query;
    }

    /**
     * Clears query builder. Passing false is useful to keep the existing
     * query conditions for another query.
     *
     * @param bool $next Pass false to avoid resetting on the next call
     * @return ORM
     */
    public function reset()
    {
        $this->dbPending = [];
        $this->_db_applied = [];
        $this->dbBuilder = null;
        $this->_with_applied = [];

        return $this;
    }

    public function loaded()
    {
        return $this->loaded;
    }

    public function saved()
    {
        return $this->saved;
    }

    public function primaryKey()
    {
        return $this->_primary_key;
    }

    public function tableName()
    {
        return $this->tableName;
    }

    public function validation()
    {
        if (!isset($this->_validation)) {
            // Initialize the validation object
            $this->_validation();
        }

        return $this->_validation;
    }

    /**
     * Creates a new "AND WHERE" condition for the query.
     *
     * @param   mixed   $column Column name or array($column, $alias) or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function where($column, $op, $value)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'and_where',
            'args' => [$column, $op, $value],
        ];

        return $this;
    }

    /**
     * Creates a new "OR WHERE" condition for the query.
     *
     * @param   mixed   $column Column name or array($column, $alias) or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function orWhere($column, $op, $value)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'or_where',
            'args' => [$column, $op, $value],
        ];

        return $this;
    }

    /**
     * Opens a new "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function whereOpen()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'and_where_open',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Opens a new "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function orWhereOpen()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = array(
            'name' => 'or_where_open',
            'args' => [],
        );

        return $this;
    }

    /**
     * Closes an open "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function whereClose()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'and_where_close',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Closes an open "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function orWhereClose()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'or_where_close',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Applies sorting with "ORDER BY ...".
     *
     * @param   mixed   $column     Column name or array($column, $alias) or object
     * @param   string  $direction  Direction of sorting
     * @return  $this
     */
    public function orderBy($column, $direction = null)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'order_by',
            'args' => [$column, $direction],
        ];

        return $this;
    }

    /**
     * Return up to "LIMIT ..." results.
     *
     * @param   integer $number Maximum results to return
     * @return  $this
     */
    public function limit($number)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'limit',
            'args' => [$number],
        ];

        return $this;
    }

    /**
     * Enables or disables selecting only unique columns using "SELECT DISTINCT"
     *
     * @param   boolean  $value Enable or disable distinct columns
     * @return  $this
     */
    public function distinct($value)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'distinct',
            'args' => [$value],
        ];

        return $this;
    }

    /**
     * Choose the columns to select from.
     *
     * @param   mixed  $columns Column name or array($column, $alias) or object
     * @param   ...
     * @return  $this
     */
    public function select($columns = null)
    {
        $columns = func_get_args();

        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'select',
            'args' => $columns,
        ];

        return $this;
    }

    /**
     * Choose the tables to select "FROM ..."
     *
     * @param   mixed  $tables  Table name or array($table, $alias) or object
     * @param   ...
     * @return  $this
     */
    public function from($tables)
    {
        $tables = func_get_args();

        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'from',
            'args' => $tables,
        ];

        return $this;
    }

    /**
     * Adds addition tables to "JOIN ...".
     *
     * @param   mixed   $table  Column name or array($column, $alias) or object
     * @param   string  $type   Join type (LEFT, RIGHT, INNER, etc)
     * @return  $this
     */
    public function join($table, $type = null)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'join',
            'args' => [$table, $type],
        ];

        return $this;
    }

    /**
     * Adds "ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed   $c1 Column name or array($column, $alias) or object
     * @param   string  $op Logic operator
     * @param   mixed   $c2 Column name or array($column, $alias) or object
     * @return  $this
     */
    public function on($c1, $op, $c2)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'on',
            'args' => [$c1, $op, $c2],
        ];

        return $this;
    }

    /**
     * Creates a "GROUP BY ..." filter.
     *
     * @param   mixed   $columns  column name or array($column, $alias) or object
     * @param   ...
     * @return  $this
     */
    public function groupBy($columns)
    {
        $columns = func_get_args();

        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'group_by',
            'args' => $columns,
        ];

        return $this;
    }

    /**
     * Creates a new "AND HAVING" condition for the query.
     *
     * @param   mixed   $column Column name or array($column, $alias) or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function having($column, $op, $value = null)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'and_having',
            'args' => [$column, $op, $value],
        ];

        return $this;
    }

    /**
     * Creates a new "OR HAVING" condition for the query.
     *
     * @param   mixed   $column Column name or array($column, $alias) or object
     * @param   string  $op     Logic operator
     * @param   mixed   $value  Column value
     * @return  $this
     */
    public function orHaving($column, $op, $value = null)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'or_having',
            'args' => [$column, $op, $value],
        ];

        return $this;
    }

    /**
     * Opens a new "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function havingOpen()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'and_having_open',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Opens a new "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function orHavingOpen()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'or_having_open',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function havingClose()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'and_having_close',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Closes an open "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function orHavingClose()
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'or_having_close',
            'args' => [],
        ];

        return $this;
    }

    /**
     * Start returning results after "OFFSET ..."
     *
     * @param   integer $number Starting result number
     * @return  $this
     */
    public function offset($number)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'offset',
            'args' => [$number],
        ];

        return $this;
    }

    /**
     * Enables the query to be cached for a specified amount of time.
     *
     * @param   integer  $lifetime  Number of seconds to cache
     * @return  $this
     */
    public function cached($lifetime = null)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'cached',
            'args' => [$lifetime],
        ];

        return $this;
    }

    /**
     * Set the value of a parameter in the query.
     *
     * @param   string  $param  Parameter key to replace
     * @param   mixed   $value  Value to use
     * @return  $this
     */
    public function param($param, $value)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'param',
            'args' => [$param, $value],
        ];

        return $this;
    }

    /**
     * Adds "USING ..." conditions for the last created JOIN statement.
     *
     * @param   string  $columns    Column name
     * @return  $this
     */
    public function using($columns)
    {
        // Add pending database call which is executed after query type is determined
        $this->dbPending[] = [
            'name' => 'using',
            'args' => [$columns],
        ];

        return $this;
    }

    /**
     * Checks whether a column value is unique.
     * Excludes itself if loaded.
     *
     * @param   string  $field  The field to check for uniqueness
     * @param   mixed   $value  The value to check for uniqueness
     * @return  boolean Whteher the value is unique
     */
    public function unique($field, $value)
    {
        $model = self::factory($this->_object_name)->where($field, '=', $value)->find();

        if ($this->loaded()) {
            return !($model->loaded() && $model->pk() != $this->pk());
        }

        return !$model->loaded();
    }

}
