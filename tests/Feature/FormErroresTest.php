<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class FormErroresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_el_formulario_muestra_los_errores_de_validacion(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrador');

        // Enviar un plan inválido (sin nombre ni precio): al seguir el redirect de
        // vuelta al formulario, el componente <x-form-errors /> muestra el aviso.
        $this->actingAs($admin)
            ->from(route('planes.create'))
            ->followingRedirects()
            ->post(route('planes.store'), [])
            ->assertOk()
            ->assertSee('Revisa estos datos');
    }
}
