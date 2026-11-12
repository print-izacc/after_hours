@extends('notes.layout')

@section('title', 'Edit Your Post')

@section('content')
    <section class="form-page">
        <a class="back-link" href="{{ route('notes.index') }}"><span aria-hidden="true">←</span> Back to the board</a>

        <header class="form-heading">
            <span class="eyebrow"><span class="live-dot"></span> YOUR WORDS, YOUR CALL</span>
            <h1>Make it <span>yours.</span></h1>
            <p>Verify your post password to save changes.</p>
        </header>

        <form method="POST" action="{{ route('notes.update', $note) }}" class="form-card glass-panel">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label for="password">Post password <span>Required to save</span></label>
                <input class="text-field @error('password') field-invalid @enderror" id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your post password" required>
                <span class="field-hint">Password verification happens before your changes are saved.</span>
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label for="poster_name">What should we call you? <span>Optional</span></label>
                <input class="text-field @error('poster_name') field-invalid @enderror" id="poster_name" name="poster_name" value="{{ old('poster_name', $note->poster_name) }}" maxlength="255" placeholder="Anonymous">
                @error('poster_name') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <label class="privacy-toggle" for="is_censored">
                <input id="is_censored" name="is_censored" type="checkbox" value="1" @checked(old('is_censored', $note->is_censored))>
                <span class="toggle-track" aria-hidden="true"></span>
                <span class="privacy-copy"><strong>Keep my name private</strong><small>Your name will be gently masked on the board.</small></span>
                <span class="privacy-lock" aria-hidden="true">⌑</span>
            </label>

            <div class="field-group">
                <label for="title">Title</label>
                <input class="text-field @error('title') field-invalid @enderror" id="title" name="title" value="{{ old('title', $note->title) }}" maxlength="255" required>
                @error('title') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="field-group">
                <label for="content">Your words</label>
                <textarea class="text-field text-area @error('content') field-invalid @enderror" id="content" name="content" rows="7" required>{{ old('content', $note->content) }}</textarea>
                @error('content') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <button class="button button-primary" type="submit">Save changes <span aria-hidden="true">↗</span></button>
                <a class="button button-quiet" href="{{ route('notes.index') }}">Cancel</a>
            </div>
            <p class="form-footnote"><span aria-hidden="true">◈</span> Your password stays private and is never displayed.</p>
        </form>
    </section>
@endsection
