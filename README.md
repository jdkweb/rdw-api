# RDW API

Laravel application to get vehicle information from [opendata.rdw.nl](https://opendata.rdw.nl) or [overheid.io](https://overheid.io). \
This wrapper can be extended to be used in Filament: [rdw-api-filament](https://github.com/jdkweb/rdw-api-filament)

---
1. [Installation](#installation)
2. [How to use](#How ot use)
3. [Demo](#demo)
4. [Extension for Filament](#filament)
---

## Installation

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
opendata.rdw.nl id the default source 
If you want to use the demo, you need to publish the config, see LINK...
#### language
```bash
php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="lang"
```

## How to use
Basic request
```php
use Jdkweb\Rdw\Facades\Rdw;
...
$result = Rdw::finder()
    ->setLicense('AB-895-P')
    ->fetch();
```
Request to the active API (config) \
All Rdw endpoints are selecteda and output is an array in local language
## Options
- finder([0|1|opendata|overheid])
  - 0 or 'opendata' => opendata.rdw.nl
  - 1 or 'overheid' => overheid.io
- selectApi([0|1|opendata|overheid]) \
  Equal to settings in finder
  - 0 or 'opendata' => opendata.rdw.nl
  - 1 or 'overheid' => overheid.io
- setEndpoints(string|array)
  - vehicle
  - vehicle_class
  - fuel
  - bodywork
  - bodywork
  - all (default)
- setLanguage(string) \
  Force output language, available:
  - nl
  - en
- format(string)
  - array (default)
  - json
  - xml
- fetch()  

### Example request
Request:
```php
$result = Rdw::finder()
    ->selectApi('overheid')
    ->setLicense('52BVL9')
    ->setEndpoints(['vehicle','fuel'])
    ->translate('en')
    ->format('json')
    ->fetch();
```
Result:
```json
{
   Vehicle: {
      registration_number: "52BVL9",
      vehicle_type: "Bus",
      brand: "VDL",
      trade_name: "CITEA LF-122/ ELECTRIC",
      expiry_date_mot: "20250921",
      date_of_registration: "20230721",
      configuration: "bus",
      number_of_seats: "37",
      ...
      ..
      .    
   },
   Fuel: {
      registration_number: "52BVL9",
      fuel_sequence_number: "1",
      fuel_description: "Elektriciteit",
      ...
      ..
```

## Demo
Two options to use the demo
### .env
```php
RDW-API-DEMO=1
```
Add this value to .env
### config
Import the rwd-api config
```bash
php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="config"
```
http://[domainname]/rdw-api/demo
```php
rdw_api_demo => 1,
```
Demo: 0 = Off | 1 = On
## Filament
```bash
composer require jdkweb/rdw-api-filament
```
