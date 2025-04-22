# Protecting Your IP Address When Hosting

## Use Dynamic DNS Service

1. Sign up for a free Dynamic DNS service like:
   - No-IP (https://www.noip.com/)
   - DuckDNS (https://www.duckdns.org/)
   - Dynu (https://www.dynu.com/)

2. Install the DDNS client on your Raspberry Pi:

```bash
# For No-IP
sudo apt-get install noip2

# For DuckDNS, create a cron job:
mkdir ~/duckdns
cd ~/duckdns
nano duck.sh
```

Add to duck.sh:
```bash
#!/bin/bash
echo url="https://www.duckdns.org/update?domains=YOURDOMAIN&token=YOUR_TOKEN&ip=" | curl -k -o ~/duckdns/duck.log -K -
```

Make it executable and create a cron job:
```bash
chmod 700 duck.sh
crontab -e
```

Add this line:
```
*/5 * * * * ~/duckdns/duck.sh >/dev/null 2>&1
```

## Use a Reverse Proxy

Consider using Cloudflare as a reverse proxy to hide your IP address:

1. Sign up for a Cloudflare account
2. Add your domain to Cloudflare
3. Update your domain's nameservers to Cloudflare's
4. Enable "Proxied" status for your A record

## Configure a Firewall

Install and configure UFW (Uncomplicated Firewall):

```bash
sudo apt-get install ufw
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow http
sudo ufw allow https
sudo ufw enable
```
