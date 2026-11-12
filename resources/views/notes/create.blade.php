@extends('notes.layout')

@section('title', 'Share a Thought')

@section('content')
    <section class="form-page">
        <a class="back-link" href="{{ route('notes.index') }}"><span aria-hidden="true">←</span> Back to the board</a>

        <header class="form-heading">
            <span class="eyebrow"><span class="live-dot"></span> NO NAMES NEEDED</span>
            <h1>Let it <span>out.</span></h1>
            <p>Some things feel lighter once they’re written down.</p>
        </header>

        <form method="POST" action="{{ route('notes.store') }}" class="form-card glass-panel">
            @csrf
            <div class="field-group">
                <label for="poster_name">What should we call you? <span>Optional</span></label>
                <input class="text-field @error('poster_name') field-invalid @enderror" id="poster_name" name="poster_name" value="{{ old('poster_name') }}" maxlength="255" placeholder="Anonymous">
                @error('poster_name') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <label class="privacy-toggle" for="is_censored">
                <input id="is_censored" name="is_censored" type="checkbox" value="1" @checked(old('is_censored'))>
                <span class="toggle-track" aria-hidden="true"></span>
                <span class="privacy-copy"><strong>Keep my name private</strong><small>Your name will be gently masked on the board.</small></span>
                <span class="privacy-lock" aria-hidden="true">⌑</span>
            </label>

            <div class="field-group">
                <label for="title">Give it a title</label>
                <input class="text-field @error('title') field-invalid @enderror" id="title" name="title" value="{{ old('title') }}" maxlength="255" placeholder="A thought I can't shake..." required>
                @error('title') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label for="content">Your words <span>Take your time</span></label>
                <textarea class="text-field text-area @error('content') field-invalid @enderror" id="content" name="content" rows="7" placeholder="Whatever's on your mind..." required>{{ old('content') }}</textarea>
                @error('content') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label for="password">A private post password</label>
                <input class="text-field @error('password') field-invalid @enderror" id="password" name="password" type="password" minlength="8" maxlength="255" autocomplete="new-password" placeholder="At least 8 characters" required>
                <span class="field-hint">Only you can use this to edit or remove your post. It can’t be recovered.</span>
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button class="button button-primary" type="submit">Release your words <span aria-hidden="true">↗</span></button>
                <a class="button button-quiet" href="{{ route('notes.index') }}">Maybe later</a>
            </div>
            <p class="form-footnote"><span aria-hidden="true">◈</span> No account. No identity. Just a moment to breathe.</p>
        </form>
    </section>
@endsection
