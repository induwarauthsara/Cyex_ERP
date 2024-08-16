var no = 0;

// Add Customer Phone Number Auto Complete Function
function customer_add() {
    // check name available in dB
    var customer_name = document.getElementById("name").value;
    var customer_mobile = document.getElementById("tele").value;
    //alert(customer_name);
    $.ajax({
        url: "inc/get_customer_mobile.php",
        method: "POST",
        data: {
            cus: customer_name
        },
        datatype: "text",
        cache: false,
        success: function (html) {
            document.getElementById('tele').value = html;
            //alert(html);
        },
    });
}

// Add Input Event Listener to #advance Input Field 
document.getElementById('advance').addEventListener('input', function () {
    var advance = document.getElementById('advance');
    // if advance is available, disable name[submit_and_fullPayment] and name[submit_and_print_fullPayment] buttons
    if (advance.value > 0) {
        document.querySelector('button[name="submit_and_fullPayment"]').disabled = true;
        document.querySelector('button[name="submit_and_print_fullPayment"]').disabled = true;
    } else {
        document.querySelector('button[name="submit_and_fullPayment"]').disabled = false;
        document.querySelector('button[name="submit_and_print_fullPayment"]').disabled = false;
    }
});


function addproduct(oneTimeProductName, oneTimeProductRate, oneTimeProductQty, oneTimeProduct) {
    const product_list = document.getElementById('list');
    const disc_list = document.getElementById('Disc');
    const worker_list = document.getElementById('worker');
    const qty_list = document.getElementById('qty');
    const rate_list = document.getElementById('rate');
    const amount_list = document.getElementById('amount_list');
    const remove_button_list = document.getElementById('remove_button_list');

    product_name = oneTimeProductName ?? document.getElementById('addproduct').value;

    function add_row(no) {

        if (!oneTimeProduct) {
            // Check this product available in #products datalist
            var dataList = document.getElementById('products');
            var options = dataList.getElementsByTagName('option');
            var productList = [];

            for (var i = 0; i < options.length; i++) {
                productList.push(options[i].value);
            }

            var available = productList.includes(product_name);

            if (!available) {
                // alert(product_name + " is not available."); // enable if Dev mode
                // Modal box for Onetime Add Product
                addonetimeproductModal(product_name);
                // Stop programme
                return
            }
        }

        // console.log(productList); // Output the product list array for debugging - for`  `   ` Dev mode

        //  let product = document.getElementById('addproduct').value;
        var product = document.createElement("input");
        product.value = product_name;
        product.id = "product_" + no;
        product.className = no, 'ooo';
        product.setAttribute("list", "products");
        product.setAttribute("name", product.id);
        product.setAttribute("onchange", "change('product', className, id)");

        // Create a Qty in row
        let qty = document.createElement("input");
        qty.id = "qty_" + no;
        qty.className = no;
        qty.type = "number";
        qty.value = oneTimeProductQty ?? 1;
        qty.setAttribute("oninput", "change('qty', className, id)");
        qty.setAttribute("step", "any")
        qty.setAttribute("name", qty.id);

        // Create a Rate in row
        let rate = document.createElement("input");
        rate.id = "rate_" + no;
        // Set Rate Value
        oneTimeProductRate ? rate.value = oneTimeProductRate :
            $.ajax({
                url: "inc/get_product_rate.php",
                method: "POST",
                data: {
                    product: product.value
                },
                datatype: "text",
                cache: false,
                success: function (html) {
                    $rate_db = rate.value = Number(html).toFixed(2);
                    amount.value = Number(qty.value * rate.value).toFixed(2);
                    add_total(no + 1);
                },
            });
        //
        rate.className = no;
        // rate = rate.value.toFixed(2);
        rate.setAttribute("oninput", "change('rate', className, id)");
        rate.setAttribute("name", rate.id);
        rate.value = Number(rate.value);


        // Create a Amount in row
        let amount = document.createElement("input");
        amount.id = "amount_" + no;
        amount.className = no;
        amount.value = Number(qty.value * rate.value).toFixed(2);
        amount.setAttribute("oninput", "change('amount', className, id)");
        amount.setAttribute("name", amount.id);

        // Create a Close Button in row
        let x = document.createElement("button");
        x.id = "_" + no;
        x.className = "x"
        x.innerText = "[x]"
        x.setAttribute("onclick", 'remove_row(id, className);');


        product_list.appendChild(product);
        qty_list.appendChild(qty);
        rate_list.appendChild(rate);
        amount_list.appendChild(amount);
        remove_button_list.appendChild(x);
    }


    add_row(no);
    document.getElementById('no').value = no;
    no++;
    add_total(no);

    //     return product.value;
    var product_name = document.getElementById('addproduct').value;

    // Clear Product Input field
    document.getElementById('addproduct').value = "";
}

function change(row, cls, id) {

    var changed_desc = document.getElementById("product_" + cls).value;
    var changed_qty = document.getElementById("qty_" + cls).value;
    var changed_rate = document.getElementById("rate_" + cls).value;
    var changed_amount = document.getElementById("amount_" + cls).value;

    // Correct Ammount row
    changed_amount = changed_qty * changed_rate;
    document.getElementById("amount_" + cls).value = changed_amount;

    // Correct Total row
    change_endline();

    //Correct Decimals of Discount & Advance           
    decimal(no); // me line eka oninam ain karanna puluwna
}

function change_endline() {
    add_total(no);
    decimal(no);
}

function decimal(no) {
    no--;
    for (; no > -1; no--) {
        let decimal_row_amount = "amount_" + no;
        let decimal_row_rate = "rate_" + no;

        //amount
        const all_amount_selector = document.querySelectorAll("#amount_list input");
        for (var i = 0; i < all_amount_selector.length; i++) {
            all_amount_selector[i].value = Number(all_amount_selector[i].value).toFixed(2);
        }

        //rate
        const all_rate_selector = document.querySelectorAll("#rate input");
        for (var i = 0; i < all_rate_selector.length; i++) {
            all_rate_selector[i].value = Number(all_rate_selector[i].value).toFixed(2);
        }

        //Total
        document.getElementById("total").value = Number(document.getElementById("total").value).toFixed(2);

        //Discount
        document.getElementById("discount").value = Number(document.getElementById("discount").value).toFixed(2);

        // Advance
        document.getElementById("advance").value = Number(advance.value).toFixed(2);

        //Balance
        document.getElementById("balance").value = Number(balance.value).toFixed(2);


    }
}

function add_total(no) {
    const all_amount_selector = document.querySelectorAll("#amount_list input");

    var amount_sum = 0;

    for (var i = 0; i < all_amount_selector.length; i++) {
        amount_sum += Number(all_amount_selector[i].value);
        document.getElementById('total').value = amount_sum;
    }

    var discount = document.getElementById("discount");
    var advance = document.getElementById("advance");
    var balance = document.getElementById("balance");

    // Make Balance
    balance.value = Number(total.value) - Number(discount.value) - Number(advance.value)

    decimal(no);

}

function remove_row(number, className) {
    let product_remove = document.getElementById("product" + number);
    let qty_remove = document.getElementById("qty" + number);
    let rate_remove = document.getElementById("rate" + number);
    let amount_remove = document.getElementById("amount" + number);
    let x_remove = document.getElementById(number);

    // Remove Row
    let remove = [product_remove, /*description_remove, worker_remove,*/ qty_remove, rate_remove, amount_remove, x_remove]
    remove.forEach(element => {
        element.remove();
    });


    // Correct Amount
    add_total(no);
    change('remove', className, number);
    change_endline();
}

// =========================== One Time Product ===========================
function addonetimeproductModal(productName) {
    // Add Modal Box for get details of one time product. Input fields: Product Name, Rate, Qty
    // check ProductName assigned

    Swal.fire({
        title: 'Add One Time Product',
        html: `<label for='oneTimeProductName' class='swal2-label'Product Name:</label>` +
            `<input id="oneTimeProductName" class="swal2-input" value="${productName}" placeholder="Enter Product Name">` +
            '<label for="oneTimeProductRate" class="swal2-label">Rate (Rs.):</label>' +
            '<input id="oneTimeProductRate" class="swal2-input" placeholder="Enter Rate">' +
            '<label for="oneTimeProductQty" class="swal2-label">Quantity:</label>' +
            '<input id="oneTimeProductQty" class="swal2-input" placeholder="Enter Quantity">',
        focusConfirm: false,
        preConfirm: () => {
            const oneTimeProductName = Swal.getPopup().querySelector('#oneTimeProductName').value;
            const oneTimeProductRate = Swal.getPopup().querySelector('#oneTimeProductRate').value;
            const oneTimeProductQty = Swal.getPopup().querySelector('#oneTimeProductQty').value;
            if (oneTimeProductName && oneTimeProductRate && oneTimeProductQty && !isNaN(oneTimeProductRate) && !isNaN(oneTimeProductQty)) {
                // Add one time product to the bill
                addproduct(oneTimeProductName, oneTimeProductRate, oneTimeProductQty, true);
            } else {
                Swal.showValidationMessage(`Please enter all fields correctly.`);
            }
        }
    });
}


add_total(no);