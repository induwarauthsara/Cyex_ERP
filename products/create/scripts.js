// Handle Product Type Change
function handleProductTypeChange() {
    var productType = document.getElementById('productType').value;

    // Hide all dynamic fields
    document.getElementById('comboFields').style.display = 'none';
    document.getElementById('digitalFields').style.display = 'none';
    document.getElementById('serviceFields').style.display = 'none';

    // Show specific fields based on product type
    if (productType === 'combo') {
        document.getElementById('comboFields').style.display = 'block';
    } else if (productType === 'digital') {
        document.getElementById('digitalFields').style.display = 'block';
    } else if (productType === 'service') {
        document.getElementById('serviceFields').style.display = 'block';
    }
}

// Add Product to Combo Table
function addComboProduct() {
    var table = document.getElementById('comboTable').getElementsByTagName('tbody')[0];
    var newRow = table.insertRow(table.rows.length);

    var productCell = newRow.insertCell(0);
    var quantityCell = newRow.insertCell(1);
    var priceCell = newRow.insertCell(2);

    productCell.innerHTML = '<input type="text" name="comboProduct[]">';
    quantityCell.innerHTML = '<input type="number" name="comboQuantity[]">';
    priceCell.innerHTML = '<input type="number" name="comboPrice[]">';
}