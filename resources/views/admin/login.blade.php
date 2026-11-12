@extends('notes.layout')

@section('title', 'Administrator Sign In')

@section('content')
    <section class="form-page">
        <a class="back-link" href="{{ route('notes.index') }}"><span aria-hidden="true">←</span> Back to the board</a>

        <header class="form-heading">
            <span class="eyebrow"><span class="live-dot"></span> PRIVATE ADMINISTRATION</span>
            <h1>Administrator <span>sign in.</span></h1>
            <p>Sign in with the administrator account provisioned by the site owner.</p>
        </header>

        <form method="POST" action="{{ route('admin.login.store') }}" class="form-card glass-panel">
            @csrf
            <div class="field-group">
                <label for="email">Email</label>
                <input class="text-field @error('email') field-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                @error('email') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label for="password">Password</label>
                <input class="text-field @error('password') field-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" required>
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <label class="privacy-toggle" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1">
                <span class="toggle-track" aria-hidden="true"></span>
                <span class="privacy-copy"><strong>Remember this device</strong></span>
            </label>

            <div class="form-actions">
                <button class="button button-primary" type="submit">Sign in <span aria-hidden="true">↗</span></button>
                <a class="button button-quiet" href="{{ route('notes.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
