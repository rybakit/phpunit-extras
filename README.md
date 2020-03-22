# PHPUnit Extras

![Continuous Integration](https://github.com/rybakit/phpunit-extras/workflows/Continuous%20Integration/badge.svg)

This repository contains functionality that makes it easy to create and integrate
your own annotations and expectations into the [PHPUnit](https://phpunit.de/) framework.
In other words, with this library, your tests may look like this:

![https://raw.githubusercontent.com/rybakit/phpunit-extras/media/phpunit-extras-example.png](../media/phpunit-extras-example.png?raw=true)

where:
1. `MySqlServer ^5.6|^8.0` is a custom requirement
2. `@sql` is a custom annotation
3. `%target_method%` is an annotation placeholder
4. `expectSelectStatementToBeExecutedOnce()` is a custom expectation.



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
