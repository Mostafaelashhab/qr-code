@php
    use App\Models\Role;

    $user ??= null;

    // "admin" maps to a center admin (full access); any other value is a custom role id.
    $roleOptions = ['admin' => __('roles.client_admin')]
        + Role::orderBy('name')->pluck('name', 'id')->all();

    $currentRef = $user?->isClientAdmin() ? 'admin' : ($user?->role_id ? (string) $user->role_id : null);
@endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-form.field name="name" :label="__('ui.name')" :value="$user?->name" required />
        <x-form.field name="email" :label="__('ui.email')" type="email" :value="$user?->email" required />
        <x-form.field name="phone" :label="__('ui.phone')" :value="$user?->phone" />
        <x-form.select name="role_ref" :label="__('ui.role')" :options="$roleOptions"
                       :selected="$currentRef" required />

        <x-form.field name="password" :label="__('ui.password')" type="password" :required="$user === null"
                      :hint="$user ? __('ui.leave_blank_password') : null" />
        <x-form.field name="password_confirmation" :label="__('ui.confirm_password')" type="password" :required="$user === null" />

        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$user?->is_active ?? true" />
        </div>
    </div>
</x-card>
