@extends('notes.layout')

@section('title', 'Public Anonymous Confession & Vent Board')

@section('head')
    <meta http-equiv="refresh" content="30">
@endsection

@section('content')
    <section class="feed-intro">
        <div class="intro-copy">
            <span class="eyebrow"><span class="live-dot"></span> A QUIET CORNER OF THE INTERNET</span>
            <h1>A place to <span>let it out.</span></h1>
            <p>Unsent words, midnight thoughts, and everything in between. Leave a little lighter.</p>
        </div>
        <a class="button button-primary intro-button" href="{{ route('notes.create') }}">
            <span>Write anonymously</span>
            <span class="button-arrow" aria-hidden="true">↗</span>
        </a>
    </section>

    <div class="feed-toolbar">
        <div class="feed-label">
            <span class="feed-label-mark" aria-hidden="true"></span>
            <h2>Whispers from the void</h2>
        </div>
        <span class="refresh-note"><span class="refresh-icon" aria-hidden="true">↻</span> Updates every 30 seconds</span>
    </div>

    @if ($notes->isEmpty())
        <section class="empty-state glass-panel">
            <span class="empty-orbit" aria-hidden="true"><span>✳</span></span>
            <h2>The board is quiet for now.</h2>
            <p>Whenever you’re ready, there’s room for your words here.</p>
            <a class="button button-secondary" href="{{ route('notes.create') }}">Leave the first thought <span aria-hidden="true">↗</span></a>
        </section>
    @else
        <section class="confession-grid" aria-label="Recent confessions">
            @foreach ($notes as $note)
                <article class="confession-card glass-panel">
                    <div class="card-topline">
                        <span class="author-chip">
                            <span class="author-icon" aria-hidden="true">◉</span>
                            {{ $note->display_author }}
                        </span>
                        <time class="post-date" datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->format('M j, Y') }}</time>
                    </div>

                    <h3 class="confession-title">{{ $note->title }}</h3>
                    <div
                        class="confession-content {{ mb_strlen($note->content) > 150 ? 'is-collapsed' : '' }}"
                        id="content-{{ $note->id }}"
                    >{{ $note->content }}</div>
                    @if (mb_strlen($note->content) > 150)
                        <button
                            class="text-toggle"
                            type="button"
                            data-content-toggle
                            data-content-target="content-{{ $note->id }}"
                            aria-expanded="false"
                        >See more</button>
                    @endif

                    <div class="card-footer">
                        <details class="action-disclosure">
                            <summary class="button button-quiet button-small">Edit <span aria-hidden="true">↗</span></summary>
                            <form class="password-form" method="POST" action="{{ route('notes.edit.authorize', $note) }}">
                                @csrf
                                <label for="edit-password-{{ $note->id }}">Post password</label>
                                <div class="password-row">
                                    <input id="edit-password-{{ $note->id }}" name="password" type="password" autocomplete="current-password" placeholder="Enter password" required>
                                    <button class="button button-primary button-small" type="submit">Continue</button>
                                </div>
                            </form>
                        </details>

                        <details class="action-disclosure">
                            <summary class="button button-danger-quiet button-small">Delete <span aria-hidden="true">×</span></summary>
                            <form class="password-form" method="POST" action="{{ route('notes.destroy', $note) }}">
                                @csrf
                                @method('DELETE')
                                <label for="delete-password-{{ $note->id }}">Post password</label>
                                <div class="password-row">
                                    <input id="delete-password-{{ $note->id }}" name="password" type="password" autocomplete="current-password" placeholder="Enter password" required>
                                    <button class="button button-danger button-small" type="submit">Delete</button>
                                </div>
                            </form>
                        </details>
                    </div>
                </article>
            @endforeach
        </section>
    @endif

    <footer class="feed-footer">
        <span>YOUR WORDS. YOUR SPACE. YOURS TO RELEASE.</span>
        <span class="footer-spark" aria-hidden="true">✳</span>
    </footer>
@endsection
