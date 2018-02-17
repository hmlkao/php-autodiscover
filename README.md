# php-autodiscover
Simple Autdiscover/Autoconfig XML generator written in PHP.
Autodiscover was developed by Microsoft to simply set an email client according to Active Directory.
Tested with:
 - Android-Mail app (Add account > Account type Other > Put your mail address with corresponding domain > Exchange > Put your password)
 - Thunderbird
 - Native Mail application on Windows 10
 - Microsoft Outlook 2016

## Prerequisites
You should have:
 - correctly set mail server ([great instructions] written by Sam Hobbs for Raspberry Pi).
 - valid TLS certificates for your subdomains autodiscover and autoconfig (eg. [Let's Encrypt]).

### DNS records
There are differencies how clients calls requests for autoconfiguration:

#### Outlook example
The process how is Outlook looking for settings is well described [here](https://technet.microsoft.com/en-us/library/cc511507.aspx?f=255&mspperror=-2147217396#Anchor_2)
Simply:


#### Thunderbird example
eg. autoconfig.&lt;domain&gt;/mail/config-v1.1.xml?emailaddress=&lt;email&gt;

You should have set DNS name for your domain
You can have set DNS records for domains for usage with common mail clients:
 - `autodiscover.<domain>`, eg. autodiscover.hmlka.cz (used by Outlook)
 - `autoconfig.<domain>`, eg. autoconfig.hmlka.cz (used by Thunderbird)

## Usage
Clone project to your web root
```bash
cd /var/www
git clone https://github.com/hmlkao/php-autodiscover.git autodiscover
```

### Web server configuration
#### Nginx
```
```
#### Apache

### Multiple domains on one machine
Copy autodiscover.php file to your web root for each of your domains, eg. /var/www/autodiscover/&lt;domain&gt;.php

NOTE: If is your configuration same for all domains it requires only different Web server settings

## Client configuration
### Thunderbird
### Outlook
### Other
You should choose Exchange as a source for email

## About
Generates valid XML for automatic configuration email client like Outlook, Thunderbird or Android-Mail.


[1]: <https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration>
[autoconfig example]: <https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration/FileFormat/HowTo>
[great instructions]: <https://samhobbs.co.uk/2013/12/raspberry-pi-email-server-part-2-dovecot>
[let's encrypt]: <https://letsencrypt.org/>
[outlook]: <https://technet.microsoft.com/en-us/library/cc511507.aspx?f=255&mspperror=-2147217396#Anchor_3>
[mobilesync]: <https://msdn.microsoft.com/en-us/library/office/hh352638(v=exchg.140).aspx>
[Discovering Account Settings by Using the Autodiscover Command]: <https://msdn.microsoft.com/en-us/library/ee200809(v=exchg.80).aspx>
[pox]: <https://msdn.microsoft.com/en-us/library/aa581522(v=exchg.150).aspx>
