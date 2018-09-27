<?php

namespace PhpAutodiscover\Responses;

use PhpAutodiscover;

class AutodiscoverResponse implements ResponseInterface
{
    private $logger;
    private $config;
    private $params;

    public function __construct (PhpAutodiscover\Logger $logger, PhpAutodiscover\Config $config, array $params = [])
    {
        $this->logger = $logger;
        $this->config = $config;
        $this->params = $params;
    }
    
    public function render ()
    {
        $this->logger->info('Autodiscover response');

        $response = file_get_contents(TEMPLATES_DIR . '/autodiscover.template');
        $replace = [
            '%SCHEMA%'       => $this->params['schema'],
            '%LOGIN%'        => $this->params['login'],
            '%DISPLAY_NAME%' => $this->config->get('general', 'display_name'),
            '%IMAP_HOST%'    => $this->config->get('imap', 'host'),
            '%IMAP_PORT%'    => $this->config->get('imap', 'port'),
            '%IMAP_SPA%'     => $this->config->get('imap', 'spa'),
            '%IMAP_SSL%'     => $this->config->get('imap', 'ssl'),
            '%SMTP_HOST%'    => $this->config->get('smtp', 'host'),
            '%SMTP_PORT%'    => $this->config->get('smtp', 'port'),
            '%SMTP_SPA%'     => $this->config->get('smtp', 'spa'),
            '%SMTP_SSL%'     => $this->config->get('smtp', 'ssl'),
        ];

        header("Content-Type: text/xml");
        $output = str_replace(array_keys($replace), array_values($replace), $response);
        $this->logger->debug($output);
        echo $output;
    }

}
