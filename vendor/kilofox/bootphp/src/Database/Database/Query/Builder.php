<?php

namespace Bootphp\Database\Database\Query;

use Bootphp\Database\Database;

/**
 * Database query builder. See [Query Builder](/database/query/builder) for usage and examples.
 *
 * @package    Bootphp/Database
 * @category   Query
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
abstract class Builder extends \Bootphp\Database\Database\Query
{
    /**
     * Compiles an array of JOIN statements into an SQL partial.
     *
     * @param   object  $db     Database instance
     * @param   array   $joins  Join statements
     * @return  string
     */
    protected function compileJoin(Database $db, array $joins)
    {
        $statements = [];

        foreach ($joins as $join) {
            // Compile each of the join statements
            $statements[] = $join->compile($db);
        }

        return implode(' ', $statements);
    }

    /**
     * Compiles an array of conditions into an SQL partial. Used for WHERE and HAVING.
     *
     * @param   object  $db          Database instance
     * @param   array   $conditions  Condition statements
     * @return  string
     */
    protected function compileConditions(Database $db, array $conditions)
    {
        $lastCondition = null;

        $sql = '';
        foreach ($conditions as $group) {
            // Process groups of conditions
            foreach ($group as $logic => $condition) {
                if ($condition === '(') {
                    if (!empty($sql) && $lastCondition !== '(') {
                        // Include logic operator
                        $sql .= ' ' . $logic . ' ';
                    }
                    $sql .= '(';
                } elseif ($condition === ')') {
                    $sql .= ')';
                } else {
                    if (!empty($sql) && $lastCondition !== '(') {
                        // Add the logic operator
                        $sql .= ' ' . $logic . ' ';
                    }

                    // Split the condition
                    list($column, $op, $value) = $condition;

                    if ($value === null) {
                        if ($op === '=') {
                            // Convert "val = null" to "val IS null"
                            $op = 'IS';
                        } elseif ($op === '!=' || $op === '<>') {
                            // Convert "val != null" to "val IS NOT null"
                            $op = 'IS NOT';
                        }
                    }

                    // Database operators are always uppercase
                    $op = strtoupper($op);

                    if ($op === 'BETWEEN' && is_array($value)) {
                        // BETWEEN always has exactly two arguments
                        list($min, $max) = $value;

                        if ((is_string($min) && array_key_exists($min, $this->parameters)) === false) {
                            // Quote the value, it is not a parameter
                            $min = $db->quote($min);
                        }

                        if ((is_string($max) && array_key_exists($max, $this->parameters)) === false) {
                            // Quote the value, it is not a parameter
                            $max = $db->quote($max);
                        }

                        // Quote the min and max value
                        $value = $min . ' AND ' . $max;
                    } elseif ((is_string($value) && array_key_exists($value, $this->parameters)) === false) {
                        // Quote the value, it is not a parameter
                        $value = $db->quote($value);
                    }

                    if ($column) {
                        if (is_array($column)) {
                            // Use the column name
                            $column = $db->quoteIdentifier(reset($column));
                        } else {
                            // Apply proper quoting to the column
                            $column = $db->quoteColumn($column);
                        }
                    }

                    // Append the statement to the query
                    $sql .= trim($column . ' ' . $op . ' ' . $value);
                }

                $lastCondition = $condition;
            }
        }

        return $sql;
    }

    /**
     * Compiles an array of set values into an SQL partial. Used for UPDATE.
     *
     * @param   object  $db      Database instance
     * @param   array   $values  Updated values
     * @return  string
     */
    protected function compileSet(Database $db, array $values)
    {
        $set = [];
        foreach ($values as $group) {
            // Split the set
            list ($column, $value) = $group;

            // Quote the column name
            $column = $db->quoteColumn($column);

            if ((is_string($value) && array_key_exists($value, $this->parameters)) === false) {
                // Quote the value, it is not a parameter
                $value = $db->quote($value);
            }

            $set[$column] = $column . ' = ' . $value;
        }

        return implode(', ', $set);
    }

    /**
     * Compiles an array of GROUP BY columns into an SQL partial.
     *
     * @param   object  $db       Database instance
     * @param   array   $columns
     * @return  string
     */
    protected function compileGroupBy(Database $db, array $columns)
    {
        $group = [];

        foreach ($columns as $column) {
            if (is_array($column)) {
                // Use the column alias
                $column = $db->quoteIdentifier(end($column));
            } else {
                // Apply proper quoting to the column
                $column = $db->quoteColumn($column);
            }

            $group[] = $column;
        }

        return 'GROUP BY ' . implode(', ', $group);
    }

    /**
     * Compiles an array of ORDER BY statements into an SQL partial.
     *
     * @param   object  $db       Database instance
     * @param   array   $columns  Sorting columns
     * @return  string
     */
    protected function compileOrderBy(Database $db, array $columns)
    {
        $sort = [];
        foreach ($columns as $group) {
            list ($column, $direction) = $group;

            if (is_array($column)) {
                // Use the column alias
                $column = $db->quoteIdentifier(end($column));
            } else {
                // Apply proper quoting to the column
                $column = $db->quoteColumn($column);
            }

            if ($direction) {
                // Make the direction uppercase
                $direction = ' ' . strtoupper($direction);
            }

            $sort[] = $column . $direction;
        }

        return 'ORDER BY ' . implode(', ', $sort);
    }

    /**
     * Reset the current builder status.
     *
     * @return  $this
     */
    abstract public function reset();
}
