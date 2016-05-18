# Connecty API gateway

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

PHP Gateway for different API clients

## Install

Via Composer

``` bash
$ composer require devTransitions/Connecty
```

## Usage

``` php
// Create the gateway object
$gw = Connecty::create('MyGateway');
// Create the request object
$my_request = $gw->myRequest($request_params);
$response = $my_request->send();
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Credits

- [All Contributors][link-contributors]

## License

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this work except in compliance with the License.
You may obtain a copy of the License in the [LICENSE File](LICENSE) file, or at:

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.


[ico-version]: https://img.shields.io/packagist/v/devTransitions/Connecty.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-Apache-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/devTransitions/Connecty/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/devTransitions/Connecty.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/devTransitions/Connecty.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/devTransitions/Connecty.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/devtransition/connecty
[link-travis]: https://travis-ci.org/devTransition/connecty
[link-scrutinizer]: https://scrutinizer-ci.com/g/devTransition/connecty/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/devTransition/connecty
[link-downloads]: https://packagist.org/packages/devTransition/connecty
[link-author]: https://github.com/devTransition
[link-contributors]: ../../contributors
