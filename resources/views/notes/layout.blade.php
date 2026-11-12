<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('head')
    <title>@yield('title', 'Public Anonymous Confession & Vent Board')</title>
    <link rel="stylesheet" href="{{ asset('css/confession-board.css') }}">
</head>
<body>
    <div class="ambient ambient-violet" aria-hidden="true"></div>
    <div class="ambient ambient-cyan" aria-hidden="true"></div>

    <header class="site-header">
        <nav class="site-nav" aria-label="Main navigation">
            <a class="brand" href="{{ route('notes.index') }}">
                <span class="brand-mark" aria-hidden="true">✳</span>
                <span>After Hours<span class="brand-period">.</span></span>
            </a>
            <a class="button button-primary button-small" href="{{ route('notes.create') }}">
                <span aria-hidden="true">＋</span>
                <span>Share a thought</span>
            </a>
            @can('access-admin')
                <a class="button button-quiet button-small" href="{{ route('admin.notes.index') }}">Moderation</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="button button-quiet button-small" type="submit">Sign out</button>
                </form>
            @endcan
        </nav>
    </header>

    <main class="page-shell">
        @yield('content')
    </main>

    @if (session('success') || $errors->any())
        <div
            class="toast-pill {{ session('success') ? 'toast-success' : 'toast-error' }}"
            role="status"
            aria-live="polite"
            data-toast
        >
            <span class="toast-indicator" aria-hidden="true"></span>
            <span>{{ session('success') ?? $errors->first() }}</span>
        </div>
    @endif

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.querySelector('[data-toast]');

            if (toast) {
                window.setTimeout(() => {
                    toast.classList.add('toast-leaving');
                    window.setTimeout(() => toast.remove(), 300);
                }, 2700);
            }

            document.addEventListener('click', (event) => {
                const toggle = event.target.closest('[data-content-toggle]');

                if (!toggle) {
                    return;
                }

                const content = document.getElementById(toggle.dataset.contentTarget);
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

                content.classList.toggle('is-collapsed', isExpanded);
                toggle.setAttribute('aria-expanded', String(!isExpanded));
                toggle.textContent = isExpanded ? 'See more' : 'See less';
            });
        });
    </script>
</body>
</html>
