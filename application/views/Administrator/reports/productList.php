<div id="productList">
    <div style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
        <div class="row">
            <div class="col-md-12">
                <a href="" style="margin: 7px 0;display:block;width:50px;" v-on:click.prevent="print">
                    <i class="fa fa-print"></i> Print
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" id="reportTable">
                    <table class="table table-bordered table-condensed record-table">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Product Id</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sale Price</th>
                                <!--<th>Action</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, sl) in products">
                                <td style="text-align:center;">{{ sl + 1 }}</td>
                                <td>{{ product.Product_Code }}</td>
                                <td>{{ product.Product_Name }}</td>
                                <td>{{ product.ProductCategory_Name }}</td>
                                <td style="text-align:right;">{{ product.Product_SellingPrice }}</td>
                                <!--<td>-->
                                <!--    <button type="button" class="button" data-toggle="modal" data-target="#colorSize" @click="colorAndSize(product)"><i class="fa fa-eye"></i></button>-->
                                <!--</td>-->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    	<!-- Modal -->
	<div id="colorSize" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Color & Size</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>SL</th>
								<th>Color</th>
								<th>Size</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, sl) in productColors">
								<td>{{ sl + 1 }}</td>
								<td>{{ item.color_name }}</td>
								<td>{{ item.size_name }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>

<script>
    new Vue({
        el: '#productList',
        data() {
            return {
                products: [],
                productColors: [],
            }
        },
        created() {
            this.getProducts();
        },
        methods: {
            getProducts() {
                axios.get('/get_products').then(res => {
                    this.products = res.data;
                })
            },
            colorAndSize(color) {
				this.productColors = color.colors;
			},
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 style="text-align:center">Product List</h4 style="text-align:center">
                            </div>
                        </div>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

                var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
                mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);
                
                mywindow.document.body.innerHTML += reportContent;

                let rows = mywindow.document.querySelectorAll('.record-table tr');
                rows.forEach(row => {
                    row.lastChild.remove();
                })

                mywindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                mywindow.print();
                mywindow.close();
            }
        }
    })
</script>