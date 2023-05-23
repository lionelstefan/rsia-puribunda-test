<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Get Point</title>
</head>

<body style="background-color: black">
    <div class="container">        
        <form enctype="multipart/form-data" id="redeem-point-form" name="redeem-point-form" class="mt-2">
            @csrf          
            <div class="alert alert-danger print-error-msg" style="display:none">
                <ul></ul>
            </div>
            <div class="mb-4">     
                <a href='{{ URL::previous() }}'><i class="fa fa-arrow-left fa-xl" style="color:red"></i></a>           
                <button id="scan-qr" class="btn btn-success btn-sm" style="float: right;"><i class="fa-solid fa-expand fa-xl"></i> Scan QR Member</button>
            </div>
            <!-- Button trigger modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <div id="status"></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>                            
                            <div id="reader" width="600px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row form-group mb-2">
                <label for="member_id" class="col-sm-2 col-form-label fw-normal" style="color:#FFD700">Member Id<span
                    class="text-danger"> *</span></label>
                <div class="col-sm-6 col-xs-12">
                    <input class="form-control form-control-sm bg-secondary text-warning border border-warning"
                        type="text" name="member_id" id="member_id" readonly="readonly" required>
                </div>
            </div>

            <div class="row form-group mb-2">
                <label for="current_point_member" class="col-sm-2 col-form-label fw-normal" style="color:#FFD700">Current Point Member<span
                    class="text-danger"> *</span></label>
                <div class="col-sm-6 col-xs-12">
                    <input class="form-control form-control-sm bg-secondary text-warning border border-warning"
                        type="text" name="current_point_member" id="current_point_member" readonly="readonly" required>
                </div>
            </div>

            <div class="row form-group mb-2">
                <label for="bill_number" class="col-sm-2 col-form-label fw-normal" style="color:#FFD700">Bill Number
                    <span class="text-danger"> *</span></label>
                <div class="col-sm-6 col-xs-12">
                    <input class="form-control form-control-sm bg-dark text-warning border border-warning"
                        type="text" name="bill_number" id="bill_number" required>
                </div>
            </div>

            {{-- <div class="row form-group mb-2">
                <label for="total_bill" class="col-sm-2 col-form-label fw-normal" style="color:#FFD700">Total Bill <span
                        class="text-danger">*</span></label>
                <div class="col-sm-6 col-xs-12">
                    <input class="form-control form-control-sm bg-dark text-warning border border-warning"
                        type="text" name="total_bill" id="total_bill" data-type="currency" required>
                </div>
            </div> --}}

            <div class="row form-group mb-2">
                <label for="total_bill" class="col-sm-2 col-form-label fw-normal" style="color:#FFD700">Total Redeem Point<span
                        class="text-danger"> *</span></label>
                <div class="col-sm-6 col-xs-12">
                    <input class="form-control form-control-sm bg-dark text-warning border border-warning"
                        type="number" name="total_redeem_point" id="total_redeem_point" required>
                </div>
            </div>

            <div class="mt-3 mb-3 text-end">                
                <button type="submit" class="btn btn-success btn-sm add"><i class="fa fa-paper-plane" aria-hidden="true"></i> Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4/default.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
//modal bootstrap
const myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
const scan = document.querySelector('#scan-qr');
const modalTitle = document.querySelector('.modal-title');
scan.addEventListener('click', (e) => {
    e.preventDefault()
    modalTitle.innerHTML = "Scan Member Id"    
    myModal.show()
});
//end of modal bootstrap

    //AUTO FORMAT CURRENCY
    $("input[data-type='currency']").on({
        keyup: function() {
            formatCurrency($(this));
        },
        blur: function() {
            formatCurrency($(this), "blur");
        }
    });

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
    }

    function formatCurrency(input, blur) {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position 
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    }
    //END OF AUTO FORMAT CURRENCY

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.add').click(function(e) {
        $('.print-error-msg').empty();
        $('.print-error-msg').hide();  
        e.preventDefault(); // Prevent the href from redirecting directly                                       
        var form_data = new FormData($('#redeem-point-form')[0]);
        //kosongin dulu element show message setiap trigger create
        Swal.fire({
            title: 'Are you sure?',
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, redeem it!',
            showCancelButton: true,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    url: '{{ route('requestRedeemPoint.post') }}',
                    data: form_data,
                    success: function(response) {
                        Swal.fire({
                            title: 'Add Data Success',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $("#redeem-point-form")[0].reset();                                
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

                        $(".print-error-msg").css('display','block');
                        $.each(all_errors, function(key, value) {
                            $('.print-error-msg').append(
                                `<li style="color:red">${value}</li>`);
                        });
                    }
                });
            }
        });
    });
    
    //qr code logic
    let config = {
        rememberLastUsedCamera: false,
        fps: 10, 
        qrbox: {
                width: 250, 
                height: 250
                }
    }
    let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    config,
    /* verbose= */ false);
    html5QrcodeScanner.render(onScanSuccess);

    function checkMemberExist(result)
    {   
        $('#status').empty();
        let member_id = result;        
        var returnObj = {};
        $.ajax({
                type: 'POST',
                dataType:'json',
                url: '{{ route('checkMemberExist.post') }}',
                data: {'id': member_id},
                async: false,
                success: function(response) {                                                      
                    returnObj['match'] = response.match;          
                    returnObj['message'] = response.message;
                    returnObj['data'] = response.data;                                             
                },
                error: function(data) {                    
                    let rawResponse     = data.responseJSON;
                    returnObj['match'] = rawResponse.match; 
                    returnObj['message'] = rawResponse.message;                                             
                    
                }                
            });            
            return returnObj;
    }

    function onScanSuccess(result) {                  
        var memberExist = checkMemberExist(result);                               
        if(memberExist.match == true)
        {          
            $('#member_id').val(result);
            $('#current_point_member').val(memberExist.data.point);  
            document.getElementById('html5-qrcode-button-camera-stop').click();        
            myModal.hide();
        }else{
            alert(memberExist.message);
        }
    }

</script>
