<?php

defined('SYSPATH') OR die('Kohana bootstrap needs to be included before tests run');

/**
 * @package    Bootphp/Image
 * @group      kohana
 * @group      kohana.image
 * @category   Test
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://http://kilofox.net/license
 */
class Kohana_ImageTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('The GD extension is not available.');
        }
    }

    /**
     * Tests the Image::save() method for files that don't have extensions
     *
     * @return  void
     */
    public function test_save_without_extension()
    {
        $image = Image::factory(MODPATH . 'image/tests/test_data/test_image');
        $this->asserttrue($image->save(Kohana::$cache_dir . '/test_image'));

        unlink(Kohana::$cache_dir . '/test_image');
    }

}

// End Kohana_ImageTest
