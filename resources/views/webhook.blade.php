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
        <h3 class="title is-3">{{$webhook->name}}</h3>

        <div class="section">
            <div class="media">
                <figure class="media-left">
                    <p class="image is-64x64">
                        <img class="image is-64x64 is-rounded" src="{{$webhook->avatar_url}}">
                    </p>
                </figure>
                <div class="media-content">
                    <div class="content">
                        <p>
                            {{$webhook->description}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section">
        <h3 id="edit-hook" class="title is-3">Edit Hook</h3>
        <form action="javascript:submitForm()">
            @csrf
            <fieldset id="frm">
                <div class="field">
                    <label class="label">Name</label>
                    <div class="control">
                        <input id="frm_name" name="frm_name" class="input" type="text" required aria-required="true"
                               title="The webhook name"
                               pattern="([ ]*[A-Za-z0-9]+[ ]*)+"
                               placeholder="Captain Hook" value="{{$webhook->name}}">
                    </div>
                    <p class="help"></p>
                </div>
                <div class="field">
                    <label class="label">Discord URL</label>
                    <div class="control">
                        <input id="frm_url" name="frm_url" class="input" type="url" required aria-required="true"
                               title="The webhook url"
                               pattern="^[ ]*(https://discordapp\.com/api/webhooks[/a-zA-Z0-9\-_]+)[ ]*$"
                               value="{{$webhook->url}}" disabled aria-disabled="true">
                    </div>
                </div>
                <div class="field ">
                    <label class="label">Description</label>
                    <div class="field">
                        <div class="control">
                        <textarea id="frm_desc" name="frm_desc" class="textarea"
                                  placeholder="PB Tracker for my server"
                                  maxlength="2048">{{$webhook->description}}</textarea>
                        </div>
                    </div>
                </div>
                <button id="frm_submit" type="submit" class="button is-primary">Submit</button>
            </fieldset>
        </form>
    </div>
</div>

<script>
    function submitForm() {
        document.querySelector('#frm').disabled = true;

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.status === 200 && this.readyState === 4) {
                let id = JSON.parse(xhr.responseText).id;
                window.location = `/dashboard/edit/${id}`;
            } else if (this.readyState === 4) {
                console.log(xhr.responseText);
                document.querySelector("#frm").disabled = false;
            }
        };
        let payload = {
            name: document.querySelector("#frm_name").value.trim(),
            description: document.querySelector("#frm_desc").value,
            _token: document.querySelector("input[name='_token']").value
        };

        xhr.open('PATCH', `#`, true);
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(JSON.stringify(payload));
    }
</script>
</body>
</html>
