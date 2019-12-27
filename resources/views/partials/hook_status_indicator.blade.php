@switch($state)
    @case(\App\Models\WebhookState::ACTIVE) <span class="tag is-success is-light">Active</span> @break
    @case(\App\Models\WebhookState::CREATED) <span class="tag is-info is-light">Created</span> @break
    @case(\App\Models\WebhookState::DEAD) <span class="tag is-warning is-light">Inactive</span> @break
    @case(\App\Models\WebhookState::INVALIDATED) <span class="tag is-danger is-light">Invalid</span> @break
@endswitch
