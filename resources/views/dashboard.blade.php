<!DOCTYPE html>
<html lang="en">
@include('partials.html_head', ['title' => 'Dashboard'])
<body class="is-widescreen">
<main class="container">
    @include('partials.page_title')

    <h2 class="subtitle is-2">Dashboard</h2>

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">PB Tracker</a></li>
            <li class="is-active"><a href="#" aria-current="page">Dashboard</a></li>
        </ul>
    </nav>

    <div class="section">
        <h3 class="title is-3">Your Hooks</h3>
        <div class="container has-text-centered">
            <a href="/dashboard/new" class="button is-success">Add New</a>
        </div>
        <div class="section">
            @foreach($webhooks as $webhook)
                <div class="media">
                    <figure class="media-left">
                        @isset($webhook->avatar_url)
                            <img class="image hook-avatar"
                                 src="{{$webhook->avatar_url}}">
                        @else
                            <img class="image hook-avatar" src="/img/no_img.svg">
                        @endisset
                    </figure>
                    <div class="media-content">
                        <div class="content">
                            <p>
                                <strong>{{$webhook->name}}</strong> @include('partials.hook_status_indicator', ['state' => $webhook->state])
                                <small
                                    class="is-italic">@isset($webhook->manager) {{ $webhook->manager->name }} @endisset</small>
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
</main>
@include('partials.footer')
</body>

</html>
