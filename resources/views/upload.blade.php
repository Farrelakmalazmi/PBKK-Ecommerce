@extends('layouts.app')

@section('content')
<main class="pt-90">
    <section class="upload-section container">
        <h1 class="text-uppercase section-title fw-normal text-center mb-4">
            Upload and Classify Your Image
        </h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4">
                    <form id="uploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-4">
                            <label for="file" class="form-label">Choose an Image</label>
                            <input type="file" name="file" id="file" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Upload and Classify</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="classification-result" class="mt-5">
            <h3 class="text-center mb-4" id="classification-title" style="display: none;">Products Related to Classification</h3>
            <div class="row" id="product-list">
                <!-- Produk akan ditambahkan secara dinamis -->
            </div>
            <div id="no-products-alert" style="display: none;">
                <div class="alert alert-warning">No products found for this classification.</div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('styles')
<style>
    .upload-section {
        padding: 60px 15px;
    }

    .card {
        border-radius: 10px;
        border: 1px solid #eaeaea;
        background-color: #f9f9f9;
    }

    #product-list .product-card-wrapper {
        margin-bottom: 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#uploadForm').submit(function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: "{{ route('upload.image') }}", // Laravel route untuk upload dan klasifikasi
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function (data) {
                    if (data.success) {
                        const classification = data.classification.predictions[0].class; // Ambil class hasil klasifikasi
                        fetchProductsByClass(classification); // Cari produk berdasarkan class
                    } else {
                        alert(data.error);
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Failed to upload and classify the image.');
                }
            });
        });

        // Fungsi untuk mencari produk berdasarkan class
        function fetchProductsByClass(classification) {
            $.ajax({
                url: "{{ route('home.search') }}", // Route pencarian produk
                method: "GET",
                data: { query: classification }, // Kirim class hasil klasifikasi sebagai query
                success: function (data) {
                    console.log('Products received:', data);

                    if (!data || data.length === 0) {
                        $('#classification-title').hide();
                        $('#no-products-alert').show();
                        return;
                    }

                    $('#classification-title').show();
                    $('#no-products-alert').hide();
                    $('#product-list').html(''); // Kosongkan daftar produk sebelumnya

                    data.forEach(function (product) {
                        const productCard = `
                            <div class="col-md-4 product-card-wrapper">
                                <div class="product-card shadow-sm p-3">
                                    <a href="{{ route('shop.product.details', ['product_slug' => '__SLUG__']) }}"
                                       class="d-block">
                                        <img src="{{ asset('uploads/products') }}/${product.image || 'default.jpg'}" 
                                             class="w-100 mb-3" alt="${product.name || 'Unnamed Product'}">
                                    </a>
                                    <h5 class="mb-1">
                                        <a href="{{ route('shop.product.details', ['product_slug' => '__SLUG__']) }}"
                                           class="text-dark">${product.name || 'Unnamed Product'}</a>
                                    </h5>
                                    <p class="text-muted mb-2">${product.category?.name || 'Unknown Category'}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="price fw-bold">$${product.sale_price || product.regular_price || 'N/A'}</span>
                                        <form method="POST" action="{{ route('cart.add') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="${product.id}">
                                            <input type="hidden" name="name" value="${product.name}">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="price" value="${product.sale_price || product.regular_price}">
                                            <button type="submit" class="btn btn-sm btn-primary">Add to Cart</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        `.replace(/__SLUG__/g, product.slug || '#');

                        $('#product-list').append(productCard);
                    });
                },
                error: function (xhr) {
                    console.error('Error fetching products:', xhr.responseText);
                    $('#product-list').html('<div class="alert alert-danger">Failed to load products.</div>');
                }
            });
        }
    });
</script>
@endpush
