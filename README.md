# RDW API
Laravel application to get vehicle information from [opendata.rdw.nl](https://opendata.rdw.nl) or [overheid.io](https://overheid.io).
This API can be extended to be used in Filament: [rdw-api-filament](https://github.com/jdkweb/rdw-api-filament)

## 1. Installation

### Require with composer
```bash
composer require jdkweb/rdw-api
```
Requires PHP 8.2 and Laravel 10 or higher
### Add service provider
```bash
eee
```
### Import (if adjustments are needed)
#### config
```bash
php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="config"
```
If you want to use the demo, you need to publish the config, see LINK...
#### language
```bash
php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="lang"
```
