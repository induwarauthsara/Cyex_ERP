<form method="POST">
    <div>
        <input type="checkbox" name="add_to_todo" id="add_to_todo" value="1" style="width: 50px; height:30px;">
        <label for="add_to_todo"> Add to TODO List </label><br>


        <label for="todoName"> TODO Name : </label> 
        <input type="text" name="todoName" id="todoName" disabled required style="border: 1px solid black; padding:5px; margin: 2px;"><br>

        <label for="todoTime"> Submission Date & Time : </label> 
        <input type="datetime-local" name="todoTime" disabled required id="todoTime" style="border: 1px solid black; padding:5px;  margin: 2px;">
    </div>
    <div>
        <input type="submit" value="Submit" style="padding: 5px; margin: 2px;">
    </div>
</form>

<script>
    const checkbox = document.getElementById('add_to_todo');
    const todoNameInput = document.getElementById('todoName');
    const todoTimeInput = document.getElementById('todoTime');

    // Adding event listener to checkbox
    checkbox.addEventListener('change', function() {
        // If checkbox is checked, enable input fields; otherwise, disable them
        if (this.checked) {
            todoNameInput.disabled = false;
            todoTimeInput.disabled = false;
        } else {
            todoNameInput.disabled = true;
            todoTimeInput.disabled = true;
        }
    });
</script>

<?php
// Check POST request method and if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Display the POST data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Check todo
    if (isset($_POST['add_to_todo'])) {
        $todoName = $_POST['todoName'];
        $todoTime = $_POST['todoTime'];
        $bill_no = '550';
        $sql = "INSERT INTO `todo`(`invoice_number`, `title`, `submision_time`) VALUES ('$bill_no','$todoName','$todoTime')";
        echo $sql;
        // insert_query($sql, "Add Todo Item Data to Database");
    }
}
