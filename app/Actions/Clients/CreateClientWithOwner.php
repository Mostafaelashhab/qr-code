<?php

namespace App\Actions\Clients;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateClientWithOwner
{
    /**
     * Create a tenant (client) together with its first admin user.
     *
     * @param  array{name: string, email?: ?string, phone?: ?string, address?: ?string, is_active?: bool}  $clientData
     * @param  array{name: string, email: string, password: string, phone?: ?string}  $ownerData
     */
    public function execute(array $clientData, array $ownerData): Client
    {
        return DB::transaction(function () use ($clientData, $ownerData): Client {
            $client = Client::create([
                ...$clientData,
                'slug' => $this->uniqueSlug($clientData['name']),
            ]);

            $client->users()->create([
                ...$ownerData,
                'role' => UserRole::ClientAdmin,
                'is_active' => true,
            ]);

            return $client;
        });
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'client';
        $slug = $base;
        $suffix = 1;

        while (Client::where('slug', $slug)->exists()) {
            $slug = "{$base}-".(++$suffix);
        }

        return $slug;
    }
}
