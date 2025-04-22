# Setting Up HTTPS on Raspberry Pi

## Install Certbot

```bash
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache
```

## Obtain SSL Certificate

```bash
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

## Auto-renewal

Certbot creates a cron job automatically, but you can test renewal with:

```bash
sudo certbot renew --dry-run
```
