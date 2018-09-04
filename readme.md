Package allows you to create advanced CRUD views with relations in light weight 
admin panel. Using plain laravel database structure, models and VueJS which 
allows to make it very flexible and can be adjusted up to your needs. 

# Installation

## For local development
1. Add this to your project repositories list in `composer.json` file


```
"repositories": [
    {
        "type": "path",
        "url": "../packages/laradium"
    }
]
```

Directory structure should look like this
```
-Project
-packages
    --laradium
    --laradium-content
```
## For global use

```
"repositories": [
        {
            "type": "git",
            "url": "https://github.com/laradium/laradium.git"
        }
    ]
```

2. ```composer require laradium/laradium dev-master```
3. ```php artisan vendor:publish --tag=laradium```
4. Configure `config/laradium.php` file with your preferences
5. Comment out `Illuminate\Translation\TranslationServiceProvider::class,` in `config/app.php` in order to enable translations
6. Run `php artisan migrate`

You should be up and running

Admin panel will be under http://your-domain.com/admin

Default credentials (_can be change in config file_): 
email:admin@laradium.com, 
pw: laradium2018

# Creating new resource

1. ```php artisan laradium:resource Task```

It will create new resource under `App\Laradium\Resource`, resource should look like this
```
<?php

namespace App\Laradium\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\ColumnSet;
use App\Models\Task;

Class TaskResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $resource = Task::class;

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        return laradium()->resource(function (FieldSet $set) {
            $set->text('type')->rules('required|min:3');
        });
    }

     /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('id', '#ID');
        });
    }
}
```

You will have 2 methods `resource` and `table`.

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
$set->wysiwyg('name')->rules('required')->translatable();
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
->additionalView('laradium-content::admin.pages.index-top', compact('channels'));
```
