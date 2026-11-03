<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\PreparaDominio;
use Tests\TestCase;

class ReporteExportTest extends TestCase
{
    use RefreshDatabase, PreparaDominio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sembrarRoles();
    }

    private function conPermisos(array $permisos)
    {
        $u = $this->usuario('Cajero');
        $u->givePermissionTo($permisos);

        return $u->fresh();
    }

    public function test_exportar_hoy_con_permiso_200_y_contenido(): void
    {
        $u = $this->conPermisos(['exportar_reportes']);
        $r = $this->actingAs($u)->get(route('reports.export'));
        $r->assertOk()->assertSee('Reporte Financiero');
        $r->assertDontSee('en construcción');
    }

    public function test_fecha_pasada_sin_historicos_redirige(): void
    {
        $u = $this->conPermisos(['exportar_reportes']);
        $this->actingAs($u)->get(route('reports.export', ['fecha' => '2020-01-01']))
            ->assertRedirect(route('reports.index'));
    }

    public function test_fecha_pasada_con_historicos_200(): void
    {
        $u = $this->conPermisos(['exportar_reportes', 'ver_reportes_historicos']);
        $this->actingAs($u)->get(route('reports.export', ['fecha' => '2020-01-01']))->assertOk()->assertSee('Reporte Financiero');
    }

    public function test_sin_permiso_exportar_403(): void
    {
        $u = $this->usuario('Cajero');
        $this->actingAs($u)->get(route('reports.export'))->assertForbidden();
    }
}
