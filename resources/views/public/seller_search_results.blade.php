<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Search Results for "{{ $city }}"</title>
</head>
<body>
    <h1>Seller Search Results for "{{ $city }}"</h1>

    @if ($sellers->isEmpty())
        <p>No sellers found in "{{ $city }}".</p>
    @else
        <div>
            @foreach ($sellers as $seller)
                <div>
                    <h3>{{ $seller->name }} ({{ $seller->sellerProfile->company_name ?? 'N/A' }})</h3>
                    <p>City: {{ $seller->sellerProfile->city ?? 'N/A' }}</p>
                    <p>Dispatch Radius: {{ $seller->sellerProfile->dispatch_radius ?? 'N/A' }} KM</p>
                    {{-- Add a link to the seller's mini-site once implemented --}}
                    <a href="#">View Seller Profile</a> 
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('home') }}">Back to Home</a>
</body>
</html>
