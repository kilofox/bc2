<?php

namespace Bootphp;

/**
 * Internationalization (i18n) class. Provides language loading and translation
 * methods without dependencies on [gettext](http://php.net/gettext).
 *
 *     // Display a translated message
 *     echo I18n::get('Hello, world');
 *
 *     // With parameter replacement
 *     echo I18n::get('Hello, :user', [':user' => $username]);
 *
 * @package    Bootphp
 * @category   Base
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kilofox.net/license
 */
class I18n
{
    /**
     * Target language: en-us, zh-cn, etc.
     *
     * @var string
     */
    public static $lang = 'en-us';

    /**
     * Translattion table.
     *
     * @var string
     */
    public static $table = [];

    /**
     * Get and set the target language.
     *
     *     // Get the current language
     *     $lang = I18n::lang();
     *
     *     // Change the current language to Chinese-Simplified
     *     I18n::lang('zh-cn');
     *
     * @param   string  $lang   New language setting
     * @return  string
     */
    public static function lang($lang = null)
    {
        if ($lang) {
            // Normalize the language
            self::$lang = strtolower($lang);
        }

        return self::$lang;
    }

    /**
     * Returns the translation table for a given language.
     *
     *     // Get all defined Chinese-Simplified messages in "file.php"
     *     $messages = I18n::load('file', 'zh-cn');
     *
     * @param   string  $langFile   Language file name to load
     * @param   string  $lang       Language to load from
     * @return  array
     */
    public static function load($langFile, $lang = null)
    {
        if ($lang) {
            self::lang($lang);
        } else {
            $lang = self::$lang;
        }

        // New translation table
        $table[$lang] = [];

        if (is_file($file = APP_PATH . '/I18n/' . $lang . '/' . $langFile . '.php')) {
            // Append the table
            $table[$lang] += require $file;

            self::$table = $table;

            return true;
        }

        return false;
    }

    /**
     * Returns translation of a string. If no translation exists, the original
     * string will be returned. The PHP function [strtr](http://php.net/strtr)
     * is used for replacing parameters.
     *
     *     $hello = I18n::get('Welcome back, :user', [':user' => $username]);
     *
     * @param   string  $string     Text to translate
     * @param   array   $values     Values to replace in the translated text
     * @param   string  $lang       Target language
     * @return  string
     */
    public static function get($string, array $values = null, $lang = null)
    {
        // The message and target languages are different. Get the translation for this message.
        if ($lang !== self::$lang) {
            if (!$lang) {
                // Use the global target language
                $lang = self::$lang;
            }

            // Get the translated string if it exists
            if (isset(self::$table[$lang][$string])) {
                $string = self::$table[$lang][$string];
            }
        }

        return empty($values) ? $string : strtr($string, $values);
    }

}
