<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'show users',
            'create users',
            'edit users',
            'delete users',

            'show apartments',
            'create apartments',
            'edit apartments',
            'checkin apartments',
            'checkout apartments',
            'empty apartments',
            'maintenance apartments',
            'delete apartments',

            'show reservations',
            'create reservations',
            'edit reservations',
            'transfer reservations',
            'cancel reservations',
            'print reservations',
            'edit terms of the reservation lease',
            "change reservation price for night",
            'delete reservations',

            'show calendar',

            'show records',

            'edit hotel information',

            'show finance',

            'show expenses',
            'create expenses',
            'delete expenses',

            'show taxes',
            'create taxes',
            'delete taxes',

            'print receipt vouchers',
            'print tax invoices',

            'show statistics',
        ];

        $_permissions = [];
        $latest_time = now();

        foreach ($permissions as $permission) {
            $_permissions[] = Permission::updateOrCreate([
                'name' => $permission,
            ], [
                'created_at' => $latest_time = $latest_time->addSecond(),
            ]);
        }

        foreach (array_diff(Permission::all()->pluck('name')->toArray(), $permissions) as $permission) {
            $p = Permission::where('name', $permission)->get()->first();
            if ($p) $p->delete();
        }

        User::find(2)->syncPermissions($_permissions);
    }
}