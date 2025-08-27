document.addEventListener('DOMContentLoaded', function() {
    // Client-side validation for registration form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
            }
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
            }
        });
    }

    // Table pagination
    const tables = document.querySelectorAll('.table');
    tables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        if (rows.length > 10) {
            let currentPage = 1;
            const rowsPerPage = 10;
            const totalPages = Math.ceil(rows.length / rowsPerPage);

            function showPage(page) {
                rows.forEach((row, index) => {
                    row.style.display = (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) ? '' : 'none';
                });
                document.querySelectorAll('.pagination').forEach(pagination => {
                    pagination.querySelector('.active')?.classList.remove('active');
                    pagination.querySelector(`[data-page="${page}"]`)?.classList.add('active');
                });
            }

            const pagination = document.createElement('nav');
            pagination.innerHTML = `
                <ul class="pagination justify-content-center mt-3">
                    <li class="page-item"><a class="page-link" href="#" data-action="prev">Previous</a></li>
                    ${Array.from({ length: totalPages }, (_, i) => `
                        <li class="page-item"><a class="page-link" href="#" data-page="${i + 1}">${i + 1}</a></li>
                    `).join('')}
                    <li class="page-item"><a class="page-link" href="#" data-action="next">Next</a></li>
                </ul>
            `;
            table.parentElement.appendChild(pagination);

            pagination.addEventListener('click', function(e) {
                e.preventDefault();
                const target = e.target;
                if (target.dataset.action === 'prev' && currentPage > 1) {
                    currentPage--;
                } else if (target.dataset.action === 'next' && currentPage < totalPages) {
                    currentPage++;
                } else if (target.dataset.page) {
                    currentPage = parseInt(target.dataset.page);
                }
                showPage(currentPage);
            });

            showPage(1);
        }
    });
});