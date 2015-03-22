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
    public function testImportExport($import, $expected = [])
    {
        $default = [
            'framework' => [
                'error' => ['level' => -1, 'detail' => true],
                'session' => ['name' => 'PHPSESSID', 'cacheLimiter' => '']
            ],
            'container' => [],
            'dispatcher' => [],
            'router' => [],
        ];

        $config = new Config();
        $config->import($import);

        $this->assertEquals(array_replace_recursive($default, $expected ? $expected : $import), $config->export());
    }

    public function importProvider()
    {
        $fnc = function () { };

        return [
            [
                [
                    'framework' => [
                        'error' => ['level' => -1, 'detail' => true],
                        'session' => ['name' => 'PHPSESSID', 'cacheLimiter' => '']
                    ],
                ]
            ],
            [
                [
                    'container' => [
                        'foo' => 'bar',
                        'name' => [
                            'component' => $fnc,
                            'shared' => false
                        ]
                    ]
                ]
            ],
            [
                [
                    'dispatcher' => [
                        'foo' => [$fnc]
                    ]
                ]
            ],
            [
                [
                    'router' => [
                        'routeName' => [
                            'pattern' => '/{foo}/({bar})/',
                            'controller' => 'Moss:sample:Sample:index',
                            'arguments' => [],
                            'methods' => []
                        ]
                    ]
                ]
            ],
            [
                [
                    'import' => [
                        [
                            'dispatcher' => [
                                'foo' => [$fnc]
                            ]
                        ]
                    ]
                ],
                [
                    'dispatcher' => [
                        'foo' => [$fnc]
                    ],
                ]
            ]
        ];
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
        return [
            [null],
            ['dev'],
            ['prod']
        ];
    }

    /**
     * @dataProvider importModeProvider
     */
    public function testImportExportWithMode($import, $expected)
    {
        $default = [
            'framework' => [
                'error' => ['level' => -1, 'detail' => true],
                'session' => ['name' => 'PHPSESSID', 'cacheLimiter' => '']
            ],
            'container' => [],
            'dispatcher' => [],
            'router' => []
        ];

        $config = new Config();
        $config->mode('dev');
        $config->import($import);

        $this->assertEquals(array_replace_recursive($default, $expected), $config->export());
    }

    public function importModeProvider()
    {
        return [
            [
                [
                    'import' => [
                        [
                            'container' => [
                                'foo' => 'bar',
                            ]
                        ]
                    ]
                ],
                [
                    'container' => [
                        'foo' => 'bar',
                    ]
                ]
            ],
            [
                [
                    'import_prod' => [
                        [
                            'container' => [
                                'foo' => 'bar'
                            ],
                        ]
                    ]
                ],
                []
            ],
            [
                [
                    'import_dev' => [
                        [
                            'container' => [
                                'foo' => 'bar'
                            ]
                        ]
                    ]
                ],
                [
                    'container' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        ];
    }

    public function testPrefixedImport()
    {
        $data = [
            'import' => [
                'prefix' => [
                    'container' => ['var' => 'value']
                ]
            ]
        ];

        $config = new Config();
        $config->import($data);
        $this->assertEquals('value', $config->get('container.prefix:var'));
    }

    public function testGet()
    {
        $result = [
            'level' => E_ALL | E_NOTICE,
            'detail' => true
        ];

        $config = new Config(['framework' => ['error' => $result]]);
        $this->assertEquals($result, $config->get('framework.error'));
    }

    public function testGetDeep()
    {
        $config = new Config(['framework' => ['error' => ['detail' => true]]]);
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

    /**
     * @dataProvider dataProvider
     */
    public function testOffsetUnset($offset, $value)
    {
        $config = new Config();
        $config[$offset] = $value;
        unset($config[$offset]);
        $this->assertEquals(4, $config->count());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOffsetGetSet($offset, $value)
    {
        $config = new Config();
        $config[$offset] = $value;
        $this->assertEquals($value, $config[$offset]);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOffsetGetEmpty($offset)
    {
        $config = new Config();
        $this->assertNull(null, $config[$offset]);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOffsetSetWithoutKey($value)
    {
        $config = new Config();
        $config[] = $value;
        $this->assertEquals($value, $config[0]);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testOffsetExists($offset, $value)
    {
        $config = new Config();
        $config[$offset] = $value;
        $this->assertTrue(isset($config[$offset]));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testCount($offset, $value)
    {
        $config = new Config();
        $config[1] = $offset;
        $config[2] = $value;
        $this->assertEquals(6, $config->count());
    }

    public function dataProvider()
    {
        return [
            ['foo', 1, ['foo' => 1]],
            ['bar', 'lorem', ['bar' => 'lorem']],
            ['yada', ['yada' => 'yada'], ['yada' => ['yada' => 'yada']]],
            ['dada', new \stdClass(), ['dada' => new \stdClass()]],
            ['foo.bar', 'yada', ['foo' => ['bar' => 'yada']], ['foo' => []]]
        ];
    }

    public function testIterator()
    {
        $config = new Config();

        $expected = [
            'framework' => [
                'error' => ['level' => -1, 'detail' => true],
                'session' => ['name' => 'PHPSESSID', 'cacheLimiter' => '']
            ],
            'container' => [],
            'dispatcher' => [],
            'router' => [],
        ];

        foreach ($config as $key => $val) {
            $this->assertTrue(isset($expected[$key]));
            $this->assertEquals($expected[$key], $val);
        }
    }
}
