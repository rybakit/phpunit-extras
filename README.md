# PHPUnit Extras

[![Quality Assurance](https://github.com/rybakit/phpunit-extras/workflows/QA/badge.svg)](https://github.com/rybakit/phpunit-extras/actions?query=workflow%3AQA)

This repository contains functionality that makes it easy to create custom attributes and expectations
and use them with the [PHPUnit](https://phpunit.de/) framework.
In other words, with this library, your tests may look like this:

```php
use App\Tests\Attribute\RequiresMySqlServer;
use App\Tests\Attribute\Sql;
use PHPUnitExtras\TestCase;

#[RequiresMySqlServer('^5.6|^8.0')]
final class CacheableRepositoryTest extends TestCase
{
    #[Sql('DROP TABLE IF EXISTS %target_method%')]
    #[Sql('CREATE TABLE %target_method% (id INT UNSIGNED PRIMARY KEY)')]
    #[Sql('INSERT INTO %target_method% (id) VALUES (1)')]
    public function testFindByIdCachesResultSet() : void
    {
        $tableName = $this->resolvePlaceholders('%target_method%');
        $repository = $this->createRepository($tableName);

        $this->expectSelectStatementToBeExecutedOnce();

        $repository->findById(1);
        $repository->findById(1);
    }
}
```

`RequiresMySqlServer` and `Sql` are project-defined attributes, handled by a
custom requirement and processor. This example shows how they fit together:

| Example element | Role |
| --- | --- |
| `#[RequiresMySqlServer(...)]` | Custom MySQL 5.6 or 8.0 requirement |
| `#[Sql(...)]` | Repeatable SQL setup attribute |
| `%target_method%` | Placeholder resolved for this test method |
| `expectSelectStatementToBeExecutedOnce()` | Custom expectation checking the repository behavior |


## Table of contents

 * [Installation](#installation)
 * [Attributes](#attributes)
   * [Requirements](#requirements)
     * [Condition](#condition)
     * [Constant](#constant)
     * [Package](#package)
   * [Placeholders](#placeholders)
     * [TargetClass](#targetclass)
     * [TargetMethod](#targetmethod)
     * [TmpDir](#tmpdir)
   * [Creating your own attribute](#creating-your-own-attribute)
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
Composer 2 is required for the built-in package version lookup used by the `package` requirement.

*To use expression-based requirements and/or expectations:*
```bash
composer require --dev symfony/expression-language
```

To install everything in one command, run:
```bash
composer require --dev rybakit/phpunit-extras \
    composer/semver \
    symfony/expression-language
```


## Attributes

PHPUnit supports a variety of attributes, the full list of which can be found in the PHPUnit manual.
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
use PHPUnit\Framework\Attributes\Before;
use PHPUnitExtras\Attribute\Attributes;

final class MyTest extends TestCase
{
    use Attributes;

    #[Before]
    protected function processTestAttributesBeforeTest() : void
    {
        $this->processTestAttributes(static::class, $this->name());
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
        <bootstrap class="PHPUnitExtras\Attribute\AttributeExtension" />
    </extensions>
</phpunit>
```

You can then use attributes provided by the library or created by yourself.


### Requirements

The library comes with the following requirements:

#### Condition

*Format:*

```php
#[RequiresIf('<condition>')]
```

where `<condition>` is an arbitrary [expression](https://symfony.com/doc/current/components/expression_language.html#expression-syntax) 
that should be evaluated to the Boolean value of true. By default, you can refer to the following [superglobal variables](https://www.php.net/manual/en/language.variables.superglobals.php) 
in expressions: `cookie`, `env`, `get`, `files`, `post`, `request` and `server`.

*Example:*

```php
use PHPUnitExtras\Attribute\RequiresIf;

#[RequiresIf('server.AWS_ACCESS_KEY_ID')]
#[RequiresIf('server.AWS_SECRET_ACCESS_KEY')]
final class AwsS3AdapterTest extends TestCase
{
    // ...
}
```

You can also define your own variables in expressions:

```php
use PHPUnitExtras\Attribute\Requirement\IfRequirement;

// ...

$context = ['db' => $this->getDbConnection()];
$attributeProcessorBuilder->addRequirement(new IfRequirement($context));
```

For a custom requirement, define its attribute and a `Requirement` that handles that attribute class:

```php
namespace App\Tests;

use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\Requirement\Requirement;
use PHPUnitExtras\Attribute\Target;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class RequiresFeature implements ProcessableAttribute
{
    public function __construct(public readonly string $feature)
    {
    }
}

final class FeatureRequirement implements Requirement
{
    public function getAttributeClass() : string
    {
        return RequiresFeature::class;
    }

    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string
    {
        \assert($attribute instanceof RequiresFeature);
        $feature = $placeholderResolver->resolve($attribute->feature, $target);

        return FeatureFlags::isEnabled($feature)
            ? null
            : \sprintf('Feature "%s" is required', $feature);
    }
}
```

Register the requirement in your test case builder with
`$builder->addRequirement(new FeatureRequirement())`, then use `#[RequiresFeature('new-checkout')]`.
The requirement gets the typed attribute, test target, and placeholder resolver. Return `null` to run the test;
return a message to skip it.


#### Constant

*Format:* `#[RequiresConstant('<constant-name>')]`
where `<constant-name>` is the constant name.

*Example:*

```php
use PHPUnitExtras\Attribute\RequiresConstant;

#[RequiresConstant('Redis::SERIALIZER_MSGPACK')]
public function testSerializeToMessagePack() : void 
{
    // ...
}
```

#### Package

*Format:* `#[RequiresPackage('<package-name> [<version-constraint>]')]`
where `<package-name>` is the name of the required package and `<version-constraint>` is a composer-like version constraint.
For details on supported constraint formats, please refer to the Composer [documentation](https://getcomposer.org/doc/articles/versions.md#writing-version-constraints).

*Example:*

```php
use PHPUnitExtras\Attribute\RequiresPackage;

#[RequiresPackage('symfony/uid ^5.1')]
public function testUseUuidAsPrimaryKey() : void 
{
    // ...
}
```

### Placeholders

Placeholders allow you to include values that depend on the target test in string arguments
to custom attributes. A placeholder is any text surrounded by `%`. If it is unknown, an error is thrown.

Below is a list of the placeholders available by default:

#### TargetClass

*Example:*

```php
namespace App\Tests;

#[Example('%target_class%')]
#[Example('%target_class_full%')]
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
#[Example('%target_method%')]
#[Example('%target_method_full%')]
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
#[Log('%tmp_dir%/%target_class%.%target_method%.log testing Foobar')]
public function testFoobar() : void 
{
    // ...
}
```

In the above example, `%tmp_dir%` will be substituted with the result 
of the [sys_get_temp_dir()](https://www.php.net/manual/en/function.sys-get-temp-dir.php) call.


### Creating your own attribute

As an example, let's implement a `#[Sql(...)]` attribute. First, create a processor class
with the name `SqlProcessor`:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Attribute\Processor\Processor;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\Target;

final class SqlProcessor implements Processor
{
    private $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAttributeClasses() : array
    {
        return [Sql::class];
    }

    public function process(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : void
    {
        \assert($attribute instanceof Sql);
        $sql = $placeholderResolver->resolve($attribute->sql, $target);
        $this->conn->exec($sql);
    }
}
```

The processor declares which attribute class it handles, resolves placeholders in its SQL, and calls `PDO::exec()`. An attribute such as `#[Sql('TRUNCATE TABLE foo')]`
is equivalent to `$this->conn->exec('TRUNCATE TABLE foo')`.

Next, create the attribute class. Its class is the key the processor registers for:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Attribute\ProcessableAttribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Sql implements ProcessableAttribute
{
    public function __construct(public readonly string $sql)
    {
    }
}
```

The processor can use the placeholder resolver it receives to replace `%table_name%`
with a unique table name for a specific test method or/and class. That will allow using dynamic table names
instead of hardcoded ones:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\Target;

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

The only thing left is to register our new processor:

```php
namespace App\Tests;

use App\Tests\PhpUnit\SqlProcessor;
use App\Tests\PhpUnit\TableNameResolver;
use PHPUnitExtras\Attribute\AttributeProcessorBuilder;
use PHPUnitExtras\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createAttributeProcessorBuilder() : AttributeProcessorBuilder
    {
        return parent::createAttributeProcessorBuilder()
            ->addProcessor(new SqlProcessor($this->getConnection()))
            ->addPlaceholderResolver(new TableNameResolver());
    }

    protected function getConnection() : \PDO
    {
        // TODO: Implement getConnection() method.
    }
}
```

After that all classes inherited from `App\Tests\TestCase` will be able to use `#[Sql(...)]`.

If no processor is registered for an attribute class, the library throws an `InvalidAttributeException`.

As mentioned [earlier](#registering-an-extension), another way to register attributes is through PHPUnit extensions.
As in the example above, you need to override the `createAttributeProcessorBuilder()` method,
but now for the `AttributeExtension` class:

```php
namespace App\Tests\PhpUnit;

use PHPUnitExtras\Attribute\AttributeExtension as BaseAttributeExtension;
use PHPUnitExtras\Attribute\AttributeProcessorBuilder;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class AttributeExtension extends BaseAttributeExtension
{
    private string $dsn = 'mysql:host=localhost;dbname=test';
    private ?\PDO $conn = null;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters) : void
    {
        if ($parameters->has('dsn')) {
            $this->dsn = $parameters->get('dsn');
        }

        parent::bootstrap($configuration, $facade, $parameters);
    }

    protected function createAttributeProcessorBuilder() : AttributeProcessorBuilder
    {
        return parent::createAttributeProcessorBuilder()
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
	    xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
	    bootstrap="vendor/autoload.php"
	>
    <!-- ... -->

	    <extensions>
	        <bootstrap class="App\Tests\PhpUnit\AttributeExtension" />
	    </extensions>
	</phpunit>
	```

To change the default connection settings, pass the new DSN value as a parameter:

```xml
<bootstrap class="App\Tests\PhpUnit\AttributeExtension">
    <parameter name="dsn" value="sqlite::memory:" />
</bootstrap>
```

> *For more information on configuring extensions, please refer to the PHPUnit manual.*



## Expectations

PHPUnit has a number of methods to set up expectations for code executed under test. Probably the most commonly used
are the `expectException*` and `expectOutput*` family of methods.
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

As an example let's implement the `expectSelectStatementToBeExecutedOnce()` method mentioned above.
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
