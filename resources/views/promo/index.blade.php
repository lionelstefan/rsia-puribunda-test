@extends('master')
@section('title_page', 'Dashboard')
@section('header_page', 'List Promo')
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
<table id="tbl-promo" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Title</th>
            <th>Start Promo</th>
            <th>End Promo</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@endsection

@section('script_js')
<script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.2/r-2.4.0/datatables.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default@4/default.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script>
    $(document).ready(function() {
    var dataTable = $('#tbl-promo').DataTable({
            //"responsive": true,            
            "processing": true,
            "serverSide": true,
            "scrollX": true,            
            "responsive":true,
            language: {
                "processing": "<div style='margin-top:12% !important;'><i class='fa fa-spinner fa-spin fa-3x fa-fw'></i>Processing...</div>",
            },
            "ajax": "{{ url('dataPromo') }}",
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
                    searchable: false
                },                
                {
                    data: "title",
                    name: "title"
                },
                {
                    data: "start_date",
                    name: "start_date"
                },
                {
                    data: "end_date",
                    name: "end_date"
                },
                {
                    data: "desc",
                    name: "desc"
                },
                {
                    data:"action",
                    name:"action"
                }
            ],
            drawCallback: function(settings) {
                $('.delete').click(function(e) {
		        e.preventDefault(); // Prevent the href from redirecting directly
                var linkURL = $(this).attr("href");
                console.log(linkURL);
                Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',                                                        
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',      
                            confirmButtonText: 'Yes, delete it!',
                            showCancelButton: true,                      
                        }).then((result) => {
                            if (result.isConfirmed) {   
                                $.ajax({ 
                                        url: linkURL,	                                    	                                    
                                        success: function(response){
                                        dataTable.ajax.reload(null, false);
                                        console.log(response);
                                        Swal.fire(
                                                    'Deleted!',
                                                    'Your data has been deleted.',
                                                    'success'
                                                );
                                            },
                                        error: function(data){                        
                                        console.log(data);
                                        Swal.fire(
                                                    'Failed',
                                                    'Your data failed to delete.',
                                                    'error'
                                                );
                                    }
                                });                                                        
                            }
                        });                                               
                    });                    
                }
            });
        });
</script>
@endsection