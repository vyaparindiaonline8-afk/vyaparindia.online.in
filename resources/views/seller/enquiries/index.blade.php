<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enquiries</title>
</head>
<body>
    <h1>My Enquiries</h1>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Buyer</th>
                <th>Message</th>
                <th>Reply</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($enquiries as $enquiry)
                <tr>
                    <td>{{ $enquiry->product->name }}</td>
                    <td>{{ $enquiry->buyer->name }}</td>
                    <td>{{ $enquiry->message }}</td>
                    <td>
                        @if ($enquiry->reply_message)
                            {{ $enquiry->reply_message }}
                        @else
                            <form action="{{ route('seller.enquiries.reply', $enquiry) }}" method="POST">
                                @csrf
                                <textarea name="reply_message" rows="3" required></textarea>
                                <button type="submit">Reply</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        @if ($enquiry->reply_message)
                            <!-- Option to edit reply if needed -->
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
