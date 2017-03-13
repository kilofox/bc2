<?php

namespace Bootphp\Auth\Driver\ORM\Model;

/**
 * Default auth user.
 *
 * @package    Bootphp/Auth
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class UserModel extends \Bootphp\ORM\ORM
{
    /**
     * Table name.
     *
     * @var string  Table name
     */
    protected $tableName = 'users';

    /**
     * A user has many tokens and roles.
     *
     * @var array   Relationhips
     */
    protected $hasMany = [
        'user_tokens' => ['model' => 'User_Token']
    ];

    /**
     * A user has many tokens and roles.
     *
     * @var array   Relationhips
     */
    protected $hasOne = [
        'role' => ['model' => 'Role', 'foreignKey' => 'role_id'],
    ];

    /**
     * Rules for the user model. Because the password is _always_ a hash when
     * it's set, you need to run an additional notEmpty rule in your controller
     * to make sure you didn't hash an empty string. The password rules should
     * be enforced outside the model or with a model helper method.
     *
     * @return  array   Rules
     */
    public function rules()
    {
        return [
            'username' => [
                ['notEmpty'],
                ['maxLength', [':value', 32]],
                [[$this, 'unique'], ['username', ':value']],
            ],
            'password' => [
                ['notEmpty'],
            ],
            'email' => [
                ['notEmpty'],
                ['email'],
                [[$this, 'unique'], ['email', ':value']],
            ],
        ];
    }

    /**
     * Filters to run when data is set in this model. The password filter
     * automatically hashes the password when it's set in the model.
     *
     * @return  array   Filters
     */
    public function filters()
    {
        return [
            'password' => [
                [[\Bootphp\Auth\Auth::instance(), 'hash']]
            ]
        ];
    }

    /**
     * Labels for fields in this model.
     *
     * @return  array   Labels
     */
    public function labels()
    {
        return [
            'username' => 'username',
            'email' => 'email address',
            'password' => 'password',
        ];
    }

    /**
     * Complete the login for a user by incrementing the logins and saving login timestamp.
     *
     * @return  void
     */
    public function completeLogin()
    {
        if ($this->loaded) {
            // Update the number of logins
            $this->logins = new \Bootphp\Database\Database\Expression('logins + 1');

            // Set the last login date
            $this->last_login = time();

            // Save the user
            $this->update();
        }
    }

    /**
     * Tests if a unique key value exists in the database.
     *
     * @param   mixed   The value to test
     * @param   string  Field name
     * @return  boolean
     */
    public function uniqueKeyExists($value, $field = null)
    {
        if ($field === null) {
            // Automatically determine field by looking at the value
            $field = $this->uniqueKey($value);
        }

        return (bool) DB::select([DB::expr('COUNT(*)'), 'total_count'])
                ->from($this->tableName)
                ->where($field, '=', $value)
                ->where($this->primaryKey, '!=', $this->pk())
                ->execute($this->db)
                ->get('total_count');
    }

    /**
     * Allows a model use both email and username as unique identifiers for login.
     *
     * @param   string  Unique value
     * @return  string  Field name
     */
    public function uniqueKey($value)
    {
        return \Bootphp\Valid::email($value) ? 'email' : 'username';
    }

    /**
     * Password validation for plain passwords.
     *
     * @param   array   $values
     * @return  Validation
     */
    public static function getPasswordValidation($values)
    {
        return \Bootphp\Validation::factory($values)
                ->rule('password', 'min_length', [':value', 8])
                ->rule('password_confirm', 'matches', [':validation', ':field', 'password']);
    }

    /**
     * Create a new user.
     *
     * Example usage:
     * ~~~
     * $user = ORM::factory('User')->create_user($_POST, array(
     * 	'username',
     * 	'password',
     * 	'email',
     * );
     * ~~~
     *
     * @param   array   $values
     * @param   array   $expected
     */
    public function createUser($values, $expected)
    {
        // Validation for passwords
        $extra_validation = Model_User::get_password_validation($values)
            ->rule('password', 'notEmpty');

        return $this->values($values, $expected)->create($extra_validation);
    }

    /**
     * Update an existing user.
     *
     * [!!] We make the assumption that if a user does not supply a password, that they do not wish to update their password.
     *
     * Example usage:
     * ~~~
     * $user = ORM::factory('User')
     * 	->where('username', '=', 'kiall')
     * 	->find()
     * 	->update_user($_POST, array(
     * 		'username',
     * 		'password',
     * 		'email',
     * 	);
     * ~~~
     *
     * @param   array   $values
     * @param   array   $expected
     */
    public function updateUser($values, $expected = null)
    {
        if (empty($values['password'])) {
            unset($values['password'], $values['password_confirm']);
        }

        // Validation for passwords
        $extra_validation = self::getPasswordValidation($values);

        return $this->values($values, $expected)->update($extra_validation);
    }

}
