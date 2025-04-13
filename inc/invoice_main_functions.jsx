// Global variable for row counter
var no = window.no ?? 0;

// Add Customer Phone Number Auto Complete Function
function customer_add() {
    // check name available in dB
    var customerNameElement = document.getElementById("name");
    var customerMobileElement = document.getElementById("tele");
    
    if (!customerNameElement || !customerMobileElement) {
        console.log("Customer elements not found in DOM");
        return;
    }
    
    var customer_name = customerNameElement.value;
    var customer_mobile = customerMobileElement.value;
    
    //alert(customer_name);
    $.ajax({
        url: "/inc/get_customer_mobile.php",
        method: "POST",
        data: {
            cus: customer_name
        },
        datatype: "text",
        cache: false,
        success: function (html) {
            //alert(html);
            if (html == "customer Not Found") {
                customerMobileElement.value = 7;
            } else {
                customerMobileElement.value = html;
            }
        },
    });
}

// Add Input Event Listener to #advance Input Field 
var advance = document.getElementById('advance');
if (advance) {
    advance.addEventListener('input', function () {
        // if advance is available, disable name[submit_and_fullPayment] and name[submit_and_print_fullPayment] buttons
        var submitButtons = document.querySelectorAll('button[name="submit_and_fullPayment"], button[name="submit_and_print_fullPayment"]');
        if (submitButtons.length > 0) {
            var disabled = advance.value > 0;
            submitButtons.forEach(function(button) {
                button.disabled = disabled;
            });
        }
    });
}


function addproduct(oneTimeProductName, oneTimeProductRate, oneTimeProductQty, oneTimeProduct) {
    const product_list = document.getElementById('list');
    const disc_list = document.getElementById('Disc');
    const worker_list = document.getElementById('worker');
    const qty_list = document.getElementById('qty');
    const rate_list = document.getElementById('rate');
    const amount_list = document.getElementById('amount_list');
    const remove_button_list = document.getElementById('remove_button_list');
    
    // Check if required elements exist
    if (!product_list || !qty_list || !rate_list || !amount_list || !remove_button_list) {
        console.error("Required DOM elements for adding products not found");
        Swal.fire({
            icon: 'error',
            title: 'Element Error',
            text: 'Required page elements not found. Please refresh the page and try again.'
        });
        return;
    }

    product_name = oneTimeProductName ?? document.getElementById('addproduct')?.value ?? '';

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
        product.setAttribute("readonly", "readonly");

        // Create a Qty in row
        let qty = document.createElement("input");
        qty.id = "qty_" + no;
        qty.className = no;
        qty.type = "number";
        qty.value = oneTimeProductQty ?? 1;
        qty.setAttribute("oninput", "change('qty', className, id)");
        qty.setAttribute("step", "0.001")
        qty.setAttribute("name", qty.id);

        // Create a Rate in row
        let rate = document.createElement("input");
        rate.id = "rate_" + no;
        // Set Rate Value
        oneTimeProductRate ? rate.value = oneTimeProductRate :
            $.ajax({
                url: "/inc/get_product_rate.php",
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

        // If it's a one-time product, add a hidden input to identify it
        if (oneTimeProduct) {
            let oneTimeProductIdentifier = document.createElement("input");
            oneTimeProductIdentifier.id = "oneTimeProductID_" + no;
            oneTimeProductIdentifier.type = "hidden";
            oneTimeProductIdentifier.value = "true";
            oneTimeProductIdentifier.setAttribute("name", "oneTimeProductID_" + no);
            product_list.appendChild(oneTimeProductIdentifier);
        }

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

function addRepair() {
    const product_list = document.getElementById('list');
    const disc_list = document.getElementById('Disc');
    const worker_list = document.getElementById('emoloyee_list'); // Ensure correct spelling in the HTML
    const qty_list = document.getElementById('qty');
    const rate_list = document.getElementById('rate');
    const amount_list = document.getElementById('amount_list');
    const remove_button_list = document.getElementById('remove_button_list');

    let product_name = document.getElementById('addproduct').value;

    function add_row(no) {

        // Check this product available in #products datalist
        var dataList = document.getElementById('products');
        var options = dataList.getElementsByTagName('option');
        var productList = [];

        for (var i = 0; i < options.length; i++) {
            productList.push(options[i].value);
        }

        var available = productList.includes(product_name);

        if (!available) {
            // show error
            Swal.fire({
                icon: 'error',
                title: 'Repair Not Found',
                text: 'This Repair Item not Found.'
            });
            // Stop programme
            return
        }

        // Create Repair Name in row
        let product = document.createElement("input");
        product.value = product_name;
        product.id = "product_" + no;
        product.className = `${no} bill-row-gap`;
        product.setAttribute("list", "products");
        product.setAttribute("name", product.id);
        product.setAttribute("onchange", "change('product', className, id)");
        product.setAttribute("readonly", "readonly");

        // Create a Cost in row
        let qty = document.createElement("input");
        qty.id = "qty_" + no;
        qty.className = `${no} bill-row-gap`;
        qty.type = "number";
        qty.setAttribute("oninput", "change('qty', className, id)");
        qty.setAttribute("step", "any")
        qty.setAttribute("name", qty.id);

        // Create a select input for Employee Name
        let worker = document.createElement("select");
        worker.id = "worker_" + no;
        worker.className = no;
        worker.setAttribute("name", worker.id);

        // Fetch and populate employee list via AJAX
        $.ajax({
            url: "../inc/get_employees.php",
            method: "POST",
            dataType: "json",
            cache: false,
            success: function (response) {
                if (response.success) {
                    response.employees.forEach(employee => {
                        let option = document.createElement('option');
                        option.value = employee;
                        option.text = employee;
                        worker.appendChild(option);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Employee Fetch Error',
                        text: 'Failed to fetch employee data. Please try again.'
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Request Error',
                    text: 'Unable to retrieve employee data from the server.'
                });
            }
        });

        // Create a Commission input for the selected employee
        let rate = document.createElement("input");
        rate.id = "rate_" + no;
        rate.className = `${no} bill-row-gap-commission`;
        rate.type = "number";
        rate.setAttribute("oninput", "change('rate', className, id)");
        rate.setAttribute("oninput", "change_endline()");
        rate.setAttribute("step", "any")
        rate.setAttribute("name", rate.id);

        // Create a Selling Price in row
        let amount = document.createElement("input");
        amount.id = "amount_" + no;
        amount.className = `${no} bill-row-gap`;
        amount.setAttribute("oninput", "change('amount', className, id)");
        amount.setAttribute("oninput", "change_endline()");
        amount.setAttribute("name", amount.id);

        // Fetch cost, commission, and selling price using AJAX
        $.ajax({
            url: "/repair/get_repair_data.php",
            method: "POST",
            data: { product: product_name },
            dataType: "json",
            cache: false,
            success: function (response) {
                if (response.success) {
                    qty.value = Number(response.cost).toFixed(2); // Set cost
                    rate.value = Number(response.commission).toFixed(2); // Set employee commission
                    amount.value = Number(response.selling_price).toFixed(2); // Set selling price
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fetch Error',
                        text: 'Failed to fetch product data. Please try again.'
                    });
                }
                add_total(no + 1); // Recalculate total after data is fetched
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Request Error',
                    text: 'Unable to retrieve data from the server.'
                });
            }
        });

        // Create a Close Button in row
        let x = document.createElement("button");
        x.id = "_" + no;
        x.className = "x"
        x.innerText = "[x]"
        x.setAttribute("onclick", 'remove_row(id, className);');

        // Append elements to the respective lists
        // Create a function to generate the new row gap
        // function createNewRowGap() {
        //     globalThis.no = no; // Set the global 'no' variable to the current 'no' value
        //     let gapDiv = document.createElement("div");
        //     gapDiv.id = "gapDiv_" + no; // Set the ID based on the 'no' variable
        //     gapDiv.innerHTML = "<br><br>"; // Add the innerHTML content
        //     return gapDiv;
        // }


        // Append elements to the respective lists
        product_list.appendChild(product);
        // product_list.appendChild(createNewRowGap()); // Create a new row gap for each append
        qty_list.appendChild(qty);
        // qty_list.appendChild(createNewRowGap()); // New row gap for quantity list
        rate_list.appendChild(worker);
        rate_list.appendChild(rate);
        // rate_list.appendChild(document.createElement("br"));
        amount_list.appendChild(amount);
        // amount_list.appendChild(createNewRowGap());
        remove_button_list.appendChild(x);
        // amount_list.appendChild(createNewRowGap()); // New row gap for amount list
    }

    // Adding the row
    add_row(no);
    document.getElementById('no').value = no;
    no++;
    add_total(no);

    // Clear Product Input field
    document.getElementById('addproduct').value = "";
}

function change(row, cls, id) {

    // var changed_desc = document.getElementById("product_" + cls).value;
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
    let worker_remove = document.getElementById("worker" + number);
    let amount_remove = document.getElementById("amount" + number);
    let x_remove = document.getElementById(number);

    // Remove Row
    let remove = [product_remove, qty_remove, rate_remove, worker_remove, amount_remove, x_remove]

    // Check Saved Sales Record Id is available
    if (document.getElementById("rowID" + number)) {
        let row_id_remove = document.getElementById("rowID" + number);
        remove.push(row_id_remove);
    }

    // Check Saved One Time Product Id is available
    if (document.getElementById("oneTimeProductID" + number)) {
        let oneTimeProductID_remove = document.getElementById("oneTimeProductID" + number);
        remove.push(oneTimeProductID_remove);
    }

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
        title: 'Add One Time Product Details',
        html: `
            <div style="text-align: right; margin-bottom: 10px;">
                <a href="/products/create/" class="btn btn-success" style="padding: 5px 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            <label for='oneTimeProductName' class='swal2-label'> Product Name:</label>
            <input id="oneTimeProductName" class="swal2-input" value="${productName}" placeholder="Enter Product Name"><br>
            <label for="oneTimeProductRegularPrice" class="swal2-label">Regular Price (Rs.):</label>
            <input id="oneTimeProductRegularPrice" class="swal2-input" type="number" step="0.01" placeholder="Enter Regular Price"><br>
            <label for="oneTimeProductDiscountPrice" class="swal2-label">Discount Price (Rs.):</label>
            <input id="oneTimeProductDiscountPrice" class="swal2-input" type="number" step="0.01" placeholder="Enter Discount Price (optional)"><br>
            <label for="oneTimeProductQty" class="swal2-label">Quantity:</label>
            <input id="oneTimeProductQty" class="swal2-input" type="number" step="0.001" placeholder="Enter Quantity">`,
        focusConfirm: false,
        preConfirm: () => {
            const oneTimeProductName = Swal.getPopup().querySelector('#oneTimeProductName').value;
            const oneTimeProductRegularPrice = Swal.getPopup().querySelector('#oneTimeProductRegularPrice').value;
            const oneTimeProductDiscountPrice = Swal.getPopup().querySelector('#oneTimeProductDiscountPrice').value;
            const oneTimeProductQty = Swal.getPopup().querySelector('#oneTimeProductQty').value;
            
            // Use discount price if provided, otherwise use regular price
            const finalRate = oneTimeProductDiscountPrice && !isNaN(oneTimeProductDiscountPrice) 
                ? oneTimeProductDiscountPrice 
                : oneTimeProductRegularPrice;
                
            if (oneTimeProductName && oneTimeProductRegularPrice && oneTimeProductQty && 
                !isNaN(oneTimeProductRegularPrice) && !isNaN(oneTimeProductQty)) {
                
                // Check if we're in the main POS interface by checking for required DOM elements
                const requiredElements = document.getElementById('list') && 
                                         document.getElementById('qty') && 
                                         document.getElementById('rate') && 
                                         document.getElementById('amount_list') && 
                                         document.getElementById('remove_button_list');
                
                if (requiredElements) {
                    // If we're in the main POS interface, add the product directly
                    addproduct(oneTimeProductName, finalRate, oneTimeProductQty, true);
                    
                    // Store the original regular price and discount price for later access
                    if (!window.oneTimeProducts) {
                        window.oneTimeProducts = [];
                    }
                    
                    window.oneTimeProducts.push({
                        name: oneTimeProductName,
                        regularPrice: parseFloat(oneTimeProductRegularPrice),
                        discountPrice: parseFloat(oneTimeProductDiscountPrice || oneTimeProductRegularPrice),
                        quantity: parseFloat(oneTimeProductQty),
                        isOneTimeProduct: true
                    });
                } else {
                    // If not in the main POS interface, we're probably in the product search
                    // Add to cart via the product list
                    if (typeof productList !== 'undefined') {
                        const regularPrice = parseFloat(oneTimeProductRegularPrice);
                        const discountPrice = parseFloat(oneTimeProductDiscountPrice || oneTimeProductRegularPrice);
                        
                        // Generate a unique batch ID for this one-time product
                        const batchId = 'OTP-' + Date.now();
                        
                        // Add to product list
                        productList.push({
                            name: oneTimeProductName,
                            product_id: 'temp_' + Date.now(),
                            batch_id: batchId,
                            regular_price: regularPrice,
                            discount_price: discountPrice,
                            quantity: parseFloat(oneTimeProductQty),
                            subtotal: (parseFloat(oneTimeProductQty) * parseFloat(finalRate)),
                            isOneTimeProduct: true
                        });
                        
                        // Store in localStorage
                        localStorage.setItem('productList', JSON.stringify(productList));
                        
                        // If these functions exist, call them to update the UI
                        if (typeof renderProductList === 'function') {
                            renderProductList();
                        }
                        
                        if (typeof calculateInvoiceTotal === 'function') {
                            calculateInvoiceTotal();
                        }
                        
                        // Store in oneTimeProducts array for tracking
                        if (!window.oneTimeProducts) {
                            window.oneTimeProducts = [];
                        }
                        
                        window.oneTimeProducts.push({
                            name: oneTimeProductName,
                            regularPrice: regularPrice,
                            discountPrice: discountPrice,
                            quantity: parseFloat(oneTimeProductQty),
                            isOneTimeProduct: true
                        });
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Added',
                            text: 'One-time product added to cart successfully',
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Unable to add product to cart. Product list not found.'
                        });
                    }
                }
            } else {
                Swal.showValidationMessage(`Please enter all required fields correctly.`);
            }
        }
    });
}


add_total(no);