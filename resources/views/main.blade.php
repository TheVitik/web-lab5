<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main</title>
    <!-- Bootstrap5 CSS CDN -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-beta1/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div class="container mt-5 mb-2 text-center" id="container">
    @if(auth()->user()->role === 'admin')
        <button class="btn btn-success" onclick="addUserRow()" id="create">Add New User</button>
    @endif
    <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
</div>
<div class="container" id="content-area"></div>
<script>
    const currentUser = {!! json_encode(auth()->user(),true) !!};
    console.log(currentUser);

    document.addEventListener('DOMContentLoaded', function () {
        if (currentUser.role === 'admin') {
            displayUsers();
        } else {
            displayUserView(currentUser);
        }
    });

    function displayUsers() {
        fetch('/users')
            .then(response => response.json())
            .then(users => displayAdminView(users))
            .catch(error => console.error('Error:', error));
    }

    function displayAdminView(users) {
        let contentArea = document.getElementById('content-area');
        let tableHtml = `<table class="table">
                            <thead>
                                <tr>
                                    <th>Login</th>
                                    <th>Password</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>`;

        users.forEach(user => {
            tableHtml += `<tr data-user-id="${user.id}">
                            <td><input type="text" class="form-control user-login" value="${user.login}"></td>
                            <td><input type="password" class="form-control user-password" value="" placeholder="New Password"></td>
                            <td>
                                <input type="checkbox" class="user-role" ${user.role === 'admin' ? 'checked' : ''}> Admin
                            </td>
                            <td>
                                <button class="btn btn-primary" onclick="saveUser(${user.id})">Save</button>
                                <button class="btn btn-danger" onclick="deleteUser(${user.id})">Delete</button>
                            </td>
                        </tr>`;
        });

        tableHtml += `</tbody></table>`;
        contentArea.innerHTML = tableHtml;
    }

    function displayUserView(user) {
        let contentArea = document.getElementById('content-area');
        contentArea.innerHTML = `<p>Your login: <strong>${user.login}</strong></p>
                                  <p>Your role: <strong>${user.role}</strong></p>`;
    }

    function saveUser(userId) {
        let userRow = document.querySelector(`tr[data-user-id='${userId}']`);
        console.log(userRow);

        let login = userRow.querySelector('.user-login').value;
        let password = userRow.querySelector('.user-password').value;
        let isAdmin = userRow.querySelector('.user-role').checked;

        let userData = {
            login: login,
            password: password,
            role: isAdmin ? 'admin' : 'user'
        };

        fetch(`/users/${userId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(userData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    displayAlert(Object.values(data.errors).join('<br>'));
                } else {
                    displayAlert(data.message, 'success');
                    displayUsers();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            })
                .then(response => response.json())
                .then(data => {
                    displayAlert(data.message, 'success');
                    document.querySelector(`tr[data-user-id='${userId}']`).remove();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }

    function addUserRow() {
        let tableBody = document.querySelector('.table tbody');
        let newRow = tableBody.insertRow(-1);
        newRow.className = 'table-warning';

        newRow.innerHTML = `
        <td><input type="text" class="form-control user-login" placeholder="Login"></td>
        <td><input type="password" class="form-control user-password" placeholder="Password"></td>
        <td>
            <input type="checkbox" class="user-role"> Admin
        </td>
        <td>
            <button class="btn btn-primary" onclick="createUser(this)">Create</button>
            <button class="btn btn-secondary" onclick="cancelNewUser(this)">Cancel</button>
        </td>`;
    }


    function createUser(buttonElement) {
        let userRow = buttonElement.closest('tr');

        let login = userRow.querySelector('.user-login').value;
        let password = userRow.querySelector('.user-password').value;
        let isAdmin = userRow.querySelector('.user-role').checked;

        let userData = {
            login: login,
            password: password,
            role: isAdmin ? 'admin' : 'user'
        };

        fetch(`/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(userData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    displayAlert(Object.values(data.errors).join('<br>'));
                } else {
                    displayAlert(data.message, 'success');
                    displayUsers();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function cancelNewUser(buttonElement) {
        let userRow = buttonElement.closest('tr');
        userRow.remove();
    }

    function displayAlert(message, type = 'danger') {
        let alertDiv = document.getElementById('alert-div');
        if (!alertDiv) {
            alertDiv = document.createElement('div');
            alertDiv.id = 'alert-div';
            document.getElementById('container').insertBefore(alertDiv, document.getElementById('create'));
        }
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = message;
        alertDiv.style.display = 'block';

        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 5000);
    }


</script>

</body>
</html>


