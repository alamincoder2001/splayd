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

    #orderDelivery label {
        font-size: 13px;
        margin-top: 3px;
    }

    #orderDelivery select {
        border-radius: 3px;
        padding: 0px;
        font-size: 13px;
    }

    #orderDelivery .form-group {
        margin-right: 10px;
    }

    .statusPendingBtn {
        background: #ff9191;
        border: 0;
        border-bottom-left-radius: 15px;
        padding: 3px 10px;
        border-top-right-radius: 15px;
        color: white;
    }

    .statusReceivedBtn {
        background: #23700a;
        border: 0;
        border-bottom-left-radius: 15px;
        padding: 3px 10px;
        border-top-right-radius: 15px;
        color: white;
    }
</style>
<div id="orderDelivery">
    <div class="row" style="border-bottom: 1px solid #ccc;padding: 3px 0;">
        <div class="col-md-12">
            <form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" v-model="searchType">
                        <option value="">All</option>
                        <option value="customer">By Customer</option>
                    </select>
                </div>

                <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'customer' && customers.length > 0 ? '' : 'none'}">
                    <label>Customer</label>
                    <v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name"></v-select>
                </div>

                <div class="form-group">
                    <input type="date" class="form-control" v-model="dateFrom">
                </div>

                <div class="form-group">
                    <input type="date" class="form-control" v-model="dateTo">
                </div>

                <div class="form-group" style="margin-top: -5px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-condensed" id="orderDeliveryTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Customer Mobile</th>
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
                        <tr v-for="(sale, sl) in orders">
                            <td>{{sl + 1}}</td>
                            <td>{{ sale.SaleMaster_InvoiceNo }}</td>
                            <td>{{ sale.SaleMaster_SaleDate }}</td>
                            <td>{{ sale.Customer_Name }}</td>
                            <td>{{ sale.Customer_Mobile }}</td>
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
                            <th>
                                <a href="" title="Delete Order" @click.prevent="deleteSale(sale.SaleMaster_SlNo)"><i class="fa fa-trash"></i></a>
                                <button @click="StatusChange(sale.SaleMaster_SlNo, sale.delivery_status)" :disabled="sale.delivery_status == 'a'? true:false" :class="sale.delivery_status == 'p'? 'statusPendingBtn' :'statusReceivedBtn'">{{sale.delivery_status == 'p' ? 'Pending': 'Received'}}</button>
                            </th>
                        </tr>
                        <tr v-if="orders.length == 0">
                            <td colspan="16">No Found Data</td>
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
        el: '#orderDelivery',
        data() {
            return {
                searchType: '',
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                orders: [],
                customers: [],
                selectedCustomer: null,
            }
        },
        created() {
            this.getOrders();
            this.getCustomers();
        },
        methods: {
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
            getCustomers() {
                axios.get('/get_customers').then(res => {
                    this.customers = res.data;
                })
            },

            getSearchResult() {
                if (this.searchType != 'customer') {
                    this.selectedCustomer = null;
                }
                let filter = {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo,
                    customerId: this.selectedCustomer == null ? '' : this.selectedCustomer.Customer_SlNo
                }

                axios.post('/get_all_order_filter', filter).then(res => {
                    this.orders = res.data;
                })
            },

            getOrders() {
                axios.get('/get_all_order').then(res => {
                    this.orders = res.data.filter(order => order.Status == 'a');
                })
            },

            StatusChange(id, status) {
                if (status == 'a') {
                    return
                }
                let deleteConf = confirm('Are you sure? you want to confirm this order?');
                if (deleteConf == false) {
                    return;
                }
                let filter = {
                    saleId: id,
                    delivery_status: 'a'
                }
                axios.post('/order_delivery_status_change', filter)
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
                                <h3 style="text-align:center">Order Delivery List</h3>
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