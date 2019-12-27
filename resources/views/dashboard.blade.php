<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.7.95/css/materialdesignicons.min.css" rel="stylesheet"/>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            padding: 15px;
        }
    </style>

    <title>Dashboard</title>
</head>
<body class="is-widescreen level">
<div class="container">
    <h1 class="title is-1">
        Discord PB Tracker
    </h1>
    <h2 class="subtitle is-2">Dashboard</h2>

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">PB Tracker</a></li>
            <li class="is-active"><a href="#" aria-current="page">Dashboard</a></li>
        </ul>
    </nav>

    <div class="section">
        <h3 class="title is-3">Hooks</h3>

        <div class="section">
            @foreach($webhooks as $webhook)
                <div class="media">
                    <figure class="media-left">
                        <p class="image is-64x64">
                            @isset($webhook->avatar_url)
                                <img class="image is-64x64 is-rounded" src="{{$webhook->avatar_url}}">
                            @else
                                <img class="image is-64x64 is-rounded" src="https://via.placeholder.com/150">
                            @endisset
                        </p>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <p>
                                <strong>{{$webhook->name}}</strong> <small
                                    class="is-italic">{{$webhook->discord_id}}</small>
                                <br>
                                {{$webhook->description}}
                            </p>
                        </div>
                    </div>
                    <div class="media-right">
                        <a href="/dashboard/edit/{{$webhook->id}}" class="button is-primary is-rounded">
                            <i class="mdi mdi-pencil"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <a href="/dashboard/new" class="button is-success">Add New</a>
    </div>
</div>
</body>
</html>
