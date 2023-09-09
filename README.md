Doctrine Copy DB Bundle
====

This bundle provides a Symfony command to copy an entire database schema, without any dependency on 
database client binaries such as `mysqldump` or `mysql`.

The primary use case is for duplicating a test database for use with [Paratest](https://github.com/paratestphp/paratest) 
in CI, where access to the database binaries is not possible, for example due to them being inside a Docker 
container that only exposes the MySQL port 3306.

This solution is significantly faster than dumping an SQL file and then importing it.

## Installation

Add the following line to your `bundles.php` file:

```php
return [
    ...
    Headsnet\DoctrineDbCopyBundle\HeadsnetDoctrineDbCopyBundle::class => ['all' => true],
    ...
];    
```

## Usage

To copy `test_1` to `test_2`, simply run:

```bash
bin/console headsnet:copy-db -s test_1 -d test_2;
```

To create multiple test database copies, you can use a for loop:

```bash
PARATEST_THREADS=6

for (( i = 2; i <= $PARATEST_THREADS; i++ )); do
    bin/console headsnet:copy-db -s test_1 -d test_$i;
done
```

## Licence

This code is released under the MIT licence. Please see the LICENSE file for more information.
