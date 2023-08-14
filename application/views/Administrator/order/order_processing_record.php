<style>
    .v-select {
        margin-bottom: 5px;
        float: right;
        min-width: 200px;
        margin-left: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
        height: 25px;
    }

    .v-select input[type=search],
    .v-select input[type=search]:focus {
        margin: 0px;
    }

    .v-select .vs__selected-options {
        overflow: hidden;
        flex-wrap: nowrap;
    }

    .v-select .selected-tag {
        margin: 2px 0px;
        white-space: nowrap;
        position: absolute;
        left: 0px;
    }

    .v-select .vs__actions {
        margin-top: -5px;
    }

    .v-select .dropdown-menu {
        width: auto;
        overflow-y: auto;
    }

    #orderProcessing label {
        font-size: 13px;
        margin-top: 3px;
    }

    #orderProcessing select {
        border-radius: 3px;
        padding: 0px;
        font-size: 13px;
    }

    #orderProcessing .form-group {
        margin-right: 10px;
    }
</style>
<div id="orderProcessing">
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-condensed" id="orderProcessingTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Employee Name</th>
                            <th>Saved By</th>
                            <th>Sub Total</th>
                            <th>VAT</th>
                            <th>Discount</th>
                            <th>Transport Cost</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(sale, sl) in orders" :style="{background: sale.Status == 'a' ? '' : '#F7CB73'}">
                            <td>{{sl + 1}}</td>
                            <td>{{ sale.SaleMaster_InvoiceNo }}</td>
                            <td>{{ sale.SaleMaster_SaleDate }}</td>
                            <td>{{ sale.Customer_Name }}</td>
                            <td>{{ sale.Employee_Name }}</td>
                            <td>{{ sale.AddBy }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_SubTotalAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_TaxAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_TotalDiscountAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_Freight }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_TotalSaleAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_PaidAmount }}</td>
                            <td style="text-align:right;">{{ sale.SaleMaster_DueAmount }}</td>
                            <td style="text-align:left;">{{ sale.SaleMaster_Description }}</td>
                            <td style="text-align:center;">
                                <a href="" title="Sale Invoice" v-bind:href="`/order_invoice_print/${sale.SaleMaster_SlNo}`" target="_blank"><i class="fa fa-file"></i></a>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <a href="javascript:" title="Edit Order" @click="checkReturnAndEdit(sale)"><i class="fa fa-edit"></i></a>
                                    <?php if($this->session->userdata('accountType') != 'e'){?>
                                    <a href="" title="Delete Order" @click.prevent="deleteSale(sale.SaleMaster_SlNo)"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                    <select style="border: 1px dashed;" v-model="sale.Status" v-if="sale.Status != 'a'" @change.prevent="OrderStatusChange(sale)">
                                        <option value="process">Processing</option>
                                        <option value="a">Delivery</option>
                                        <option value="cancel" style="color: red;">Cancel</option>
                                    </select>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr v-if="orders.length == 0">
                            <td colspan="15">No Found Data</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#orderProcessing',
        data() {
            return {
                orders: []
            }
        },
        created() {
            this.getOrders();
        },
        methods: {
            getOrders() {
                axios.get('/get_all_order').then(res => {
                    this.orders = res.data.filter(order => order.Status == 'process');
                })
            },
            OrderStatusChange(sale) {
                let deleteConf = confirm('Are you sure? you want to confirm this order?');
                if (deleteConf == false) {
                    this.getOrders();
                    return;
                }
                let filter = {
                    saleId: sale.SaleMaster_SlNo,
                    Status: sale.Status
                }
                axios.post('/order_status_change', filter)
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getOrders();
                        }
                    })
            },
            checkReturnAndEdit(sale) {
                if (sale.is_service == 'true') {
                    window.open('/order/service/' + sale.SaleMaster_SlNo, '_blank');
                } else {
                    window.open('/order/product/' + sale.SaleMaster_SlNo, '_blank');
                }
            },
            deleteSale(saleId) {
                let deleteConf = confirm('Are you sure?');
                if (deleteConf == false) {
                    return;
                }
                axios.post('/delete_order', {
                        saleId: saleId
                    })
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getOrders();
                        }
                    })
            },
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h3 style="text-align:center">Order Processing List</h3>
                            </div>
                        </div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}, left=0, top=0`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.body.innerHTML += reportContent;

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>