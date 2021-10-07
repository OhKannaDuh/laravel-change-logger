Change Logger
=============

Getting Started
---------------
Install the package:
```bash
composer require ohkannaduh/laravel-change-logger
```

Migrate the database if you plan on using the database logger:
```bash
php artisan migrate
```

Set up is simple, just add the trait to your model:

```php
namespace App\Models;

use OhKannaDuh\ChangeLogger\Models\Behaviours\LogsChanges;

final class SomeModel extends Model
{
    use LogsChanges;
    ...
}
```

There are a number of variables you can add to your object to customise the use of the logger:

- `$observedAttributes`

When added to an object we can define what attributes are watched, any none observed attributes will be removed from the log. If this is blank then it is ignoerd.
```php
namespace App\Models;

use OhKannaDuh\ChangeLogger\Models\Behaviours\LogsChanges;

final class SomeModel extends Model
{
    use LogsChanges;
    ...

    /** string[] */
    protected $observedAttributes = [
        'name',
        'password',
    ];
}
...

/** @var SomeModel $someModel */
$someModel->update([
    'name' => 'Luigi',
    'country' => 'The Mushroom Kingdom',
]);
// Only name will be shown in logs, the country attribute is not being observed
```

- `$withoutAttributes`

When added to an object we can define attributes to exclude from logged output.

```php
namespace App\Models;

use OhKannaDuh\ChangeLogger\Models\Behaviours\LogsChanges;

final class SomeModel extends Model
{
    use LogsChanges;
    ...

    /** string[] */
    protected $withoutAttributes = [
        'name',
    ];
}
...

/** @var SomeModel $someModel */
$someModel->update([
    'name' => 'Luigi',
    'country' => 'The Mushroom Kingdom',
]);
// the name attribute will be removed from the log as it is declared in $withoutAttributes
```

- `$obfuscateAttributes`

When added to an object we can define attributes to obfustcate in logged output.

```php
namespace App\Models;

use OhKannaDuh\ChangeLogger\Models\Behaviours\LogsChanges;

final class SomeModel extends Model
{
    use LogsChanges;
    ...

    /** string[] */
    protected $obfuscateAttributes = [
        'password',
    ];
}
...

/** @var SomeModel $someModel */
$someModel->update([
    'name' => 'Luigi',
    'password' => 'secret',
]);
// the password attribute will be replaced in the logged output by a random string, now we can track that it changed but
// not log the value
```

Config
------
You can publish the config file for modification by calling
```bash
php artisan vendor:publish --provider=OhKannaDuh\\ChangeLogger\\Providers\\ChangeLoggerServiceProvider --tag=config
```
This will create the file `config/change-logger.php`.

- `migrate` (default: true)

This determines wether the package will migrate its tables when you call any artisan migrate command.

- `table` (default: 'model_change_log')

The table name for the `ChangeLog` model, this is used then using the `DatabaseChangeLogger`.

- `default` (default: 'OhKannaDuh\\ChangeLogger\\LogChangeLogger')

The default logger implementation to use. This must be the concrete class name of an object that implements `OhKannaDuh\ChangeLogger\ChangeLoggerInterface`.

- `log_blank_changes` (default: false)

Determines whether or not blank changes are ignored and not logged.

What's planned for the future?
------------------------------
- Api Logger (Send changed data to an api for external processing)