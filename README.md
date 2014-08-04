# Uptime Robot for Panic Status Board

![](https://dl.dropboxusercontent.com/u/29924102/statusboard-uptimerobot/all.png)

First of all sorry for my bad English :) This script was created for personal use. If you find any bugs or you would like to contribute, fill an issue or send PR. Thanks.

## Requirements

- PHP >= 5.2.0
- [Status Board](http://panic.com/statusboard/) iPad app (paid)
- [Uptime Robot](https://uptimerobot.com) account (free)

## Install instructions

### 1. Copy config file

Make copy of `config.php.example` and name it `config.php`. Setup `date_default_timezone_set` (see [list of supported timezones](http://php.net/manual/en/timezones.php)). You can also change some configs variables like refresh rate, color, icons (see [list of Font Awesome icons](http://fortawesome.github.io/Font-Awesome/icons/)) and more.

### 2. Upload files

Use your favourite FTP client and copy `config.php` and `index.php` to your web server.

### 3. Uptime Robot

On Uptime Robot website go to `My Settings` -> `Alert Contacts` and click on `Add Alert Contact`. Select `Alert Contact Type`, set `Friendly Name` and fill `URL to Notify` with url to `index.php` on your web server with `?save&` (see image below). Finally click on `Create Alert Contact`.

![](https://dl.dropboxusercontent.com/u/29924102/statusboard-uptimerobot/webhook.png) 

You need to set this alert contact to existing monitors.

### 4. Status Board

Add DYI widget (the one with <>) and set URL to `index.php` on your web server. You can add parametr `with_history` (like `index.php?with_history` - see examples below).

#### Examples

Default: All up

![](https://dl.dropboxusercontent.com/u/29924102/statusboard-uptimerobot/up.png)

Default: Down

![](https://dl.dropboxusercontent.com/u/29924102/statusboard-uptimerobot/down.png)

With history: All up

![](https://dl.dropboxusercontent.com/u/29924102/statusboard-uptimerobot/up_with-history.png)

With history: Down

![](https://dl.dropboxusercontent.com/u/29924102/statusboard-uptimerobot/down_with-history.png)

## More widgets for Status Board 

- [GitLab Issues](https://github.com/WebEntity/statusboard-gitlab)
- [Daylite Projects](https://github.com/WebEntity/statusboard-daylite-projects)
- [Daylite Opportunities](https://github.com/WebEntity/statusboard-daylite-opportunities)

