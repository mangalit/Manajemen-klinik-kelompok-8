<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Data provider untuk skenario akses ilegal.
     */
    public static function illegalAccessProvider(): array
    {
        return [
            'Pasien mencoba akses Admin Dashboard' => [UserRole::PASIEN->value, 'admin.dashboard'],
            'Pasien mencoba akses Manajemen Poli' => [UserRole::PASIEN->value, 'admin.polis.index'],
            'Pasien mencoba akses Laporan Pendapatan' => [UserRole::PASIEN->value, 'admin.reports.revenue'],
            'Dokter mencoba akses Manajemen Dokter' => [UserRole::DOKTER->value, 'admin.doctors.index'],
            'Dokter mencoba akses Laporan Pendapatan' => [UserRole::DOKTER->value, 'admin.reports.revenue'],
            'Kasir mencoba akses Manajemen Poli' => [UserRole::KASIR->value, 'admin.polis.index'],
            'Kasir mencoba akses Dashboard Dokter' => [UserRole::KASIR->value, 'dokter.dashboard'],
            'Pasien mencoba akses Dashboard Kasir' => [UserRole::PASIEN->value, 'kasir.dashboard'],
        ];
    }

    /**
     * @dataProvider illegalAccessProvider
     */
    public function test_unauthorized_roles_cannot_access_restricted_routes(string $role, string $routeName): void
    {
        $user = User::factory()->create(['role' => $role]);

        // Beberapa route mungkin butuh parameter, tapi untuk test middleware role, 
        // abort(403) biasanya dipicu sebelum binding model dicek jika middleware ditaruh dengan benar.
        try {
            $url = route($routeName);
        } catch (\Exception $e) {
            // Jika route butuh parameter (seperti edit), tambahkan ID dummy 1
            $url = route($routeName, 1);
        }

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_any_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('/login');
        $this->get(route('dokter.dashboard'))->assertRedirect('/login');
        $this->get(route('kasir.dashboard'))->assertRedirect('/login');
        $this->get(route('pasien.dashboard'))->assertRedirect('/login');
    }
}
