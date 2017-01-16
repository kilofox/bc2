<?php

/**
 * @package    Bootphp/Cache
 * @group      kohana
 * @group      kohana.cache
 * @category   Test
 * @author     Tinsh <kilofox2000@gmail.com>
 * @copyright  (C) 2005-2017 Kilofox Studio
 * @license    http://kohanaphp.com/license
 */
abstract class Kohana_CacheBasicMethodsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var     Cache driver for this test
     */
    protected $_cache_driver;

    /**
     * This method MUST be implemented by each driver to setup the `Cache`
     * instance for each test.
     *
     * This method should do the following tasks for each driver test:
     *
     *  - Test the Cache instance driver is available, skip test otherwise
     *  - Setup the Cache instance
     *  - Call the parent setup method, `parent::setUp()`
     *
     * @return  void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Accessor method to `$_cache_driver`.
     *
     * @return  Cache
     * @return  self
     */
    public function cache(Cache $cache = null)
    {
        if ($cache === null)
            return $this->_cache_driver;

        $this->_cache_driver = $cache;
        return $this;
    }

    /**
     * Data provider for test_set_get()
     *
     * @return  array
     */
    public function provider_set_get()
    {
        $object = new StdClass;
        $object->foo = 'foo';
        $object->bar = 'bar';

        $html_text = <<<TESTTEXT
<!doctype html>
<head>
</head>

<body>
</body>
</html>
TESTTEXT;

        return array(
            array(
                array(
                    'id' => 'string', // Key to set to cache
                    'value' => 'foobar', // Value to set to key
                    'ttl' => 0, // Time to live
                    'wait' => false, // Test wait time to let cache expire
                    'type' => 'string', // Type test
                    'default' => null         // Default value get should return
                ),
                'foobar'
            ),
            array(
                array(
                    'id' => 'integer',
                    'value' => 101010,
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'integer',
                    'default' => null
                ),
                101010
            ),
            array(
                array(
                    'id' => 'float',
                    'value' => 10.00,
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'float',
                    'default' => null
                ),
                10.00
            ),
            array(
                array(
                    'id' => 'array',
                    'value' => array(
                        'key' => 'foo',
                        'value' => 'bar'
                    ),
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'array',
                    'default' => null
                ),
                array(
                    'key' => 'foo',
                    'value' => 'bar'
                )
            ),
            array(
                array(
                    'id' => 'boolean',
                    'value' => true,
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'boolean',
                    'default' => null
                ),
                true
            ),
            array(
                array(
                    'id' => 'null',
                    'value' => null,
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'null',
                    'default' => null
                ),
                null
            ),
            array(
                array(
                    'id' => 'object',
                    'value' => $object,
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'object',
                    'default' => null
                ),
                $object
            ),
            array(
                array(
                    'id' => 'bar\\ with / troublesome key',
                    'value' => 'foo bar snafu',
                    'ttl' => 0,
                    'wait' => false,
                    'type' => 'string',
                    'default' => null
                ),
                'foo bar snafu'
            ),
            array(
                array(
                    'id' => 'test ttl 0 means never expire',
                    'value' => 'cache value that should last',
                    'ttl' => 0,
                    'wait' => 1,
                    'type' => 'string',
                    'default' => null
                ),
                'cache value that should last'
            ),
            array(
                array(
                    'id' => 'bar',
                    'value' => 'foo',
                    'ttl' => 3,
                    'wait' => 5,
                    'type' => 'null',
                    'default' => null
                ),
                null
            ),
            array(
                array(
                    'id' => 'snafu',
                    'value' => 'fubar',
                    'ttl' => 3,
                    'wait' => 5,
                    'type' => 'string',
                    'default' => 'something completely different!'
                ),
                'something completely different!'
            ),
            array(
                array(
                    'id' => 'new line test with HTML',
                    'value' => $html_text,
                    'ttl' => 10,
                    'wait' => false,
                    'type' => 'string',
                    'default' => null,
                ),
                $html_text
            ),
            array(
                array(
                    'id' => 'test with 60*5',
                    'value' => 'blabla',
                    'ttl' => 60 * 5,
                    'wait' => false,
                    'type' => 'string',
                    'default' => null,
                ),
                'blabla'
            ),
            array(
                array(
                    'id' => 'test with 60*50',
                    'value' => 'bla bla',
                    'ttl' => 60 * 50,
                    'wait' => false,
                    'type' => 'string',
                    'default' => null,
                ),
                'bla bla'
            )
        );
    }

    /**
     * Tests the [Cache::set()] method, testing;
     *
     *  - The value is cached
     *  - The lifetime is respected
     *  - The returned value type is as expected
     *  - The default not-found value is respected
     *
     * @dataProvider provider_set_get
     *
     * @param   array    data
     * @param   mixed    expected
     * @return  void
     */
    public function test_set_get(array $data, $expected)
    {
        $cache = $this->cache();
        extract($data);

        $this->asserttrue($cache->set($id, $value, $ttl));

        if ($wait !== false) {
            // Lets let the cache expire
            sleep($wait);
        }

        $result = $cache->get($id, $default);
        $this->assertEquals($expected, $result);
        $this->assertInternalType($type, $result);

        unset($id, $value, $ttl, $wait, $type, $default);
    }

    /**
     * Tests the [Cache::delete()] method, testing;
     *
     *  - The a cached value is deleted from cache
     *  - The cache returns a true value upon deletion
     *  - The cache returns a false value if no value exists to delete
     *
     * @return  void
     */
    public function test_delete()
    {
        // Init
        $cache = $this->cache();
        $cache->delete_all();

        // Test deletion if real cached value
        if (!$cache->set('test_delete_1', 'This should not be here!', 0)) {
            $this->fail('Unable to set cache value to delete!');
        }

        // Test delete returns true and check the value is gone
        $this->asserttrue($cache->delete('test_delete_1'));
        $this->assertnull($cache->get('test_delete_1'));

        // Test non-existant cache value returns false if no error
        $this->assertfalse($cache->delete('test_delete_1'));
    }

    /**
     * Tests [Cache::delete_all()] works as specified
     *
     * @return  void
     * @uses    Kohana_CacheBasicMethodsTest::provider_set_get()
     */
    public function test_delete_all()
    {
        // Init
        $cache = $this->cache();
        $data = $this->provider_set_get();

        foreach ($data as $key => $values) {
            extract($values[0]);
            if (!$cache->set($id, $value)) {
                $this->fail('Unable to set: ' . $key . ' => ' . $value . ' to cache');
            }
            unset($id, $value, $ttl, $wait, $type, $default);
        }

        // Test delete_all is successful
        $this->asserttrue($cache->delete_all());

        foreach ($data as $key => $values) {
            // Verify data has been purged
            $this->assertSame('Cache Deleted!', $cache->get($values[0]['id'], 'Cache Deleted!'));
        }
    }

}

// End Kohana_CacheBasicMethodsTest
