<?php

namespace PhpAutodiscover;

/**
 * Description of Config
 *
 * @author Ondrej Homolka <ondrej.homolka@gmail.com>
 */
class Config implements ConfigInterface
{

    // Default values
    private $config = [
        'general' => [
            'domain'   => 'example.com',
            'display_name' => 'Mail',
            'login_format' => 'username',
            'log_dir'  => 'log',
            'log_file' => 'autodiscover.log',
            'log_lvl'  => 'warning',
        ],
        'smtp' => [
            'host'   => 'smtp.example.com',
            'port'   => 465,
            'ssl'    => 'on',
            'socket' => 'SSL',
            'spa'    => 'off',
        ],
        'imap' => [
            'host'   => 'imap.example.com',
            'port'   => 993,
            'ssl'    => 'on',
            'socket' => 'SSL',
            'spa'    => 'off',
        ],
    ];

    /**
     * Load configuration from file or from direct input
     *
     * @param string|array $configuration
     * @throws ConfigException
     */
    public function load ($configuration): void
    {
        if (!is_array($configuration) && !file_exists($configuration)) {
            throw new ParseInputException('Configuration format was not recognized');
        }

        if (file_exists($configuration) && is_readable($configuration)) {
            $conf_file = $configuration;
            $configuration = [];
            
            // YAML support
            //if (!function_exists('yaml_parse_file')) {
            //    throw new ConfigException('Package like php-pecl-yaml HAVE TO be installed!');
            //}
            //$configuration = yaml_parse_file($conf_file, true);

            // INI support
            if (!$configuration) {
                $configuration = parse_ini_file($conf_file, true);
            }
            
            if (!$configuration) {
                throw new ParseFileException('Given config file ' . var_export($conf_file, true) . ' cannot be parsed!');
            }
        }

        $this->config = $this->merge($this->config, $configuration);
    }

    /**
     * It returns configuration even from nested array
     * 
     * @example
     *   $this->config = [
     *       'a' => [
     *           'aa' => [
     *               'aaa' => 111,
     *               'aab' => 112,
     *           ],
     *           'ab' => [
     *               'aba' => 121,
     *           ],
     *           'ac' => 13,
     *       ],
     *       'b' => [
     *           'ba' => 21,
     *       ],
     *   ];
     *   Conf->get()          retruns the whole configuration
     *   Conf->get('a')       returns array ['aa' => ..., 'ab' => ..., 'ac' => ...]
     *   Conf->get('a', 'ab') returns array ['aba' => 121]
     *   Conf->get('b', 'ba') returns integer 13
     *   Conf->get('c')       returns null
     *
     * @param  string   $keys
     * @return mixed    requested value or null if key not exists
     */
    public function get (string ...$keys)
    {
        if (func_num_args() == 0) {
            return $this->config;
        } else {
            $key = array_pop($keys);
            $part = $this->get(...$keys);
            return (isset($part[$key])) ? $part[$key] : null;
        }
    }

    /**
     * array_merge() function cannot work with arrays within arrays
     * All variable types can be inserted, if type differs it takes always the second one
     * Values are rewritten by values which were given later
     *
     * @example
     *   merge([
     *       'a' => [
     *           'aa' => [
     *               'aaa' => 111,
     *               'aab' => 112,
     *           ],
     *           'ab' => [
     *               'aba' => 121,
     *           ],
     *       ],
     *       'b' => [
     *           'ba' => 21,
     *       ],
     *   ],
     *   [
     *       'a' => [
     *           'aa' => [
     *               'aac' => 111,
     *               'aad' => 112,
     *           ],
     *           'ab' => [
     *               'aba' => 'xxx',
     *           ],
     *           'ac' => 13,
     *       ],
     *       'b' => 2,
     *   ]);
     * will result to
     *   [
     *       'a' => [
     *           'aa' => [
     *               'aaa' => 111,
     *               'aab' => 112,
     *               'aac' => 111,
     *               'aad' => 112,
     *           ],
     *           'ab' => [
     *               'aba' => 'xxx',
     *           ],
     *           'ac' => 13,
     *       ],
     *       'b' => 2,
     *   ]
     *
     * @example
     *   merge([
     *       'a',
     *   ],
     *   [
     *       'b' => 2,
     *   ],
     *   'c'
     *   );
     * will result to
     *   'c'
     *
     * @param mixed $configurations
     */
    private function merge (...$configurations)
    {
        $out = [];

        // If there is more then two arguments, it will first merge first two,
        //   then the result with third, then with forth, etc.
        if (count($configurations) > 2) {
            $out = array_shift($configurations);
            foreach ($configurations as $configuration) {
                $out = $this->merge($out, $configuration);
            }
            return $out;
        }

        $first = $configurations[0];
        $second = $configurations[1];

        // Only two arrays can be merged, when are types different always prefer second given value
        if (!is_array($first) || !is_array($second)) {
            return $second;
        }

        // Get all unique keys from both arrays
        foreach (array_unique(array_merge(array_keys($first), array_keys($second))) as $key) {
            // Both arrays have the same key > merge values under this keys
            if (isset($first[$key]) && isset($second[$key])) {
                $out[$key] = $this->merge($first[$key], $second[$key]);
            // If only one array has the key > add the value to output
            } else {
                $out[$key] = isset($first[$key]) ? $first[$key] : $second[$key];
            }
        }

        // Return result
        return $out;
    }

}

class ConfigException extends \RuntimeException
{
}

class ParseInputException extends ConfigException
{
}

class ParseFileException extends ConfigException
{
}
