$(document).ready(function(){
    function overall_fetch(page = 1, limit = 10) {
        $.ajax({
            url: `{{ route('superadmin.fetch') }}?page=${page}&limit=${limit}`,
            type: "GET",
            success: function (data) {
                console.log(data);
                let body = "";
                const users = data.data; // Assuming Laravel's paginate returns `data` array
                users.forEach(user => {
                    body += `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>${user.last_ip}</td>
                            <td>${user.role}</td>
                             <td>${user.role}</td>
                          
                        </tr>`;
                });
                $('#user_details').html(body);
    
                // Update pagination
                let paginationHtml = '<ul class="pagination">';
    
                // First button
                if (data.current_page > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <button class="page-link" data-page="1">First</button>
                        </li>`;
                }
    
                // Previous button
                if (data.current_page > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <button class="page-link" data-page="${data.current_page - 1}">Previous</button>
                        </li>`;
                }
    
                // Determine the range of visible page buttons
                const totalPages = data.last_page;
                const startPage = Math.max(1, data.current_page - 2); // Start 2 pages before current
                const endPage = Math.min(totalPages, data.current_page + 2); // End 2 pages after current
    
                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml += `
                        <li class="page-item ${i === data.current_page ? 'active' : ''}">
                            <button class="page-link" data-page="${i}">${i}</button>
                        </li>`;
                }
    
                // Next button
                if (data.current_page < totalPages) {
                    paginationHtml += `
                        <li class="page-item">
                            <button class="page-link" data-page="${data.current_page + 1}">Next</button>
                        </li>`;
                }
    
                // Last button
                if (data.current_page < totalPages) {
                    paginationHtml += `
                        <li class="page-item">
                            <button class="page-link" data-page="${totalPages}">Last</button>
                        </li>`;
                }
    
                paginationHtml += '</ul>';
                $('#pagination').html(paginationHtml);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    }
    
    // Add event listener for pagination buttons
    $(document).on('click', '.page-link', function () {
        const page = $(this).data('page');
        overall_fetch(page);
    });
    
    });