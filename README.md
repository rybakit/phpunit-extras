# PHPUnit Extras

This repository contains functionality that makes it easy to create and integrate
your own annotations and expectations into the [PHPUnit](https://phpunit.de/) framework. 


## Installation

```bash
composer require --dev rybakit/phpunit-extras
```

In addition, depending on which functional you will use, you may need to install the following packages:

*To use version-related requirements:*
```bash
composer require --dev composer/semver
```

*To use the "package" requirement:*
```bash
composer require --dev ocramius/package-versions
```

*To use expectations based on expressions:*
```bash
composer require --dev symfony/expression-language
```

To install everything in one command, run:
```bash
composer require --dev rybakit/phpunit-extras \
    composer/semver \
    ocramius/package-versions \
    symfony/expression-language
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
