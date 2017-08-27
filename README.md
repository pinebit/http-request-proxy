# SIMPLE HTTP REQUEST PROXY (in PHP)

This is a simple implementation of HTTP request (AJAX) proxy script which you can add to your webserver.

The purpose of this script is simple: proxy all HTTP requests being made from one domain to another. This is in particular useful to avoid CORS issues when you cannot make HTTP requests to a certain server.

## Features

* The script uses vanilla PHP, should work with php 5.x,
* Currently supports GET and POST requests, but can be easily extended to support other HTTP methods,
* Copies all cookies to/from a request,
* HTTPS ready.

## Contents

* `proxy.php` is the main script, it is self-descriptive,
* `http_build_url.php` a helper function borrowed from https://github.com/jakeasmith/http_build_url,
* `.htaccess` a sample server configuration that feeds HTTP requests to `proxy.php` script.

## License

MIT
