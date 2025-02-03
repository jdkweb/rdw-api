# RDW API

Laravel application to get vehicle information from [opendata.rdw.nl](https://opendata.rdw.nl) or [overheid.io](https://overheid.io). \
This wrapper can be extended to be used in Filament: [rdw-api-filament](https://github.com/jdkweb/rdw-api-filament)

## Table of contents

- [Installation](#installation)
- [Translation](#translation)
- [How to use](#How ot use)
- [Demo](#demo)
- [Extension for Filament](#filament)


## Installation
Requires PHP 8.2 and Laravel 10 or higher \

Install the package via composer:
```bash
composer require jdkweb/rdw-api
```
If needed you can publish the config
```bash
php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="config"
```
## Translation
If changes are needed you can publish the translation files
```bash
# published in: trans/vendor/jdkweb/rdw-api
php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="lang"
```
For this package there are two translations:
- [Dutch (nl)](https://github.com/jdkweb/rdw-api/tree/main/lang/nl)  Default
- [English (en)](https://github.com/jdkweb/rdw-api/tree/main/lang/en)

## How to use
### Basic usage
```php
use Jdkweb\Rdw\Facades\Rdw;
...
$result = Rdw::finder()
    ->setLicense('AB-895-P')
    ->fetch();
```
Request to the active API (is set in the config, default is opendata.rdw.nl) \
All RDW endpoints are selected and output is an array in the local language
### Options
#### Select other API
```php
->selectApi(int|string) // 0|opendata | 1|overheid    
```
Can be used to overwrite te config settings 
- 0 or 'opendata' for using the RDW API opendata.rdw.nl **[default]**
- 1 or 'overheid' for using the overheid.io API

#### Select endpoints for request 
```php
->setEndpoints(string|array)

# examples
    ->setEndpoints('all')
    ->setEndpoints('vehicle')
    ->setEndpoints(['vehicle','fuel'])
```
Available endpoints (not case sensitive):
- vehicle
- vehicle_class
- fuel
- bodywork
- bodywork_specific
- axles 
- all **[default]**
#### Set output language
```php
->forceTranslation(string)
```
Force output language, so form can be English and RDW response in Dutch. \
Available:
  - nl **[default]**
  - en
#### Format of the response output
```php
->format(string)
```
- array **[default]**
- json
- xml
#### Send the request
```php
->fetch()
```
### Example request
Request:
```php
$result = Rdw::finder()
    ->selectApi('overheid')
    ->setLicense('52BVL9')
    ->setEndpoints(['vehicle','fuel'])
    ->forceTranslation('en')
    ->format('json')
    ->fetch();
```
Rexponse:
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
There is a demo available to test this wrapper \
Two options to use the demo:
### .env
```php
RDW-API-DEMO=1
```
Add this value to .env
### config
Import the rwd-api config en set the value to 1
```php
rdw_api_demo => 1,
```
Demo: 0 = Off | 1 = On
### Demo url
```html
http://[domainname]/rdw-api/demo
```

## Filament
To use this wrapper in [Filament](https://filamentphp.com/) install the filament extension
```bash
composer require jdkweb/rdw-api-filament
```
See: [jdkweb/rdw-api-filament](https://github.com/jdkweb/rdw-api-filament)

