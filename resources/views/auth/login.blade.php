<x-layouts.auth title="Log in">
    <h1 class="mb-0 font-display fs-20 fw-extrabold tracking-display text-radix-dark">Admin log in</h1>

    @if (session('status'))
        <p class="rx-notice mb-0 mt-4">{{ session('status') }}</p>
    @endif

    @error('email')
        <p class="rx-notice rx-notice--error mb-0 mt-4">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('login') }}" class="vstack gap-5 mt-6">
        @csrf

        <x-ui.text-field label="Email" name="email" type="email" :value="old('email')" required />
        <x-ui.text-field label="Password" name="password" type="password" required />

        <div class="form-check mb-0 fs-13 text-muted">
            <input type="checkbox" name="remember" id="remember" class="form-check-input rx-check">
            <label for="remember" class="form-check-label">Remember me</label>
        </div>

        <x-ui.button type="submit" variant="primary" size="lg" class="w-100">Log in</x-ui.button>
    </form>

    <a href="{{ route('password.request') }}" class="d-block mt-5 text-center fs-13 fw-medium text-radix-red-deep">
        Forgot your password?
    </a>
</x-layouts.auth>
