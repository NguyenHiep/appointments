<div class="wrap">
	<h2 class='opt-title'>
		<span id='icon-options-general' class='analytics-options'>
			<img src="<?php echo AFX_URL . '/assets/images/icon16.png'; ?>" alt="">
		</span>
		AppointFox - <?php _e( 'Payments', 'appointfox' ); ?>
	</h2>

	<div class="appointfox-tbs">
		<div class="container-fluid">
			<div class="appointfox-wrap">  
				<div class="row">      
					<div class="col-md-12">
						<!-- <div class="btn-group pull-right" style="margin-bottom: 10px">
							<a id="btnAdd" class="btn btn-primary" href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=afx-payments-add&_ajax_nonce=<?php echo $add_nonce; ?>" data-toggle="mainmodal"><i class="fa fa-plus"></i> Add New Payment</a>
						</div> -->
						<div class="clearfix"></div>
						<div id="no-more-tables">
							<table id="table_payments" class="table table-striped table-bordered table-hover table-appointfox" style="width: 100%">
								<thead>
									<tr>
										<th class="text-center" style="width: 20px; padding: 8px"><input id="checkAll" type="checkbox" value="all"/></th>
										<th>
											<?php _e( 'Date/Time', 'appointfox' ); ?>
										</th>
										<th>
											<?php _e( 'Customer', 'appointfox' ); ?>
										</th>
										<th>
											<?php _e( 'Service', 'appointfox' ); ?>
										</th>
										<th>
											<?php _e( 'Appointment Date/Time', 'appointfox' ); ?>
										</th>
										<th>
											<?php _e( 'Amount', 'appointfox' ); ?>
										</th>
										<th>
										<?php _e( 'Method', 'appointfox' ); ?>
										</th>
										<th>
											<?php _e( 'Status', 'appointfox' ); ?>
										</th>
										<th class="th-action" style="width: 140px">
											<?php _e( 'Action', 'appointfox' ); ?>
										</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="9">
											<button id="btnBulkDelete" class="btn btn-danger btn-xs ladda-button" data-tablename="payments" data-style="expand-left" data-url="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=afx-payments-bulkdelete-process&_ajax_nonce=<?php echo $bulkdelete_nonce; ?>">
                                                <span class="ladda-label">
                                                    <i class="fa fa-trash-o"></i> <?php _e( 'Delete', 'appointfox' ); ?>
                                                </span>
											</button>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>


	</div>

	<div id="appointfox-modal" class="appointfox-tbs">

		<!-- Modal -->
		<div class="modal fade" id="mainmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title" id="myModalLabel"><?php _e( 'Loading', 'appointfox' ); ?>...</h4>
					</div>
					<div class="modal-body" style="text-align: center">
						<img src="<?php echo AFX_URL; ?>assets/images/loading.gif"/>
					</div>
				</div>
			</div>
		</div>

		<div id="mainmodal_loading" style="display: none">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title" id="myModalLabel"><?php _e( 'Loading', 'appointfox' ); ?>...</h4>
					</div>
					<div class="modal-body" style="text-align: center">
						<img src="<?php echo AFX_URL; ?>assets/images/loading.gif"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	$('#mainmodal').on('hidden.bs.modal', function () {
		initAjaxModal();
//        initDeleteButton();
	});

	Ladda.bind('button[type=submit]');
	Ladda.bind('.ladda-button');
</script>

