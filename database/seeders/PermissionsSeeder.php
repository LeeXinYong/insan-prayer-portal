<?php

namespace Database\Seeders;

use App\Providers\AuthServiceProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Seeder;
use phpDocumentor\Reflection\Types\Iterable_;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $data = self::data();

        // do not use delete directly to query so that Permission::deleting can be called
        Permission::query()->whereNotIn('name', $data)->get()->each(function($permission) {
           $permission->delete();
        });

        foreach ($data as $value) {
            Permission::query()->firstOrCreate([
                'name' => $value,
            ]);
        }
    }

    private static function data() : iterable
    {
        return AuthServiceProvider::getAllPoliciesMethods();
    }

//    public function data()
//    {
//        $data = [];
//        // list of model permission
//        $model = ['content', 'user', 'role', 'permission'];
//
//        foreach ($model as $value) {
//            foreach ($this->crudActions($value) as $action) {
//                $data[] = ['name' => $action];
//            }
//        }
//
//        return $data;
//    }
//
//    public function crudActions($name)
//    {
//        $actions = [];
//        // list of permission actions
//        $crud = ['create', 'read', 'update', 'delete'];
//
//        foreach ($crud as $value) {
//            $actions[] = $value.' '.$name;
//        }
//
//        return $actions;
//    }
}
