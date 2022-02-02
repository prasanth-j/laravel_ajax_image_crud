<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ajax Image CRUD</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">

    <!-- Toastr CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <!-- DataTables CSS CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" />
</head>

<body>
    <div class="container">
        <div class="row gy-4 my-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Add Product
                    </div>
                    <div class="card-body">
                        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="addProductForm">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="product_name">Product Name</label>
                                <input type="text" class="form-control" name="product_name" id="product_name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="product_image">Product Image</label>
                                <input type="file" accept="image/*" class="form-control" name="product_image" id="product_image" onchange="document.getElementById('img_holder').src = window.URL.createObjectURL(this.files[0])">
                            </div>
                            <div class="mb-3">
                                <img class="img-thumbnail rounded mx-auto d-block" width="150px" id="img_holder" src="https://dummyimage.com/200x200/cccccc/969696.png&text=Preview" alt="Preview Image">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Product List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="productTable">
                                <thead>
                                    <th>#</th>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Action</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('product.update') }}" method="POST" enctype="multipart/form-data" id="editProductForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="edit_product_id" id="edit_product_id">
                        <div class="form-group mb-3">
                            <label for="edit_product_name">Product Name</label>
                            <input type="text" class="form-control" name="edit_product_name" id="edit_product_name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_product_image">Product Image</label>
                            <input type="file" accept="image/*" class="form-control" name="edit_product_image" id="edit_product_image" onchange="document.getElementById('edit_img_holder').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <img class="img-thumbnail rounded mx-auto d-block" width="150px" id="edit_img_holder" src="https://dummyimage.com/200x200/cccccc/969696.png&text=Preview" alt="Preview Image">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- DataTables JS CDN -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('/js/app.js') }}"></script>

    <script>
        $(function() {

            // Pusher realtime ajax reload
            Echo.channel('product-update')
                .listen('ProductUpdate', (e) => {
                    $('#productTable').DataTable().ajax.reload(null, false);
                    toastr["success"]('<div><img src="/storage/files/products/' + e.productImg + '" class="rounded img-fluid img-thumbnail" width="50px" alt="' + e.productName + ' Logo"> ' + e.productMsg + ' product ' + e.productName + '.</div>');
                });

            // Fetch Products
            $('#productTable').DataTable({
                processing: false,
                serverSide: true,
                info: true,
                ajax: "{{route('product.index')}}",
                "pageLength": 5,
                "aLengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "align-middle",
                        orderable: true,
                        bSearchable: false,
                    },
                    {
                        data: 'product_image',
                        name: 'product_image',
                        className: "align-middle",
                        orderable: false,
                        bSearchable: false,
                        render: function(data, type, full, meta) {
                            return '<img class="img-fluid img-thumbnail" src="/storage/files/products/' + data + '" width="50px"/>';
                        }
                    },
                    {
                        data: 'product_name',
                        name: 'product_name',
                        className: "align-middle",
                        orderable: true,
                        bSearchable: true,
                    },
                    {
                        data: 'id',
                        name: 'id',
                        className: "align-middle",
                        orderable: false,
                        bSearchable: false,
                        render: function(data, type, full, meta) {
                            return '<div class="btn-group btn-group-sm" role="group">\
                                        <button type="button" class="btn btn-primary" data-id ="' + data + '" id="editBtn">Edit</button>\
                                        <button type="button" class="btn btn-danger" data-id ="' + data + '" id="deleteBtn">Delete</button>\
                                    </div>';
                        }
                    }
                ]
            });

            // Add & Update Product
            $('form').submit(function(e) {
                e.preventDefault();
                $(this).find('button[type=submit]').prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');

                var form = this;

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: $(form).attr('action'),
                    method: $(form).attr('method'),
                    data: new FormData(form),
                    processData: false,
                    dataType: 'json',
                    contentType: false,
                    beforeSend: function() {
                        $(form).find('div.invalid-feedback').remove();
                        $(form).find('input').removeClass('is-invalid');
                    },
                    success: function(data) {
                        if (data.code == 0 && data.status == 'error') {
                            $.each(data.errors, function(key, value) {
                                $(form).find('input#' + key).addClass('is-invalid').after('<div class="invalid-feedback">' + value + '</div>');
                            });
                        } else if (data.code == 0 && data.status == 'warning') {
                            toastr[data.status](data.msg);
                        } else if (data.code == 1 && data.status == 'success' && data.method == 'store') {
                            $('#productTable').DataTable().ajax.reload(null, false);
                            $(form)[0].reset();
                            $('#img_holder').attr('src', 'https://dummyimage.com/200x200/cccccc/969696.png&text=Preview');
                            toastr[data.status](data.msg);
                        } else if (data.code == 1 && data.status == 'success' && data.method == 'update') {
                            $('#productTable').DataTable().ajax.reload(null, false);
                            $(form)[0].reset();
                            $('#edit_img_holder').attr('src', 'https://dummyimage.com/200x200/cccccc/969696.png&text=Preview');
                            toastr[data.status](data.msg);
                            $('#editProductModal').modal('hide');
                        } else {
                            toastr["error"]("Unknown error! Product not " + data.method + "d.");
                        }

                        $('#addProductForm').find('button[type=submit]').text('Add').prop('disabled', false);
                        $('#editProductForm').find('button[type=submit]').text('Update').prop('disabled', false);
                    },
                    error: function(data) {
                        toastr["error"]("Something went wrong!");
                    }
                });
            });

            // Edit Product
            $(document).on('click', '#editBtn', function() {
                var productId = $(this).data('id');
                $('#editProductModal').find('form')[0].reset();
                $.get('product/' + productId + '/edit', function(data) {
                    $('#edit_product_id').val(data.product.id);
                    $('#edit_product_name').val(data.product.product_name);
                    $('#edit_img_holder').attr('src', '/storage/files/products/' + data.product.product_image);
                    $('#editProductModal').modal('show');
                }, 'json');
            });

            // Delete Product
            $(document).on('click', '#deleteBtn', function() {
                var productId = $(this).data('id');
                let confirmText = "Are You sure! You want to delete?";

                if (confirm(confirmText) == true) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('product')}}" + '/' + productId,
                        success: function(data) {
                            if (data.code == 1 && data.status == 'success' && data.method == 'destroy') {
                                $('#productTable').DataTable().ajax.reload(null, false);
                                toastr[data.status](data.msg);
                            } else if (data.code == 0 && data.status == 'warning' && data.method == 'destroy') {
                                $('#productTable').DataTable().ajax.reload(null, false);
                                toastr[data.status](data.msg);
                            } else {
                                toastr["error"]("Unknown error! Product not deleted.");
                            }
                        },
                        error: function(data) {
                            toastr["error"]("Something went wrong!");
                        }
                    });
                }
            });
        });


        // Toastr Configurations
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "100",
            "hideDuration": "500",
            "timeOut": "3000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    </script>
</body>

</html>