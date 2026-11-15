<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DatosElectoralesSeeder::class,
        ]);

        // Crear usuario admin automáticamente
        User::updateOrCreate(
            ['email' => 'ruben@observatorio.com'],
            [
                'name' => 'ruben',
                'password' => bcrypt('admin123'),
            ]
        );
    }
}
