<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyapar India - B2B Marketplace</title>
</head>
<body>
    <h1>Welcome to Vyapar India</h1>

    <nav>
        @guest
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
        @else
            @if (Auth::user()->is_admin())
                <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            @elseif (Auth::user()->is_seller())
                <a href="{{ route('seller.dashboard') }}">Seller Dashboard</a>
            @elseif (Auth::user()->is_buyer())
                <a href="{{ route('buyer.dashboard') }}">Buyer Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @endguest
    </nav>

    <form action="{{ route('search') }}" method="GET">
        <input type="text" name="query" placeholder="Search products...">
        <button type="submit">Search</button>
    </form>

    <form action="{{ route('search_sellers_by_city') }}" method="GET">
        <input type="text" name="city" placeholder="Search sellers by city...">
        <button type="submit">Search Sellers</button>
    </form>


    <h2>Products</h2>
    <div>
        @foreach ($products as $product)
            <div>
                <h3><a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                <p>{{ $product->price }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>
