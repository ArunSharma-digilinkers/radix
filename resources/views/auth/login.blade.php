<x-layouts.auth title="Log in">
    <h1 class="font-display text-xl font-extrabold tracking-display text-radix-dark">Admin log in</h1>

    @if (session('status'))
        <p class="mt-4 rounded-lg bg-surface-sunken px-3.5 py-2.5 text-[0.8125rem] text-ink-soft">{{ session('status') }}</p>
    @endif

    @error('email')
        <p class="mt-4 rounded-lg bg-radix-red/10 px-3.5 py-2.5 text-[0.8125rem] text-radix-red-deep">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('login') }}" class="mt-6 grid gap-5">
        @csrf

        <x-ui.text-field label="Email" name="email" type="email" :value="old('email')" required />
        <x-ui.text-field label="Password" name="password" type="password" required />

        <label class="flex items-center gap-2 text-[0.8125rem] text-muted">
            <input type="checkbox" name="remember" class="rounded border-line-control text-radix-red focus:ring-radix-red">
            Remember me
        </label>

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">Log in</x-ui.button>
    </form>

    <a href="{{ route('password.request') }}" class="mt-5 block text-center text-[0.8125rem] font-medium text-radix-red-deep">
        Forgot your password?
    </a>
</x-layouts.auth>
