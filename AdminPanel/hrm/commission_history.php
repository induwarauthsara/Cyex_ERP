<?php
include '../nav.php';

// Prepare SQL for default view (all records)
$sql = "SELECT 
            ech.invoice_number, 
            ech.created_at, 
            e.emp_name, 
            ech.product_name, 
            ech.product_profit, 
            ech.commission_percentage, 
            ech.commission_amount 
        FROM employee_commission_history ech 
        LEFT JOIN employees e ON ech.employee_id = e.employ_id 
        ORDER BY ech.created_at DESC";

// Get employees for filter
$Employee_names_sql = "SELECT employ_id, emp_name FROM employees WHERE status = 1";
$employee_names_result = mysqli_query($con, $Employee_names_sql);
?>

<title>Commission History</title>

<!-- Header and Controls -->
<div style='display: flex; justify-content: space-between; align-items: center; margin: 20px 20px;'>
    <h1>Commission History</h1>
    
    <div style='display: flex; gap: 10px; align-items: center;'>
        <a href='index.php'><button>Back to HRM</button></a>
        <a href='viewPayrolls.php'><button>Payroll</button></a>
        <button id="filterBtn" style="height: 35px; background-color: #17a2b8;">Filter Records</button>
    </div>
</div>

<div style="margin: 0 20px;">
    <!-- Table to display Commission data -->
    <table id="DataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Employee</th>
                <th>Product</th>
                <th>Profit (Rs)</th>
                <th>Comm %</th>
                <th>Amount (Rs)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = mysqli_query($con, $sql);

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $date = date('Y-m-d H:i', strtotime($row['created_at']));
                    echo "<tr>
                        <td>" . $date . "</td>
                        <td><a href='/invoice/print.php?id=" . $row['invoice_number'] . "' target='_blank'>" . $row['invoice_number'] . "</a></td>
                        <td>" . $row['emp_name'] . "</td>
                        <td>" . $row['product_name'] . "</td>
                        <td>" . number_format($row['product_profit'], 2) . "</td>
                        <td>" . number_format($row['commission_percentage'], 2) . "%</td>
                        <td style='font-weight:bold; color:green;'>" . number_format($row['commission_amount'], 2) . "</td>
                    </tr>";
                }
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align:right">Total:</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized and destroy it
        if ($.fn.DataTable.isDataTable('#DataTable')) {
            $('#DataTable').DataTable().destroy();
        }

        // Initialize DataTable
        var table = $('#DataTable').DataTable({
            "order": [[ 0, "desc" ]],
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
    
                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
    
                // Total over all pages
                total = api
                    .column( 6 )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Total over this page
                pageTotal = api
                    .column( 6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );
    
                // Update footer
                $( api.column( 6 ).footer() ).html(
                    'Rs. '+ pageTotal.toFixed(2) +' (Total: Rs. '+ total.toFixed(2) +')'
                );
            }
        });

        // Get URL parameters for pre-filtering (if implemented later via GET)
        var urlParams = new URLSearchParams(window.location.search);
        var empParam = urlParams.get('emp');
        var startParam = urlParams.get('start');
        var endParam = urlParams.get('end');

        if(empParam || (startParam && endParam)) {
             $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var employeeName = data[2]; // Employee Name column
                var date = data[0].split(' ')[0]; // Date column (YYYY-MM-DD)

                var isNameMatch = !empParam || employeeName === empParam;
                var isDateInRange = true;
                
                if (startParam && endParam) {
                    isDateInRange = (date >= startParam && date <= endParam);
                }

                return isNameMatch && isDateInRange;
            });
            table.draw();
        }

        // Filter Button Logic
        $('#filterBtn').click(function() {
            Swal.fire({
                title: 'Filter Commission Records',
                html: `
                    <form id="filterForm">
                        <label for="emp">Employee Name (Optional):</label>
                        <select id="emp" name="emp" class="swal2-select">
                            <option value="">All Employees</option>
                            <?php
                            if ($employee_names_result) {
                                while ($emp = mysqli_fetch_assoc($employee_names_result)) {
                                    echo "<option value='" . $emp['emp_name'] . "'>" . $emp['emp_name'] . "</option>";
                                }
                            }
                            ?>
                        </select>

                        <label for="start_date">Start Date:</label>
                        <input type="date" id="start_date" class="swal2-input">
                        
                        <label for="end_date">End Date:</label>
                        <input type="date" id="end_date" class="swal2-input">
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Apply Filter',
                preConfirm: function() {
                    return {
                        emp: document.getElementById('emp').value,
                        start: document.getElementById('start_date').value,
                        end: document.getElementById('end_date').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var emp = result.value.emp;
                    var start = result.value.start;
                    var end = result.value.end;
                    
                    // Reload page with filter params
                    var newUrl = 'commission_history.php?';
                    if(emp) newUrl += 'emp=' + encodeURIComponent(emp) + '&';
                    if(start) newUrl += 'start=' + start + '&';
                    if(end) newUrl += 'end=' + end;
                    
                    window.location.href = newUrl;
                }
            });
        });
    });
</script>
