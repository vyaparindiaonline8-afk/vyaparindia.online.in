<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->id }}</title>
</head>
<body>
    <h1>Order #{{ $order->id }}</h1>

    <p><strong>Buyer:</strong> {{ $order->buyer->name }}</p>
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
                @if ($product->user_id == Auth::id())
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->pivot->price }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <h2>Update Status</h2>
    <form action="{{ route('seller.orders.updateStatus', $order) }}" method="POST">
        @csrf
        @method('PUT')
        <select name="status">
            <option value="pending" @if($order->status == 'pending') selected @endif>Pending</option>
            <option value="processing" @if($order->status == 'processing') selected @endif>Processing</option>
            <option value="shipped" @if($order->status == 'shipped') selected @endif>Shipped</option>
            <option value="delivered" @if($order->status == 'delivered') selected @endif>Delivered</option>
            <option value="cancelled" @if($order->status == 'cancelled') selected @endif>Cancelled</option>
        </select>
        <button type="submit">Update Status</button>
    </form>
</body>
</html>
