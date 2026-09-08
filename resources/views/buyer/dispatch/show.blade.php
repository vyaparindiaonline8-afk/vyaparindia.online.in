<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Details for Order #{{ $order->id }}</title>
</head>
<body>
    <h1>Dispatch Details for Order #{{ $order->id }}</h1>

    @if ($order->dispatch)
        <p><strong>Courier Name:</strong> {{ $order->dispatch->courier_name }}</p>
        <p><strong>Tracking Number:</strong> {{ $order->dispatch->tracking_number }}</p>
        <p><strong>Notes:</strong> {{ $order->dispatch->notes }}</p>
    @else
        <p>No dispatch details available yet.</p>
    @endif
</body>
</html>
