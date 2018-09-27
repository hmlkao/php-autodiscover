<?php

namespace PhpAutodiscover;

/**
 *
 * @author Ondrej Homolka <ondrej.homolka@gmail.com>
 */
interface ConfigInterface
{

    /**
     * Load configuration from file or from direct input
     *
     * @param string|array $configuration
     * @throws ConfigException
     */
    public function load ($configuration): void;

    /**
     * Get key from configuration
     *
     * @param string $key
     * @return mixed
     */
    public function get (string ...$keys);
    
}
