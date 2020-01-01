<!DOCTYPE html>
<html lang="en">
@include('partials.html_head', ['title' => 'Discord PB Tracker'])
<body class="hero is-fullheight is-default has-text-weight-bold">
<div class="hero-body">
    <div class="container is-vcentered has-text-centered">
        <h1 class="title is-1">
            Discord PB Tracker
        </h1>
        <h2 class="subtitle is-3">A
            <a href="https://speedrun.com" title="Speedrun.com" target="_blank" rel="noreferrer">Speedrun.com</a>
            PB Tracker for Discord.</h2>
        @auth
            <a class="button is-large is-primary" href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <div class="section">
                <a class="button is-large is-primary" href="{{ route('login') }}">Login with Discord</a>
            </div>
            <div>
                <img alt="Message preview" src="/img/preview.png"/>
            </div>
    </div>
    @endauth
</div>
</div>
</body>
</html>
