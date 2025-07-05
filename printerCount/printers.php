<?php
ob_start();
include 'DataTable_cdn.php';
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_clean();
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    switch ($action) {
        case 'addPrinter':
            $printerName = $input['printerName'];
            $ipAddress = $input['ipAddress'];
            $typeCount = $input['typeCount'];

            $stmt = $con->prepare("INSERT INTO printer_counter_printers (printerName, ipAddress, typeCount) VALUES (?, ?, ?)");
            $stmt->bind_param('ssi', $printerName, $ipAddress, $typeCount);
            $stmt->execute();
            $printerID = $stmt->insert_id;

            for ($i = 1; $i <= $typeCount; $i++) {
                $typeName = $input['types'][$i]['typeName'];
                $cost = $input['types'][$i]['cost'];
                $totalCount = $input['types'][$i]['totalCount'];

                $stmt = $con->prepare("INSERT INTO printer_counter_types (printerID, typeName, cost, totalCount) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('isii', $printerID, $typeName, $cost, $totalCount);
                $stmt->execute();
            }

            echo json_encode(['success' => true]);
            break;

        case 'editPrinter':
            $printerID = $input['printerID'];
            $printerName = $input['printerName'];
            $ipAddress = $input['ipAddress'];

            $stmt = $con->prepare("UPDATE printer_counter_printers SET printerName = ?, ipAddress = ? WHERE printerID = ?");
            $stmt->bind_param('ssi', $printerName, $ipAddress, $printerID);
            $stmt->execute();

            echo json_encode(['success' => true]);
            break;

        case 'deactivatePrinter':
            $printerName = $input['printerName'];

            $stmt = $con->prepare("UPDATE printer_counter_printers SET active = 0 WHERE printerName = ?");
            $stmt->bind_param('s', $printerName);
            $stmt->execute();

            echo json_encode(['success' => true]);
            break;

        case 'addType':
            $printerID = $input['printerID'];
            $typeName = $input['typeName'];
            $cost = $input['cost'];
            $totalCount = $input['totalCount'];

            $stmt = $con->prepare("INSERT INTO printer_counter_types (printerID, typeName, cost, totalCount) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('isii', $printerID, $typeName, $cost, $totalCount);
            $stmt->execute();

            $stmt = $con->prepare("UPDATE printer_counter_printers SET typeCount = typeCount + 1 WHERE printerID = ?");
            $stmt->bind_param('i', $printerID);
            $stmt->execute();

            echo json_encode(['success' => true]);
            break;

        case 'removeType':
            $typeID = $input['typeID'];

            $stmt = $con->prepare("DELETE FROM printer_counter_types WHERE typeID = ?");
            $stmt->bind_param('i', $typeID);
            $stmt->execute();

            $stmt = $con->prepare("UPDATE printer_counter_printers SET typeCount = typeCount - 1 WHERE printerID = (SELECT printerID FROM printer_counter_types WHERE typeID = ?)");
            $stmt->bind_param('i', $typeID);
            $stmt->execute();

            echo json_encode(['success' => true]);
            break;

        case 'changeCost':
            $typeID = $input['typeID'];
            $cost = $input['cost'];

            $stmt = $con->prepare("UPDATE printer_counter_types SET cost = ? WHERE typeID = ?");
            $stmt->bind_param('ii', $cost, $typeID);
            $stmt->execute();

            echo json_encode(['success' => true]);
            break;

        case 'getPrinterDetails':
            $printerName = $input['printerName'];

            $stmt = $con->prepare("SELECT printerID, printerName, ipAddress FROM printer_counter_printers WHERE printerName = ? AND active = 1");
            $stmt->bind_param('s', $printerName);
            $stmt->execute();
            $result = $stmt->get_result();
            $printer = $result->fetch_assoc();

            echo json_encode($printer);
            break;

        case 'editCount':
            $typeID = $input['typeID'];
            $totalCount = $input['totalCount'];

            $stmt = $con->prepare("UPDATE printer_counter_types SET totalCount = ? WHERE typeID = ?");
            $stmt->bind_param('ii', $totalCount, $typeID);
            $stmt->execute();

            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Printers</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Modern CSS Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --success: #10b981;
            --success-hover: #059669;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --warning: #f59e0b;
            --warning-hover: #d97706;
            --background: #f9fafb;
            --card: #ffffff;
            --text: #111827;
            --text-secondary: rgb(2, 6, 14);
            --border: rgb(0, 0, 0);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text);
            background-color: var(--background);
            padding: 24px;
        }

        /* Modern Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header styles */
        h1 {
            color: var(--text);
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 2rem;
            letter-spacing: -0.025em;
        }

        /* Button styles */
        button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            gap: 0.5rem;
            background: #ffd600;
            margin: 5px;
        }

        #addNewPrinter {
            background-color: var(--success);
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            margin: 1.5rem 0;
            box-shadow: var(--shadow);
        }

        #addNewPrinter:hover {
            background-color: var(--success-hover);
        }

        .editPrinter {
            background-color: var(--primary);
            color: white;
        }

        .editPrinter:hover {
            background-color: var(--primary-hover);
        }

        .deactivatePrinter {
            background-color: var(--danger);
            color: white;
        }

        .deactivatePrinter:hover {
            background-color: var(--danger-hover);
        }

        .addType {
            background-color: var(--success);
            color: white;
        }

        .addType:hover {
            background-color: var(--success-hover);
        }

        .button-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* Table styles */
        .table-container {
            background: var(--card);
            border-radius: 1rem;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin: 1.5rem 0;
        }

        #printerTable {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        #printerTable th,
        #printerTable td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        #printerTable th {
            background-color: var(--background);
            font-weight: 600;
            color: var(--text);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        #printerTable td {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        #printerTable tbody tr:hover {
            background-color: var(--background);
        }

        /* Action buttons in table */
        .table-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 0.375rem;
        }

        /* Badge styles for status */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Links */
        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            color: var(--primary-hover);
        }

        /* SweetAlert2 customization */
        .swal2-popup {
            border-radius: 1rem;
            padding: 2rem;
        }

        .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            color: var(--text) !important;
        }

        .swal2-input {
            border-radius: 0.5rem !important;
            border: 1px solid var(--border) !important;
            padding: 0.75rem 1rem !important;
            margin: 0.75rem 0 !important;
        }

        .swal2-confirm {
            background-color: var(--primary) !important;
            border-radius: 0.5rem !important;
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
        }

        .stat-card h3 {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Responsive design */
        @media screen and (max-width: 768px) {
            body {
                padding: 16px;
            }

            .table-container {
                border-radius: 0.75rem;
                margin: 1rem -1rem;
            }

            .button-group {
                flex-direction: column;
            }

            .button-group button {
                width: 100%;
            }

            #printerTable th,
            #printerTable td {
                padding: 0.75rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        /* Loading spinner */
        .loading-spinner {
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Toast notifications */
        .toast {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: var(--card);
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <h1>Manage Printers</h1>

    <button id="addNewPrinter">Add New Printer</button>

    <table id="printerTable" border="1">
        <tr>
            <th>Printer Name</th>
            <th>Print Type</th>
            <th>Cost</th>
            <th>Current Total Count</th>
        </tr>
        <?php
        $sql = "SELECT printerName, ipAddress FROM printer_counter_printers WHERE active = '1'";
        $resultPrinterNames = $con->query($sql);
        if ($resultPrinterNames->num_rows > 0) {
            while ($rowPrinterNames = $resultPrinterNames->fetch_assoc()) {
                $printerName = $rowPrinterNames['printerName'];
                $ipAddress = $rowPrinterNames['ipAddress'];

                $sql = "SELECT COUNT(printer_counter_types.typeName) AS typeCount FROM printer_counter_types, printer_counter_printers WHERE printer_counter_printers.printerName = '$printerName' AND printer_counter_printers.active = '1' AND printer_counter_types.printerID = printer_counter_printers.printerID";
                $result = $con->query($sql);
                $row = $result->fetch_assoc();
                $total = $row['typeCount'];

                echo "<tr><th rowspan='$total'><a href='$ipAddress' target='_blank'>$printerName</a> <br>
                <button class='editPrinter' data-name='$printerName'>Edit Printer</button>
                <button class='deactivatePrinter' data-name='$printerName'>Deactivate Printer</button>
                <button class='addType' data-name='$printerName'>Add New Type</button>
                </th>";

                $sql = "SELECT printer_counter_types.typeName, printer_counter_types.typeID, printer_counter_types.totalCount, printer_counter_types.cost FROM printer_counter_types, printer_counter_printers WHERE printer_counter_printers.printerName = '$printerName' AND printer_counter_printers.active = '1' AND printer_counter_types.printerID = printer_counter_printers.printerID";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<td>" . $row['typeName'] . "<br><button class='removeType' data-id='" . $row['typeID'] . "'>Remove</button></td>";
                        echo "<td>" . $row['cost'] . "<br><button class='changeCost' data-id='" . $row['typeID'] . "'>Change</button></td>";
                        echo "<td>" . $row['totalCount'] . "<br><button class='editCount' data-id='" . $row['typeID'] . "'>Edit</button></td></tr>";
                    }
                }
            }
        }
        ?>
    </table>

    <script>
        // Add New Printer
        document.getElementById('addNewPrinter').addEventListener('click', () => {
            Swal.fire({
                title: 'Add New Printer',
                html: `
            <div class="input-group">
                <input type="text" id="printerName" class="swal2-input" placeholder="Printer Name">
                <input type="text" id="ipAddress" class="swal2-input" placeholder="IP Address">
                <input type="number" id="typeCount" class="swal2-input" min="1" value="1" placeholder="Type Count">
            </div>
            <div id="typeInputs">
                <div class="type-group" data-index="1">
                    <h4 class="type-header">Type 1</h4>
                    <input type="text" id="typeName_1" class="swal2-input" placeholder="Type Name">
                    <input type="number" id="cost_1" class="swal2-input" placeholder="Cost">
                    <input type="number" id="totalCount_1" class="swal2-input" placeholder="Total Count">
                </div>
            </div>
        `,
                didOpen: () => {
                    // Add event listener for typeCount changes
                    document.getElementById('typeCount').addEventListener('change', (e) => {
                        const count = parseInt(e.target.value);
                        updateTypeInputs(count);
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    const printerName = document.getElementById('printerName').value;
                    const ipAddress = document.getElementById('ipAddress').value;
                    const typeCount = parseInt(document.getElementById('typeCount').value);

                    if (!printerName || !ipAddress || !typeCount) {
                        Swal.showValidationMessage('Printer name, IP address, and type count are required');
                        return false;
                    }

                    // Validate and collect all type inputs
                    const types = {};
                    for (let i = 1; i <= typeCount; i++) {
                        const typeName = document.getElementById(`typeName_${i}`).value;
                        const cost = document.getElementById(`cost_${i}`).value;
                        const totalCount = document.getElementById(`totalCount_${i}`).value;

                        if (!typeName || !cost || !totalCount) {
                            Swal.showValidationMessage(`All fields for Type ${i} are required`);
                            return false;
                        }

                        types[i] = {
                            typeName,
                            cost,
                            totalCount
                        };
                    }

                    return {
                        printerName,
                        ipAddress,
                        typeCount,
                        types
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('printers.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                action: 'addPrinter',
                                ...result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success', 'Printer added successfully!', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Failed to add printer', 'error');
                        });
                }
            });
        });

        function updateTypeInputs(count) {
            const typeInputsContainer = document.getElementById('typeInputs');
            const currentCount = typeInputsContainer.children.length;

            if (count > currentCount) {
                // Add new type input groups
                for (let i = currentCount + 1; i <= count; i++) {
                    const typeGroup = document.createElement('div');
                    typeGroup.className = 'type-group';
                    typeGroup.dataset.index = i;
                    typeGroup.innerHTML = `
                <h4 class="type-header">Type ${i}</h4>
                <input type="text" id="typeName_${i}" class="swal2-input" placeholder="Type Name">
                <input type="number" id="cost_${i}" class="swal2-input" placeholder="Cost">
                <input type="number" id="totalCount_${i}" class="swal2-input" placeholder="Total Count">
            `;
                    typeInputsContainer.appendChild(typeGroup);
                }
            } else if (count < currentCount) {
                // Remove excess type input groups
                for (let i = currentCount; i > count; i--) {
                    const lastGroup = typeInputsContainer.lastChild;
                    if (lastGroup) {
                        typeInputsContainer.removeChild(lastGroup);
                    }
                }
            }
        }

        // Edit Printer
        document.querySelectorAll('.editPrinter').forEach(button => {
            button.addEventListener('click', () => {
                const printerName = button.dataset.name;

                // First fetch current printer details
                fetch(`printers.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'getPrinterDetails',
                            printerName: printerName
                        })
                    })
                    .then(response => response.json())
                    .then(printer => {
                        Swal.fire({
                            title: 'Edit Printer',
                            html: `
                    <input type="text" id="printerName" class="swal2-input" value="${printer.printerName}" placeholder="Printer Name">
                    <input type="text" id="ipAddress" class="swal2-input" value="${printer.ipAddress}" placeholder="IP Address">
                `,
                            showCancelButton: true,
                            confirmButtonText: 'Save',
                            preConfirm: () => {
                                const newPrinterName = document.getElementById('printerName').value;
                                const ipAddress = document.getElementById('ipAddress').value;

                                if (!newPrinterName || !ipAddress) {
                                    Swal.showValidationMessage('All fields are required');
                                }

                                return {
                                    newPrinterName,
                                    ipAddress
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch('printers.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            action: 'editPrinter',
                                            printerID: printer.printerID,
                                            printerName: result.value.newPrinterName,
                                            ipAddress: result.value.ipAddress
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire('Success', 'Printer updated successfully!', 'success')
                                                .then(() => location.reload());
                                        } else {
                                            Swal.fire('Error', data.message, 'error');
                                        }
                                    });
                            }
                        });
                    });
            });
        });

        // Deactivate Printer
        document.querySelectorAll('.deactivatePrinter').forEach(button => {
            button.addEventListener('click', () => {
                const printerName = button.dataset.name;

                Swal.fire({
                    title: 'Confirm Deactivation',
                    text: `Are you sure you want to deactivate ${printerName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, deactivate it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('printers.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    action: 'deactivatePrinter',
                                    printerName: printerName
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Success', 'Printer deactivated successfully!', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            });
                    }
                });
            });
        });

        // Add New Type
        document.querySelectorAll('.addType').forEach(button => {
            button.addEventListener('click', () => {
                const printerName = button.dataset.name;

                // First fetch printer details to get the correct printerID
                fetch('printers.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'getPrinterDetails',
                            printerName: printerName
                        })
                    })
                    .then(response => response.json())
                    .then(printer => {
                        Swal.fire({
                            title: 'Add New Type',
                            html: `
                    <div class="form-group">
                        <label for="typeName" class="swal2-label">Type Name:</label>
                        <input type="text" id="typeName" class="swal2-input" placeholder="Enter type name">
                    </div>
                    <div class="form-group">
                        <label for="cost" class="swal2-label">Cost:</label>
                        <input type="number" id="cost" class="swal2-input" placeholder="Enter cost">
                    </div>
                    <div class="form-group">
                        <label for="totalCount" class="swal2-label">Total Count:</label>
                        <input type="number" id="totalCount" class="swal2-input" placeholder="Enter total count">
                    </div>
                `,
                            showCancelButton: true,
                            confirmButtonText: 'Save',
                            preConfirm: () => {
                                const typeName = document.getElementById('typeName').value;
                                const cost = document.getElementById('cost').value;
                                const totalCount = document.getElementById('totalCount').value;

                                if (!typeName || !cost || !totalCount) {
                                    Swal.showValidationMessage('All fields are required');
                                }

                                return {
                                    typeName,
                                    cost,
                                    totalCount
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch('printers.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            action: 'addType',
                                            printerID: printer.printerID, // Now using the correct printerID
                                            ...result.value
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire('Success', 'Type added successfully!', 'success')
                                                .then(() => location.reload());
                                        } else {
                                            Swal.fire('Error', data.message, 'error');
                                        }
                                    });
                            }
                        });
                    });
            });
        });

        // Remove Type
        document.querySelectorAll('.removeType').forEach(button => {
            button.addEventListener('click', () => {
                const typeID = button.dataset.id;
                let typeName = button.parentElement.textContent.split('\n')[0]; // Get the type name from the cell
                // Remove the "Remove" button text
                typeName = typeName.replace('Remove', '').trim();

                Swal.fire({
                    title: 'Confirm Removal',
                    text: `Are you sure you want to remove ${typeName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('printers.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    action: 'removeType',
                                    typeID: typeID
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Success', 'Type removed successfully!', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            });
                    }
                });
            });
        });

        // Change Cost
        document.querySelectorAll('.changeCost').forEach(button => {
            button.addEventListener('click', () => {
                const typeID = button.dataset.id;
                const currentCost = button.parentElement.textContent.split('\n')[0]; // Get current cost

                Swal.fire({
                    title: 'Change Cost',
                    html: `
                <div class="form-group">
                    <label for="cost" class="swal2-label">New Cost:</label>
                    <input type="number" id="cost" class="swal2-input" value="${currentCost}" placeholder="Enter new cost">
                </div>
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        const cost = document.getElementById('cost').value;
                        if (!cost) {
                            Swal.showValidationMessage('Cost is required');
                        }
                        return {
                            cost
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('printers.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    action: 'changeCost',
                                    typeID: typeID,
                                    cost: result.value.cost
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Success', 'Cost updated successfully!', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            });
                    }
                });
            });
        });

        // Edit Count
        document.querySelectorAll('.editCount').forEach(button => {
            button.addEventListener('click', () => {
                const typeID = button.dataset.id;
                const currentCount = button.parentElement.textContent.split('\n')[0]; // Get current count

                Swal.fire({
                    title: 'Edit Count',
                    html: `
                <div class="form-group">
                    <label for="totalCount" class="swal2-label">New Total Count:</label>
                    <input type="number" id="totalCount" class="swal2-input" value="${currentCount}" placeholder="Enter new total count">
                </div>
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    preConfirm: () => {
                        const totalCount = document.getElementById('totalCount').value;
                        if (!totalCount) {
                            Swal.showValidationMessage('Total count is required');
                        }
                        return {
                            totalCount
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('printers.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    action: 'editCount',
                                    typeID: typeID,
                                    totalCount: result.value.totalCount
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('Success', 'Count updated successfully!', 'success')
                                        .then(() => location.reload());
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            });
                    }
                });
            });
        });

        // Add some CSS for the new labels
        const style = document.createElement('style');
        style.textContent = `
    .form-group {
        margin-bottom: 1rem;
        text-align: left;
    }
    .swal2-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #374151;
        font-size: 0.875rem;
    }
    .swal2-input {
        margin-top: 0 !important;
    }
`;
        document.head.appendChild(style);
    </script>
</body>

</html>