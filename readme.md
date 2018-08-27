Package allows you to create advanced CRUD views with relations in light weight admin panel. Using plain laravel database structure, models and VueJS which allows to make it very flexible and can be adjusted up to your needs. 

# Installation
1. Add this to your project repositories list in `composer.json` file


```
"repositories": [
    {
        "type": "path",
        "url": "../packages/aven-package"
    }
]
```

Directory structure should look like this
```
-Project
-packages
    --aven-package
    --aven-content
```

2. ```composer require netcore/aven dev-master```
3. ```php artisan vendor:publish --tag=aven```
4. Configure `config/aven.php` file with your preferences
5. Comment out `Illuminate\Translation\TranslationServiceProvider::class,` in `config/app.php` in order to enable translations
6. Run `php artisan migrate`

You should be up and running

Admin panel will be under http://your-domain.com/admin

Default credentials (_can be change in config file_): email:admin@netcore.lv, pw: aven2018

# Creating new resource

1. ```php artisan aven:resource Task```

It will create new resource under `App\Aven\Resource`, resource should look like this
```
<?php

namespace App\Aven\Resources;

use Netcore\Aven\Aven\AbstractAvenResource;
use Netcore\Aven\Aven\FieldSet;
use Netcore\Aven\Aven\Resource;
use Netcore\Aven\Aven\ColumnSet;
use Netcore\Aven\Aven\Table;
use App\Models\Task;

Class TaskResource extends AbstractAvenResource
{

    /**
     * @var string
     */
    protected $resource = Task::class;

    /**
     * @return \Netcore\Aven\Aven\Resource
     */
    public function resource()
    {
        return (new Resource)->make(function (FieldSet $set) {
            $set->text('type')->rules('required|min:3');
        });
    }

     /**
     * @return Table
     */
    public function table()
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('id', '#ID');
        });
    }
}
```

You will have 2 methods `resource` and `table`.

2. You need to add created resource in `config/aven.php` under resources

# Resource
Here you can specify field configuration for your create and edit actions.

## Available field list

## Text

```
$set->text('name')->rules('required')->translatable();
```

## Textarea
```
$set->textarea('name')->rules('required')->translatable();
```

## Wysiwyg
```
$set->wysiwy('name')->rules('required')->translatable();
```

## Email
```
$set->email('email')->rules('required|email');
```

## Password
```
$set->password('password')->rules('required|confirmed|min:3|max:36');
$set->password('password_confirmation');
```

## Boolean
```
$set->boolean('is_active');
```

## Select

```
$set->select('target')
    ->options([
        '_self'   => 'Self',
        '_target' => 'Target',
    ])
    ->rules('required');
```

## Date

```
$set->date('starts_at')->rules('date_format:Y-m-d');
```

## Time

```
$set->time('starts_at')->rules('date_format:H:i');
```

## DateTime

```
$set->datetime('starts_at')->rules('date_format:Y-m-d H:i');
```

## Color

```
$set->color('color');
```

## HasMany
1. first argument must be the name of the relation
2. Fields method should contain fields that is required for relation
3. You can make items sortable, if you add sortable column to your table and specify column name in `sortable` method
```
$set->hasMany('items')
    ->fields(function (FieldSet $set) {
        $set->boolean('is_active');
        $set->select('target')->options([
            '_self'   => 'Self',
            '_target' => 'Target',
        ])->rules('required');
        $set->text('name')->rules('required')->translatable();
        $set->text('url')->rules('required')->translatable();
    })
    ->sortable('sequence_no');
```

## HasOne
1. first argument must be the name of the relation
2. Fields method should contain fields that is required for relation
```
$set->hasOne('author')
    ->fields(function (FieldSet $set) {
        $set->text('name');
    });
```

## BelongsTo 
1. First argument must be class of relation where items will belong
```
$set->belongsTo(Article::class)
    ->hideIf(true) // optional if value is true, field will be hidden and value will be set from "default" method
    ->default(2);
```

## BelongsToMany
1. First argument must be name of the relation in model
2. Second argument is label for field
```
$set->belongsToMany('menuItems', 'Menu items');
```

## Tab (Lets you put certain field groups under certain tabs for better UI)
1. First argument must be the name of the tab
2. Fields method must contain fields that will be under this tab

```
$set->tab('Items')->fields(function (FieldSet $set) {
    $set->hasMany('items')->fields(function (FieldSet $set) {
        $set->boolean('is_active');
        $set->select('target')->options([
            '_self'   => 'Self',
            '_target' => 'Target',
        ])->rules('required');
        $set->text('name')->rules('required')->translatable();
        $set->text('url')->rules('required')->translatable();
    })->sortable('sequence_no');
});
```

## File
For managing files we use https://github.com/czim/laravel-paperclip package.
You don't need to set it up individually because it comes with base package. 
```
$set->file('image');
```
By default package uses Laravel's public storage, we do not like to deal with symlinks that's why we have changed configuration to this:
Storage configuration in `config/paperclip.php`

```
'storage' => [
    // The Laravel storage disk to use.
    'disk' => 'public_root',

    // Per disk, the base URL where attachments are stored at
    'base-urls' => [
        'public_root' => config('app.url') . '/uploads',
    ],
],
```

Add new disk to `config/filesystems.php`

```
'public_root' => [
    'driver' => 'local',
    'root'   => public_path('uploads'),
],
```
# Table

Available fields for columns

## Add
```
$column->add('column_name_in_table', 'Nice column name');
```

## Editable (Allows you to edit database column from index view)
```
$column->add('column_name_in_table', 'Nice column name')->editable();
```

## Modify (Allow you to modify columns data output)
```
$column->add('is_active', 'Is Visible?')
    ->modify(function ($item) {
        return $item->is_active ? 'Yes' : 'No';
    });
```

## Translatable
```
$column->add('title')->translatable();
```
NOTE: You need to specify additional method for Table `relations(['translations'])` to enable eager loading and improve perfomance

Available methods for Table

## Actions (edit, create, delete)
```
->actions(['edit', 'delete'])
```
## Relations
```
->relations(['translations'])
```
## Additional view which will be included above the table
```
->additionalView('aven-content::admin.pages.index-top', compact('channels'));
```