Package allows you to create advanced CRUD views with relations in light wight admin panel. Using plain laravel database structure, models and VueJS which allows to make it very flexible and can be adjusted up to your needs. 

##Installation
1. Add this to your project repositories list in `composer.json` file
```$xslt
"repositories": [
    {
      "type": "path",
      "url": "../packages/aven-package"
    }
  ]
```

2. ```composer require netcore/aven dev-master```
3. ```php artisan vendor:publish --tag=aven```
4. Configure `config/aven.php` file with your preferences
5. Comment out `Illuminate\Translation\TranslationServiceProvider::class,` in `config/app.php` in order to enable translations

##Creating new resource

1. ```php artisan aven:resource Task```

It will create new resource under `App\Aven\Resource`, resource should look like this
```$xslt
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

###Resource
Here you can specify field configuration for your create and edit actions.

####Available field list

Text
```
$set->text('name')->rules('required')->translatable();
```
Textarea
```$xslt
$set->textarea('name')->rules('required')->translatable();
```
Wysiwyg
```$xslt
$set->wysiwy('name')->rules('required')->translatable();
```
Boolean
```$xslt
$set->boolean('is_active');
```
Select

```$xslt
$set->select('target')->options([
                        '_self'   => 'Self',
                        '_target' => 'Target',
                    ])->rules('required');
```
HasMany
1. first argument must be the name of the relation
2. Fields method should contain fields that is required for relation
3. You can make items sortable, if you add sortable column to your table and specify column name in `sortable` method
```$xslt
$set->hasMany('items')->fields(function (FieldSet $set) {
                    $set->boolean('is_active');
                    $set->select('target')->options([
                        '_self'   => 'Self',
                        '_target' => 'Target',
                    ])->rules('required');
                    $set->text('name')->rules('required')->translatable();
                    $set->text('url')->rules('required')->translatable();
                })->sortable('sequence_no');
```
Tab (Lets you put certain field groups under certain tabs for better UI)
1. First argument must be the name of the tab
2. Fields method must contain fields that will be under this tab

```$xslt
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

###Table

Available fields for columns

Add
```$xslt
$column->add('column_name_in_table', 'Nice column name');
```

Editable (Allows you to edit database column from index view)
```$xslt
$column->add('column_name_in_table', 'Nice column name')->editable();
```

Modify (Allow you to modify columns data output)
```$xslt
$column->add('is_active', 'Is Visible?')->modify(function ($item) {
                return $item->is_active ? 'Yes' : 'No';
            });
```

Translatable
```$xslt
$column->add('title')->translatable();
```
NOTE: You need to specify additional method for Table `relations(['translations''])` to enable eager loading and improve perfomance

Available methods for Table

Actions (edit, create, delete)
```$xslt
->actions(['edit', 'delete'])
```
Relations
```$xslt
->relations(['translations'])
```
Additional view which will be included above the table
```$xslt
->additionalView('aven-content::admin.pages.index-top', compact('channels'));
```