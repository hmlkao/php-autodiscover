# php-autodiscover
Simple Autdiscover/Autoconfig XML generator written in PHP.
Autodiscover was developed by Microsoft to simply set an email client according to Active Directory.

Another way how to autoconfigure mail clients are
 - SRV DNS records according to [RFC6186](https://tools.ietf.org/html/rfc6186)

Tested with:
 - Android E-mail app
 - Thunderbird
 - Mail (Windows 10 native client)
 - Microsoft Outlook 2010
 - Evolution (Gnome native client)

## Prerequisites
You should have:
 - correctly set mail server ([great instructions] written by Sam Hobbs for Raspberry Pi).
 - correctly configured DNS records for subdomains `autodiscover.<domain>` and `autoconfig.<domain>`
 - valid TLS certificates for your subdomains `autodiscover.<domain>` and `autoconfig.<domain>` (eg. [Let's Encrypt]).
 - [installed composer](https://getcomposer.org), project depends on some other PHP libraries

### DNS records
There are differencies how clients calls for autoconfiguration:
 - `autodiscover.<domain>`, eg. autodiscover.example.com (used by Exchange-like accounts)
 - `autoconfig.<domain>`, eg. autoconfig.example.com (used by Thunderbird)

## Usage
Clone project to your web root
```bash
cd /var/www
git clone https://github.com/hmlkao/php-autodiscover.git autodiscover
cd autodiscover
composer install
```

## Web server configuration examples

### Nginx
```
```
### Apache

### Multiple domains on one machine
Copy autodiscover.php file to your web root for each of your domains, eg. `/var/www/autodiscover/example-1.php`

NOTE: If is your configuration same for all domains it requires only different Web server settings

## Email clients

### Microsoft Office Outlook 2010 (v14.0.7212.5000)
The process how is Outlook looking for settings is well described [here](https://technet.microsoft.com/en-us/library/cc511507.aspx?f=255&mspperror=-2147217396#Anchor_2)
1. Open File > Add account
1. Enter your mail
1. This version tries only URL `GET https://autodiscover.<domain>/autodiscover/autodiscover.xml`
    ```xml
    <Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/mobilesync/requestschema/2006">
      <Request>
        <EMailAddress>%email%</EMailAddress>
        <AcceptableResponseSchema>http://schemas.microsoft.com/exchange/autodiscover/mobilesync/responseschema/2006</AcceptableResponseSchema>
      </Request>
    </Autodiscover>
    ```

### Microsoft Mail (v16005.10730.20115.0)
Native application available in Microsoft Windows 10.
1. Add account > Other (POP, IMAP)
1. Enter your mail address and password

### Mozilla Thunderbird (v60.0)
1. Open Menu > Preferences > Account Settings
1. In the left bottom corner open Account Actions > Add Mail Account...
1. Enter 'Email address' and 'Password' and click on Continue
1. Thunderbird tries open URL `GET https://autoconfig.<domain>/mail/config-v1.1.xml?emailaddress=%email%`
   where `%email%` is requested email address, eg. `jon.doe@example.com`

### Evolution (v3.28.5)

You HAVE TO add account directly in Gnome settings, not it Evolution (it doesn't work from some reason)

1. Settings > Online Accounts > click on More at the bottom > IMAP and SMTP
1. Enter your mail address and password
1. You will see IMAP settings at first
1. Then SMTP settings
1. Evolution tries open URLs `POST https://<domain>/autodiscover/autodiscover.xml` and `POST https://autodiscover.<domain>/autodiscover/autodiscover.xml` with data:
    ```xml
    <Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/outlook/requestschema/2006">
      <Request>
        <EMailAddress>%email%</EMailAddress>
        <AcceptableResponseSchema>http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a</AcceptableResponseSchema>
      </Request>
    </Autodiscover>
    ```
    where `%email%` is requested email address, eg. `jon.doe@example.com`
1. After all you find your mailbox in Evolution

### Android E-mail (v6.41.18)
NOT SUPPORTED - it sends [mobilesync] request

1. Go to Settings > Add account > Microsoft Exchange
1. Application tries open URL `POST https://autodiscover.<domain>/autodiscover/autodiscover.xml` with data:
    ```xml
    <Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/mobilesync/requestschema/2006">
      <Request>
        <EMailAddress>%email%</EMailAddress>
        <AcceptableResponseSchema>http://schemas.microsoft.com/exchange/autodiscover/mobilesync/responseschema/2006</AcceptableResponseSchema>
      </Request>
    </Autodiscover>
    ```
    where `%email%` is requested email address, eg. `jon.doe@example.com`

### Other
You should choose Exchange as a source for email.

## TODO
- Nginx examples
- Apache examples
- Usage description
- Factory to create response


[1]: <https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration>
[autoconfig example]: <https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration/FileFormat/HowTo>
[great instructions]: <https://samhobbs.co.uk/2013/12/raspberry-pi-email-server-part-2-dovecot>
[let's encrypt]: <https://letsencrypt.org/>
[outlook]: <https://technet.microsoft.com/en-us/library/cc511507.aspx?f=255&mspperror=-2147217396#Anchor_3>
[mobilesync]: <https://msdn.microsoft.com/en-us/library/office/hh352638(v=exchg.140).aspx>
[Discovering Account Settings by Using the Autodiscover Command]: <https://msdn.microsoft.com/en-us/library/ee200809(v=exchg.80).aspx>
[pox]: <https://msdn.microsoft.com/en-us/library/aa581522(v=exchg.150).aspx>
