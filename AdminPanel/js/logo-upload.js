// Logo Upload Handler for Settings Page
$(document).ready(function() {
    $('#logoFileInput').change(function() {
        const file = this.files[0];
        if (!file) return;

        // Validate file type
        const validTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            Swal.fire('Invalid File', 'Please select a PNG or JPEG image file.', 'error');
            $(this).val('');
            return;
        }

        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('File Too Large', 'Please select an image smaller than 2MB.', 'error');
            $(this).val('');
            return;
        }

        // Show preview immediately
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#logoPreview').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        // Upload the file
        const formData = new FormData();
        formData.append('logo_file', file);

        const statusDiv = $('#uploadStatus');
        statusDiv.html('<i class="fas fa-spinner fa-spin"></i> Uploading...').css('color', '#007bff');

        $.ajax({
            url: 'api/upload_logo.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    statusDiv.html('<i class="fas fa-check-circle"></i> Logo uploaded successfully!').css('color', '#28a745');
                    // Update the preview with the new logo (add timestamp to bypass cache)
                    $('#logoPreview').attr('src', '../logo.png?t=' + new Date().getTime());
                    Swal.fire({
                        icon: 'success',
                        title: 'Logo Updated!',
                        text: 'Your company logo has been replaced successfully.',
                        timer: 2000,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        statusDiv.html('');
                    }, 3000);
                } else {
                    statusDiv.html('<i class="fas fa-times-circle"></i> Upload failed').css('color', '#dc3545');
                    Swal.fire('Upload Failed', response.message || 'Failed to upload logo', 'error');
                }
            },
            error: function(xhr, status, error) {
                statusDiv.html('<i class="fas fa-times-circle"></i> Upload error').css('color', '#dc3545');
                Swal.fire('Upload Error', 'Failed to upload logo. Please try again.', 'error');
                console.error('Upload error:', error);
            }
        });
    });
});
