<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Dispatch Details for Order #{{ $order->id }}</title>
</head>
<body>
    <h1>Add Dispatch Details for Order #{{ $order->id }}</h1>

    <form action="{{ route('seller.orders.dispatch.store', $order) }}" method="POST">
        @csrf
        <div>
            <label for="courier_name">Courier Name</label>
            <input type="text" name="courier_name" id="courier_name" required>
        </div>
        <div>
            <label for="tracking_number">Tracking Number</label>
            <input type="text" name="tracking_number" id="tracking_number" required>
        </div>
        <div>
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes"></textarea>
        </div>
        <button type="submit">Add Dispatch Details</button>
    </form>
</body>
</html>
