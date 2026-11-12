@extends('notes.layout')

@section('title', 'Confession Moderation')

@section('content')
    <section class="feed-intro">
        <div class="intro-copy">
            <span class="eyebrow"><span class="live-dot"></span> ADMINISTRATOR ONLY</span>
            <h1>Board <span>moderation.</span></h1>
            <p>Review and remove posts that should no longer appear on the board.</p>
        </div>
    </section>

    @if ($notes->isEmpty())
        <section class="empty-state glass-panel">
            <h2>No posts to moderate.</h2>
        </section>
    @else
        <section class="confession-grid" aria-label="Posts available for moderation">
            @foreach ($notes as $note)
                @php($isExpired = $note->created_at->lt($publicSince))
                <article class="confession-card glass-panel {{ $isExpired ? 'is-expired' : '' }}">
                    <div class="card-topline">
                        <span class="author-chip">{{ $note->display_author }}</span>
                        <time class="post-date" datetime="{{ $note->created_at->toIso8601String() }}">{{ $note->created_at->format('M j, Y') }}</time>
                    </div>
                    @if ($isExpired)
                        <span class="expired-note-label">Expired · hidden from public board</span>
                    @endif
                    <h2 class="confession-title">{{ $note->title }}</h2>
                    <div class="confession-content">{{ $note->content }}</div>
                    <div class="card-footer">
                        <form method="POST" action="{{ route('admin.notes.destroy', $note) }}">
                            @csrf
                            @method('DELETE')
                            <button class="button button-danger button-small" type="submit">Remove post</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="feed-toolbar">{{ $notes->links() }}</div>
    @endif
@endsection
