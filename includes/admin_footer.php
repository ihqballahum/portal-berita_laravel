		</section>
	</div>

	<footer class="main-footer">
		&copy; <?= date('Y') ?> <b><?= e(SITE_NAME) ?></b>. All rights reserved.
	</footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/js/adminlte.min.js"></script>
<?php if (in_array('datatables', $pakai)): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap.min.js"></script>
<script>$(function () { $('.tabel').DataTable({ order: [], columnDefs: [{ orderable: false, targets: 'no-sort' }] }); });</script>
<?php endif; ?>
<?php if (in_array('summernote', $pakai)): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
<script>$(function () { $('.editor').summernote({ height: 320 }); });</script>
<?php endif; ?>
<script>
// Pencarian menu sidebar (seperti kotak "Search..." pada tampilan acuan)
$('#cari-menu').on('keyup', function () {
	var k = $(this).val().toLowerCase();
	$('.sidebar-menu li.menu-item').each(function () {
		$(this).toggle($(this).text().toLowerCase().indexOf(k) > -1);
	});
});
</script>
</body>
</html>
