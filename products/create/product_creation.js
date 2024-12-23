document.addEventListener('DOMContentLoaded', () => {
    const productTypeSelect = document.getElementById('productType');
    const standardFields = document.getElementById('standardFields');
    const comboFields = document.getElementById('comboFields');
    const digitalFields = document.getElementById('digitalFields');
    const serviceFields = document.getElementById('serviceFields');
    const productCodeInput = document.getElementById('productCode');
    const addComboProductBtn = document.getElementById('addComboProductBtn');
    const comboProductsList = document.getElementById('comboProductsList');

    // Handle Product Type Change
    productTypeSelect.addEventListener('change', (e) => {
        const selectedType = e.target.value;

        // Hide all specific fields first
        standardFields.style.display = 'none';
        comboFields.style.display = 'none';
        digitalFields.style.display = 'none';
        serviceFields.style.display = 'none';

        // Show relevant fields based on selection
        switch (selectedType) {
            case 'standard':
                standardFields.style.display = 'block';
                generateProductCode('STD');
                break;
            case 'combo':
                comboFields.style.display = 'block';
                generateProductCode('CMB');
                break;
            case 'digital':
                digitalFields.style.display = 'block';
                generateProductCode('DIG');
                break;
            case 'service':
                serviceFields.style.display = 'block';
                generateProductCode('SRV');
                break;
        }
    });

    // Generate Product Code based on type
    function generateProductCode(prefix) {
        const timestamp = new Date().getTime();
        productCodeInput.value = `${prefix}-${timestamp}`;
    }

    // Add Combo Product Row
    addComboProductBtn.addEventListener('click', () => {
        Swal.fire({
            title: 'Add Product to Combo',
            html: `
                <select id="comboProductSelect" class="form-select mb-2">
                    <option>Select Product</option>
                    <!-- Populate with AJAX/PHP from existing products -->
                </select>
                <input type="number" id="comboProductQuantity" class="form-control mb-2" placeholder="Quantity">
                <input type="number" id="comboProductPrice" class="form-control" placeholder="Price" step="0.01">
            `,
            showCancelButton: true,
            confirmButtonText: 'Add',
            preConfirm: () => {
                const product = document.getElementById('comboProductSelect').value;
                const quantity = document.getElementById('comboProductQuantity').value;
                const price = document.getElementById('comboProductPrice').value;

                if (!product || !quantity || !price) {
                    Swal.showValidationMessage('Please fill all fields');
                }
                return {
                    product,
                    quantity,
                    price
                };
            }
        }).then((result) => {
            if (result.value) {
                const newRow = comboProductsList.insertRow();
                newRow.innerHTML = `
                    <td>${result.value.product}</td>
                    <td>${result.value.quantity}</td>
                    <td>${result.value.price}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-combo-product">Remove</button>
                    </td>
                `;

                // Add remove functionality
                newRow.querySelector('.remove-combo-product').addEventListener('click', () => {
                    newRow.remove();
                });
            }
        });
    });

    // Form Submission with Validation
    document.getElementById('productForm').addEventListener('submit', (e) => {
        e.preventDefault();

        Swal.fire({
            title: 'Create Product',
            text: 'Are you sure you want to create this product?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform form submission via AJAX
                const formData = new FormData(e.target);

                fetch('process_product.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Created!', 'Product has been created successfully.', 'success');
                            e.target.reset();
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    });
            }
        });
    });
});