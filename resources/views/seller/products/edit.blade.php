<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>
<body>
    <h1>Edit Product</h1>

    <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ $product->name }}" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" required>{{ $product->description }}</textarea>
        </div>
        <div>
            <label for="price">Price</label>
            <input type="number" name="price" id="price" step="0.01" value="{{ $product->price }}" required>
        </div>
        <div>
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if($product->category_id == $category->id) selected @endif>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="image">Image</label>
            <input type="file" name="image" id="image">
            @if ($product->image)
                <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" width="100">
            @endif
        </div>
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
