$(document).on('keydown', function(event) {
    if (event.key === "+") {
        event.preventDefault(); // Prevent the default behavior of typing "+"
        searchCustomer();
    }
});


function searchCustomer() {
    Swal.fire({
        title: 'Select Customer',
        html: `
                <label for="swal-customer-phone">Customer Phone Number :</label>
                <input id="swal-customer-phone" class="swal2-input" autofocus autocomplete="off" maxlength="10">
                <label for="swal-customer-name">Customer Name:</label>
                <input id="swal-customer-name" class="swal2-input" autocomplete="off">
                <div div id = "customerContainer"
                class = "w-100 position-absolute text-white overflow-auto"
                style = "max-height: 200px; background: #fff; border-radius: 3px; display: none; max-height: 400px; background: #fff; overflow-y: auto; width:100%; z-index:1000;"></div>
            `,
        focusConfirm: false,
        showConfirmButton: false,
        showCancelButton: false,
        didOpen: () => {
            $('#swal-customer-phone').focus();
        },
        preConfirm: () => {
            return {
                name: $('#swal-customer-name').val(),
                phone: $('#swal-customer-phone').val()
            };
        },
    }).then((result) => {
        if (result.isConfirmed) {
            $('#name').val(result.value.name);
            $('#tele').val(result.value.phone);
        }
    });

    let highlightedIndex = -1;

    // Handle input event on phone and name fields
    $('#swal-customer-phone, #swal-customer-name').on('input', function() {
        const phone = $('#swal-customer-phone').val();
        const name = $('#swal-customer-name').val();

        // Instant focus change if 10 digits are entered
        if (phone.length === 10) {
            $('#swal-customer-name').focus();
        }

        // Trigger AJAX search on any input change
        $.ajax({
            url: '/inc/search_customers.php',
            type: 'GET',
            data: {
                phone,
                name
            },
            success: function(response) {
                $('#customerContainer').empty().show(); // Clear previous suggestions

                if (response.length === 0 && phone.length === 10) {
                    // No matching customer; focus on name for adding new customer
                    $('#swal-customer-name').focus();
                } else if (phone.length === 10 && response.length === 1) {
                    // Automatically select if only one customer is found
                    selectCustomer(response[0]);
                    Swal.close();
                } else {
                    // Multiple customers found - display them as suggestions
                    response.forEach((customer) => {
                        const customerDiv = $(`<div class="m-2" data-name="${customer.name}" data-phone="${customer.phone}" style="cursor: pointer; margin-bottom:10px; border:1px solid black; border-radius: 3px;">
                            Name: ${customer.name} <br/> Phone: ${customer.phone}
                        </div>`);

                        // Add click event for selection
                        customerDiv.on('click', function() {
                            selectCustomer(customer);
                            Swal.close();
                        });

                        $('#customerContainer').append(customerDiv);
                    });
                }

                highlightedIndex = -1; // Reset highlight index
            }
        });
    });

    // Handle keyboard navigation and selection
    $('#swal-customer-phone, #swal-customer-name').on('keydown', function(e) {
        const suggestions = $('#customerContainer div');
        const phone = $('#swal-customer-phone').val();
        const name = $('#swal-customer-name').val();

        if (e.key === "ArrowDown") {
            e.preventDefault();
            if (highlightedIndex < suggestions.length - 1) {
                highlightedIndex++;
                updateHighlight(suggestions);
            }
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            if (highlightedIndex > 0) {
                highlightedIndex--;
                updateHighlight(suggestions);
            }
        } else if (e.key === "Enter") {
            e.preventDefault();

            if (suggestions.length === 0 && phone.length === 10 && name) {
                // No customer found - add new customer if no suggestions
                addNewCustomer(name, phone);
                Swal.close();
            } else if (highlightedIndex >= 0 && suggestions[highlightedIndex]) {
                // If a customer is highlighted, select that customer
                const selectedSuggestion = $(suggestions[highlightedIndex]);
                selectCustomer({
                    name: selectedSuggestion.data('name'),
                    phone: selectedSuggestion.data('phone')
                });
                Swal.close();
            } else if (phone.length === 10 && suggestions.length === 1) {
                // If only one suggestion is available, select it
                selectCustomer({
                    name: $(suggestions[0]).data('name'),
                    phone: $(suggestions[0]).data('phone')
                });
                Swal.close();
            }
        }
    });

    // Update highlight function
    function updateHighlight(suggestions) {
        suggestions.removeClass('highlighted');
        if (highlightedIndex >= 0) {
            $(suggestions[highlightedIndex]).addClass('highlighted');
        }
    }

    // Function to populate selected customer data
    function selectCustomer(customer) {
        $('#name').val(customer.name);
        $('#tele').val(customer.phone);
    }

    // Function to handle adding a new customer with JSON response handling
    function addNewCustomer(name, phone) {
        $.ajax({
            url: '/inc/save_new_customer.php',
            type: 'POST',
            data: {
                name,
                phone
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#name').val(response.customer.name);
                    $('#tele').val(response.customer.phone);
                    Swal.fire('New Customer Added', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'There was an issue adding the new customer.', 'error');
            }
        });
    }
}