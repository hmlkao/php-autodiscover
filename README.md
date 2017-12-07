# php-autodiscover
Autdiscover/Autoconfig XML generator written in PHP
## Prerequisites
You should have correctly set mail server, great instructions can be found in [2]
## Usage
Copy autodiscover.php file to your web root, eg. /var/www/autodiscover/autodiscover.php
### DNS records
### Web server configuration
#### Nginx
#### Apache

## About
It simply generates valid XML for autoconfiguration email client like Outlook, Thunderbird or Gmail.
Autodiscover always requires HTTPS connections!

There are differencies how clients calls requests for autoconfiguration:
### Outlook
Firstly look for domain autodiscover.<domain>

## Sources
[1] https://developer.mozilla.org/en-US/docs/Mozilla/Thunderbird/Autoconfiguration
[2] https://samhobbs.co.uk/2013/12/raspberry-pi-email-server-part-2-dovecot
