@extends('master')
@section('title_page', 'Add Promo')
@section('header_page', 'Create Promo')

@section('content')

    <form method="POST" enctype="multipart/form-data" class="mt-4" id="add-promo-form" name="add-promo-form">
        @csrf
        <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
        </div>
        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Title <span class="text-danger"> *</span></label>
            <div class="col-sm-6 col-12">
                <input class="form-control form-control-sm" type="text" name="title" id="title" required>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="venue_location" class="col-sm-2 col-form-label fw-normal">Venue <span class="text-danger">
                    *</span></label>
            <div class="col-sm-6 col-12">
                <select name="venue_location" id="venue_location" class="form-control">
                    <option value="">Select Location</option>
                    @foreach ($dataVenue as $data)
                        <option value="{{ $data['key'] }}">{{ $data['data']['title_venue'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Start Date <span class="text-danger">
                    *</span></label>
            <div class="col-md-3 col-6">
                <div class="col-sm-12">
                    <input class="form-control form-control-sm" type="text" name="sDate" id="sDate" required />
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="input-group mb-3">
                    <div class="col-sm-6 col-8">
                        <input class="form-control form-control-sm" type="text" name="sTime" id="sTime" required />
                    </div>
                    <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                </div>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">End Date <span class="text-danger">
                    *</span></label>
            <div class="col-md-3 col-6">
                <div class="col-sm-12">
                    <input class="form-control form-control-sm" type="text" name="eDate" id="eDate" required />
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="input-group mb-3">
                    <div class="col-sm-6 col-8">
                        <input class="form-control form-control-sm" type="text" name="eTime" id="eTime" required />
                    </div>
                    <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                </div>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Upload Image <span class="text-danger">
                    *</span></label>
            <div class="col-sm-5 col-12">
                <input class="form-control form-control-sm" type="file" name="file" id="file" required>
                <span><i class="fs-6 text-muted">File Extension: Img/Png, Max File Size: 2 Mb</i></span>
            </div>
        </div>
        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Description <span class="text-danger">
                    *</span></label>
            <div class="col-sm-6 col-12">
                <textarea class="form-control form-control-sm" name="desc" id="desc" required></textarea>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/jquery-clockpicker.min.js">
    </script>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4/default.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            $(function() {
                $('input[name="sDate"]').daterangepicker({
                    locale: {
                        format: 'DD-MMM-YYYY'
                    },
                    autoApply: true,
                    singleDatePicker: true,
                    showDropdowns: true,
                    minDate: new Date(),
                    minYear: new Date(),
                });

                $('input[name="eDate"]').daterangepicker({
                    locale: {
                        format: 'DD-MMM-YYYY'
                    },
                    autoApply: true,
                    singleDatePicker: true,
                    showDropdowns: true,
                    minYear: new Date(),
                    minDate: new Date(),
                });

                $('input[name="sTime"]').clockpicker({
                    autoclose: true
                });

                $('input[name="eTime"]').clockpicker({
                    autoclose: true
                });
            });
            $('.add').click(function(e) {
                $('.print-error-msg').empty();
                $('.print-error-msg').hide();
                e
            .preventDefault(); // Prevent the href from redirecting directly                                       
                var form_data = new FormData($('#add-promo-form')[0]);
                //kosongin dulu element show message setiap trigger create
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, create it!',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'POST',
                            contentType: false,
                            processData: false,
                            url: '{{ route('requestPromo.post') }}',
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
