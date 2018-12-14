<?php

namespace Laradium\Laradium\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;

class UserResource extends AbstractResource
{

    /**
     * UserResource constructor.
     */
    public function __construct()
    {
        $this->resource = config('auth.providers.users.model');

        parent::__construct();
    }

    /**
     * @return Resource
     */
    public function resource()
    {
        return laradium()->resource(function (FieldSet $set) {
            $user = request()->route()->user;
            $emailUniqueRule = $user ? 'unique:users,email,' . $user : 'unique:users';
            $passwordRule = $user ? 'nullable' : 'required';

            $set->text('name')->rules('required|min:3|max:255');
            $set->email('email')->rules('required|email|min:3|max:255|' . $emailUniqueRule);
            $set->password('password')->rules($passwordRule . '|confirmed|min:3|max:255')->col(6);
            $set->password('password_confirmation')->rules('nullable|min:3|max:255')->col(6);
            $set->boolean('is_admin');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('id', '#ID');
            $column->add('name', 'Name');
            $column->add('email', 'Email');
        });
    }
}