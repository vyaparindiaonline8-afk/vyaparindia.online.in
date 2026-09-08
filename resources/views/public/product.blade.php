<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
</head>
<body>
    <h1>{{ $product->name }}</h1>

    <div>
        @if ($product->image)
            <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" width="300">
        @endif
        <p><strong>Price:</strong> {{ $product->price }}</p>
        <p><strong>Category:</strong> {{ $product->category->name }}</p>
        <p><strong>Seller:</strong> {{ $product->seller->name }}</p>
        <p><strong>Description:</strong></p>
        <p>{{ $product->description }}</p>

        @auth
            @if (Auth::user()->is_buyer())
                <a href="{{ route('buyer.products.enquiry.create', $product) }}">Make an Enquiry</a>
            @endif
        @endguest
    </div>

    <a href="{{ route('home') }}">Back to Home</a>
</body>
</html>
