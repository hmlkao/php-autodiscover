<?php
/*
Generates configuration for various email clients.
This script should generate valid config for:
    - Thunderbird
    - Android E-mail (choose Exchange as a server type)
    - Windows Outlook

Example of request from Android E-mail
<?xml version="1.0" encoding="utf-8"?>
<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/mobilesync/requestschema/2006">
 <Request>
  <EMailAddress>ondra@hmlka.cz</EMailAddress>
  <AcceptableResponseSchema>http://schemas.microsoft.com/exchange/autodiscover/mobilesync/responseschema/2006</AcceptableResponseSchema>
 </Request>
</Autodiscover>

*/



/****************************** CONFIGURATION *********************************/

// General configuration
global $config;
$config['general'] = [
    'domain'       => 'hmlka.cz',
    'displayname'  => 'Hmlka mail',
    'login_format' => 'username', // [username|email]
    'logfile'      => '/tmp/autodiscover.log',
    'debug'        => true, // Save debug messages to logfile
];
// SMTP server configuration
$config['smtp'] = [
    'host'        => 'smtp.' . $config['general']['domain'],
    'port'        => 465,
    'ssl'         => 'on', // Autodiscover only [on|off]
    'socket'      => 'SSL', // Autoconfig only [SSL|STARTTLS|plain]
    'spa'         => 'off', // Secure Password Authentication [on|off]
];
// IMAP server configuration
$config['imap'] = [
    'host'        => 'imap.' . $config['general']['domain'],
    'port'        => 993,
    'ssl'         => 'on', // Autodiscover only [on|off]
    'socket'      => 'SSL', // Autoconfig only [SSL|STARTTLS|plain]
    'spa'         => 'off', // Secure Password Authentication [on|off]
];




/******************************** GENERATOR ***********************************/
logger($_SERVER, 'DEBUG');
logger($_POST, 'DEBUG');

header("Content-Type: text/xml");



// Response like Exchange server
if ($_SERVER['SERVER_NAME'] == 'autodiscover.' . $config['general']['domain']) {

$request = file_get_contents("php://input");
logger($request);

preg_match("/\<EMailAddress\>(.*?)\<\/EMailAddress\>/", $request, $email);
if (!empty($email)) {
$email = $email[1];
logger($email, 'DEBUG');
$login = ($config['general']['login_format'] == 'email') ? $email : explode('@', $email)[0];
logger($login, 'DEBUG');

preg_match("/\<AcceptableResponseSchema\>(.*?)\<\/AcceptableResponseSchema\>/", $request, $schema);
$schema = (isset($schema[1])) ? $schema[1] : false;
logger($schema, 'DEBUG');

if (strpos($schema, 'mobilesync') === FALSE) {
logger('Outlook response');
?>
<?xml version="1.0" encoding="UTF-8"?>

<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006">
    <Response xmlns="<?php echo $schema; ?>">
        <User>
            <DisplayName><?php echo $config['general']['displayname']; ?></DisplayName>
        </User>

        <Account>
            <AccountType>email</AccountType>
            <Action>settings</Action>
            <Protocol>
                <Type>IMAP</Type>
                <Server><?php echo $config['imap']['host']; ?></Server>
                <Port><?php echo $config['imap']['port']; ?></Port>
                <LoginName><?php echo $login; ?></LoginName>
                <SPA><?php echo ($config['imap']['spa']); ?></SPA>
                <SSL><?php echo ($config['imap']['ssl']); ?></SSL>
            </Protocol>
            <Protocol>
                <Type>SMTP</Type>
                <Server><?php echo $config['smtp']['host']; ?></Server>
                <Port><?php echo $config['smtp']['port']; ?></Port>
                <LoginName><?php echo $login; ?></LoginName>
                <SPA><?php echo $config['smtp']['spa']; ?></SPA>
                <SSL><?php echo $config['smtp']['ssl']; ?></SSL>
            </Protocol>
        </Account>
    </Response>
</Autodiscover>

<?php
} else {
logger('Mobilesync response');
?>

<?xml version="1.0" encoding="UTF-8"?>
<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<Response xmlns="<?php echo $schema; ?>">
    <Culture>en:us</Culture>
    <User>
        <DisplayName><?php echo $login ?></DisplayName>
        <EMailAddress><?php echo $email ?></EMailAddress>
    </User>
    <Action>
        <Settings>
            <Server>
                <Type>MobileSync</Type>
                <Url></Url>
                <Name></Name>
            </Server>
        </Settings>
    </Action>
</Response>

<?php
}


// Response for Thunderbird
} elseif ($_SERVER['SERVER_NAME'] == 'autoconfig.' . $config['general']['domain']) {
?>
<?xml version="1.0" encoding="UTF-8"?>

<clientConfig version="1.1">
    <emailProvider id="<?php echo $config['general']['domain']; ?>">
        <domain><?php echo $config['general']['domain']; ?></domain>
        <displayName><?php echo $config['general']['displayname']; ?></displayName>
        <incomingServer type="imap">
            <hostname><?php echo $config['imap']['host']; ?></hostname>
            <port><?php echo $config['imap']['port']; ?></port>
            <socketType><?php echo $config['imap']['socket']; ?></socketType>
            <authentication><?php echo ($config['imap']['spa'] == 'on') ? 'password-encrypted' : 'password-cleartext';?></authentication>
            <username><?php echo ($config['general']['login'] == 'email') ? '%EMAILADDRESS%' : '%EMAILLOCALPART%'; ?></username>
        </incomingServer>
        <outgoingServer type="smtp">
            <hostname><?php echo $config['smtp']['host']; ?></hostname>
            <port><?php echo $config['smtp']['port']; ?></port>
            <socketType><?php echo $config['smtp']['socket']; ?></socketType>
            <authentication><?php echo ($config['smtp']['spa'] == 'on') ? 'password-encrypted' : 'password-cleartext';?></authentication>
            <username><?php echo ($config['general']['login'] == 'email') ? '%EMAILADDRESS%' : '%EMAILLOCALPART%'; ?></username>
        </outgoingServer>
    </emailProvider>
</clientConfig>

<?php
// Request from unknown client
}
}
?>
<?xml version="1.0" encoding="utf-8"?>

<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006">
    <Response>
        <Error Time="<?php echo date('H:i:s.v') ?>" Id="1">
            <ErrorCode>600</ErrorCode>
            <Message>Invalid Request</Message>
            <DebugData />
        </Error>
    </Response>
</Autodiscover>

<?php



/***************************** HELPER FUNCTIONS *******************************/

/**
 * Log message to file
 *
 * @param  string  $message  Message to log
 * @param  string  $level    Log level
 */
function logger ()
{
    global $config;

    $level = 'INFO';
    $args = func_get_args();
    if (in_array(strtoupper($args[func_num_args() - 1]), ['DEBUG', 'INFO', 'WARNING', 'ERROR'])) {
        $level = strtoupper(array_pop($args));
    }

    if (!$config['general']['debug']  &&  $level == 'DEBUG') {
        return;
    }

    $message = '';
    foreach ($args as $arg) {
        $message .= (is_string($arg)) ? $arg : var_export($arg, true);
        $message .= ' ';
    }

    file_put_contents(
        $config['general']['logfile'],
        date('c') . "\t[$level]\t" . $message . PHP_EOL,
        FILE_APPEND);
}
