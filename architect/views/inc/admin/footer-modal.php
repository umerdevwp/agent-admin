<div class="operation-alert-message" id="operation-alert-message"></div>
<script src="<?php echo FRONT_JS_URL ?>bootstrap.min.js"></script>
<script src="<?php echo ADMIN_JS_URL; ?>bootstrap-autocomplete.js"></script> 
<script type="text/javascript" src="<?php echo ADMIN_JS_URL.'moment.min.js'; ?>"></script>
<script src="<?php echo ADMIN_JS_URL.'daterangepicker.js'; ?>"></script>
<script type="text/javascript" src="<?php echo ADMIN_JS_URL ?>imo_admin.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_JS_URL ?>functions.js"></script>
<?php if(isset($custom_assets)): ?>
	<?php $this->load->view('inc/loadscripts', $custom_assets, FALSE); ?>
<?php endif; ?>
<?php if(isset($operation_message)): ?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			showMessage('<?php echo $operation_message['message']; ?>','<?php echo $operation_message['type']; ?>');
		});
	</script>
<?php endif; ?>
<style>
html,body{background:#ffffff}
  .modal-dialog .close:hover {
    color: red;
    filter: alpha(opacity=100);
    opacity: 1;
	}
	.modal-dialog .close {
    color: red;
    filter: alpha(opacity=70);
    opacity: .7;
	}
  </style>
</body>
</html>
