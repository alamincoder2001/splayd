<div id="salesInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<sales-invoice v-bind:sales_id="salesId"></sales-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/components/salesInvoice.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/barcode.min.js"></script>
<script>
	new Vue({
			el: '#salesInvoice',
			components: {
				salesInvoice,
			},
			data() {
				return {
					salesId: parseInt('<?php echo $salesId; ?>')
				}
			}
		})

		JsBarcode("#barcode", "<?php echo $invoice; ?>", {
			format: "CODE128",
			height: 45,
			displayValue: false
		});
</script>