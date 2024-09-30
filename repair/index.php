<form method="post" action="add_repair_invoice.php">
    <label for="category">Repair Category:</label>
    <select name="category_id" id="category" required>
        <?php
        // Fetch and display repair categories from the database
        $result = mysqli_query($conn, "SELECT * FROM repair_categories");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
        }
        ?>
    </select>

    <label for="item">Repair Item:</label>
    <input list="items" name="item_id" id="item" required>
    <datalist id="items">
        <!-- This will be dynamically populated based on the selected category via JavaScript -->
    </datalist>

    <label for="cost">Cost (Stock Cost):</label>
    <input type="text" name="cost" id="cost" value="" required>

    <label for="worker_commission">Worker Commission:</label>
    <input type="number" name="worker_commission" id="worker_commission" value="0" required>

    <label for="selling_price">Selling Price:</label>
    <input type="text" name="selling_price" id="selling_price" required>

    <label for="worker">Worker Name:</label>
    <select name="worker_id" id="worker" required>
        <?php
        // Fetch and display workers from the employee table
        $result = mysqli_query($conn, "SELECT * FROM employee");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <button type="submit">Submit Repair Invoice</button>
</form>