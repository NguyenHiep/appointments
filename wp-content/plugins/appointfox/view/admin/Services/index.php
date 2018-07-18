<div id="app">
	<div class="wrap">
		<h2 class='opt-title'>
			<span id='icon-options-general' class='analytics-options'>
				<img src="<?php echo AFX_URL . '/assets/images/icon16.png'; ?>" alt="">
			</span>
			AppointFox - <?php echo __( 'Services', 'appointfox' ); ?>
		</h2>

		<div class="appointfox-tbs">
			<div class="container-fluid"></div>
			<div class="appointfox-wrap">
				<div class="row">
					<template v-if="!$data">
						<div class="loading-bar">
							<i class="fa fa-spinner fa-spin fa-2x" aria-hidden="true"></i>
							<br> <?php _e( 'Loading', 'appointfox' ); ?>.....
						</div>
					</template>

					<template v-cloak>
						<div class="col-md-4">
							<div style="margin-bottom: 20px">
								<a id="btnAddService" class="btn btn-primary" @click.prevent="showFormService()">
									<i class="fa fa-plus"></i> <?php _e( 'Add New Service', 'appointfox' ); ?></a>
								<a id="btnAddCategory" class="btn btn-primary" @click.prevent="showFormCategory()">
									<i class="fa fa-plus"></i> <?php _e( 'Add New Category', 'appointfox' ); ?></a>
							</div>
							<div id="PanelLoading" v-show="isLoading">
								<img src="<?php echo AFX_URL; ?>assets/images/loading.gif" />
							</div>
							<div id="app-services-list">
								<div v-if="services.length > 0 || categories.length > 0">
									<div class="list-group">
										<!--List services-->
										<a v-for="service in services" :key="service.id" v-if="service.category_id == 0" class="list-group-item" @click.prevent="showFormService(service)"
											:class="{ active : selected_service == service.id }">
											{{ service.title }} ({{ displayDuration(service.duration) }} @ {{ currency }}{{ service.price }})
										</a>
									</div>

									<template v-for="(category, index) in categories">
										<h4 class="category-name">
											{{ category.name }}
											<span class="panel-category-action">
												<div class="btn-group">
													<a href="#" class="btn btn-default btn-xs btn-category-edit" @click.prevent="showFormCategory(category)"><?php _e( 'Edit', 'appointfox' ); ?></a>
													<a href="#" class="btn btn-danger btn-xs btn-category-delete" @click.prevent="deleteCategory(category, index)"><?php _e( 'Delete', 'appointfox' ); ?></a>
												</div>
											</span>
										</h4>

										<div class="list-group">
											<!--List services-->
											<a v-for="service in services" :key="service.id" v-if="category.id == service.category_id" class="list-group-item" @click.prevent="showFormService(service)"
												:class="{ active : selected_service == service.id }">
												{{ service.title }} ({{ displayDuration(service.duration) }} @ {{ currency }}{{ service.price }})
											</a>
										</div>
									</template>
								</div>
								<div v-else>
									<p v-show="!isLoading">
										<?php _e( 'Nothing left in the list', 'appointfox' ); ?>. <?php _e( 'Add a new category or service via button above', 'appointfox' ); ?>.
									</p>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div id="PanelWelcome" class="welcome-crud" v-show="isShowWelcome">
								<?php _e( 'Click', 'appointfox' ); ?>
								<span class="label label-info"><?php _e( 'Add New Service', 'appointfox' ); ?></span> <?php _e( 'to create new Service or Click on Service\'s Item to edit or delete
								a Service', 'appointfox' ); ?>.
								<br />
								<br /> <?php _e( 'Click', 'appointfox' ); ?>
								<span class="label label-info"><?php _e( 'Add New Category', 'appointfox' ); ?></span> <?php _e( 'to create new Category or hover on Category name to edit or delete
								a Category', 'appointfox' ); ?>.
								<br />
								<br />
								<br /> <?php _e( 'Don\'t forget to assign a staff after new service created', 'appointfox' ); ?>.
							</div>
							<div id="PanelFormService" v-show="isShowFormService">
								<form class="well" id="FormService" method="POST" v-on:submit.prevent="saveService" action="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=afx-services-add-process&_ajax_nonce=<?php echo $add_process_nonce; ?>">
									<div class="row">
										<div class="col-md-8">
											<input id="ServiceId" name="data[Service][id]" type="hidden" v-model="formService.id">
											<div class="form-group">
												<label for="ServiceTitle"><?php _e( 'Title', 'appointfox' ); ?></label>
												<input v-model="formService.title" id="ServiceTitle" name="data[Service][title]" type="text" class="form-control" placeholder="<?php _e( 'Title', 'appointfox' ); ?>">
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="ServiceAccess"><?php _e( 'Access', 'appointfox' ); ?></label>
												<select v-model="formService.access" id="ServiceAccess" name="data[Service][access]" class="form-control">
													<option value="Public"><?php _e( 'Public', 'appointfox' ); ?></option>
													<option value="Private"><?php _e( 'Private', 'appointfox' ); ?></option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="ServiceDuration"><?php _e( 'Duration', 'appointfox' ); ?></label>
												<select v-model="formService.duration" id="ServiceDuration" name="data[Service][duration]" class="form-control">
													<option value="<?php echo 60 * 5; ?>">5 min</option>
													<option value="<?php echo 60 * 10; ?>">10 min</option>
													<option value="<?php echo 60 * 15; ?>">15 min</option>
													<option value="<?php echo 60 * 30; ?>">30 min</option>
													<option value="<?php echo 60 * 60; ?>">1 hour</option>
													<option value="<?php echo 60 * 60 * 2; ?>">2 hours</option>
													<option value="<?php echo 60 * 60 * 3; ?>">3 hours</option>
													<option value="<?php echo 60 * 60 * 4; ?>">4 hours</option>
													<option value="<?php echo 60 * 60 * 5; ?>">5 hours</option>
													<!-- <option value="<?php echo 60 * 60 * 24; ?>">Daily</option> -->
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="ServicePrice"><?php _e( 'Price', 'appointfox' ); ?></label>
												<div class="input-group">
													<span class="input-group-addon">{{ currency }}</span>
													<input v-model="formService.price" v-money="money" id="ServicePrice" name="data[Service][price]" class="form-control" placeholder="0.00">
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label for="ServiceColor"><?php _e( 'Color', 'appointfox' ); ?></label>
												<div class="appointfox-color-picker-wrapper">
													<input v-model="formService.color" id="ServiceColor" name="data[Service][color]" type="text" class="form-control color-field"
														placeholder="" style="display: inline-block">
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="ServiceCategoryId"><?php _e( 'Category', 'appointfox' ); ?></label>
												<select v-model="formService.category_id" id="ServiceCategoryId" name="data[Service][category_id]" class="form-control" aria-describedby="helpBlock">
													<option value="">(<?php _e( 'none', 'appointfox' ); ?>)</option>
													<option v-for="category in categories" :value="category.id">{{ category.name }}</option>
												</select>
												<span id="helpBlock" class="help-block">
													<a href="#" class="btn btn-link" data-toggle="modal" data-target="#ModalFormCategory"><?php _e( 'Create a new Category', 'appointfox' ); ?>...</a>
												</span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="ServiceStaffs" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 control-label"><?php _e( 'Assigned Staffs', 'appointfox' ); ?></label>
												<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
													<select multiple id="ServiceStaffs" name="data[Service][staffs]" class="form-control el-select2"
														aria-describedby="helpBlock">
														<option v-for="staff in staffs" :value="staff.id">{{ staff.full_name }}</option>
													</select>
													<!-- <span id="helpBlock" class="help-block">
														<a href="#" class="btn btn-link" data-toggle="modal" data-target="#ModalFormStaff">Create a new Staff...</a>
													</span> -->
												</div>
											</div>
										</div>
									</div>
                                    <!--<div class="row">
                                      <div class="col-md-12">
                                        <div class="form-group">
                                          <label for="ServiceImage"><?php /*_e( 'Image', 'appointfox' ); */?></label>
                                          <input v-model="formService.image" id="ServiceImage" type="text" name="data[Service][image]" class="form-control image-service">
                                          <button class="image-service-upload btn btn-primary " type="button" style="margin-top: 10px;">Upload image</button>
                                        </div>
                                      </div>
                                    </div>-->
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="ServiceInfo"><?php _e( 'Info', 'appointfox' ); ?></label>
												<textarea v-model="formService.note" id="ServiceInfo" name="data[Service][note]" cols="30" rows="5" class="form-control"></textarea>
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
										<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
									</button>
									<button v-show="modeFormService === 'edit'" @click.prevent="deleteService()" id="btnDeleteService" type="button" class="btn btn-danger ladda-button"
										data-style="expand-left">
										<span class="ladda-label"><?php _e( 'Delete', 'appointfox' ); ?></span>
									</button>
									<a id="btnCancelSaveService" href="#" class="btn btn-link" @click.prevent="hideFormService"><?php _e( 'Close', 'appointfox' ); ?></a>
								</form>
							</div>
						</div>
					</template>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="ModalFormCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<form id="FormCategory" method="POST" v-on:submit.prevent="saveCategory">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4 class="modal-title" id="ModalFormCategoryTitle">
									<span v-if="modeFormCategory === 'add'"><?php _e( 'Create a new Category', 'appointfox' ); ?>...</span>
									<span v-else><?php _e( 'Edit Category', 'appointfox' ); ?></span>
								</h4>
							</div>
							<div class="modal-body">
								<input id="CategoryId" name="data[Category][id]" type="hidden" v-model="formCategory.id">
								<div class="form-group">
									<label for="inputCategoryName"><?php _e( 'Category Name', 'appointfox' ); ?></label>
									<input id="CategoryName" name="data[Category][name]" v-model="formCategory.name" type="text" class="form-control" placeholder="<?php _e( 'Category Name', 'appointfox' ); ?>">
									<!-- <span v-if="formCategoryErrors['name']" class="error text-danger">{{ formCategoryErrors['name'] }}</span> -->
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-link" data-dismiss="modal"><?php _e( 'Cancel', 'appointfox' ); ?></button>
								<button type="submit" class="btn btn-success ladda-button" data-style="expand-left">
									<span class="ladda-label"><?php _e( 'Save', 'appointfox' ); ?></span>
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>