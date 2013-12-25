<?php
namespace moss\config;

/**
 * Configuration representation
 *
 * @package Moss Config
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Config implements ConfigInterface
{

    protected $config = array(
        'framework' => array(
            'error' => array(
                'display' => true,
                'level' => -1,
                'detail' => true
            ),
            'session' => array(
                'name' => 'PHPSESSID',
                'cacheLimiter' => ''
            ),
            'cookie' => array(
                'domain' => null,
                'path' => '/',
                'http' => true,
                'ttl' => 2592000 // one month
            )
        ),
        'namespaces' => array(),
        'container' => array(),
        'dispatcher' => array(),
        'router' => array()
    );

    /**
     * Creates Config instance
     *
     * @param array $arr
     *
     * @throws ConfigException
     */
    public function __construct($arr = array())
    {
        $this->import($arr);
    }

    /**
     * Reads configuration properties from passed array
     *
     * @param array $import
     *
     * @return $this
     */
    public function import(array $import)
    {
        foreach ($import as $key => $node) {
            switch ($key) {
                case 'container':
                    $node = $this->applyContainerDefaults($node);
                    break;
                case 'dispatcher':
                    $node = $this->applyDispatcherDefaults($node);
                    break;
                case 'router':
                    $node = $this->applyRouterDefaults($node);
                    break;
            }

            $this->config[$key] = array_merge($this->config[$key], $node);
        }

        return $this;
    }

    /**
     * Returns current stored configuration as array
     *
     * @return array
     */
    public function export()
    {
        return $this->config;
    }

    /**
     * Applies default values or missing properties for containers component definition
     *
     * @param array $array
     * @param array $defaults
     *
     * @return array
     */
    private function applyContainerDefaults(array $array, $defaults = array('arguments' => array(), 'methods' => array(), 'shared' => false))
    {
        foreach ($array as &$node) {
            if (!isset($node['class']) && !isset($node['closure'])) {
                continue;
            }

            if(is_array($node)) {
                $node = array_merge($defaults, $node);
            }
            unset($node);
        }

        return $array;
    }

    /**
     * Applies default values or missing properties for event listener definition
     *
     * @param array $array
     * @param array $defaults
     *
     * @return array
     * @throws ConfigException
     */
    private function applyDispatcherDefaults(array $array, $defaults = array('method' => null, 'arguments' => array()))
    {
        foreach ($array as &$evt) {
            foreach ($evt as &$node) {
                if (!isset($node['component']) && !isset($node['closure'])) {
                    throw new ConfigException('Missing required "component" or "closure" property in event listener definition');
                }

                if(is_array($node)) {
                    $node = array_merge($defaults, $node);
                }
                unset($node);
            }
            unset($evt);
        }

        return $array;
    }

    /**
     * Applies default values or missing properties for route definition
     *
     * @param array $array
     * @param array $defaults
     *
     * @return array
     * @throws ConfigException
     */
    private function applyRouterDefaults(array $array, $defaults = array('arguments' => array()))
    {
        foreach ($array as &$node) {
            if (!isset($node['pattern'])) {
                throw new ConfigException('Missing required "pattern" property in route definition');
            }

            if (!isset($node['controller'])) {
                throw new ConfigException('Missing required "controller" property in route definition');
            }

            if(is_array($node)) {
                $node = array_merge($defaults, $node);
            }
            unset($node);
        }

        return $array;
    }

    /**
     * Returns core variable value
     * If variable is undefined - returns false
     *
     * @param string $var     name of core variable
     * @param mixed  $default default value if variable not found
     *
     * @return mixed
     */
    public function get($var, $default = null)
    {
        return $this->getArrValue($this->config, $var, $default);
    }

    /**
     * Returns offset value from array or default value if offset does not exists
     *
     * @param array|\ArrayAccess $array
     * @param string             $offset
     * @param mixed              $default
     *
     * @return mixed
     */
    protected function getArrValue($array, $offset, $default = null)
    {
        $keys = explode('.', $offset);
        while ($i = array_shift($keys)) {
            if (!isset($array[$i])) {
                return $default;
            }

            $array = $array[$i];
        }

        return $array;
    }
}
