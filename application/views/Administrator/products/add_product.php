<style>
	.v-select {
		margin-bottom: 5px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	/* .v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	} */

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	/* .v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	} */

	#products label {
		font-size: 13px;
	}

	#products select {
		border-radius: 3px;
	}

	#products .add-button {
		padding: 2.5px;
		width: 28px;
		background-color: #298db4;
		display: block;
		text-align: center;
		color: white;
	}

	#products .add-button:hover {
		background-color: #41add6;
		color: white;
	}

	.add-color-btn {
		padding: 2.5px 8px;
		background-color: #df032b;
		display: block;
		text-align: center;
		color: white;
		border: none;
		border-radius: 3px;
	}

	.cart-item {
		padding: 0px 8px;
		border: 1px solid #ccc;
	}

	.information {
		/* border: 1px solid #89AED8; */
		background-color: #EEEEEE;
		border-radius: 3px;
		margin: 7px 13px;
	}

	.color_heading {
		background: #DDDDDD;
		padding: 5px;
		font-size: 12px;
		color: #323A89;
	}

	.btn-colorAdd {
		padding: 2px 7px;
		background: #B74635 !important;
		border: none !important;
		border-radius: 3px;
		float: right;
	}
</style>
<div id="products">
	<form @submit.prevent="saveProduct">
		<div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom: 15px;">
			<div class="col-xs-12 col-md-6">
				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Product Id:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Code">
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Category:</label>
					<div class="col-xs-7 col-md-7">
						<select class="form-control" v-if="categories.length == 0"></select>
						<v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name" v-if="categories.length > 0"></v-select>
					</div>
					<div class="col-xs-1 col-md-1" style="padding:0;margin-left: -15px;"><a href="/category" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix" style="display:none;">
					<label class="control-label col-xs-4 col-md-4">Brand:</label>
					<div class="col-xs-7 col-md-7">
						<select class="form-control" v-if="brands.length == 0"></select>
						<v-select v-bind:options="brands" v-model="selectedBrand" label="brand_name" v-if="brands.length > 0"></v-select>
					</div>
					<div class="col-xs-1 col-md-1" style="padding:0;margin-left: -15px;"><a href="" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Product Name:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Name" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Unit:</label>
					<div class="col-xs-7 col-md-7">
						<select class="form-control" v-if="units.length == 0"></select>
						<v-select v-bind:options="units" v-model="selectedUnit" label="Unit_Name" v-if="units.length > 0"></v-select>
					</div>
					<div class="col-xs-1 col-md-1" style="padding:0;margin-left: -15px;"><a href="/unit" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">VAT:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.vat">
					</div>
				</div>

				<!-- <div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Size:</label>
					<div class="col-xs-8 col-md-7">
						<v-select v-bind:options="sizes" v-model="selectedSize" label="size_name" multiple></v-select>
					</div>
				</div> -->

				<!-- <div class="information">
					<div class="col-sm-12 color_heading"> <strong>Product Size & Color</strong> <button class="btn btn-colorAdd" type="button" data-toggle="modal" data-target="#myModal">+Add</button></div>
					<div class="table-responsive" style="padding: 5px 3px 0px 3px;">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>SL</th>
									<th>Color</th>
									<th>Size</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(item, ind) in cart">
									<td>{{ ind + 1 }}</td>
									<td>{{ item.colorName }}</td>
									<td>{{ item.sizeName }}</td>
									<td><a href="" v-on:click.prevent="removeFromCart(ind)"><i class="fa fa-trash"></i></a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div> -->
			</div>

			<div class="col-xs-12 col-md-6">
				
				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Re-order level:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.Product_ReOrederLevel" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Purchase Rate:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" id="purchase_rate" class="form-control" v-model="product.Product_Purchase_Rate" required v-bind:disabled="product.is_service ? true : false">
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Sales Rate:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.Product_SellingPrice" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Wholesale Rate:</label>
					<div class="col-xs-8 col-md-7">
						<input type="text" class="form-control" v-model="product.Product_WholesaleRate" required>
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="control-label col-xs-4 col-md-4">Is Service:</label>
					<div class="col-xs-8 col-md-7">
						<input type="checkbox" v-model="product.is_service" @change="changeIsService">
					</div>
				</div>

				<div class="form-group clearfix">
					<div class="col-xs-8 col-md-7 col-md-offset-4">
						<input type="submit" class="btn btn-success btn-sm" value="Save">
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
							<td>{{ row.Product_Code }}</td>
							<td>{{ row.Product_Name }}</td>
							<td>{{ row.ProductCategory_Name }}</td>
							<td>{{ row.Product_Purchase_Rate }}</td>
							<td>{{ row.Product_SellingPrice }}</td>
							<td>{{ row.Product_WholesaleRate }}</td>
							<td>{{ row.vat }}</td>
							<td>{{ row.is_service }}</td>
							<td>{{ row.Unit_Name }}</td>
							<td>
								<!-- <button type="button" class="button" data-toggle="modal" data-target="#colorSize" @click="colorAndSize(row)"><i class="fa fa-eye"></i></button> -->

								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<button type="button" class="button edit" @click="editProduct(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<?php if($this->session->userdata('accountType') != 'e'){?>
									<button type="button" class="button" @click="deleteProduct(row.Product_SlNo)">
										<i class="fa fa-trash"></i>
									</button>
								<?php } ?>
									<button type="button" class="button" @click="window.location = `/Administrator/products/barcodeGenerate/${row.Product_SlNo}`">
										<i class="fa fa-barcode"></i>
									</button>
								<?php } ?>

							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<!-- <div id="colorSize" class="modal fade" role="dialog">
		<div class="modal-dialog"> 
			
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Size</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>SL</th>
								<th>Size</th>
								<th>Stock</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, sl) in productColors">
								<td>{{ sl + 1 }}</td>
								<td>{{ item.size_name }}</td>
								<td>{{ item.stock }}</td>
								<td>
									<button type="button" class="button" @click="window.location = `/Administrator/products/barcodeGenerate/${item.id}`">
										<i class="fa fa-barcode"></i>
									</button>
									<button type="button" class="button" @click="deleteSize(item.id)"><i class="fa fa-trash"></i></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>-->
	<!-- Modal -->
	<!-- <div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Add Color & Size</h4>
				</div>
				<form @submit.prevent="addToCart">
					<div class="modal-body">
						<div class="row">
							<div class="form-group">
								<label class="col-sm-3 control-label" for="examination"> Product Color </label>
								<label class="col-sm-1 control-label">:</label>
								<div class="col-sm-7">
									<select class="form-control" v-if="colors.length == 0"></select>
									<v-select v-bind:options="colors" v-model="selectedColor" label="color_name" v-if="colors.length > 0"></v-select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="amount"> Product Size </label>
								<label class="col-sm-1 control-label">:</label>
								<div class="col-sm-7">
									<select class="form-control" v-if="sizes.length == 0"></select>
									<v-select v-bind:options="sizes" v-model="selectedSize" label="size_name" v-if="sizes.length > 0"></v-select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-sm btn-success">Add To Cart</button>
					</div>
				</form>
			</div>
		</div>
	</div> -->

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
						Product_SlNo: '',
						Product_Code: "<?php echo $productCode; ?>",
						Product_Name: '',
						ProductCategory_ID: '',
						brand: '',
						Product_ReOrederLevel: '',
						Product_Purchase_Rate: '',
						Product_SellingPrice: '',
						Product_WholesaleRate: 0,
						Unit_ID: '',
						vat: 0,
						per_unit_convert: 0,
						is_service: false
					},
					products: [],
					productColors: [],
					categories: [],
					selectedCategory: null,
					brands: [],
					selectedBrand: null,
					units: [],
					// colors: [],
					// selectedColor: {
					// 	color_SiNo: '',
					// 	color_name: ''
					// },
					// sizes: [],
					// selectedSize: null,
					// cart: [],
					selectedUnit: null,

					columns: [{
							label: 'Product Id',
							field: 'Product_Code',
							align: 'center',
							filterable: false
						},
						{
							label: 'Product Name',
							field: 'Product_Name',
							align: 'center'
						},
						{
							label: 'Category',
							field: 'ProductCategory_Name',
							align: 'center'
						},
						{
							label: 'Purchase Price',
							field: 'Product_Purchase_Rate',
							align: 'center'
						},
						{
							label: 'Sales Price',
							field: 'Product_SellingPrice',
							align: 'center'
						},
						{
							label: 'Wholesale Price',
							field: 'Product_WholesaleRate',
							align: 'center'
						},
						{
							label: 'VAT',
							field: 'vat',
							align: 'center'
						},
						{
							label: 'Is Service',
							field: 'is_service',
							align: 'center'
						},
						{
							label: 'Unit',
							field: 'Unit_Name',
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
					filter: ''
				}
			},
			created() {
				this.getCategories();
				this.getBrands();
				this.getUnits();
				this.getProducts();
				this.getSizes();
			},
			methods: {
				changeIsService() {
					if (this.product.is_service) {
						this.product.Product_Purchase_Rate = 0;
					}
				},
				getCategories() {
					axios.get('/get_categories').then(res => {
						this.categories = res.data;
					})
				},
				getBrands() {
					axios.get('/get_brands').then(res => {
						this.brands = res.data;
					})
				},
				getSizes() {
					axios.get('/get_sizes').then(res => {
						this.sizes = res.data;
					})
				},
				getUnits() {
					axios.get('/get_units').then(res => {
						this.units = res.data;
					})
				},

				getProducts() {
					axios.get('/get_products').then(res => {
						this.products = res.data;
					})
				},
				saveProduct() {
					if (this.selectedCategory == null) {
						alert('Select category');
						return;
					}
					if (this.selectedUnit == null) {
						alert('Select unit');
						return;
					}
					// if (this.selectedSize == null) {
					// 	alert('Select Size');
					// 	return;
					// }

					if (this.selectedBrand != null) {
						this.product.brand = this.selectedBrand.brand_SiNo;
					}

					this.product.ProductCategory_ID = this.selectedCategory.ProductCategory_SlNo;
					// this.product.color = this.selectedColor.color_SiNo;
					this.product.Unit_ID = this.selectedUnit.Unit_SlNo;

					let url = '/add_product';
					if (this.product.Product_SlNo != 0) {
						url = '/update_product';
					}

					let data = {
						product: this.product,
						cart: this.selectedSize,
					}

					axios.post(url, data)
						.then(res => {
							let r = res.data;
							alert(r.message);
							if (r.success) {
								this.clearForm();
								this.product.Product_Code = r.productId;
								this.getProducts();
							}
						})

				},
				editProduct(product) {
					this.cart = [];
					let keys = Object.keys(this.product);
					keys.forEach(key => {
						this.product[key] = product[key];
					})

					this.product.is_service = product.is_service == 'true' ? true : false;

					this.selectedCategory = {
						ProductCategory_SlNo: product.ProductCategory_ID,
						ProductCategory_Name: product.ProductCategory_Name
					}

					// selected size
					let si = [];
					product.colors.forEach(color => {
						let cartColor = {
							size_SiNo: color.size_id,
							size_name: color.size_name,
						}
						si.push(cartColor);
					})
					this.selectedSize = si;

					this.selectedUnit = {
						Unit_SlNo: product.Unit_ID,
						Unit_Name: product.Unit_Name
					}
				},
				colorAndSize(row) {
					// console.log(row)
					this.productColors = row.colors;
				},
				deleteSize(sizeId) {
					if (confirm("Are you sure?")) {
						axios.post('/delete_size', {
							sizeId: sizeId
						}).then(res => {
							let r = res.data;
							alert(r.message);
							this.getProducts();
						})
					}
				},
				deleteProduct(productId) {
					let deleteConfirm = confirm('Are you sure?');
					if (deleteConfirm == false) {
						return;
					}
					axios.post('/delete_product', {
						productId: productId
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getProducts();
						}
					})
				},
				clearForm() {
					let keys = Object.keys(this.product);
					keys.forEach(key => {
						if (typeof(this.product[key]) == "string") {
							this.product[key] = '';
						} else if (typeof(this.product[key]) == "number") {
							this.product[key] = 0;
						}
					})
					this.selectedCategory = null;
					this.selectedSize = null;
					this.selectedUnit = null;
				}
			}
		})
	</script>