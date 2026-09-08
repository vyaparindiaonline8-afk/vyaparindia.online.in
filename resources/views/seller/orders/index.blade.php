<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
</head>
<body>
    <h1>My Orders</h1>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Buyer</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->buyer->name }}</td>
                    <td>{{ $order->total_price }}</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        <a href="{{ route('seller.orders.show', $order) }}">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
