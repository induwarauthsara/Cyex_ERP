              // Add Todo
              // Active TODO Input Fields accourding to Checkbox
              // Selecting checkbox and input fields
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

              // Add Todo POP UP BOX
              document.querySelector('.add_todo').addEventListener('click', function() {
                  Swal.fire({
                      title: 'Add TODO Work',
                      html: '<label for="todoName" class="swal2-label">Work Name:</label>' +
                          '<input id="todoName" class="swal2-input" placeholder="Enter Work Name">' +
                          '<label for="todoTime" class="swal2-label">Submission Date & Time</label>' +
                          '<input id="todoTime" class="swal2-input" type="datetime-local" placeholder="Enter amount">',
                      focusConfirm: false,
                      preConfirm: () => {
                          const todoName = Swal.getPopup().querySelector('#todoName').value;
                          const todoTime = Swal.getPopup().querySelector('#todoTime').value;
                          if (todoName && todoTime) {
                              return fetch("inc/add_todo_item.php?todoName=" + encodeURIComponent(todoName) + "&todoTime=" + todoTime, {
                                      method: 'GET',
                                  })
                                  .then(response => response.text())
                                  .then(html => {
                                      Swal.fire({
                                          icon: 'success',
                                          title: 'Success',
                                          text: "Successfully added TODO : " + todoName + ". It must done at " + todoTime,
                                          showConfirmButton: false,
                                          timer: 2000 // Close alert after 2 seconds
                                      });
                                      // Refresh todo section after adding new todo item
                                      refreshTodoSection();
                                  })
                                  .catch(error => {
                                      console.error('Error:', error);
                                      Swal.fire({
                                          icon: 'error',
                                          title: 'Oops...',
                                          text: 'Something went wrong!',
                                      });
                                  });
                          } else {
                              Swal.showValidationMessage(`Please enter both work name and submission time.`);
                          }
                      }
                  });
              });

              // Function to Submit TODO as Completed
              function complete_todo(todoID) {
                  // const todoID = this.getAttribute('data-todo-id');
                  fetch("inc/update_todo_status.php?todoId=" + todoID) // Replace with your server-side script to update todo status
                      .then(response => response.text())
                      .then(data => {
                          Swal.fire({
                              icon: 'success',
                              title: 'Success',
                              text: data,
                              showConfirmButton: false,
                              timer: 2000 // Close alert after 2 seconds
                          });
                          // Refresh todo section after completing todo item
                          refreshTodoSection();
                      })
                      .catch(error => {
                          console.error('Error:', error);
                          Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: 'Something went wrong!',
                          });
                      });
              }

              // Function to refresh todo section
              function refreshTodoSection() {
                  fetch("inc/refresh_todo_section.php") // Replace with your server-side script to fetch updated todo list
                      .then(response => response.text())
                      .then(data => {
                          document.querySelector('.todoList').innerHTML = data;
                      })
                      .catch(error => {
                          console.error('Error:', error);
                          // Handle error if necessary
                      });
              }