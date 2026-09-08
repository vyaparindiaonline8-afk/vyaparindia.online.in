<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
</head>
<body>
    <h1>Create Order</h1>

    <form action="{{ route('buyer.orders.store') }}" method="POST">
        @csrf
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <input type="number" name="products[{{ $product->id }}]" value="0" min="0">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit">Place Order</button>
    </form>
</body>
</html>
