<x-layouts.auth title="Reset password">
    <h1 class="font-display text-xl font-extrabold tracking-display text-radix-dark">Set a new password</h1>

    @error('email')
        <p class="mt-4 rounded-lg bg-radix-red/10 px-3.5 py-2.5 text-[0.8125rem] text-radix-red-deep">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 grid gap-5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <x-ui.text-field label="Email" name="email" type="email" :value="$email" required />
        <x-ui.text-field label="New password" name="password" type="password" required />
        <x-ui.text-field label="Confirm password" name="password_confirmation" type="password" required />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">Reset password</x-ui.button>
    </form>
</x-layouts.auth>
