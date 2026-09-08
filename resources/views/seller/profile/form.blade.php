<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Profile</title>
</head>
<body>
    <h1>{{ isset($profile) ? 'Edit' : 'Create' }} Seller Profile</h1>

    <form action="{{ isset($profile) ? route('seller.profile.update') : route('seller.profile.store') }}" method="POST">
        @csrf
        @if(isset($profile))
            @method('PUT')
        @endif

        <div>
            <label for="company_name">Company Name</label>
            <input type="text" name="company_name" id="company_name" value="{{ $profile->company_name ?? '' }}" required>
        </div>
        <div>
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" value="{{ $profile->phone_number ?? '' }}" required>
        </div>
        <div>
            <label for="address">Address</label>
            <textarea name="address" id="address" required>{{ $profile->address ?? '' }}</textarea>
        </div>
        <div>
            <label for="city">City</label>
            <input type="text" name="city" id="city" value="{{ $profile->city ?? '' }}" required>
        </div>
        <div>
            <label for="state">State</label>
            <input type="text" name="state" id="state" value="{{ $profile->state ?? '' }}" required>
        </div>
        <div>
            <label for="country">Country</label>
            <input type="text" name="country" id="country" value="{{ $profile->country ?? '' }}" required>
        </div>
        <div>
            <label for="gst_number">GST Number</label>
            <input type="text" name="gst_number" id="gst_number" value="{{ $profile->gst_number ?? '' }}">
        </div>
        <div>
            <label for="dispatch_radius">Dispatch Radius (in KM)</label>
            <input type="number" name="dispatch_radius" id="dispatch_radius" value="{{ $profile->dispatch_radius ?? '' }}">
        </div>
        <button type="submit">{{ isset($profile) ? 'Update' : 'Create' }} Profile</button>
    </form>
</body>
</html>
