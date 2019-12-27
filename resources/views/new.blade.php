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

    <title>Add New Hook</title>
</head>
<body class="is-widescreen level">
<div class="container">
    <h1 class="title is-1">
        Discord PB Tracker
    </h1>
    <h2 class="subtitle is-2">Add New Hook</h2>

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">PB Tracker</a></li>
            <li><a href="/dashboard">Dashboard</a></li>
            <li class="is-active"><a href="#" aria-current="page">Add New Hook</a></li>
        </ul>
    </nav>

    <form action="javascript:submitForm()">
        @csrf
        <fieldset id="frm">
            <div class="field">
                <label class="label">Name</label>
                <div class="control">
                    <input id="frm_name" name="frm_name" class="input" type="text" required aria-required="true"
                           title="The webhook name"
                           pattern="([ ]*[A-Za-z0-9]+[ ]*)+"
                           placeholder="Captain Hook">
                </div>
                <p class="help">The display name of your hook</p>
            </div>
            <div class="field">
                <label class="label">Discord URL</label>
                <div class="control">
                    <input id="frm_url" name="frm_url" class="input" type="url" required aria-required="true"
                           title="The webhook url"
                           pattern="^[ ]*(https://discordapp\.com/api/webhooks[/a-zA-Z0-9\-_]+)[ ]*$"
                           placeholder="https://discordapp.com/api/webhooks/...">
                </div>
                <p class="help">The webhook url.</p>
            </div>
            <div class="field ">
                <label class="label">Description</label>
                <div class="field">
                    <div class="control">
                        <textarea id="frm_desc" name="frm_desc" class="textarea"
                                  placeholder="PB Tracker for my server" maxlength="2048"></textarea>
                    </div>
                </div>
            </div>
            <button id="frm_submit" type="submit" class="button is-primary">Submit</button>
        </fieldset>
    </form>
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
            url: document.querySelector("#frm_url").value.trim(),
            description: document.querySelector("#frm_desc").value,
            _token: document.querySelector("input[name='_token']").value
        };

        xhr.open('POST', `#`, true);
        xhr.setRequestHeader('Content-type', 'application/json');
        xhr.send(JSON.stringify(payload));
    }
</script>
</body>
</html>
