<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
</head>
<body>
    <h1>Search Results for "{{ request('query') }}"</h1>

    @if ($products->isEmpty())
        <p>No products found matching your query.</p>
    @else
        <div>
            @foreach ($products as $product)
                <div>
                    <h3><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                    <p>{{ $product->price }}</p>
                    <p>{{ Str::limit($product->description, 100) }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('home') }}">Back to Home</a>
</body>
</html>
