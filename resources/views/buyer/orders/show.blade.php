<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->id }}</title>
</head>
<body>
    <h1>Order #{{ $order->id }}</h1>

    <p><strong>Status:</strong> {{ $order->status }}</p>
    <p><strong>Total Price:</strong> {{ $order->total_price }}</p>

    <h2>Products</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->pivot->price }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
