<?php

namespace Bootphp;
/**
 * HTML helper class. Provides generic methods for generating various HTML
 * tags and making output HTML safe.
 *
 * @package	BootPHP
 * @category	Helpers
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio
 */
class Html
{
	/**
	 * @var  array  preferred order of attributes
	 */
	public static $attribute_order = array
		(
		'action',
		'method',
		'type',
		'id',
		'name',
		'value',
		'href',
		'src',
		'width',
		'height',
		'cols',
		'rows',
		'size',
		'maxlength',
		'rel',
		'media',
		'accept-charset',
		'accept',
		'tabindex',
		'accesskey',
		'alt',
		'title',
		'class',
		'style',
		'selected',
		'checked',
		'readonly',
		'disabled',
	);
	/**
	 * @var  boolean  automatically target external URLs to a new window?
	 */
	public static $windowed_urls = false;
	/**
	 * Convert special characters to HTML entities. All untrusted content
	 * should be passed through this method to prevent XSS injections.
	 *
	 * 	 echo Html::chars($username);
	 *
	 * @param	string	 string to convert
	 * @param	boolean  encode existing entities
	 * @return	string
	 */
	public static function chars($value)
	{
		return htmlspecialchars((string)$value, ENT_QUOTES);
	}
	/**
	 * Convert all applicable characters to HTML entities. All characters
	 * that cannot be represented in HTML with the current character set
	 * will be converted to entities.
	 *
	 * 	 echo Html::entities($username);
	 *
	 * @param	string	 string to convert
	 * @param	boolean  encode existing entities
	 * @return	string
	 */
	public static function entities($value, $double_encode = true)
	{
		return htmlentities((string)$value, ENT_QUOTES, BootPHP::$charset, $double_encode);
	}
	/**
	 * Create HTML link anchors. Note that the title is not escaped, to allow
	 * HTML elements within links (images, etc).
	 *
	 * 	 echo Html::anchor('/user/profile', 'My Profile');
	 *
	 * @param	string	 URL || URI string
	 * @param	string	 link text
	 * @param	array	HTML anchor attributes
	 * @param	mixed	protocol to pass to URL::base()
	 * @param	boolean  include the index page
	 * @return	string
	 * @uses	URL::base
	 * @uses	URL::site
	 * @uses	Html::attributes
	 */
	public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = true)
	{
		if ( $title === NULL )
		{
			// Use the URI as the title
			$title = $uri;
		}
		if ( $uri === '' )
		{
			// Only use the base URL
			$uri = URL::base($protocol, $index);
		}
		else
		{
			if ( strpos($uri, '://') !== false )
			{
				if ( Html::$windowed_urls === true && empty($attributes['target']) )
				{
					// Make the link open in a new window
					$attributes['target'] = '_blank';
				}
			}
			elseif ( $uri[0] !== '#' )
			{
				// Make the URI absolute for non-id anchors
				$uri = URL::site($uri, $protocol, $index);
			}
		}
		// Add the sanitized link to the attributes
		$attributes['href'] = $uri;
		return '<a' . Html::attributes($attributes) . '>' . $title . '</a>';
	}
	/**
	 * Creates an HTML anchor to a file. Note that the title is not escaped,
	 * to allow HTML elements within links (images, etc).
	 *
	 * 	 echo Html::file_anchor('media/doc/user_guide.pdf', 'User Guide');
	 *
	 * @param	string	name of file to link to
	 * @param	string	link text
	 * @param	array   HTML anchor attributes
	 * @param	mixed	protocol to pass to URL::base()
	 * @param	boolean  include the index page
	 * @return	string
	 * @uses	URL::base
	 * @uses	Html::attributes
	 */
	public static function file_anchor($file, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = false)
	{
		if ( $title === NULL )
		{
			// Use the file name as the title
			$title = basename($file);
		}
		// Add the file link to the attributes
		$attributes['href'] = URL::base($protocol, $index) . $file;
		return '<a' . Html::attributes($attributes) . '>' . $title . '</a>';
	}
	/**
	 * Creates an email (mailto:) anchor. Note that the title is not escaped,
	 * to allow HTML elements within links (images, etc).
	 *
	 * 	 echo Html::mailto($address);
	 *
	 * @param	string	email address to send to
	 * @param	string	link text
	 * @param	array   HTML anchor attributes
	 * @return	string
	 * @uses	Html::attributes
	 */
	public static function mailto($email, $title = NULL, array $attributes = NULL)
	{
		if ( $title === NULL )
		{
			// Use the email address as the title
			$title = $email;
		}
		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;' . $email . '"' . self::attributes($attributes) . '>' . $title . '</a>';
	}
	/**
	 * Creates a style sheet link element.
	 *
	 * 	 echo Html::style('media/css/screen.css');
	 *
	 * @param	string	 file name
	 * @param	array	default attributes
	 * @param	mixed	protocol to pass to URL::base()
	 * @param	boolean  include the index page
	 * @return	string
	 * @uses	URL::base
	 * @uses	Html::attributes
	 */
	public static function style($file, array $attributes = NULL, $protocol = NULL, $index = false)
	{
		if ( strpos($file, '://') === false )
		{
			// Add the base URL
			$file = URL::base($protocol, $index) . $file;
		}
		// Set the stylesheet link
		$attributes['href'] = $file;
		// Set the stylesheet rel
		$attributes['rel'] = 'stylesheet';
		// Set the stylesheet type
		$attributes['type'] = 'text/css';
		return '<link' . self::attributes($attributes) . '/>';
	}
	/**
	 * Creates a script link.
	 *
	 * 	 echo Html::script('media/js/jquery.min.js');
	 *
	 * @param	string	 file name
	 * @param	array	default attributes
	 * @param	mixed	protocol to pass to URL::base()
	 * @param	boolean  include the index page
	 * @return	string
	 * @uses	URL::base
	 * @uses	Html::attributes
	 */
	public static function script($file, array $attributes = NULL, $protocol = NULL, $index = false)
	{
		if ( strpos($file, '://') === false )
		{
			// Add the base URL
			$file = URL::base($protocol, $index) . $file;
		}
		// Set the script link
		$attributes['src'] = $file;
		// Set the script type
		$attributes['type'] = 'text/javascript';
		return '<script' . self::attributes($attributes) . '></script>';
	}
	/**
	 * Creates a image link.
	 *
	 * 	 echo Html::image('media/img/logo.png', array('alt' => 'My Company'));
	 *
	 * @param	string	 file name
	 * @param	array	default attributes
	 * @param	mixed	protocol to pass to URL::base()
	 * @param	boolean  include the index page
	 * @return	string
	 * @uses	URL::base
	 * @uses	Html::attributes
	 */
	public static function image($file, array $attributes = NULL, $protocol = NULL, $index = false)
	{
		if ( strpos($file, '://') === false )
		{
			// Add the base URL
			$file = URL::base($protocol, $index) . $file;
		}
		// Add the image link
		$attributes['src'] = $file;
		return '<img' . self::attributes($attributes) . '/>';
	}
	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 * Attributes will be sorted using Html::$attribute_order for consistency.
	 *
	 * 	 echo '<div'.Html::attributes($attrs).'>'.$content.'</div>';
	 *
	 * @param	array   attribute list
	 * @return	string
	 */
	public static function attributes(array $attributes = NULL)
	{
		if ( empty($attributes) )
			return '';
		$sorted = [];
		foreach( self::$attribute_order as $key )
		{
			if ( isset($attributes[$key]) )
			{
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}
		// Combine the sorted attributes
		$attributes = $sorted + $attributes;
		$compiled = '';
		foreach( $attributes as $key => $value )
		{
			if ( $value === NULL )
			{
				// Skip attributes that have NULL values
				continue;
			}
			if ( is_int($key) )
			{
				// Assume non-associative keys are mirrored attributes
				$key = $value;
			}
			// Add the attribute value
			$compiled .= ' ' . $key . '="' . self::chars($value) . '"';
		}
		return $compiled;
	}
}