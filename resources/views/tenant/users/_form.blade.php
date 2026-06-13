@php
    use App\Enums\UserRole;

    $roleOptions = collect(UserRole::assignableByClientAdmin())
        ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->label()])
        ->all();

    $user ??= null;
@endphp

<x-card>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <x-form.field name="name" :label="__('ui.name')" :value="$user?->name" required />
        <x-form.field name="email" :label="__('ui.email')" type="email" :value="$user?->email" required />
        <x-form.field name="phone" :label="__('ui.phone')" :value="$user?->phone" />
        <x-form.select name="role" :label="__('ui.role')" :options="$roleOptions"
                       :selected="$user?->role?->value ?? 'client_user'" required />

        <x-form.field name="password" :label="__('ui.password')" type="password" :required="$user === null"
                      :hint="$user ? __('ui.leave_blank_password') : null" />
        <x-form.field name="password_confirmation" :label="__('ui.confirm_password')" type="password" :required="$user === null" />

        <div class="sm:col-span-2">
            <x-form.checkbox name="is_active" :label="__('ui.active')" :checked="$user?->is_active ?? true" />
        </div>
    </div>
</x-card>
