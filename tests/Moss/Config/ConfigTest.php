<?php
namespace Moss\Config;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-01-16 at 21:59:22.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider importProvider
     */
    public function testImportExport($import, $expected = array())
    {
        $default = array(
            'framework' => array(
                'error' => array('display' => true, 'level' => -1, 'detail' => true),
                'session' => array('name' => 'PHPSESSID', 'cacheLimiter' => ''),
                'cookie' => array('domain' => null, 'path' => '/', 'http' => true, 'ttl' => 2592000)
            ),
            'container' => array(),
            'dispatcher' => array(),
            'router' => array(),
        );

        $config = new Config();
        $config->import($import);

        $this->assertEquals(array_replace_recursive($default, $expected ? $expected : $import), $config->export());
    }

    public function importProvider()
    {
        return array(
            array(
                array(
                    'framework' => array(
                        'error' => array('display' => true, 'level' => E_ALL | E_NOTICE, 'detail' => true),
                        'session' => array('name' => 'PHPSESSID', 'cacheLimiter' => ''),
                        'cookie' => array('domain' => null, 'path' => '/', 'http' => true, 'ttl' => 2592000)
                    ),
                )
            ),
            array(
                array(
                    'container' => array(
                        'foo' => 'bar',
                        'name' => array(
                            'component' => function () { },
                            'shared' => false
                        )
                    ),
                )
            ),
            array(
                array(
                    'dispatcher' => array(
                        'foo' => array(
                            function () { },
                        )
                    ),
                )
            ),
            array(
                array(
                    'router' => array(
                        'routeName' => array(
                            'pattern' => '/{foo}/({bar})/',
                            'controller' => 'Moss:sample:Sample:index',
                            'arguments' => array(),
                            'methods' => array(),
                        )
                    )
                )
            ),
            array(
                array(
                    'import' => array(
                        array(
                            'dispatcher' => array(
                                'foo' => array(
                                    function () { }
                                )
                            )
                        )
                    )
                ),
                array(
                    'dispatcher' => array(
                        'foo' => array(
                            function () { }
                        )
                    ),
                )
            )
        );
    }

    /**
     * @dataProvider modeProvider
     */
    public function testMode($mode)
    {
        $config = new Config();
        $this->assertEquals($mode, $config->mode($mode));
    }

    public function modeProvider()
    {
        return array(
            array(null),
            array('dev'),
            array('prod')
        );
    }

    /**
     * @dataProvider importModeProvider
     */
    public function testImportExportWithMode($import, $expected)
    {
        $default = array(
            'framework' => array(
                'error' => array('display' => true, 'level' => -1, 'detail' => true),
                'session' => array('name' => 'PHPSESSID', 'cacheLimiter' => ''),
                'cookie' => array('domain' => null, 'path' => '/', 'http' => true, 'ttl' => 2592000)
            ),
            'container' => array(),
            'dispatcher' => array(),
            'router' => array(),
        );

        $config = new Config();
        $config->mode('dev');
        $config->import($import);

        $this->assertEquals(array_replace_recursive($default, $expected), $config->export());
    }

    public function importModeProvider()
    {
        return array(
            array(
                array(
                    'import' => array(
                        array(
                            'container' => array(
                                'foo' => 'bar',
                            ),
                        )
                    )
                ),
                array(
                    'container' => array(
                        'foo' => 'bar',
                    ),
                )
            ),
            array(
                array(
                    'import_prod' => array(
                        array(
                            'container' => array(
                                'foo' => 'bar',
                            ),
                        )
                    )
                ),
                array()
            ),
            array(
                array(
                    'import_dev' => array(
                        array(
                            'container' => array(
                                'foo' => 'bar',
                            ),
                        )
                    )
                ),
                array(
                    'container' => array(
                        'foo' => 'bar',
                    ),
                )
            )
        );
    }

    /**
     * @expectedException \Moss\Config\ConfigException
     * @expectedExceptionMessage Event listener must be callable
     */
    public function testInvalidDispatcherImport()
    {
        $result = array(
            'dispatcher' => array(
                'foo' => array(
                    'yada' => array()
                )
            )
        );

        $config = new Config();
        $config->import($result);
    }

    /**
     * @expectedException \Moss\Config\ConfigException
     * @expectedExceptionMessage Missing required "pattern" property in route definition
     */
    public function testInvalidRouterImportWithoutPattern()
    {
        $result = array(
            'router' => array(
                'routeName' => array(
                    'controller' => 'yada:yada'
                )
            )
        );

        $config = new Config();
        $config->import($result);
    }

    /**
     * @expectedException \Moss\Config\ConfigException
     * @expectedExceptionMessage Missing required "controller" property in route definition
     */
    public function testInvalidRouterImportWithoutController()
    {
        $result = array(
            'router' => array(
                'routeName' => array(
                    'pattern' => 'yada/yada'
                )
            )
        );

        $config = new Config();
        $config->import($result);
    }

    public function testGet()
    {
        $result = array(
            'level' => E_ALL | E_NOTICE,
            'detail' => true
        );

        $config = new Config(array('framework' => array('error' => $result)));
        $this->assertEquals($result, $config->get('framework.error'));
    }

    public function testGetDeep()
    {
        $config = new Config(array('framework' => array('error' => array('detail' => true))));
        $this->assertTrue($config->get('framework.error.detail'));
    }

    public function testGetBlank()
    {
        $config = new Config();
        $this->assertNull($config->get('foo'));
    }

    public function testGetDeepBlank()
    {
        $config = new Config();
        $this->assertNull($config->get('directories.foo'));
    }
}