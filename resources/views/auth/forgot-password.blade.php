<x-layouts.auth title="Forgot password">
    <h1 class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">Reset your password</h1>
    <p class="mb-0 mt-2 fs-13-5 lh-relaxed text-muted">
        Enter your email and we&rsquo;ll send you a reset link.
    </p>

    @if (session('status'))
        <p class="rx-notice mb-0 mt-4">{{ session('status') }}</p>
    @endif

    @error('email')
        <p class="rx-notice rx-notice--error mb-0 mt-4">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('password.email') }}" class="vstack gap-5 mt-6">
        @csrf

        <x-ui.text-field label="Email" name="email" type="email" :value="old('email')" required />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-100">Send reset link</x-ui.button>
    </form>

    <a href="{{ route('login') }}" class="d-block mt-5 text-center fs-13 fw-medium text-radix-red-deep">
        Back to log in
    </a>
</x-layouts.auth>
