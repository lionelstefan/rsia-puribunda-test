@extends ('master')
@section('title_page', 'Karyawan')
@section('header_page', 'Edit Karyawan')

@section('content')

    <form method="POST" enctype="multipart/form-data" class="mt-4" id="edit-karyawan-form" name="edit-karyawan-form">
        @csrf
        <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
        </div>
        <input type="hidden" id="id" name="id" value="{{ $karyawan['id'] }}">
        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Nama Karyawan <span class="text-danger">
                    *</span></label>
            <div class="col-sm-6 col-12">
                <input class="form-control form-control-sm" type="text" name="name" id="name"
                    value="{{ $karyawan['name'] }}" required>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Username <span class="text-danger"> *</span></label>
            <div class="col-sm-6 col-12">
                <input class="form-control form-control-sm" type="text" name="username" id="username" value="{{ $karyawan['username'] }}" required>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Password <span class="text-danger"> *</span></label>
            <div class="col-sm-6 col-12">
                <input class="form-control form-control-sm" type="text" name="password" id="password" required>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Unit <span class="text-danger"> *</span></label>
            <div class="col-sm-6 col-12">
                <select name="unit" class="unit-dropdown" required>
                    @foreach ($unit as $data)
                        <option value="{{ $data['id'] }}" @php echo $data['id'] === $karyawan['unit_id'] ? 'selected' : ''; @endphp>{{ $data['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row form-group mb-2">
            <label for="title" class="col-sm-2 col-form-label fw-normal">Jabatan <span class="text-danger"> *</span></label>
            <div class="col-sm-6 col-12">
                <select name="jabatan[]"  class="jabatan-dropdown" multiple="multiple" required>
                    @foreach ($jabatan as $data)
                        <option value="{{ $data['id'] }}">{{ $data['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <a href='{{ URL::previous() }}' class='btn btn-danger btn-sm'>Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm edit">Submit</button>
        </div>
    </form>
@endsection

@section('script_js')
    {{-- jquery and js plugin cdn --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4/default.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.unit-dropdown').select2({
                width: '100%'
            });

            $('.jabatan-dropdown').select2({
                width: '100%',
                maximumSelectionLength: 2,
                allowClear: true,
                tags: true,
                tokenSeparators: [',', ' ']
            });

            const jabatan_val = JSON.parse(@php echo $karyawan['jabatan']; @endphp)

            $('.jabatan-dropdown').val(jabatan_val).trigger('change');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.edit').click(function(e) {
                $('.print-error-msg').empty();
                $('.print-error-msg').hide();
                e.preventDefault(); // Prevent the href from redirecting directly                                       
                var form_data = new FormData($('#edit-karyawan-form')[0]);

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
                            url: '{{ route('karyawan.confirm-edit') }}',
                            data: form_data,
                            success: function(response) {
                                Swal.fire({
                                    title: 'Edit Data Success',
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
                                    'Your data failed to update.',
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
