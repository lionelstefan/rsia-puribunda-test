@extends('master')
@section('title_page', 'Dashboard')
@section('header_page', 'List Transaction')
<style type="text/css">
    .dataTables_processing {
        position: absolute;
        top: 0px;
        left: 50%;
        width: 250px;
        margin-top: 100px;
        margin-left: -125px;
        border: none;
        text-align: center;
        color: #999;
        font-size: 15px;
        padding: 2px 0;
    }
</style>
@section('content')
@if(Session::has('success'))
<div class="alert alert-success">
    {{Session::get('success')}}
</div>
@endif
@if(Session::has('fail'))
<div class="alert alert-danger">
    {{Session::get('fail')}}
</div>
@endif
<table id="tbl-event" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Member Id</th>            
            <th>Bill Number</th>                        
            <th>Point</th>
            <th>Transaction Type</th> 
            <th>Created Date</th>            
        </tr>
    </thead>
</table>
@endsection

@section('script_js')
<script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.2/r-2.4.0/datatables.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4/default.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script>
    $(document).ready(function() {
        var dataTable = $('#tbl-event').DataTable({
            //"responsive": true,            
            "processing": true,
            "serverSide": true,
            "scrollX": true,
            "responsive":true,
            language: {
                "processing": "<div style='margin-top:12% !important;'><i class='fa fa-spinner fa-spin fa-3x fa-fw'></i>Processing...</div>",
            },
            "ajax": "{{ route('requestDataTransaction.get') }}",
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            "columns": [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    width: '10px',
                    orderable: false, 
                    searchable: false,
                },
                {
                    data: "user_id",
                    name: "user_id"
                },
                {
                    data: "no_bill",
                    name: "no_bill"
                },
                {
                    data: "total_point",
                    name: "total_point"
                },
                {
                    data: "tx_type",
                    name: "tx_type"
                },
                {
                    data: "created_date",
                    name: "created_date"
                }
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {                 
                console.log(nRow);
                if (aData.tx_type == "RP") {                           
                    $(nRow).find('td:eq(3)').css('color', 'red');
                } else if (aData.tx_type == "GP") {
                    $(nRow).find('td:eq(3)').css('color', 'green');
                    }                
                }
            });
        });
</script>
@endsection