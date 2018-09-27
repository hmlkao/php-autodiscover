<?php


/******************************* CONFIGURATION ********************************/

define('APP_DIR', __DIR__ . '/..');
define('TEMPLATES_DIR', APP_DIR . '/app/templates');

date_default_timezone_set('Europe/Prague');

$logger = new PhpAutodiscover\Logger($config);


/********************************* GENERATOR **********************************/

// Response for Exchange-like account
if (strpos(filter_input(INPUT_SERVER, 'HTTP_HOST'), 'autodiscover') !== false) {
    $request_xml = file_get_contents("php://input");
    $logger->debug($request_xml);
    $request = json_decode(json_encode(simplexml_load_string($request_xml)), true);
    $logger->debug($request);

    if (!$request) {
        $response = new PhpAutodiscover\Responses\UnknownResponse($logger, $config);
    }
    else {
        $login = ($config->get('general', 'login_format') == 'email') ?
                    $request['Request']['EMailAddress'] :
                    explode('@', $request['Request']['EMailAddress'])[0];

        $response = new PhpAutodiscover\Responses\AutodiscoverResponse($logger, $config, [
            'schema' => $request['Request']['AcceptableResponseSchema'],
            'login' => $login,
        ]);
    }
}
// Response for Thunderbird-like request
else if (strpos(filter_input(INPUT_SERVER, 'HTTP_HOST'), 'autoconfig') !== false) {
    $email = filter_input(INPUT_GET, 'emailaddress');
    $login = ($config->get('general', 'login_format') == 'email') ?
                $email :
                explode('@', $email)[0];

    $response = new PhpAutodiscover\Responses\AutoconfigResponse($logger, $config, [
        'login' => $login,
    ]);
}
// Unknown response
else {
    $response = new PhpAutodiscover\Responses\UnknownResponse($logger, $config);
}

$response->render();
