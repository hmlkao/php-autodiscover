<?php

namespace PhpAutodiscover\Responses;

class UnknownResponse implements ResponseInterface
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
        $this->logger->info('Unknown response');
        
        $response = file_get_contents(TEMPLATES_DIR . '/unknown.template');
        $replace = [
            '%TIME%' => date('H:i:s.v'),
        ];

        header("Content-Type: text/xml");
        $output = str_replace(array_keys($replace), array_values($replace), $response);
        $this->logger->debug($output);
        echo $output;
    }

}
