@extends('seller-site.layout')

@section('content')
    <h2>About Us</h2>
    <h3>Company: {{ $sellerPage->user->sellerProfile->company_name ?? 'N/A' }}</h3>
    <p>Address: {{ $sellerPage->user->sellerProfile->address ?? 'N/A' }}, {{ $sellerPage->user->sellerProfile->city ?? 'N/A' }}, {{ $sellerPage->user->sellerProfile->state ?? 'N/A' }}, {{ $sellerPage->user->sellerProfile->country ?? 'N/A' }}</p>
    <p>Phone: {{ $sellerPage->user->sellerProfile->phone_number ?? 'N/A' }}</p>
    <p>GST Number: {{ $sellerPage->user->sellerProfile->gst_number ?? 'N/A' }}</p>
    <p>Dispatch Radius: {{ $sellerPage->user->sellerProfile->dispatch_radius ?? 'N/A' }} KM</p>
@endsection
