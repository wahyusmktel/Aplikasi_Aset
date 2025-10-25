<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',
            'assets.dispose',
            'assets.assign',
            'assets.move',
            'assets.status.update',
            'assets.export',
            'assets.audit.view',
            'refs.manage', // data referensi
        ];
        foreach ($perms as $p) Permission::firstOrCreate(['name' => $p]);

        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $op    = Role::firstOrCreate(['name' => 'Operator']);
        $view  = Role::firstOrCreate(['name' => 'Viewer']);

        $admin->syncPermissions($perms);
        $op->syncPermissions([
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.assign',
            'assets.move',
            'assets.status.update',
            'assets.export',
            'assets.audit.view'
        ]);
        $view->syncPermissions(['assets.view', 'assets.export']);
    }
}
