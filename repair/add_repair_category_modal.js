document.querySelector('#add_new_category').addEventListener('click', function() {
    Swal.fire({
        title: 'Add New Repair Category',
        html: `
            <label for="categoryName" class="swal2-label">Category Name:</label>
            <input id="categoryName" class="swal2-input" placeholder="Enter Category Name">
        `,
        focusConfirm: false,
        preConfirm: () => {
            const categoryName = document.getElementById('categoryName').value;

            // Validate input
            if (!categoryName) {
                Swal.showValidationMessage('Please enter a category name');
                return false;
            }

            // Send the request to add the new category
            return fetch(`AddRepairCategory-submit.php?categoryName=${encodeURIComponent(categoryName)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to add category');
                    }
                    return response.text();
                })
                .catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Category added successfully!',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
});