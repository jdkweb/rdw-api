# RDW API

Laravel wrapper for the Dutch open mobility data. Basic Vehicle Registration All non-sensitive data of the Dutch vehicle fleet. 

Laravel application to get vehicle information from [opendata.rdw.nl](https://opendata.rdw.nl) or [overheid.io](https://overheid.io). \
This wrapper can be extended to be used in Filament: [rdw-api-filament](https://github.com/jdkweb/rdw-api-filament)

## Table of contents

- [Installation](#installation)
- [Translation](#translation)
- [Usage](#usage)
  - [Request](#request)
  - [Response](#response)
- [Demo](#demo)
- [Change Default API](#api)
- [Extension for Filament](#filament)


## Installation
Requires PHP 8.1 and Laravel 10 or higher 

Install the package via composer:
```bash
composer require jdkweb/rdw-api
```
If needed you can publish the config
```bash
php artisan vendor:publish --provider="Jdkweb\RdwApi\RdwServiceProvider" --tag="config"
```
For changing options see: [change API](#api) and [Demo](#demo)
## Translation
If changes are needed you can publish the translation files
```bash
# published in: trans/vendor/jdkweb/rdw-api
php artisan vendor:publish --provider="Jdkweb\RdwApi\RdwServiceProvider" --tag="lang"
``` 
Translations available:
- [Dutch (nl)](https://github.com/jdkweb/rdw-api/tree/main/lang/nl)
- [English (en)](https://github.com/jdkweb/rdw-api/tree/main/lang/en)

# Usage
- [Request: RdwApiRequest](#request)
- [Response RdwApiResponse](#response)
## Request
### Basic usage
```php
use Jdkweb\RdwApi\Controllers\RdwApiRequest;
...
$result = (object) RdwApiRequest::make()
    ->setLicenseplate('AB-895-P')
    ->fetch();
```
- Request to the active API (default: opendata.rdw.nl) 
- All RDW endpoints are selected
- [RdwApiResponse](#RdwApiResponse) object is returned
### All options used
```php
use Jdkweb\RdwApi\Controllers\RdwApiRequest;
use Jdkweb\RdwApi\Enums\OutputFormat;
use Jdkweb\RdwApi\Enums\Endpoints;
...
$result = RdwApiRequest::make()
    ->setAPI(0)
    ->setLicenseplate('AB-895-P')
    ->setEndpoints(Endpoints::cases())
    ->setOutputformat(OutputFormat::JSON)
    ->setLanguage('en')
    ->fetch(true);
```
### Options
#### Select other API than default
```php
->setApi(int|string) // 0 | opendata | 1 | overheid    
```
Overwrite the config settings 
- 0 or 'opendata' for using the RDW API opendata.rdw.nl **[default]**
- 1 or 'overheidio' for using the overheid.io API

#### Set Licenseplate
```php
->setLicense('AB-895-P')
```
With or without hyphen-minus

#### Select endpoints for request 
```php
use \Jdkweb\RdwApi\Enums\Endpoints;
...
->setEndpoints(array)

# examples:

    // Call to all endpoints
    ->setEndpoints(Endpoints::cases())
    
    // Specific selection
    ->setEndpoints([
        Endpoints::VEHICLE,
        Endpoints::FUEL
    ])
    
    // Use enum names, case insensitive 
    ->setEndpoints([
        'vehicle',
        'fuel'
    ])
```
Available endpoints (not case sensitive):
- Endpoints::VEHICLE | vehicle
- Endpoints::VEHICLE_CLASS |vehicle_class
- Endpoints::FUEL | fuel
- Endpoints::BODYWORK | bodywork
- Endpoints::BODYWORK_SPECIFIC | bodywork_specific
- Endpoints::AXLES | axles 
- Endpoints::cases() **[default]**

#### Format of the response output
```php
use \Jdkweb\RdwApi\Enums\OutputFormat;
...
->setOuputformat(string|OutputFormat)

# examples
    
    // Enum
    ->setOuputformat(OutputFormat::JSON)
    
    // name, case insensitive
    ->setOuputformat('json')
```
- OutputFormat::ARRAY | array **[default]**
- OutputFormat::JSON | json
- OutputFormat::XML | xml

by using this method the response contains a formated output. see [RdwApiResponse](#RdwApiResponse)  

#### Set output language
```php
->setLanguage(string)
```
Force output language, so form can be English and RDW response in Dutch. \
Available:
  - nl 
  - en

#### Send the request
```php
->fetch(?bool $return = null) 
```
[RdwApiResponse](#RdwApiResponse) object will be returned \
When boolean isset and true RdwApiRequest object will be returned 

## Response
Response data form the RDW API request in $result:
```php
Jdkweb\RdwApi\Controllers\RdwApiResponse {#2800 ▼
  +response: array:2 [▶]    // API response
  +request: {#3036 ▶}       // Request vars
  +output: array:2 [▶]      // Formated output when setOutputFormat is used
  +status: true
}
```
### Response methods
#### Format for response data
```php
$result->toArray()
```
```php
$result->toJson()
```

```php
$result->toXml(bool $pretty)
```
Boolean to make xml readable

```php
$result->toObject()
```
#### quickSearch method
Get specific values form response data, always use Dutch key for the value.
```php
$result->quickSearch(string $keyname) // Keynames are Dutch

# examples:

    // Brand:  TOYOTA
    $result->quickSearch('merk')
    
    // Vehicle type: Personenauto            
    $result->quickSearch('voertuigsoort')
    
    // Track width firste axle: 147  
    $result->quickSearch('1.spoorbreedte')
    
    // First fuel description, hybrid have two
    $set('brandstof_omschrijving', $result->quickSearch('1.brandstof_omschrijving'));
    
    // Second axle legally permitted maximum axle load
    $set('aslast', $result->quickSearch('2.wettelijk_toegestane_maximum_aslast'));
```
- Use dutch key for the value
- When more results:\
Axles: axle_location_code for first and second: 
  - 1.plaatscode_as 
  - 2.plaatscode_as

### Example request
Request:
```php
$result = RdwApiRequest::make()
    ->setLicenseplate('52BVL9')
    ->setEndpoints(Endpoints::cases())
    ->setOutputformat(OutputFormat::JSON)
    ->setLanguage('en')
    ->fetch(true);
```
the method setOutputformat creates a prepared output else output is empty

Response:
```php
$result->output
# OR
$result->toJson()
```
$result is an RdwApiResponse object

Json output:
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
1. ### .env
   ```php
    RDW_API_DEMO=1
   ```
   Add this value to .env
2. ### config
   Import the rwd-api config en set the value to 1 ([Installation](#installation))
   ```php
    rdw_api_demo => 1,
   ```
   Demo: 0 = Off | 1 = On

### Demo url
```html
http://[domainname]/rdw-api/demo
```

## API
Changing Default API\
- 0: [opendata.rdw.nl](https://opendata.rdw.nl) 
- 1: [overheid.io](https://overheid.io)

Use setApi method in request
```php
->setApi(int $apiKey)
```
Or import the rwd-api config ([Installation](#installation)) \
And set 'rdw_api_use' to the correct value 

> To use https://overheid.io a token is needed \  
Place the token in the config: 'rdw_api_key'.

## Filament
To use this wrapper in [Filament](https://filamentphp.com/) install the filament extension
```bash
composer require jdkweb/rdw-api-filament
```
Github: [jdkweb/rdw-api-filament](https://github.com/jdkweb/rdw-api-filament) \
packagist: [jdkweb/rdw-api-filament](https://packagist.org/packages/jdkweb/rdw-api-filament)

