<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiry for {{ $product->name }}</title>
</head>
<body>
    <h1>Enquiry for {{ $product->name }}</h1>

    <form action="{{ route('buyer.products.enquiry.store', $product) }}" method="POST">
        @csrf
        <div>
            <label for="message">Message</label>
            <textarea name="message" id="message" required></textarea>
        </div>
        <button type="submit">Send Enquiry</button>
    </form>
</body>
</html>
