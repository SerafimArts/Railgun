<p align="center">
    <a href="https://railt.org"><img src="https://railt.org/images/logo-dark.svg" width="200" alt="Railt" /></a>
</p>
<p align="center">
    <a href="https://travis-ci.org/railt/railt"><img src="https://travis-ci.org/railt/railt.svg?branch=1.4.x" alt="Travis CI" /></a>
    <a href="https://codeclimate.com/github/railt/railt/test_coverage"><img src="https://api.codeclimate.com/v1/badges/07b06e5fc97ecbfaafb6/test_coverage" /></a>
    <a href="https://codeclimate.com/github/railt/railt/maintainability"><img src="https://api.codeclimate.com/v1/badges/07b06e5fc97ecbfaafb6/maintainability" /></a>
    <a href="https://packagist.org/packages/railt/railt"><img src="https://img.shields.io/badge/PHP-7.1+-brightgreen.svg" alt="PHP 7.1+"></a>
</p>
<p align="center">
    <a href="https://railt.org"><img src="https://img.shields.io/badge/official-site-blue.svg" alt="railt.org"></a>
    <a href="https://discord.gg/ND7SpD4"><img src="https://img.shields.io/badge/discord-chat-blue.svg" alt="Discord"></a>
    <a href="https://packagist.org/packages/railt/railt"><img src="https://poser.pugx.org/railt/railt/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/railt/railt"><img src="https://poser.pugx.org/railt/railt/downloads" alt="Total Downloads"></a>
    <a href="https://raw.githubusercontent.com/railt/railt/master/LICENSE.md"><img src="https://poser.pugx.org/railt/railt/license" alt="License MIT"></a>
</p>

## Introduction

This is a pure PHP realization of the **GraphQL** protocol based on the 
[webonyx/graphql-php](https://github.com/webonyx/graphql-php#fields) 
implementations of the official GraphQL Specification 
located on [Facebook GitHub](http://facebook.github.io/graphql/).

The difference from the above implementations is that the Railt provides the 
ability to describe the types and extended control of their behavior, 
thereby solving problems such as:

- Simplifying type declarations
- Types reusage
- Significant simplification of the construction of the API
- More flexible integration with frameworks (e.g. 
[Laravel](https://github.com/laravel/framework) or [Symfony](https://github.com/symfony/symfony))
- And others

## Installation

- Add into your `composer.json`:
```json
{
    "scripts": {
        "post-autoload-dump": [
            "Railt\\Discovery\\Manifest::discover"
        ]
    }
}
```
- `composer require railt/railt`

## Quick start

The documentation is in the process of writing, therefore, 
in order to understand how it works, a quick start.

### `schema.graphqls`

This is our main GraphQL application schema.

```graphql
schema {
    query: Example
}

type Example {
    say(message: String = "Hello"): String! 
        @route(action: "ExampleController@say")
}
```

### `ExampleController.php`

The GraphQL request `query { say }` handler indicated in the `@route` directive

```php
class ExampleController
{
    public function say(string $message): string
    {
        return $message;
    }
}
```

### `index.php`

This is the main file that handles all requests to the application. 
With the same success this role can be performed by any controller 
in the MVP (MVC with passive models) application, for example on 
the basis of a Symfony or Laravel.

```php
<?php
use Railt\Io\File;
use Railt\Http\Factory;
use Railt\Foundation\Application;
use Railt\Http\Provider\GlobalsProvider;


$loader = require __DIR__ . '/vendor/autoload.php';

//
// Creating a new Application
//
$app = new Application();

//
// Create a connection
//
$connection = $app->connect(File::fromPathname(__DIR__ . '/schema.graphqls'));

//
// Processing of HTTP Request
//
$responses = $connection->request(Factory::create(new GlobalsProvider()));

//
// And send the HTTP Response
//
$responses->send();
```

**Response:**

```json
{
    "say": "Something is awesome!"
}
```

## Learning Railt

> This documentation can contain NOT RELEVANT information and currently in progress.

- [Russian](https://ru.railt.org)
- [English](https://en.railt.org)

## Contributing

Thank you for considering contributing to the Railt Framework! 
The contribution guide can be found in the [documentation](https://railt.org/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Railt, please send an e-mail to maintainer 
at nesk@xakep.ru. All security vulnerabilities will be promptly addressed.

## License

The Railt Framework is open-sourced software licensed under 
the [MIT license](https://opensource.org/licenses/MIT).

## Development Status

| Packages                                                               | Release                                                                                                                                  | CI Status                                                                                                                     |
|------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| [`railt/railt`](https://github.com/railt/railt)                        | [![Latest Stable Version](https://poser.pugx.org/railt/railt/version)](https://packagist.org/packages/railt/railt)                       | [![Travis CI](https://travis-ci.org/railt/railt.svg?branch=1.4.x)](https://travis-ci.org/railt/railt)                        |
| [`railt/compiler`](https://github.com/railt/compiler)                  | [![Latest Stable Version](https://poser.pugx.org/railt/compiler/version)](https://packagist.org/packages/railt/compiler)                 | [![Travis CI](https://travis-ci.org/railt/compiler.svg?branch=1.4.x)](https://travis-ci.org/railt/compiler)                  |
| [`railt/discovery`](https://github.com/railt/discovery)                | [![Latest Stable Version](https://poser.pugx.org/railt/discovery/version)](https://packagist.org/packages/railt/discovery)               | [![Travis CI](https://travis-ci.org/railt/discovery.svg?branch=1.4.x)](https://travis-ci.org/railt/discovery)                |
| [`railt/laravel-provider`](https://github.com/railt/laravel-provider)  | [![Latest Stable Version](https://poser.pugx.org/railt/laravel-provider/version)](https://packagist.org/packages/railt/laravel-provider) | [![Travis CI](https://travis-ci.org/railt/laravel-provider.svg?branch=1.4.x)](https://travis-ci.org/railt/laravel-provider)  |
| [`railt/symfony-bundle`](https://github.com/railt/symfony-bundle)      | [![Latest Stable Version](https://poser.pugx.org/railt/symfony-bundle/version)](https://packagist.org/packages/railt/symfony-bundle)     | [![Travis CI](https://travis-ci.org/railt/symfony-bundle.svg?branch=1.4.x)](https://travis-ci.org/railt/symfony-bundle)      |

| Components                                                             | Release                                                                                                                                  | CI Status                                                                                                                     |
|------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| [`railt/container`](https://github.com/railt/container)                | [![Latest Stable Version](https://poser.pugx.org/railt/container/version)](https://packagist.org/packages/railt/container)               | [![Travis CI](https://travis-ci.org/railt/container.svg?branch=1.4.x)](https://travis-ci.org/railt/container)                |
| [`railt/http`](https://github.com/railt/http)                          | [![Latest Stable Version](https://poser.pugx.org/railt/http/version)](https://packagist.org/packages/railt/http)                         | [![Travis CI](https://travis-ci.org/railt/http.svg?branch=1.4.x)](https://travis-ci.org/railt/http)                          |
| [`railt/io`](https://github.com/railt/io)                              | [![Latest Stable Version](https://poser.pugx.org/railt/io/version)](https://packagist.org/packages/railt/io)                             | [![Travis CI](https://travis-ci.org/railt/io.svg?branch=1.4.x)](https://travis-ci.org/railt/io)                              |
| [`railt/sdl`](https://github.com/railt/sdl)                            | [![Latest Stable Version](https://poser.pugx.org/railt/sdl/version)](https://packagist.org/packages/railt/sdl)                           | [![Travis CI](https://travis-ci.org/railt/sdl.svg?branch=1.4.x)](https://travis-ci.org/railt/sdl)                            |
| [`railt/storage`](https://github.com/railt/storage)                    | [![Latest Stable Version](https://poser.pugx.org/railt/storage/version)](https://packagist.org/packages/railt/storage)                   | [![Travis CI](https://travis-ci.org/railt/storage.svg?branch=1.4.x)](https://travis-ci.org/railt/storage)                    |
| [`railt/lexer`](https://github.com/railt/lexer)                        | [![Latest Stable Version](https://poser.pugx.org/railt/lexer/version)](https://packagist.org/packages/railt/lexer)                       | [![Travis CI](https://travis-ci.org/railt/lexer.svg?branch=1.4.x)](https://travis-ci.org/railt/lexer)                        |
| [`railt/parser`](https://github.com/railt/parser)                      | [![Latest Stable Version](https://poser.pugx.org/railt/parser/version)](https://packagist.org/packages/railt/parser)                     | [![Travis CI](https://travis-ci.org/railt/parser.svg?branch=1.4.x)](https://travis-ci.org/railt/parser)                      |
| [`railt/json`](https://github.com/railt/json)                          | [![Latest Stable Version](https://poser.pugx.org/railt/json/version)](https://packagist.org/packages/railt/json)                         | [![Travis CI](https://travis-ci.org/railt/json.svg?branch=1.4.x)](https://travis-ci.org/railt/json)                          |
| [`railt/dumper`](https://github.com/railt/dumper)                      | [![Latest Stable Version](https://poser.pugx.org/railt/dumper/version)](https://packagist.org/packages/railt/dumper)                     | [![Travis CI](https://travis-ci.org/railt/dumper.svg?branch=1.4.x)](https://travis-ci.org/railt/dumper)                      |



## Supported By

<p align="center">
    <a href="https://www.jetbrains.com/?from=Railt" target="_blank"><img src="https://habrastorage.org/webt/oc/-2/ek/oc-2eklcyr_ncszrzytmlu8_vky.png" alt="JetBrains" /></a>
    <a href="https://rambler-co.ru/" target="_blank"><img src="https://habrastorage.org/webt/wp/wu/wp/wpwuwpqpkskjfs0yjdjry5jvoog.png" alt="Rambler&Co" /></a>
</p>
