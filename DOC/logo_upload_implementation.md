# System Settings Logo Upload Implementation

## Summary of Changes

### 1. Modified `/AdminPanel/settings.php`

**Logo Section (Lines 627-645):**
- **Removed:** Text input field for "Logo URL/Path"
- **Added:** Image upload interface with:
  - Live preview of current logo (displays `../logo.png`)
  - "Choose New Logo" button to trigger file selection
  - File type validation (PNG, JPEG, JPG only)
  - File size validation (maximum 2MB)
  - Upload status indicator
  - Hidden input to maintain compatibility

**API Base URL Section (Line 646):**
- **Hidden:** The entire "API Base URL (For Mobile App)" field by adding `style="display: none;"` to the parent div
- The field still exists in the HTML but is not visible to users
- The value is still submitted with the form to maintain backend compatibility

### 2. Created `/AdminPanel/api/upload_logo.php`

New API endpoint to handle logo file uploads with the following features:

- **Security:**
  - Requires user to be logged in (`checkLogin.php`)
  - Validates file type (only PNG, JPEG, JPG allowed)
  - Validates file size (maximum 2MB)
  - Uses `mime_content_type()` for reliable file type checking

- **Functionality:**
  - Backs up existing logo before replacement (`logo_backup_YYYY-MM-DD_HH-ii-ss.png`)
  - Replaces `../logo.png` with the uploaded file
  - Logs the action with user details
  - Returns JSON response with success/error status

- **Response Format:**
  ```json
  {
    "success": true,
    "message": "Logo uploaded successfully",
    "path": "logo.png",
    "timestamp": 1234567890
  }
  ```

### 3. Created `/AdminPanel/js/logo-upload.js`

JavaScript file that handles the client-side logic:

- **File Selection:**
  - Triggered when user selects a file via the hidden input
  - Client-side validation before upload (file type and size)
  - Shows immediate preview using FileReader API

- **Upload Process:**
  - Uses FormData and AJAX to upload file
  - Displays spinner during upload
  - Shows success/error messages using SweetAlert2
  - Updates preview image with timestamp to bypass cache
  - Clears status message after 3 seconds

- **User Feedback:**
  - Invalid file type: Error popup
  - File too large: Error popup
  - Upload in progress: Spinner with "Uploading..." message
  - Upload success: Toast notification + success message
  - Upload failure: Error popup with server message

## How It Works

1. **User clicks "Choose New Logo"** → File picker opens
2. **User selects an image file** → Client-side validation runs
3. **If valid** → Preview updates immediately → File uploads to server
4. **Server receives file** → Validates again → Backs up old logo → Saves new logo
5. **Success response** → Preview updates with new logo → Success notification displays

## Final Setup Required

### Add Script Include to settings.php

You need to manually add the following line to `/AdminPanel/settings.php` after line 1100 (after the `</div> <!-- End Settings Container -->` comment):

```html
<!-- Logo Upload Script -->
<script src="js/logo-upload.js"></script>
```

**Location:** Insert between line 1100 and the  existing `<script>` tag on line 1102.

**Before:**
```html
</div> <!-- End Settings Container -->

<script>
```

**After:**
```html
</div> <!-- End Settings Container -->

<!-- Logo Upload Script -->
<script src="js/logo-upload.js"></script>

<script>
```

## Testing

After adding the script include:

1. Navigate to `/AdminPanel/settings.php`
2. Scroll to the "Company Profile" section
3. You should see:
   - Current logo is displayed (if exists)
   - "Choose New Logo" button is visible
   - "API Base URL" field is **hidden**
4. Click "Choose New Logo"
5. Select a PNG or JPEG image (max 2MB)
6. Preview should update immediately
7. Upload should complete with success notification
8. Check that `../logo.png` has been replaced with your uploaded image

## Troubleshooting

**Logo not appearing:**
- Check that `logo.png` exists in the root directory
- Verify the path `../logo.png` is correct from the AdminPanel folder

**Upload fails:**
- Check file permissions on the root directory (needs write access)
- Verify the API endpoint path is correct
- Check browser console for JavaScript errors
- Verify that `checkLogin.php` and `connection.php` are included properly

**Script not loading:**
- Verify that `js/logo-upload.js` file exists in `/AdminPanel/js/`
- Check browser console for 404 errors
- Ensure the script include tag was added correctly

**API Base URL still visible:**
- Hard refresh the page (Ctrl+F5)
- Check that the `style="display: none;"` attribute is on the correct div (line 646)

## Backup Information

- Old logos are automatically backed up to the root directory with timestamps
- Format: `logo_backup_2026-01-16_03-24-38.png`
- These can be manually restored if needed by renaming them back to `logo.png`

## Benefits of This Implementation

1. **User-Friendly:** Simple drag-and-drop-style interface
2. **Safe:** Always backs up before replacing
3. **Validated:** Both client and server-side validation
4. **Automatic:** Logo path is always `logo.png` - no manual entry needed
5. **Clean UI:** API Base URL field hidden but still functional in backend
6. **Real-time Feedback:** Users see preview and status immediately

## File Summary

**Modified Files:**
- `/AdminPanel/settings.php` (UI changes)

**New Files:**
- `/AdminPanel/api/upload_logo.php` (Upload handler)
- `/AdminPanel/js/logo-upload.js` (Client-side logic)

**Affected Files:**
- `../logo.png` (Gets replaced when user uploads)
- `../logo_backup_*.png` (Backup files created automatically)
