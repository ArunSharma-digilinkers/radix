<x-layouts.auth title="Forgot password">
    <h1 class="font-display text-xl font-extrabold tracking-display text-radix-dark">Reset your password</h1>
    <p class="mt-2 text-[0.84375rem] leading-relaxed text-muted">
        Enter your email and we&rsquo;ll send you a reset link.
    </p>

    @if (session('status'))
        <p class="mt-4 rounded-lg bg-surface-sunken px-3.5 py-2.5 text-[0.8125rem] text-ink-soft">{{ session('status') }}</p>
    @endif

    @error('email')
        <p class="mt-4 rounded-lg bg-radix-red/10 px-3.5 py-2.5 text-[0.8125rem] text-radix-red-deep">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 grid gap-5">
        @csrf

        <x-ui.text-field label="Email" name="email" type="email" :value="old('email')" required />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">Send reset link</x-ui.button>
    </form>

    <a href="{{ route('login') }}" class="mt-5 block text-center text-[0.8125rem] font-medium text-radix-red-deep">
        Back to log in
    </a>
</x-layouts.auth>
