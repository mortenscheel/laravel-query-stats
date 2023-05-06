# QueryStats

Compiles statistics about queries in any Laravel application.
```
[2023-05-06 22:24:41] local.INFO: Duplicate query: select * from "users" {"count":3,"time":0.07}
[2023-05-06 22:24:41] local.INFO: Query stats {"count":4,"time":0.27,"duplicates":2,"unique":2}
```

## Installation

Via Composer

``` bash
$ composer require mortenscheel/query-stats
```

## Usage
```php
\QueryStats::enable(); // Start recording queries
\QueryStats::disable(); // Suspend query recording
\QueryStats::logChannel('stderr'); // Select the log channel to use for output
\QueryStats::showAllQueries(); // Queries will be written to the log as they happen
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email morten@mortenscheel.com instead of using the issue tracker.

## Credits

- [Morten Scheel][link-author]

## License

MIT. Please see the [license file](license.md) for more information.

[link-author]: https://github.com/mortenscheel
