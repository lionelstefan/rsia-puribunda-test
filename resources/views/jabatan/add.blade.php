@extends('master')
@section('title_page', 'Jabatan')
@section('header_page', 'Create Jabatan')
@section('content')

    <form enctype="multipart/form-data" id="add-jabatan-form" name="add-jabatan-form" class="mt-4">
        @csrf
        <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
        </div>
        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Nama Jabatan <span class="text-danger"> *</span></label>
            <div class="col-sm-6 col-12">
                <input class="form-control form-control-sm" type="text" name="name" id="name" required>
            </div>
        </div>

        <div class="mt-3">
            <a href='{{ URL::previous() }}' class='btn btn-danger btn-sm'>Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm add">Submit</button>
        </div>
    </form>
@endsection

@section('script_js')
    <!-- jquery and js plugin cdn -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4/default.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.add').click(function(e) {
                $('.print-error-msg').empty();
                $('.print-error-msg').hide();
                e
                    .preventDefault(); // Prevent the href from redirecting directly                                       
                var form_data = new FormData($('#add-jabatan-form')[0]);
                //kosongin dulu element show message setiap trigger create
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            contentType: false,
                            processData: false,
                            url: '{{ route('jabatan.add') }}',
                            data: form_data,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Add Data Success',
                                    icon: 'success',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'Ok',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location = response.url;
                                    }
                                });
                            },
                            error: function(data) {
                                Swal.fire(
                                    'Failed',
                                    'Your data failed to created.',
                                    'error'
                                );
                                let response = data.responseJSON;
                                let all_errors = response.errors;

                                console.log('all_errors', all_errors);

                                $(".print-error-msg").css('display', 'block');

                                $.each(all_errors, function(key, value) {
                                    $('.print-error-msg').append(
                                        `<li style="color:red">${value}</li>`
                                    );
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
