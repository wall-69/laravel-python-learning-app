<div class="alerts-container">
    @session('success')
        <x-alert type="success" :message="session('success')"></x-alert>
    @endsession
    @session('warning')
        <x-alert type="warning" :message="session('warning')"></x-alert>
    @endsession
    @session('danger')
        <x-alert type="danger" :message="session('danger')"></x-alert>
    @endsession
</div>
