<div id="products">
	<form @submit.prevent="saveProduct">
		<div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom: 15px;">
			<div class="col-xs-12 col-md-6">
				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Product_Key Name:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.Key_Name">
					</div>
				</div>
				<div class="form-group clearfix">
				<label class="control-label col-xs-4 col-md-4"></label>
					<div class="col-xs-8 col-md-7 text-right">
						<button :disabled="onProgress ? true: false" type="submit" class="btn btn-success btn-xs" style="padding:5px 15px;">Save</button>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="row">
		<div class="col-xs-12 col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-xs-12 col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="products" :filter-by="filter">
					<template scope="{ row }">
						<tr>
							<td>{{ row.Key_SlNo }}</td>
							<td>{{ row.Key_Name }}</td>
							<td>

								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<button type="button" class="button edit" @click="editProduct(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<?php if ($this->session->userdata('accountType') != 'e') { ?>
										<button type="button" class="button" @click="deleteProduct(row.Key_SlNo)">
											<i class="fa fa-trash"></i>
										</button>
									<?php } ?>
								<?php } ?>

							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

	<script>
		Vue.component('v-select', VueSelect.VueSelect);
		new Vue({
			el: '#products',
			data() {
				return {
					product: {
						Key_SlNo: '',
						Key_Name: '',
					},
					products: [],

					columns: [{
							label: 'Key Sl',
							field: 'Sl',
							align: 'center',
							filterable: false
						},
						{
							label: 'Key Name',
							field: 'Key_Name',
							align: 'center'
						},
						{
							label: 'Action',
							align: 'center',
							filterable: false
						}
					],
					page: 1,
					per_page: 10,
					filter: '',

					onProgress: false,
				}
			},
			created() {
				this.getProductKeys();
			},
			methods: {
				getProductKeys() {
					axios.get('/get_productkeys').then(res => {
						this.products = res.data;
					})
				},
				saveProduct() {
					if (this.product.Key_Name == '') {
						alert('Product Key empty');
						return;
					}

					let url = '/insertproductkey';
					let data = {
						productkey: this.product,
					}

					this.onProgress = true;

					axios.post(url, data)
						.then(res => {
							let r = res.data;
							alert(r);
							this.getProductKeys();
							this.clearForm();
							this.onProgress = false;
						})

				},
				editProduct(product) {
					this.product = {
						Key_SlNo: product.Key_SlNo,
						Key_Name: product.Key_Name,
					}
				},
				deleteProduct(productId) {
					let deleteConfirm = confirm('Are you sure?');
					if (deleteConfirm == false) {
						return;
					}
					axios.post('/productkeydelete', {
						keyId: productId
					}).then(res => {
						let r = res.data;
						alert(r);
						this.getProductKeys();
					})
				},
				clearForm(){
					this.product = {
						Key_SlNo: '',
						Key_Name: '',
					}
				}
			}
		})
	</script>