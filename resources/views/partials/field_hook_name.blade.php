<div class="field">
    <label for="frm_name" class="label">Name</label>
    <div class="control">
        <input id="frm_name" name="frm_name" class="input" type="text" required aria-required="true"
               minlength="1"
               maxlength="40"
               title="The webhook name"
               pattern="([ ]*[^ ]+[ ]*)+"
               placeholder="Captain Hook"
               @isset($value) value="{{ $value }}" @endisset
        >
    </div>
    <p class="help">The display name of your hook.</p>
</div>
