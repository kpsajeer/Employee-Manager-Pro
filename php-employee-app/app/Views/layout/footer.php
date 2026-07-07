<?php declare(strict_types=1); ?>
    </main><!-- /#page-content -->
</div><!-- /#main-wrapper -->

<!-- Bootstrap 5 JS bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmQERDkEc5Vek0mhCmmrCk3G/UBd"
        crossorigin="anonymous"></script>

<script>
    /* ── Sidebar toggle for mobile ─────────────────────────────────── */
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebar-backdrop').classList.toggle('show');
    }

    /* ── Auto-dismiss alerts after 5 s ────────────────────────────── */
    document.querySelectorAll('.alert.fade.show').forEach(function (el) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 5000);
    });

    /* ── Delete-confirmation modal helper ──────────────────────────── */
    (function () {
        const modal = document.getElementById('deleteModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            const btn      = event.relatedTarget;
            const empId    = btn.getAttribute('data-emp-id');
            const empName  = btn.getAttribute('data-emp-name');
            const form     = modal.querySelector('#deleteForm');
            const nameSpan = modal.querySelector('#deleteEmpName');

            form.action   = '/employees/' + empId + '/delete';
            nameSpan.textContent = empName;
        });
    })();
</script>
</body>
</html>
