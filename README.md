Doctrine Copy DB Bundle
====

This bundle provides a Symfony command to copy an entire database schema, without any dependency on 
database client binaries such as `mysqldump` or `mysql`.

The primary use case is for duplicating a test database for use with [Paratest](https://github.com/paratestphp/paratest) 
in CI, where access to the database binaries is not possible, for example due to them being inside a Docker 
container that only exposes the MySQL port 3306.

This solution is significantly faster than dumping an SQL file and then importing it.

## Installation

```bash
composer require --dev headsnet/doctrine-db-copy-bundle
```

If you use Symfony Flex you don't need to configure anything further. 

Otherwise, add the following line to your `bundles.php` file:

```php
return [
    ...
    Headsnet\DoctrineDbCopyBundle\HeadsnetDoctrineDbCopyBundle::class => ['dev' => true, 'test' => true],
    ...
];    
```

## Usage

To copy `test_1` to `test_2`, simply run:

```bash
bin/console headsnet:copy-db <src-db> <dest-db>
```

To create multiple test database copies, you can use a for loop:

```bash
PARATEST_THREADS=6

for (( i = 2; i <= $PARATEST_THREADS; i++ )); do
    bin/console headsnet:copy-db test_1 test_$i;
done
```

## Licence

This code is released under the MIT licence. Please see the LICENSE file for more information.
