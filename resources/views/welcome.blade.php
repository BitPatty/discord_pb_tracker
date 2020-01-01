<!DOCTYPE html>
<html lang="en">
@include('partials.header', ['title' => 'Discord PB Tracker'])
<body class="hero is-fullheight is-default has-text-weight-bold">
<div class="hero-body">
    <div class="container is-vcentered has-text-centered">
        <h1 class="title is-1">
            Discord PB Tracker
        </h1>
        @auth
            <a class="button is-large is-primary" href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a class="button is-large is-primary" href="{{ route('login') }}">Login</a>
        @endauth
    </div>
</div>
</body>
</html>
