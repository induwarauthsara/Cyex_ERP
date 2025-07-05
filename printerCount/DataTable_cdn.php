<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css"> -->
<!-- <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script> -->
<!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script> -->

<!-- adding datatable cdn -->
<!-- <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script> -->

<!-- adding datatable style cdn -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css"> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css"> -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-components-web/14.0.0/material-components-web.min.css"> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.material.css"> -->


<!-- adding cdn to datatable file export -->
<!-- <script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> -->
<!-- <script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.print.min.js"></script> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.5/css/buttons.dataTables.min.css"> -->

<!--adding cdn to  datatable responsive -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css"> -->
<!-- <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script> -->

<!-- adding cdn to datatable SearchBuilder -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.4.0/css/searchBuilder.dataTables.min.css"> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.3.1/css/dataTables.dateTime.min.css"> -->
<!-- <script src="https://cdn.datatables.net/searchbuilder/1.4.0/js/dataTables.searchBuilder.min.js"></script> -->
<!-- <script src="https://cdn.datatables.net/datetime/1.3.1/js/dataTables.dateTime.min.js"></script> -->

<!-- adding cnd to datatable select -->
<!-- <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script> -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css"> -->

<!-- ---------------------------------------------------------------------------------- -->
<link href="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.1.8/af-2.7.0/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/dt/jq-3.7.0/jszip-3.10.1/dt-2.1.8/af-2.7.0/b-3.2.0/b-colvis-3.2.0/b-html5-3.2.0/b-print-3.2.0/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.1/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js"></script>


<!-- initialing datatable -->
<script>
    $(document).ready(function() {
        $('#DataTable').DataTable({
            buttons: ['copy', 'excel', 'pdf', 'print' // 'pageLength',
            ],
            select: true,
            responsive: true,
            dom: 'lBfrtipQ', // Adding Conditon for "Q" at end
            order: [
                [0, 'desc']
            ],

        });
    });
</script>

<style>
    /* #DataTable_wrapper {
        width: calc(100% - 200px);
        position: absolute;
        right: 50px;
    } */

    .dt-search {
        right: 20px;
        position: absolute;
        top: 20px;
    }
</style>