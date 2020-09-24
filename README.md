# PHPUnit Extras

![Quality Assurance](https://github.com/rybakit/phpunit-extras/workflows/QA/badge.svg)

This repository contains functionality that makes it easy to create and integrate
your own annotations and expectations into the [PHPUnit](https://phpunit.de/) framework.
In other words, with this library, your tests may look like this:

![https://raw.githubusercontent.com/rybakit/phpunit-extras/media/phpunit-extras-example.png](../media/phpunit-extras-example.png?raw=true)

where:
1. `MySqlServer ^5.6|^8.0` is a custom requirement
2. `@sql` is a custom annotation
3. `%target_method%` is an annotation placeholder
4. `expectSelectStatementToBeExecutedOnce()` is a custom expectation.


## Table of contents

 * [Installation](#installation)
 * [Annotations](#annotations)
   * [Processors](#processors)
     * [Requires](#requires)
   * [Requirements](#requirements)
     * [Condition](#condition)
     * [Constant](#constant)
     * [Package](#package)
   * [Placeholders](#placeholders)
     * [TargetClass](#targetclass)
     * [TargetMethod](#targetmethod)
     * [TmpDir](#tmpdir)
   * [Creating your own annotation](#creating-your-own-annotation)
 * [Expectations](#expectations)
   * [Usage example](#usage-example)
   * [Advanced example](#advanced-example)
 * [Testing](#testing)
 * [License](#license)


## Installation

```bash
composer require --dev rybakit/phpunit-extras
```

In addition, depending on which functionality you will use, you may need to install the following packages:

*To use version-related requirements:*
```bash
composer require --dev composer/semver
```

*To use the "package" requirement:*
```bash
composer require --dev ocramius/package-versions
```

*To use expression-based requirements and/or expectations:*
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


## Annotations

PHPUnit supports a variety of annotations, the full list of which can be found [here](https://phpunit.readthedocs.io/en/latest/annotations.html).
With this library, you can easily expand this list by using one of the following options:

#### Inheriting from the base test case class

```php
use PHPUnitExtras\TestCase;

final class MyTest extends TestCase
{
    // ...
}
```

#### Using a trait

```php
use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\Annotations;

final class MyTest extends TestCase
{
    use Annotations;

    protected function setUp() : void
    {
        $this->processAnnotations(static::class, $this->getName(false) ?? '');
    }

    // ...
}
```
 
#### Registering an extension

```xml
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
>
    <!-- ... -->

    <extensions>
        <extension class="PHPUnitExtras\Annotation\AnnotationExtension" />
    </extensions>
</phpunit>
```

You can then use annotations provided by the library or created by yourself.


### Processors

The annotation processor is a class that implements the behavior of your annotation.

> *The library is currently shipped with only the "Required" processor.
> For inspiration and more examples of annotation processors take a look
> at the [tarantool/phpunit-extras](https://github.com/tarantool-php/phpunit-extras#processors) package.*


#### Requires

This processor extends the standard PHPUnit [@requires](https://phpunit.readthedocs.io/en/latest/annotations.html#requires) 
annotation by allowing you to add your own requirements.

### Requirements

The library comes with the following requirements:

#### Condition

*Format:*

```
@requires condition <condition>
```

where `<condition>` is an arbitrary [expression](https://symfony.com/doc/current/components/expression_language.html#expression-syntax) that should be evaluated to a Boolean value.
By default, you can refer to the following [superglobal variables](https://www.php.net/manual/en/language.variables.superglobals.php) in expressions:
`cookie`, `env`, `get`, `files`, `post`, `request` and `server`.

*Example:*

```php
/**
 * @requires condition server.AWS_ACCESS_KEY_ID
 * @requires condition server.AWS_SECRET_ACCESS_KEY
 */
final class AwsS3AdapterTest extends TestCase
{
    // ...
}
```

You can also define your own variables in expressions:

```php
use PHPUnitExtras\Annotation\Requirement\ConditionRequirement;

// ...

$context = ['db' => $this->getDbConnection()];
$annotationProcessorBuilder->addRequirement(new ConditionRequirement($context));
```


#### Constant

*Format:*

```
@requires constant <constant-name>
```
where `<constant-name>` is the constant name.

*Example:*

```php
/**
 * @requires constant Redis::SERIALIZER_MSGPACK
 */
public function testSerializeToMessagePack() : void 
{
    // ...
}
```

#### Package

*Format:*

```
@requires package <package-name> [<version-constraint>]
```
where `<package-name>` is the name of the required package and `<version-constraint>` is a composer-like version constraint.
For details on supported constraint formats, please refer to the Composer [documentation](https://getcomposer.org/doc/articles/versions.md#writing-version-constraints).

*Example:*

```php
/**
 * @requires package symfony/uid ^5.1
 */
public function testUseUuidAsPrimaryKey() : void 
{
    // ...
}
```

### Placeholders

Placeholders allow you to dynamically include specific values in your annotations.
The placeholder is any text surrounded by the symbol `%`. An annotation can have
any number of placeholders. If the placeholder is unknown, an error will be thrown.

Below is a list of the placeholders available by default:

#### TargetClass

*Example:*

```php
namespace App\Tests;

/**
 * @example %target_class%
 * @example %target_class_full%
 */
final class FoobarTest extends TestCase
{
    // ...
}
```

In the above example, `%target_class%` will be substituted with `FoobarTest` 
and `%target_class_full%` will be substituted with `App\Tests\FoobarTest`.


#### TargetMethod

*Example:*

```php
/**
 * @example %target_method%
 * @example %target_method_full%
 */
public function testFoobar() : void 
{
    // ...
}
```

In the above example, `%target_method%` will be substituted with `Foobar` 
and `%target_method_full%` will be substituted with `testFoobar`.


#### TmpDir

*Example:*

```php
/**
 * @log %tmp_dir%/%target_class%.%target_method%.log testing Foobar
 */
public function testFoobar() : void 
{
    // ...
}
```

In the above example, `%tmp_dir%` will be substituted with the result 
of the [sys_get_temp_dir()](https://www.php.net/manual/en/function.sys-get-temp-dir.php) call.


### Creating your own annotation

As an example, let's implement the annotation `@sql` from the picture above. To do this, create a processor class 
with the name `SqlProcessor`:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Annotation\Processor\Processor;

final class SqlProcessor implements Processor
{
    private $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getName() : string
    {
        return 'sql';
    }

    public function process(string $value) : void
    {
        $this->conn->exec($value);
    }
}
```

That's it. All this processor does is register the `@sql` tag and call `PDO::exec()`, passing everything
that comes after the tag as an argument. In other words, an annotation such as `@sql TRUNCATE TABLE foo` 
is equivalent to `$this->conn->exec('TRUNCATE TABLE foo')`.

Also, just for the purpose of example, let's create a placeholder resolver that replaces `%table_name%`
with a unique table name for a specific test method or/and class. That will allow using dynamic table names
instead of hardcoded ones:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Annotation\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Annotation\Target;

final class TableNameResolver implements PlaceholderResolver
{
    public function getName() : string
    {
        return 'table_name';
    }

    /**
     * Replaces all occurrences of "%table_name%" with 
     * "table_<short-class-name>[_<short-method-name>]".
     */
    public function resolve(string $value, Target $target) : string
    {
        $tableName = 'table_'.$target->getClassShortName();
        if ($target->isOnMethod()) {
            $tableName .= '_'.$target->getMethodShortName();
        }

        return strtr($value, ['%table_name%' => $tableName]);
    }
}
```

The only thing left is to register our new annotation:

```php
namespace App\Tests;

use App\Tests\PhpUnit\SqlProcessor;
use App\Tests\PhpUnit\TableNameResolver;
use PHPUnitExtras\Annotation\AnnotationProcessorBuilder;
use PHPUnitExtras\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        return parent::createAnnotationProcessorBuilder()
            ->addProcessor(new SqlProcessor($this->getConnection()))
            ->addPlaceholderResolver(new TableNameResolver());
    }

    protected function getConnection() : \PDO
    {
        // TODO: Implement getConnection() method.
    }
}
```

After that all classes inherited from `App\Tests\TestCase` will be able to use the tag `@sql`.

> *Don't worry if you forgot to inherit from the base class where your annotations are registered 
> or if you made a mistake in the annotation name, the library will warn you about an unknown annotation.*

As mentioned [earlier](#registering-an-extension), another way to register annotations is through PHPUnit extensions.
As in the example above, you need to override the `createAnnotationProcessorBuilder()` method,
but now for the `AnnotationExtension` class:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Annotation\AnnotationExtension as BaseAnnotationExtension;
use PHPUnitExtras\Annotation\AnnotationProcessorBuilder;

class AnnotationExtension extends BaseAnnotationExtension
{
    private $dsn;
    private $conn;

    public function __construct($dsn = 'mysql:host=localhost;dbname=test')
    {
        $this->dsn = $dsn;
    }

    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        return parent::createAnnotationProcessorBuilder()
            ->addProcessor(new SqlProcessor($this->getConnection()))
            ->addPlaceholderResolver(new TableNameResolver());
    }

    protected function getConnection() : \PDO
    {
        return $this->conn ?? $this->conn = new \PDO($this->dsn);
    }
}
```
After that, register your extension:

```xml
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
>
    <!-- ... -->

    <extensions>
        <extension class="App\Tests\PhpUnit\AnnotationExtension" />
    </extensions>
</phpunit>
```

To change the default connection settings, pass the new DSN value as an argument:

```xml
<extension class="App\Tests\PhpUnit\AnnotationExtension">
    <arguments>
        <string>sqlite::memory:</string>
    </arguments>
</extension>
```

> *For more information on configuring extensions, please follow this [link](https://phpunit.readthedocs.io/en/latest/extending-phpunit.html#configuring-extensions).*



## Expectations

PHPUnit has a number of methods to set up expectations for code executed under test. Probably the most commonly used
are the [expectException*](https://phpunit.readthedocs.io/en/latest/writing-tests-for-phpunit.html#testing-exceptions)
and [expectOutput*](https://phpunit.readthedocs.io/en/latest/writing-tests-for-phpunit.html#testing-output) family of methods.
The library provides the possibility to create your own expectations with ease.


### Usage example

As an example, let's create an expectation, which verifies that the code under test creates a file.
Let's call it `FileCreatedExpectation`:

```php
namespace App\Tests\PhpUnit;

use PHPUnit\Framework\Assert;
use PHPUnitExtras\Expectation\Expectation;

final class FileCreatedExpectation implements Expectation
{
    private $filename;

    public function __construct(string $filename)
    {
        Assert::assertFileDoesNotExist($filename);
        $this->filename = $filename;
    }

    public function verify() : void
    {
        Assert::assertFileExists($this->filename);
    }
}
```

Now, to be able to use this expectation, inherit your test case class from `PHPUnitExtras\TestCase`
(recommended) or include the `PHPUnitExtras\Expectation\Expectations` trait:

```php
use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Expectation\Expectations;

final class MyTest extends TestCase
{
    use Expectations;

    protected function tearDown() : void
    {
        $this->verifyExpectations();
    }

    // ...
}
```
After that, call your expectation as shown below:

```php
public function testDumpPdfToFile() : void
{
    $filename = sprintf('%s/foobar.pdf', sys_get_temp_dir());

    $this->expect(new FileCreatedExpectation($filename));
    $this->generator->dump($filename);
}
```

For convenience, you can put this statement in a separate method and group your expectations into a trait:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Expectation\Expectation;

trait FileExpectations
{
    public function expectFileToBeCreated(string $filename) : void
    {
        $this->expect(new FileCreatedExpectation($filename));
    }

    // ...

    abstract protected function expect(Expectation $expectation) : void;
}
```

### Advanced example

Thanks to the Symfony [ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html) component, 
you can create expectations with more complex verification rules without much hassle.

As an example let's implement the `expectSelectStatementToBeExecutedOnce()` method from the picture above.
To do this, create an expression context that will be responsible for collecting the necessary statistics 
on `SELECT` statement calls:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Expectation\ExpressionContext;

final class SelectStatementCountContext implements ExpressionContext
{
    private $conn;
    private $expression;
    private $initialValue;
    private $finalValue;

    private function __construct(\PDO $conn, string $expression)
    {
        $this->conn = $conn;
        $this->expression = $expression;
        $this->initialValue = $this->getValue();
    }

    public static function exactly(\PDO $conn, int $count) : self
    {
        return new self($conn, "new_count === old_count + $count");
    }

    public static function atLeast(\PDO $conn, int $count) : self
    {
        return new self($conn, "new_count >= old_count + $count");
    }

    public static function atMost(\PDO $conn, int $count) : self
    {
        return new self($conn, "new_count <= old_count + $count");
    }

    public function getExpression() : string
    {
        return $this->expression;
    }

    public function getValues() : array
    {
        if (null === $this->finalValue) {
            $this->finalValue = $this->getValue();
        }

        return [
            'old_count' => $this->initialValue,
            'new_count' => $this->finalValue,
        ];
    }

    private function getValue() : int
    {
        $stmt = $this->conn->query("SHOW GLOBAL STATUS LIKE 'Com_select'");
        $stmt->execute();

        return (int) $stmt->fetchColumn(1);
    }
}
```

Now create a trait which holds all our statement expectations:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Expectation\Expectation;
use PHPUnitExtras\Expectation\ExpressionExpectation;

trait SelectStatementExpectations
{
    public function expectSelectStatementToBeExecuted(int $count) : void
    {
        $context = SelectStatementCountContext::exactly($this->getConnection(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSelectStatementToBeExecutedOnce() : void
    {
        $this->expectSelectStatementToBeExecuted(1);
    }

    // ...

    abstract protected function expect(Expectation $expectation) : void;
    abstract protected function getConnection() : \PDO;
}
```

And finally, include that trait in your test case class:

```php
use App\Tests\PhpUnit\SelectStatementExpectations;
use PHPUnitExtras\TestCase;

final class CacheableRepositoryTest extends TestCase
{
    use SelectStatementExpectations;

    public function testFindByIdCachesResultSet() : void
    {
        $repository = $this->createRepository();

        $this->expectSelectStatementToBeExecutedOnce();

        $repository->findById(1);
        $repository->findById(1);
    }

    // ...

    protected function getConnection() : \PDO
    {
        // TODO: Implement getConnection() method.
    }
}
```

> *For inspiration and more examples of expectations take a look
> at the [tarantool/phpunit-extras](https://github.com/tarantool-php/phpunit-extras#expectations) package.*


## Testing

Before running tests, the development dependencies must be installed:

```bash
composer install
```

Then, to run all the tests:

```bash
vendor/bin/phpunit
vendor/bin/phpunit -c phpunit-extension.xml
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
