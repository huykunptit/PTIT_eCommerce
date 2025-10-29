@if(($section ?? 'styles') === 'styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" />
@endif

@if(($section ?? 'scripts') === 'scripts')
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
  <script>
    (function(){
      var id = @json($tableId ?? 'data-table');
      var nonOrderable = @json($nonOrderable ?? []);
      $('#'+id).DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        dom: 'Bfrtip',
        buttons: [
          { extend: 'copy', className: 'btn btn-sm btn-warning' },
          { extend: 'csv', className: 'btn btn-sm btn-warning' },
          { extend: 'excel', className: 'btn btn-sm btn-warning' },
          { extend: 'pdf', className: 'btn btn-sm btn-warning' },
          { extend: 'print', className: 'btn btn-sm btn-warning' },
          { extend: 'colvis', className: 'btn btn-sm btn-warning' }
        ],
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/vi.json' },
        columnDefs: [ { orderable:false, targets: nonOrderable } ]
      });
    })();
  </script>
@endif


