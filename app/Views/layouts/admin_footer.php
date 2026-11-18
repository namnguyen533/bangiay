        </main>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const adminSidebarToggle = document.querySelector('.admin-sidebar-toggle');
    const adminSidebarBackdrop = document.querySelector('.admin-sidebar-backdrop');

    function closeAdminSidebar() {
        document.body.classList.remove('admin-sidebar-open');
        if (adminSidebarToggle) {
            adminSidebarToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (adminSidebarToggle && adminSidebarBackdrop) {
        adminSidebarToggle.addEventListener('click', function () {
            const isOpen = document.body.classList.toggle('admin-sidebar-open');
            adminSidebarToggle.setAttribute('aria-expanded', String(isOpen));
        });
        adminSidebarBackdrop.addEventListener('click', closeAdminSidebar);
        document.querySelectorAll('.admin-sidebar .nav-link').forEach(function (link) {
            link.addEventListener('click', closeAdminSidebar);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAdminSidebar();
            }
        });
    }
</script>
</body>
</html>
