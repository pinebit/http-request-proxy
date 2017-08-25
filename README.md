# PHP AJAX PROXY

This is a very simple implementation of HTTP AJAX proxy method which you can add to your webserver.

The purpose of this script is simple: proxy all AJAX requests being made from one domain to another.
This is in particular useful to avoid CORS issues when you cannot make AJAX requests to a certain server.

## Contents

* `proxy.php` is the main script, it is self-descriptive
* `http_build_url.php` a helper function borrowed from https://github.com/jakeasmith/http_build_url
* `.htaccess` a sample server configuration that feeds HTTP requests to `proxy.php` script.

