<!DOCTYPE html>
<html lang="en">
@include('partials.header', ['title' => 'Dashboard'])
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
        <a href="/dashboard/new" class="button is-success">Add New</a>
        <div class="section">
            @foreach($webhooks as $webhook)
                <div class="media">
                    <figure class="media-left">
                        <p class="image is-64x64">
                            @isset($webhook->avatar_url)
                                <img class="image is-64x64 is-rounded"
                                     src="{{$webhook->avatar_url}}">
                            @else
                                <img class="image is-64x64 is-rounded" src="https://via.placeholder.com/150">
                            @endisset
                        </p>
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <p>
                                <strong>{{$webhook->name}}</strong> @include('partials.hook_status_indicator', ['state' => $webhook->state])
                                <small
                                    class="is-italic">{{$webhook->discord_id}}</small>
                                <br>
                                {{$webhook->description}}
                            </p>
                        </div>
                    </div>
                    <div class="media-right">
                        @if($webhook->state === \App\Models\WebhookState::INVALIDATED)
                            <a href="/dashboard/edit/{{$webhook->id}}" class="button is-info is-rounded">
                                <i class="mdi mdi-eye"></i>
                            </a>
                        @else
                            <a href="/dashboard/edit/{{$webhook->id}}" class="button is-primary is-rounded">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
</body>
</html>
