<div class="col-lg-12">
    <div class="form-group">
        <label for="full_name">{{ __('Full Name') }}:</label>
        <p id="full_name" class="form-control">{{ $member->full_name }}</p>
    </div>
    <div class="form-group">
        <label for="phone_number">{{ __('Phone Number (WhatsApp)') }}:</label>
        <p id="phone_number" class="form-control">{{ $member->contact_number }}</p>
    </div>
    <div class="form-group">
        <label for="email">{{ __('Email Address (used in pelataran account)') }}:</label>
        <p id="email" class="form-control">{{ $member->email }}</p>
    </div>
    <div class="form-group">
        <label for="city">{{ __('Domicile (City)') }}:</label>
        <p id="city" class="form-control">{{ $member->domisili }}</p>
    </div>
    <div class="form-group">
        <label for="corporation">{{ __('Instance / Company Name') }}:</label>
        <p id="corporation" class="form-control">{{ $member->corporation }}</p>
    </div>
</div>
