<?php

use App\Enums\Permission;
use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->nullOnDelete();
        });

        $this->backfillDefaultRoles();
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
        });
    }

    /**
     * Give every center that already has staff a default "Staff" role granting
     * all module permissions (matching the access client_user had before), and
     * point those staff at it.
     */
    private function backfillDefaultRoles(): void
    {
        $clientIds = DB::table('users')
            ->where('role', UserRole::ClientUser->value)
            ->whereNotNull('client_id')
            ->distinct()
            ->pluck('client_id');

        foreach ($clientIds as $clientId) {
            $roleId = DB::table('roles')->insertGetId([
                'client_id' => $clientId,
                'name' => 'Staff',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $rows = array_map(fn (string $permission): array => [
                'role_id' => $roleId,
                'permission' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ], Permission::allValues());

            DB::table('role_permissions')->insert($rows);

            DB::table('users')
                ->where('client_id', $clientId)
                ->where('role', UserRole::ClientUser->value)
                ->update(['role_id' => $roleId]);
        }
    }
};
