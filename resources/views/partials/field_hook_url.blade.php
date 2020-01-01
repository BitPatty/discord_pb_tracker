<div class="field">
    <label class="label">Discord URL</label>
    <div class="control">
        <input id="frm_url" name="frm_url" class="input" type="url" required aria-required="true"
               title="The webhook url"
               pattern="^[ ]*(https://discordapp\.com/api/webhooks[/a-zA-Z0-9\-_]+)[ ]*$"
               placeholder="https://discordapp.com/api/webhooks/..."
               @isset($value) value="{{ $value }}" @endisset
               @isset($disabled) disabled aria-disabled="true" @endisset
        >
    </div>
    <p class="help">The webhooks url, see <a
            href="https://support.discordapp.com/hc/en-us/articles/228383668-Intro-to-Webhooks"
            target="_blank" rel="noreferrer">here</a> on how you can set up a Discord webhook.</p>
</div>
