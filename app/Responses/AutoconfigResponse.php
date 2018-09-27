<?php

namespace PhpAutodiscover\Responses;

use PhpAutodiscover;

class AutoconfigResponse implements ResponseInterface
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
        $this->logger->info('Autoconfig response');

        $response = file_get_contents(TEMPLATES_DIR . '/autoconfig.template');
        $replace = [
            '%DOMAIN%'       => $this->config->get('general', 'domain'),
            '%USERNAME%'     => $this->params['login'],
            '%DISPLAY_NAME%' => $this->config->get('general', 'display_name'),
            '%IMAP_HOST%'    => $this->config->get('imap', 'host'),
            '%IMAP_PORT%'    => $this->config->get('imap', 'port'),
            '%IMAP_SOCKET%'  => $this->config->get('imap', 'socket'),
            '%IMAP_AUTHENTICATION%' => ($this->config->get('imap', 'spa') == 'on') ? 'password-encrypted' : 'password-cleartext',
            '%SMTP_HOST%'    => $this->config->get('smtp', 'host'),
            '%SMTP_PORT%'    => $this->config->get('smtp', 'port'),
            '%SMTP_SOCKET%'  => $this->config->get('smtp', 'socket'),
            '%SMTP_AUTHENTICATION%' => ($this->config->get('smtp', 'spa') == 'on') ? 'password-encrypted' : 'password-cleartext',
        ];

        header("Content-Type: text/xml");
        $output = str_replace(array_keys($replace), array_values($replace), $response);
        $this->logger->debug($output);
        echo $output;
    }

}
