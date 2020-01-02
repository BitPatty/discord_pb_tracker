<!DOCTYPE html>
<html lang="en">
@include('partials.html_head', ['title' => 'Add new Hook'])
<body class="is-widescreen">
<main class="container">
    @include('partials.page_title')
    <h2 class="subtitle is-2">Add New Hook</h2>

    <nav class="breadcrumb" aria-label="breadcrumbs">
        <ul>
            <li><a href="/">PB Tracker</a></li>
            <li><a href="/dashboard">Dashboard</a></li>
            <li class="is-active"><a href="#" aria-current="page">Add New Hook</a></li>
        </ul>
    </nav>

    <form action="javascript:submitForm()">
        <fieldset id="frm">
            @csrf
            @include('partials.field_hook_name')
            @include('partials.field_hook_url')
            @include('partials.field_hook_desc')
            <button id="frm_submit" type="submit" class="button is-primary">Submit</button>
        </fieldset>
    </form>
</main>
</body>
<script
    src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    function submitForm() {
        document.querySelector('#frm_submit').classList.toggle('is-loading');
        document.querySelector('#frm').disabled = true;

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.status === 200 && this.readyState === 4) {
                let id = JSON.parse(xhr.responseText).id;
                window.location = `/dashboard/edit/${id}`;
                toastr.success('Hook added');
            } else if (this.readyState === 4) {
                console.log(xhr.responseText);
                let errorMessage = xhr.statusText;
                try {
                    const msg = JSON.parse(xhr.responseText).message;
                    if (msg) errorMessage = msg;
                } catch {
                }
                document.querySelector('#frm_submit').classList.toggle('is-loading');
                document.querySelector('#frm').disabled = false;
                toastr.error(errorMessage, 'Failed to create hook');
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
</html>
