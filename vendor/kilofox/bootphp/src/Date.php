<?php

namespace Bootphp;
/**
 * 日期辅助类。
 *
 * @package	BootPHP
 * @category	辅助类
 * @author		Tinsh <kilofox2000@gmail.com>
 * @copyright	(C) 2005-2016 Kilofox Studio

 */
class Date
{
	// 各种时间单位的秒数
	const YEAR = 31556926;
	const MONTH = 2629744;
	const WEEK = 604800;
	const DAY = 86400;
	const HOUR = 3600;
	const MINUTE = 60;
	// Date::months() 的可用格式
	const MONTHS_LONG = '%B';
	const MONTHS_SHORT = '%b';
	/**
	 * Date::formattedTime() 使用的默认时间格式
	 * @var string
	 */
	public static $format = 'Y-m-d H:i:s';
	/**
	 * Date::formattedTime() 使用的时区
	 * @link http://php.net/manual/zh/timezones.php
	 * @var string
	 */
	public static $timezone;
	/**
	 * 返回两个时区的偏移量（以秒为单位）。用来对不同时区的用户显示日期。
	 *
	 * 	 $seconds = Date::offset('Asia/Shanghai', 'GMT');
	 *
	 * [!!] PHP支持的时区列表可以在 <http://php.net/manual/zh/timezones> 找到。
	 *
	 * @param	string	要查找偏移量的时区
	 * @param	string	基准时区
	 * @param	mixed	Unix时间戳或日期字符串
	 * @return	integer
	 */
	public static function offset($remote, $local = NULL, $now = NULL)
	{
		if ( $local === NULL )
		{
			// 使用默认时区
			$local = date_default_timezone_get();
		}
		if ( is_int($now) )
		{
			// 将时间戳转换为字符串
			$now = date(DateTime::RFC2822, $now);
		}
		// 创建时区对象
		$zoneRemote = new DateTimeZone($remote);
		$zoneLocal = new DateTimeZone($local);
		// 根据时区创建日期对象
		$timeRemote = new DateTime($now, $zoneRemote);
		$timeLocal = new DateTime($now, $zoneLocal);
		// 找到偏移量
		$offset = $zoneRemote->getOffset($timeRemote) - $zoneLocal->getOffset($timeLocal);
		return $offset;
	}
	/**
	 * Number of seconds in a minute, incrementing by a step. Typically used as
	 * a shortcut for generating a list that can used in a form.
	 *
	 * 	 $seconds = Date::seconds(); // 01, 02, 03, ..., 58, 59, 60
	 *
	 * @param	integer	amount to increment each step by, 1 to 30
	 * @param	integer	start value
	 * @param	integer	end value
	 * @return	array	A mirrored (foo => foo) array from 1-60.
	 */
	public static function seconds($step = 1, $start = 0, $end = 60)
	{
		// Always integer
		$step = (int)$step;
		$seconds = [];
		for( $i = $start; $i < $end; $i += $step )
		{
			$seconds[$i] = sprintf('%02d', $i);
		}
		return $seconds;
	}
	/**
	 * Number of minutes in an hour, incrementing by a step. Typically used as
	 * a shortcut for generating a list that can be used in a form.
	 *
	 * 	 $minutes = Date::minutes(); // 05, 10, 15, ..., 50, 55, 60
	 *
	 * @uses	Date::seconds
	 * @param	integer  amount to increment each step by, 1 to 30
	 * @return	array	A mirrored (foo => foo) array from 1-60.
	 */
	public static function minutes($step = 5)
	{
		// Because there are the same number of minutes as seconds in this set,
		// we choose to re-use seconds(), rather than creating an entirely new
		// function. Shhhh, it's cheating! ;) There are several more of these
		// in the following methods.
		return Date::seconds($step);
	}
	/**
	 * Number of hours in a day. Typically used as a shortcut for generating a
	 * list that can be used in a form.
	 *
	 * 	 $hours = Date::hours(); // 01, 02, 03, ..., 10, 11, 12
	 *
	 * @param	integer	amount to increment each step by
	 * @param	boolean	use 24-hour time
	 * @param	integer	the hour to start at
	 * @return	array	A mirrored (foo => foo) array from start-12 || start-23.
	 */
	public static function hours($step = 1, $long = false, $start = NULL)
	{
		// Default values
		$step = (int)$step;
		$long = (bool)$long;
		$hours = [];
		// Set the default start if none was specified.
		if ( $start === NULL )
		{
			$start = ($long === false) ? 1 : 0;
		}
		$hours = [];
		// 24-hour time has 24 hours, instead of 12
		$size = ($long === true) ? 23 : 12;
		for( $i = $start; $i <= $size; $i += $step )
		{
			$hours[$i] = (string)$i;
		}
		return $hours;
	}
	/**
	 * Returns AM || PM, based on a given hour (in 24 hour format).
	 *
	 * 	 $type = Date::ampm(12); // PM
	 * 	 $type = Date::ampm(1);  // AM
	 *
	 * @param	integer	number of the hour
	 * @return	string
	 */
	public static function ampm($hour)
	{
		// Always integer
		$hour = (int)$hour;
		return ($hour > 11) ? 'PM' : 'AM';
	}
	/**
	 * Adjusts a non-24-hour number into a 24-hour number.
	 *
	 * 	 $hour = Date::adjust(3, 'pm'); // 15
	 *
	 * @param	integer	hour to adjust
	 * @param	string	AM 或 PM
	 * @return	string
	 */
	public static function adjust($hour, $ampm)
	{
		$hour = (int)$hour;
		$ampm = strtolower($ampm);
		switch( $ampm )
		{
			case 'am':
				if ( $hour == 12 )
				{
					$hour = 0;
				}
				break;
			case 'pm':
				if ( $hour < 12 )
				{
					$hour += 12;
				}
				break;
		}
		return sprintf('%02d', $hour);
	}
	/**
	 * Number of days in a given month && year. Typically used as a shortcut
	 * for generating a list that can be used in a form.
	 *
	 * 	 Date::days(4, 2010); // 1, 2, 3, ..., 28, 29, 30
	 *
	 * @param	integer	number of month
	 * @param	integer	number of year to check month, defaults to the current year
	 * @return	array	A mirrored (foo => foo) array of the days.
	 */
	public static function days($month, $year = false)
	{
		static $months;
		if ( $year === false )
		{
			// Use the current year by default
			$year = date('Y');
		}
		// Always integers
		$month = (int)$month;
		$year = (int)$year;
		// We use caching for months, because time functions are used
		if ( empty($months[$year][$month]) )
		{
			$months[$year][$month] = [];
			// Use date to find the number of days in the given month
			$total = date('t', mktime(1, 0, 0, $month, 1, $year)) + 1;
			for( $i = 1; $i < $total; $i++ )
			{
				$months[$year][$month][$i] = (string)$i;
			}
		}
		return $months[$year][$month];
	}
	/**
	 * Number of months in a year. Typically used as a shortcut for generating
	 * a list that can be used in a form.
	 *
	 * By default a mirrored array of $month_number => $month_number is returned
	 *
	 * 	 Date::months();
	 * 	 // aray(1 => 1, 2 => 2, 3 => 3, ..., 12 => 12)
	 *
	 * But you can customise this by passing in either Date::MONTHS_LONG
	 *
	 * 	 Date::months(Date::MONTHS_LONG);
	 * 	 // array(1 => 'January', 2 => 'February', ..., 12 => 'December')
	 *
	 * Or Date::MONTHS_SHORT
	 *
	 * 	 Date::months(Date::MONTHS_SHORT);
	 * 	 // array(1 => 'Jan', 2 => 'Feb', ..., 12 => 'Dec')
	 *
	 * @uses	Date::hours
	 * @param	string	The format to use for months
	 * @return	array	An array of months based on the specified format
	 */
	public static function months($format = NULL)
	{
		$months = [];
		if ( $format === Date::MONTHS_LONG || $format === Date::MONTHS_SHORT )
		{
			for( $i = 1; $i <= 12; ++$i )
			{
				$months[$i] = strftime($format, mktime(0, 0, 0, $i, 1));
			}
		}
		else
		{
			$months = Date::hours();
		}
		return $months;
	}
	/**
	 * Returns an array of years between a starting && ending year. By default,
	 * the the current year - 5 && current year + 5 will be used. Typically used
	 * as a shortcut for generating a list that can be used in a form.
	 *
	 * 	 $years = Date::years(2000, 2010); // 2000, 2001, ..., 2009, 2010
	 *
	 * @param	integer	starting year (default is current year - 5)
	 * @param	integer	ending year (default is current year + 5)
	 * @return	array
	 */
	public static function years($start = false, $end = false)
	{
		// Default values
		$start = ($start === false) ? (date('Y') - 5) : (int)$start;
		$end = ($end === false) ? (date('Y') + 5) : (int)$end;
		$years = [];
		for( $i = $start; $i <= $end; $i++ )
		{
			$years[$i] = (string)$i;
		}
		return $years;
	}
	/**
	 * 以人类易读的格式返回两个时间戳的时间差。
	 * 如果没给定第二个时间戳，会使用当前时间。
	 * 显示跨度的时候也可以考虑使用 [Date::fuzzySpan] 。
	 *
	 * 	 $span = Date::span(60, 182, 'minutes,seconds'); // array('minutes' => 2, 'seconds' => 2)
	 * 	 $span = Date::span(60, 182, 'minutes'); // 2
	 *
	 * @param	integer	timestamp to find the span of
	 * @param	integer	timestamp to use as the baseline
	 * @param	string	formatting string
	 * @return	string	when only a single output is requested
	 * @return	array	associative list of all outputs requested
	 */
	public static function span($remote, $local = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// 规范输出
		$output = trim(strtolower((string)$output));
		if ( !$output )
		{
			// 无效的输出
			return false;
		}
		// Array with the output formats
		$output = preg_split('/[^a-z]+/', $output);
		// Convert the list of outputs to an associative array
		$output = array_combine($output, array_fill(0, count($output), 0));
		// Make the output values into keys
		extract(array_flip($output), EXTR_SKIP);
		if ( $local === NULL )
		{
			// Calculate the span from the current time
			$local = time();
		}
		// 计算时间跨度（秒）
		$timespan = abs($remote - $local);
		if ( isset($output['years']) )
		{
			$timespan -= Date::YEAR * ($output['years'] = (int)floor($timespan / Date::YEAR));
		}
		if ( isset($output['months']) )
		{
			$timespan -= Date::MONTH * ($output['months'] = (int)floor($timespan / Date::MONTH));
		}
		if ( isset($output['weeks']) )
		{
			$timespan -= Date::WEEK * ($output['weeks'] = (int)floor($timespan / Date::WEEK));
		}
		if ( isset($output['days']) )
		{
			$timespan -= Date::DAY * ($output['days'] = (int)floor($timespan / Date::DAY));
		}
		if ( isset($output['hours']) )
		{
			$timespan -= Date::HOUR * ($output['hours'] = (int)floor($timespan / Date::HOUR));
		}
		if ( isset($output['minutes']) )
		{
			$timespan -= Date::MINUTE * ($output['minutes'] = (int)floor($timespan / Date::MINUTE));
		}
		// Seconds ago, 1
		if ( isset($output['seconds']) )
		{
			$output['seconds'] = $timespan;
		}
		if ( count($output) === 1 )
		{
			// Only a single output was requested, return it
			return array_pop($output);
		}
		// 返回数组
		return $output;
	}
	/**
	 * 以“模糊”的方式返回某个时间与现在的时间差。
	 * 用模糊的时间来代替日期，有时便于阅读和理解。
	 *
	 * 	 $span = Date::fuzzySpan(time() - 10); // "一分钟以前"
	 * 	 $span = Date::fuzzySpan(time() + 20); // "几分钟以内"
	 *
	 * @param	integer “远程”时间戳
	 * @return	string
	 */
	public static function fuzzySpan($timestamp)
	{
		// 确定偏移的秒数
		$offset = abs(time() - $timestamp);
		if ( $offset <= Date::MINUTE )
		{
			$span = '一分钟';
		}
		elseif ( $offset < (Date::MINUTE * 20) )
		{
			$span = '几分钟';
		}
		elseif ( $offset < Date::HOUR )
		{
			$span = '一小时';
		}
		elseif ( $offset < (Date::HOUR * 4) )
		{
			$span = '几个小时';
		}
		elseif ( $offset < Date::DAY )
		{
			$span = '一天';
		}
		elseif ( $offset < (Date::DAY * 2) )
		{
			$span = '大约一天';
		}
		elseif ( $offset < (Date::DAY * 4) )
		{
			$span = '几天时间';
		}
		elseif ( $offset < Date::WEEK )
		{
			$span = '一星期';
		}
		elseif ( $offset < (Date::WEEK * 2) )
		{
			$span = '大约一星期';
		}
		elseif ( $offset < Date::MONTH )
		{
			$span = '一个月';
		}
		elseif ( $offset < (Date::MONTH * 2) )
		{
			$span = '大约一个月';
		}
		elseif ( $offset < (Date::MONTH * 4) )
		{
			$span = '几个月';
		}
		elseif ( $offset < Date::YEAR )
		{
			$span = '一年';
		}
		elseif ( $offset < (Date::YEAR * 2) )
		{
			$span = '大约一年';
		}
		elseif ( $offset < (Date::YEAR * 4) )
		{
			$span = '几年';
		}
		elseif ( $offset < (Date::YEAR * 8) )
		{
			$span = '若干年';
		}
		elseif ( $offset < (Date::YEAR * 12) )
		{
			$span = '大约十年';
		}
		elseif ( $offset < (Date::YEAR * 24) )
		{
			$span = '几十年';
		}
		else
		{
			$span = '很长时间';
		}
		if ( $timestamp <= $localTimestamp )
		{
			// 这是过去
			return $span . '以前';
		}
		else
		{
			// 这是未来
			return $span . '以内';
		}
	}
	/**
	 * 返回一个带有指定格式的日期/时间字符串
	 *
	 * 	 $time = Date::formattedTime('2015-08-05 14:44:23');
	 *
	 * @see	 http://php.net/manual/zh/datetime.construct.php
	 * @param	string	日期/时间字符串
	 * @param	string	时间戳格式
	 * @param	string	时区
	 * @return	string
	 */
	public static function formattedTime($datetimeStr = 'now', $format = NULL, $timezone = NULL)
	{
		$format = $format == NULL ? self::$format : $format;
		$timezone = $timezone === NULL ? self::$timezone : $timezone;

		$tz = new \DateTimeZone($timezone ? $timezone : date_default_timezone_get());
		$time = new \DateTime($datetimeStr, $tz);

		$time->setTimezone($tz);

		return $time->format($format);
	}
	/**
	 * 将Unix时间戳转换成人类可读的日期/时间字符串
	 *
	 * @param	integer	Unix时间戳
	 * @param	string	时间戳格式
	 * @param	string	时区
	 * @return	string
	 */
	public static function unixToHuman($timestamp = NULL, $format = NULL, $timezone = NULL)
	{
		$timestamp = $timestamp === NULL ? time() : $timestamp;
		return self::formattedTime('@' . $timestamp, $format, $timezone);
	}
}