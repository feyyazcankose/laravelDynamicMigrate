# Laravel Dynamic Migrate

The Laravel Dynamic Migrate package allows you to dynamically create or update database tables based on your Eloquent models. This can save you time and effort, as you no longer have to manually create or update your database tables every time you make changes to your models.

## Installation
You can install the Laravel Dynamic Migrate package via Composer:

```composer require feyyazcankose/laravel-dynamic-migrate```

Once the package is installed, you can run the following command to generate the necessary migration files:

```
php artisan dynamic:migrate
```
## Usage
To use the Laravel Dynamic Migrate package, you need to add the setColumns() method to your Eloquent models. This method should define the columns for your database table, using the Blueprint class from the Illuminate\Database\Schema namespace. Here's an example:

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Feyyazcankose\LaravelDynamicMigrate\DynamicMigration;

class MyModel extends Model
{
    protected $table = 'my_model';

    public function setColumns(Blueprint $table)
    {
        $table->increments('id');
        $table->string('name');
        $table->text('description')->nullable();
        $table->timestamps();
    }

    // Optional: You can also define an `updateColumns()` method to update your table columns
    // public function updateColumns(Blueprint $table)
    // {
    //     DynamicMigration::renameColumn('old_column_name', 'new_column_name',$this->table, $table);
    //     DynamicMigration::dropColumn('column_to_delete',$this->table, $table);
    //     DynamicMigration::addColumn('new_column_name',$this->table, function(Blueprint $table) {
    //         $table->string('new_column_name')->nullable();
    //     });
    //     DynamicMigration::changeColumn('column_to_change',$this->table, function(Blueprint $table) {
    //         $table->string('column_to_change')->nullable();
    //     });
    // }
}
```

Once you have defined the ```setColumns()``` method for your models, you can run the dynamic:migrate command to generate or update your database tables:

```
php artisan dynamic:migrate
```
Contributing
If you find any issues with the Laravel Dynamic Migrate package, or if you have any suggestions for new features or improvements, feel free to open an issue or submit a pull request on GitHub.

License
The Laravel Dynamic Migrate package is open-source software licensed under the MIT license.





