<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="https://cdn.materialdesignicons.com/4.7.95/css/materialdesignicons.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            padding: 15px;
        }
    </style>
    <title>Edit Webhook {{$webhook->name}}</title>
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
            <li><a href="/dashboard">Dashboard</a></li>
            <li class="is-active"><a href="#" aria-current="page">Edit Webhook</a></li>
        </ul>
    </nav>
    <div class="section">
        <h3 id="edit-hook" class="title is-3">Edit Hook</h3>
        <form action="javascript:submitForm()">
            <fieldset id="frm" @if($webhook->state === \App\Models\WebhookState::INVALIDATED) disabled
                      aria-disabled="true" @endif>
                @csrf
                <div class="field">
                    <label for="frm_name" class="label">Name</label>
                    <div class="control">
                        <input id="frm_name" name="frm_name" class="input" type="text" required aria-required="true"
                               title="The webhook name"
                               pattern="([ ]*[A-Za-z0-9]+[ ]*)+"
                               placeholder="Captain Hook" value="{{$webhook->name}}">
                    </div>
                    <p class="help"></p>
                </div>
                <div class="field">
                    <label for="frm_url" class="label">Discord URL</label>
                    <div class="control">
                        <input id="frm_url" name="frm_url" class="input" type="url" required aria-required="true"
                               title="The webhook url"
                               pattern="^[ ]*(https://discordapp\.com/api/webhooks[/a-zA-Z0-9\-_]+)[ ]*$"
                               value="{{$webhook->url}}" disabled aria-disabled="true">
                    </div>
                </div>
                <div class="field ">
                    <label for="frm_desc" class="label">Description</label>
                    <div class="control">
                        <textarea id="frm_desc" name="frm_desc" class="textarea"
                                  placeholder="PB Tracker for my server"
                                  maxlength="2048">{{$webhook->description}}</textarea>
                    </div>
                </div>
                <div class="field ">
                    <label for="frm_state" class="label">State</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="frm_state" name="frm_state">
                                <option value="ACTIVE"
                                        @if($webhook->state === \App\Models\WebhookState::ACTIVE) selected
                                        aria-selected="true" @endif>Active
                                </option>
                                <option value="DEAD"
                                        @if($webhook->state !== \App\Models\WebhookState::ACTIVE) selected
                                        aria-selected="true" @endif>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <button id="frm_submit" type="submit" class="button is-primary">Submit</button>
            </fieldset>
        </form>
    </div>
    <div class="section">
        <h3 class="title is-3">Tracked Runners</h3>
        <div id="runner_list" class="buttons">
            @foreach($webhook->trackers->sortBy('src_name') as $tracker)
                <button type="button" onclick="removeRunner(this)" data-tracker-id="{{$tracker->id}}"
                        @if($webhook->state === \App\Models\WebhookState::INVALIDATED)
                        disabled
                        aria-disabled="true" @endif
                        class="button is-danger mdi mdi-trash-can-outline has-text-weight-bold">{{$tracker->src_name}}</button>
            @endforeach
        </div>
        <fieldset id="frm_runners" @if($webhook->state === \App\Models\WebhookState::INVALIDATED) disabled
                  aria-disabled="true" @endif>
            <div class="field">
                <label class="label">Name</label>
                <div class="control">
                    <input id="frm_runnername" name="frm_runnername" class="input" type="text" required
                           aria-required="true"
                           title="The runners speedrun.com username"
                           pattern="([ ]*[A-Za-z0-9-_]+[ ]*)+"
                           placeholder="psychonauter">
                </div>
                <p class="help">The runners speedrun.com username.</p>
            </div>
            <button id="frm_runners_submit" type="button" onclick="addRunner()" class="button is-primary">Add
            </button>
        </fieldset>
    </div>
</div>
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    document.querySelector('#frm_runners').addEventListener("keydown", (e) => {
        console.log(e);

        if (e.keyCode == 13) {
            addRunner();
        }
    });

    function addRunner() {
        document.querySelector('#frm_runners_submit').classList.toggle('is-loading');

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.status === 200 && this.readyState === 4) {
                const data = JSON.parse(this.responseText);
                const node = document.createElement('span');
                node.setAttribute('data-tracker-id', data.id);
                node.setAttribute('class', 'button is-danger mdi mdi-trash-can-outline has-text-weight-bold');
                node.setAttribute('onclick', 'removeRunner(this)');
                node.innerHTML = data.src_name;
                document.querySelector('#runner_list').appendChild(node);
                document.querySelector('#frm_runners_submit').classList.toggle('is-loading');
                document.querySelector('#frm_runnername').value = null;
                toastr.success(`Runner ${data.src_name} added`);
            } else if (this.readyState === 4) {
                console.log(xhr.responseText);
                document.querySelector('#frm_runners_submit').classList.toggle('is-loading');
                toastr.error(`Error adding runner`);
            }
        };

        let payload = {
            runner: document.querySelector("#frm_runnername").value.trim(),
            _token: document.querySelector("input[name='_token']").value
        };

        xhr.open('PUT', `#`, true);
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(JSON.stringify(payload));
    }

    function removeRunner(e) {
        if (e.getAttribute('aria-disabled')) return;

        const id = e.getAttribute('data-tracker-id');
        if (!id) return;

        let xhr = new XMLHttpRequest();
        xhr.affectedNode = e;
        xhr.affectedNode.classList.toggle('is-loading');
        xhr.affectedNode.disabled = true;

        xhr.affectedNode = e;
        xhr.onreadystatechange = function () {
            if (this.status === 200 && this.readyState === 4) {
                this.affectedNode.remove();
                toastr.success('Runner removed');
            } else if (this.readyState === 4) {
                console.log(xhr.responseText);
                this.affectedNode.classList.toggle('is-loading');
                this.affectedNode.disabled = false;
                toastr.error('Failed to remove runner', xhr.statusText);
            }
        };
        let payload = {
            runner: document.querySelector("#frm_runnername").value.trim(),
            _token: document.querySelector("input[name='_token']").value
        };

        xhr.open('DELETE', `${window.location.href}/${id}`, true);
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(JSON.stringify(payload));
    }

    function submitForm() {
        document.querySelector('#frm_submit').classList.toggle('is-loading');
        document.querySelector('#frm').disabled = true;

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.status === 200 && this.readyState === 4) {
                let id = JSON.parse(xhr.responseText).id;
                toastr.success('Hook updated');
                document.querySelector('#frm_submit').classList.toggle('is-loading');
                document.querySelector('#frm').disabled = false;
            } else if (this.readyState === 4) {
                console.log(xhr.responseText);
                document.querySelector('#frm_submit').classList.toggle('is-loading');
                document.querySelector('#frm').disabled = false;
                toastr.error('Failed to update hook', xhr.statusText);
            }
        };

        const payload = {
            name: document.querySelector("#frm_name").value.trim(),
            description: document.querySelector("#frm_desc").value,
            state: document.querySelector("#frm_state").value,
            _token: document.querySelector("input[name='_token']").value
        };

        xhr.open('PATCH', `#`, true);
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(JSON.stringify(payload));
    }
</script>
</body>
</html>
