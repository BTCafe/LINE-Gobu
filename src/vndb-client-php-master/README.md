# vndb-client-php

A vndb.org client for PHP. It can be used both as a PHP library and as a stand-alone CLI tool

## Library usage:

```php
use VndbClient\Client;

$client = new Client();
$client->connect();
$client->login($username, $password);
$res = $client->sendCommand('dbstats'); // send raw command
$res = $client->getVisualNovelDataById(5);
$res = $client->getReleaseDataById(21446);
$res = $client->getProducerDataById(24);
$res = $client->getCharacterDataById(537);
```
All methods return a `VndbClient\Response` object, containing `->getType()` and `->getData()` methods to read the response.

## CLI usage

```
./bin/vndb-client vndb:getbyid your_username your_password vn 5
./bin/vndb-client vndb:getbyid your_username your_password release 5
./bin/vndb-client vndb:getbyid your_username your_password producer 5
./bin/vndb-client vndb:getbyid your_username your_password character 5
```

## The VNDB Protocol

For details on the workings of this API, and for a description of the returned data, please check:

        https://vndb.org/d11

## Composer / Packagist

The library is available on packagist.org as `joostfaassen/vndb-client-php`

## License

MIT
