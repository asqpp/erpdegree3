$(document).ready(function() {
    "use strict";
    var csrf_test_name = $('#CSRF_TOKEN').val();
    var base_url = $('#base_url').val();
    var currency = $("#currency").val();
    var report = $('#reportlist').DataTable({
        responsive: false,
// sPaginationType: "full_numbers",
        // bJQueryUI: true,
        // "fnDrawCallback": function ( oSettings ) {
        //     /* Need to redo the counters if filtered or sorted */
        //     if ( oSettings.bSorted || oSettings.bFiltered )
        //     {
        //         for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
        //         {
        //             $('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
        //         }
        //     }
        // },
        
        // "aoColumnDefs": [
        //     { "bSortable": false, "aTargets": [ 0 ] }
        // ],
        // fnRowCallback: function(nRow, aData, iDisplayIndex) {
        //     $.each(nRow.cells, function(k, v) {
        //         if (k <= 1 /*additional logic goes here */) {
        //           // $(this) is current cell
        //           $(this).addClass('bold');
        //         }
        //     });
        //     return nRow;
        // },
        "aaSorting": [
            [1, "asc"]
        ],
        "columnDefs": [{
                "bSortable": false,
                // "aTargets": [0, 2, 3, 4, 5, 6]
                "aTargets": [0,5, 7]
            },

        ],
        'processing': true,
        'serverSide': true,


        'lengthMenu': [
            [10, 25, 50, 100, 250, 500,1000, -1],
            [10, 25, 50, 100, 250, 500,1000, "ALL"]
        ],

        dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
        buttons: [{
            extend: "copy",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29] //Your Colume value those you want
            },
            className: "btn-sm prints"
        }, {
            extend: "csv",
            title: "Report List",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29] //Your Colume value those you want print
            },
            className: "btn-sm prints"
        }, {
            extend: "excel",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29] //Your Colume value those you want print
            },
            title: "Report List",
            className: "btn-sm prints"
        }, {
            extend: "pdf",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29] //Your Colume value those you want print
            },
            title: "Report List",
            className: "btn-sm prints"
        }, {
            extend: "print",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29] //Your Colume value those you want print
            },
            title: "Insurance Club Report",
            className: "btn-sm prints"
        }],

        'serverMethod': 'post',
        'ajax': {
            'url': base_url + 'report/report/getSalesReportList',
            "data": function(data) {
                data.fromdate = $('#from_date').val();
                data.todate = $('#to_date').val();
                data.broker_id = $('#broker_id').val();
                data.csrf_test_name = csrf_test_name;
            }
        },
        'columns': [
            {
                data: 's_n'
            },
            {
                data: 'invoice_id'
            },
            {
                data: 'date'
            },
            {
                data: 'document_date'
            },
            {
                data: 'customer_name'
            },
            {
                data: 'supplier_name'
            },
            {
                data: 'policy_type'
            },
            {
                data: 'product_name'
            },
            {
                data: 'policy_no'
            },
            {
                data: 'endorsement_no'
            },
            {
                data: 'policy_from'
            },
            {
                data: 'premium_amount'
            },
            {
                data: 'premium_vat'
            },
            {
                data: 'total_premium_amount'
            },
            {
                data: 'gross_commission'
            },
            {
                data: 'gross_commission_amount'
            },
            {
                data: 'gross_commission_vat'
            },
            {
                data: 'total_gross_commission_amount'
            },
            {
                data: 'broker_name'
            },
            {
                data: 'broker_commission'
            },
            {
                data: 'broker_commission_amount'
            },
            {
                data: 'debit_note_no'
            },
            {
                data: 'commission_credit_note_no'
            },
            {
                data: 'incentive_credit_note_no'
            },
            {
                data: 'paid_amount'
            },
            {
                data: 'due_amount'
            },
            {
                data: 'datediff'
            },
            {
                data: 'documentdatediff'
            },
            {
                data: 'type'
            },
            // {
            //     data: 'total_amount'
            // },
            {
                data: 'total_amount',
                class: "total_amount text-right",
                render: $.fn.dataTable.render.number(',', '.', 2, currency)
            },
        ],
        "createdRow": function (row, data, index) {
            if (data.type === "Incentive") {
                $(row).addClass('yellowRow');
            }
        },
        "footerCallback": function(row, data, start, end, display) {
            var api = this.api();
            api.columns('.total_amount', {
                page: 'current'
            }).every(function() {
                var sum = this
                    .data()
                    .reduce(function(a, b) {
                        var x = parseFloat(a) || 0;
                        var y = parseFloat(b) || 0;
                        return x + y;
                    }, 0);
                $(this.footer()).html(currency + ' ' + sum.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });
        }
        // customize: function( xlsx ) {
        //     var sheet = xlsx.xl.worksheets['sheet1.xlsx'];
        //     $('row:first c', sheet).attr( 's', '42' );
        // }
    });
// report.on('draw', function () {
//          report.rows({ search: 'applied', order: 'applied', filter: 'applied' }).data().each(function (d, i) {
//              d[0] = i + 1;
//          });
//      });

//  report.on('order.dt search.dt', function () {
//      report.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
//           cell.innerHTML = i + 1;
//      });
//  }).draw(false);
    $('#btn-filter').click(function() {
        report.ajax.reload();
    });
});