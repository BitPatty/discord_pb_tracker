<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <style>
        html, body {
            font-family: 'Nunito', sans-serif;
        }
    </style>

    <title>Discord PB Tracker</title>
</head>
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
