Unit tests
----------

The unit tests are written using [PHPUnit](https://phpunit.de/)

The example introduces the basic conventions and steps for writing tests with PHPUnit:

   1. The tests for a class "Class" go into a class "ClassTest".
   2. And the unit test files are located in: `<classname>Test.php`.
   3. The tests are public methods that are named test*.

To run the unit tests with PHPUnit, use the following command in the project directory:

```bash
./vendor/bin/phpunit
```

TestDox

PHPUnit’s TestDox functionality looks at a test class and all the test method names and 
converts them from camel case (or snake_case) PHP names to sentences: testBalanceIsInitiallyZero() 
(or test_balance_is_initially_zero() becomes “Balance is initially zero”.

```bash
./vendor/bin/phpunit --testdox
```

Output test coverage reports

```bash
./vendor/bin/phpunit --coverage-html tests/coverage/
```

This will create a folder named reports in your project root.

See: https://phpunit.readthedocs.io/