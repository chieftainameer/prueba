$(document).ready(function() {
    // Make an AJAX request to the ApiClass.php file
    $.ajax({
        url: 'ApiClass.php',
        dataType: 'json',
        success: function(response) {
            var users = response.data;

            if(response.data.length < 0)
            {
                alert(response.message);
                return;
            }

            // Create a table and add header row
            var table = $('<table>');
            table.append('<tr><th>Name</th><th>Email</th><th>Image</th></tr>');

            // Add rows for each user
            $.each(users, function(index, user) {
                var row = $('<tr>');
                row.append('<td><img src="' + user.picture.thumbnail + '"></td>');
                row.append('<td>' + user.name.first +' '+ user.name.last + '</td>');
                row.append('<td>' + user.email + '</td>');
                table.append(row);
            });

            // Append the table to the HTML element with id "users-table"
            $('#users-table').html(table);
        }
    });
});