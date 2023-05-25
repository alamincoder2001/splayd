<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
        background: #fff;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
    .information {
		/* border: 1px solid #89AED8; */
		background-color: #EEEEEE;
		border-radius: 3px;
		margin: 7px 13px;
	}
	.commission_heading {
		background: #DDDDDD;
		padding: 5px 10px;
        border-radius: 3px;
		font-size: 17px;
		color: #323A89;
	}
	.commission_balance {
        border: 1px solid #89AED8;
		background-color: #fff;
		border-radius: 3px;
		margin: 7px 13px;
	}
    .commission {
        background: #DDDDDD;
		padding: 5px;
		font-size: 15px;
		color: #323A89;
        text-align: center;
        border-radius: 3px;
    }
    .balance {
        text-align: center;
        font-size: 15px;
        padding-top: 10px;
        font-weight: 700;
    }
    .withdraw-btn {
        margin-top: 5px;
        padding: 2px 12px;
    }
</style>

<div id="commissionWithdraw">
    <div class="row">
        <div class="col-md-4 col-12 col-md-offset-3">
            <div class="information">
                <div class="commission_heading"> 
                    <strong><i style="font-size: 17px;" class="fa fa-money"></i> &nbsp;Supplier Commission Withdraw</strong> 
                </div>
                <form @submit.prevent="SaveCommissionWithdraw" style="padding: 10px 0px;">
                    <div class="form-group clearfix">
                        <label class="control-label col-md-4">Date:</label>
                        <div class="col-md-8">
                            <input type="date" class="form-control" v-model="withdraw.date">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="control-label col-md-4">Supplier:</label>
                        <div class="col-md-8">
                            <v-select v-bind:options="suppliers" v-model="selectedSupplier" label="display_name" v-on:input="supplierOnChange"></v-select>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="control-label col-md-4">Amount:</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" v-model="withdraw.amount">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="control-label col-md-4">Comment:</label>
                        <div class="col-md-8">
                            <textarea class="form-control" v-model="withdraw.note" cols="2" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-group clearfix" style="padding: 0px 12px; text-align: right;">
                       <button class="withdraw-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-2">
            <div class="commission_balance">
                <div class="commission">
                    <strong>Commission</strong> 
                </div>
                <div class="balance">
                    <p>{{ Balance }}</p>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="withdraws" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>{{ row.date }}</td>
							<td>{{ row.Supplier_Name }}</td>
							<td>{{ row.Supplier_Code }}</td>
							<td>{{ row.amount }}</td>
							<td>{{ row.note }}</td>
							<td>
								<?php if($this->session->userdata('accountType') != 'u'){?>
								<button type="button" class="button edit" @click="editWithdraw(row)">
									<i class="fa fa-pencil"></i>
								</button>
								<button type="button" class="button" @click="deleteWithdraw(row.id)">
									<i class="fa fa-trash"></i>
								</button>
								<?php }?>
							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
		el: '#commissionWithdraw',
		data(){
			return {
                withdraw: {
                    id: 0,
                    supplier_id: '',
                    date: moment().format('YYYY-MM-DD'),
                    amount: 0.00,
                    note: ''

                },
                withdraws: [],
                suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: null,
					display_name: 'Select Supplier'
				},
                Balance: 0.00,

                columns: [
                    { label: 'Sl', align: 'center', filterable: false},
                    { label: 'Date', field: 'date', align: 'center' },
                    { label: 'Supplier Name', field: 'Supplier_Name', align: 'center' },
					{ label: 'Supplier Code', field: 'Supplier_Code', align: 'center' },
                    { label: 'Amount', field: 'amount', align: 'center' },
                    { label: 'Comment', field: 'note', align: 'center' },
                    { label: 'Action', align: 'center', filterable: false }
                ],
                page: 1,
                per_page: 10,
                filter: ''
            }
        },
        created() {
            this.getSuppliers();
            this.getWithdrwaCommissions();
        },
        methods: {
            getSuppliers() {
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
				})
			},
            supplierOnChange() {
                if(this.selectedSupplier.Supplier_SlNo != null && this.selectedSupplier.Supplier_SlNo != '') {
                    axios.post('/get_commission_list', {supplierId:this.selectedSupplier.Supplier_SlNo})
                    .then(res => {
                        let r = res.data;
                        this.Balance = r[0].balance;
                    })
                }
            },
            getWithdrwaCommissions() {
                axios.post('/get_commission_withdraw').then(res => {
                    this.withdraws = res.data.map((item, sl) => {
                        item.sl = sl + 1;
                        return item;
                    });
                })
            },
            SaveCommissionWithdraw() {
                if(this.selectedSupplier.Supplier_SlNo == null) {
                    alert('Select Supplier');
                    return;
                }
                
                if(this.withdraw.amount == 0 && this.withdraw.amount == ''){
                    alert('Enter Withdraw Amount');
                    return;
                }

                if(this.withdraw.id == 0 && parseFloat(this.withdraw.amount) > parseFloat(this.Balance)) {
                    alert('Withdraw amount more then commission amount!');
                    return;
                }

                let url = '/save_commission_withdraw';
                if(this.withdraw.id != 0) {
                    url = '/update_commission_withdraw';
                }
                this.withdraw.supplier_id = this.selectedSupplier.Supplier_SlNo;
                axios.post(url, this.withdraw)
                .then(res => {
                    let r = res.data;
					alert(r.message);
					if(r.success){
						this.resetForm();
						this.getWithdrwaCommissions();
                        this.supplierOnChange();
					}
                })
            },
            editWithdraw(withdraw) {
                let keys = Object.keys(this.withdraw);
				keys.forEach(key => {
					this.withdraw[key] = withdraw[key];
				})

                this.selectedSupplier = {
                    Supplier_SlNo: withdraw.supplier_id,
					display_name: withdraw.Supplier_Name
                }
            },
            deleteWithdraw(withdrawId) {
                let deleteConfirm = confirm('Are you sure?');
				if(deleteConfirm == false){
					return;
				}
				axios.post('/delete_withdraw_commission', {withdrawId: withdrawId}).then(res => {
					let r = res.data;
					alert(r.message);
					if(r.success){
						this.getWithdrwaCommissions();
                        this.supplierOnChange();
					}
				})
            },
            resetForm() {
                this.withdraw = {
                    id: 0,
                    supplier_id: '',
                    date: moment().format('YYYY-MM-DD'),
                    amount: 0.00,
                    note: ''

                }
                this.selectedSupplier = {
					Supplier_SlNo: null,
					display_name: 'Select Supplier'
				}
                this.Balance = 0.00;
            }
        }
    })
</script>