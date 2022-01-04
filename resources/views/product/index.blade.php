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
        <div class="row gy-4 mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Add Product
                    </div>
                    <div class="card-body">
                        <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
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
                                    ADD
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
                        <table class="table table-hover" id="productTable">
                            <thead>
                                <th>#</th>
                                <th>Product Image</th>
                                <th>Product Name</th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Toastr JS CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- DataTables JS CDN -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(function() {

            $('form').submit(function(e) {
                e.preventDefault();

                $(this).find('button[type=submit]').text('Adding...').prop('disabled', true).prepend('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ');

                var form = this;

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
                        } else if (data.code == 1 && data.status == 'success') {
                            $('#productTable').DataTable().ajax.reload(null, false);
                            $(form)[0].reset();
                            $('#img_holder').attr('src', 'https://dummyimage.com/200x200/cccccc/969696.png&text=Preview');
                            toastr[data.status](data.msg);
                        }
                        $('form').find('button[type=submit]').text('Add').prop('disabled', false);
                    }
                });
            });

            // Fetch Products


            $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                info: true,
                ajax: "{{route('product.fetch')}}",
                "pageLength": 5,
                "aLengthMenu": [
                    [5, 10, 25, 50, -1],
                    [5, 10, 25, 50, "All"]
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: "align-middle"
                    },
                    {
                        data: 'product_image',
                        name: 'product_image',
                        className: "align-middle",
                        render: function(data, type, full, meta) {
                            return '<img class="img-fluid img-thumbnail" src="/storage/files/products/' + data + '" width="50px"/>';
                        }
                    },
                    {
                        data: 'product_name',
                        name: 'product_name',
                        className: "align-middle"
                    },
                ]
            });

        });


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