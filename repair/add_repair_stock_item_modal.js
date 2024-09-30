document.querySelector('#add_raw_item').addEventListener('click', function() {
    Swal.fire({
        title: 'Add Repair Stock Item',
        html: `
            <label for="itemName" class="swal2-label">Item Name:</label>
            <input id="itemName" class="swal2-input" placeholder="Enter Item Name">
            <label for="itemPrice" class="swal2-label">Item Price (Cost):</label>
            <input id="itemPrice" class="swal2-input" placeholder="Enter Item Price">
            <label for="itemQty" class="swal2-label">Stock Available Quantity:</label>
            <input id="itemQty" class="swal2-input" placeholder="Enter Quantity">
        `,
        focusConfirm: false,
        preConfirm: () => {
            const itemName = document.getElementById('itemName').value;
            const itemPrice = document.getElementById('itemPrice').value;
            const itemQty = document.getElementById('itemQty').value;

            // Validate inputs
            if (!itemName || !itemPrice || !itemQty) {
                Swal.showValidationMessage('Please fill in all fields');
                return false;
            }
            if (isNaN(itemPrice) || parseFloat(itemPrice) <= 0 || parseFloat(itemPrice) >= 1e11) {
                Swal.showValidationMessage('Please enter a valid item price');
                return false;
            }
            if (isNaN(itemQty) || parseInt(itemQty) <= 0 || parseInt(itemQty) >= 1e10) {
                Swal.showValidationMessage('Please enter a valid quantity');
                return false;
            }

            // Proceed with adding the raw item
            return fetch(`add_repair_stock_item-submit.php?itemName=${encodeURIComponent(itemName)}&itemPrice=${itemPrice}&itemQty=${itemQty}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to add item');
                    }
                    return response.text();
                })
                .then(html => {
                    // Update dataList and table
                    const rawItemsDataList = document.getElementById("rawItemsDataList");
                    const option = document.createElement("option");
                    option.value = itemName;
                    rawItemsDataList.appendChild(option);

                    const newRow = `<tr>
                        <td>${itemName}</td>
                        <td>${itemQty}</td>
                        <td>${parseFloat(itemPrice).toFixed(2)}</td>
                        <td><button class='deleteRawItem'>X</button></td>
                    </tr>`;
                    document.getElementById("rawItemsBody").innerHTML += newRow;

                    // Attach delete event to the new button
                    attachDeleteEvent();

                    // Update totals after adding the item
                    updateTotals();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Successfully added raw item: ${itemName} with price ${itemPrice} and quantity ${itemQty}`,
                        showConfirmButton: false,
                        timer: 2000
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                });
        }
    });

    function attachDeleteEvent() {
        const deleteButtons = document.getElementsByClassName("deleteRawItem");
        Array.from(deleteButtons).forEach(button => {
            button.addEventListener("click", function() {
                this.closest('tr').remove(); // Remove the row
                updateTotals(); // Update totals after removing the item
            });
        });
    }
});