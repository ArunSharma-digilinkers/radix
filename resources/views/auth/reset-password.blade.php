<x-layouts.auth title="Reset password">
    <h1 class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">Set a new password</h1>

    @error('email')
        <p class="rx-notice rx-notice--error mb-0 mt-4">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('password.update') }}" class="vstack gap-5 mt-6">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <x-ui.text-field label="Email" name="email" type="email" :value="$email" required />
        <x-ui.text-field label="New password" name="password" type="password" required />
        <x-ui.text-field label="Confirm password" name="password_confirmation" type="password" required />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-100">Reset password</x-ui.button>
    </form>
</x-layouts.auth>
