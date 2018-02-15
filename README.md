# php-autodiscover
Simple Autdiscover/Autoconfig XML generator written in PHP

## Prerequisites
You should have:
 - correctly set mail server ([great instructions] written by Sam Hobbs for Raspberry Pi).
 - valid TLS certificates for your domain, autodiscover always requires HTTPS connections (eg. [Let's Encrypt]).

There are differencies how clients calls requests for autoconfiguration:
### Outlook example
Outlook firstly look for domain **autodiscover.<domain>** where try to reach

### DNS records
You should have set DNS name for your domain
You can have set DNS records for domains:
 - autodiscover.<domain>, eg. autodiscover.hmlka.cz (used by Outlook)
 - autoconfig.<domain>, eg. autoconfig.hmlka.cz (used by Thunderbird)
### Web server configuration
#### Nginx
```
```
#### Apache

## Usage
Copy autodiscover.php file to your web root, eg. /var/www/autodiscover/<domain>.php
Tested with:
 - Gmail application on Android 5.0
 - Thunderbird 52.4.0 on Fedora 27
 - Native Mail application on Windows 10
 - Microsoft Outlook 2016

### Multiple domains on one machine

## Client configuration
### Thunderbird
### Outlook
### Other
You should choose Exchange as a source for email

## About
It simply generates valid XML for autoconfiguration email client like Outlook, Thunderbird or Gmail.


[1]: <https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration>
[autoconfig example]: <https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration/FileFormat/HowTo>
[great instructions]: <https://samhobbs.co.uk/2013/12/raspberry-pi-email-server-part-2-dovecot>
[let's encrypt]: <https://letsencrypt.org/>
[outlook]: <https://technet.microsoft.com/en-us/library/cc511507.aspx?f=255&mspperror=-2147217396#Anchor_3>
[activesync]: <https://msdn.microsoft.com/en-us/library/office/hh352638(v=exchg.140).aspx>
[pox]: <https://msdn.microsoft.com/en-us/library/aa581522(v=exchg.150).aspx>
