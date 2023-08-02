const salesInvoice = Vue.component("sales-invoice", {
  template: `
        <div>
            <div class="row">
                <div class="col-xs-12">
                    <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
            
            <div id="invoiceContent">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asde>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" style="font-size:15px;">
                        <div class="col-xs-5">
                            <strong>Invoice ID</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.SaleMaster_InvoiceNo || '&nbsp;' }}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Date</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.SaleMaster_SaleDate || '&nbsp;' }}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Time</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.AddTime | formatDateTime('h:mm a') || '&nbsp;' }}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Customer Id</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.Customer_Code || '&nbsp;' }}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Name</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.Customer_Name || '&nbsp;'}}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Address</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.Customer_Address || '&nbsp;' }}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Phone</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.Customer_Mobile || '&nbsp;' }}</strong>
                        </div>
                        <div class="col-xs-5">
                            <strong>Sales by</strong>
                        </div>
                        <div class="col-xs-1"><strong>:</strong></div>
                        <div class="col-xs-6">
                            <strong>{{ sales.AddBy || '&nbsp;'}}</strong>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:10px">
                    <div class="col-xs-12 text-center">
                        <div _h098asdh>
                            Invoice
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-xs-12">
                        <table _a584de>
                            <thead>
                                <tr>
                                    <td><strong>Sl.</strong></td>
                                    <td><strong>Description</strong></td>
                                    <!--<td><strong>Size</strong></td>-->
                                    <td><strong>Qnty</strong></td>
                                    <td><strong>Unit Price</strong></td>
                                    <td><strong>Total</strong></strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, sl) in cart">
                                    <td><strong>{{ sl + 1 }}</strong></td>
                                    <td><strong>{{ product.Product_Name }}</strong></td>
                                    <!--<td><strong>{{ product.size_name }}</strong></td>-->
                                    <td><strong>{{ product.SaleDetails_TotalQuantity }}</strong></td>
                                    <td><strong>{{ product.SaleDetails_Rate }}</strong></td>
                                    <td align="right"><strong>{{ product.SaleDetails_TotalAmount }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <table _a584de style="margin-top: 15px;dispaly:none" :style="{display: exchanges.length  > 0 ? '' : 'none'}">
                            <thead>
                                <tr>
                                    <td colspan="7"><span style="border-bottom:1px solid #000">Product Exchange Information</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Sl.</strong></td>
                                    <td><strong>Description</strong></td>
                                    <!--<td><strong>Size</strong></td>-->
                                    <td><strong>Qnty</strong></td>
                                    <td><strong>Unit</strong></td>
                                    <td><strong>Unit Price</strong></td>
                                    <td><strong>Total</strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, sl) in exchanges">
                                    <td><strong>{{ sl + 1 }}</strong></td>
                                    <td><strong>{{ product.Product_Name }}</strong></td>
                                    <!--<td><strong>{{ product.size_name }}</strong></td>-->
                                    <td><strong>{{ product.quantity }}</strong></td>
                                    <td><strong>{{ product.Unit_Name }}</strong></td>
                                    <td><strong>{{ product.rate }}</strong></td>
                                    <td align="right" style="font-weight:bold"><strong>{{ product.total }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asdg>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div style="border:1px solid #000; margin-top:5px;padding:5px;overflow:hidden">
                            <h5><strong>Payments</strong></h5>
                            <table>
                                <tr>
                                    <td><strong>Cash:</strong></td>
                                    <td style="text-align:right"><strong>{{ sales.SaleMaster_cashPaid == null ? '0.00' : sales.SaleMaster_cashPaid  }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Bank:</strong></td>
                                    <td style="text-align:right"><strong>{{ sales.SaleMaster_bankPaid == null ? '0.00' : sales.SaleMaster_bankPaid  }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="border-bottom: 1px solid #1a1a1a;"></td>
                                </tr>
                                <tr>
                                    <td><strong>Total:</strong></td>
                                    <td style="text-align:right"><strong>{{ (parseFloat(sales.SaleMaster_PaidAmount)) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <table _t92sadbc2>
                            <tr>
                                <td><strong>Sub Total:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_SubTotalAmount }}</td>
                            </tr>
                            <tr style="display:none;">
                                <td><strong>VAT:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TaxAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Discount:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TotalDiscountAmount }}</td>
                            </tr>
                            <tr id="transport">
                                <td><strong>Transport Cost:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_Freight }}</td>
                            </tr>
                            <tr><td colspan="2" style="border-bottom: 1px solid black"></td></tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TotalSaleAmount }}</td>
                            </tr>
                            <tr v-if='sales.SaleMaster_bankPaid > 0'>
                            <td><strong>Bank/Bkesh Charge(1.5%):</strong></td>
                            <td style="text-align:right">{{ (sales.SaleMaster_bankPaidwithChagre - sales.SaleMaster_PaidAmount) | decimal }}</td>
                            </tr>
                            <tr v-if='sales.SaleMaster_bankPaid > 0'>
                            <td><strong>Total Paid:</strong></td>
                            <td style="text-align:right">{{ sales.SaleMaster_bankPaidwithChagre}}</td>
                            </tr>
                            <tr v-else>
                                <td><strong>Paid:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_PaidAmount }}</td>
                            </tr>
                           
                            <tr v-if='sales.returnAmount > 0'>
                                <td><strong>Return Amount:</strong></td>
                                <td style="text-align:right">{{ sales.returnAmount }}</td>
                            </tr>
                            <tr v-if='sales.takeAmount > 0'>
                                <td><strong>Pay Amount:</strong></td>
                                <td style="text-align:right">{{ sales.takeAmount }}</td>
                            </tr>
                            
                            <tr><td colspan="2" style="border-bottom: 1px solid black"></td></tr>
                            <tr>
                                <td style="font-size:18px;"><strong>Due:</strong></td>
                                <td style="text-align:right;font-size:18px;">{{ sales.SaleMaster_DueAmount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <!--
                        <strong>In Word: </strong> {{ convertNumberToWords(sales.SaleMaster_TotalSaleAmount) }}<br><br>
                        <strong>Note: </strong>
                        -->
                        <p style="white-space: pre-line;font-size:15px;">
                            NO REFUND. You can only <strong>exchange within 3days</strong>
                        </p>
                        <p style="text-align:center;">                            
                            <img style="width: 60%;height: 70px;" id="barcode" />
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `,
  props: ["sales_id"],
  data() {
    return {
      sales: {
        SaleMaster_InvoiceNo: null,
        SalseCustomer_IDNo: null,
        SaleMaster_SaleDate: null,
        Customer_Name: null,
        Customer_Address: null,
        Customer_Mobile: null,
        SaleMaster_TotalSaleAmount: null,
        SaleMaster_TotalDiscountAmount: null,
        SaleMaster_TaxAmount: null,
        SaleMaster_Freight: null,
        SaleMaster_SubTotalAmount: null,
        SaleMaster_PaidAmount: null,
        SaleMaster_DueAmount: null,
        SaleMaster_Previous_Due: null,
        SaleMaster_Description: null,
        AddBy: null,
      },
      cart: [],
      exchanges: [],
      style: null,
      companyProfile: null,
      currentBranch: null,
    };
  },
  filters: {
    formatDateTime(dt, format) {
      return dt == "" || dt == null ? "" : moment(dt).format(format);
    },
    decimal(value) {
        return value == null ? '0.00' : parseFloat(value).toFixed(2);
    }
  },
  created() {
    this.setStyle();
    this.getSales();
    this.getCurrentBranch();
  },
  methods: {
    getSales() {
      axios.post("/get_sales", { salesId: this.sales_id }).then((res) => {
        this.sales = res.data.sales[0];
        this.cart = res.data.saleDetails;
        this.exchanges = res.data.exchanges;
      });
    },
    getCurrentBranch() {
      axios.get("/get_current_branch").then((res) => {
        this.currentBranch = res.data;
      });
    },
    setStyle() {
      this.style = document.createElement("style");
      this.style.innerHTML = `
                div[_h098asde]{
                    font-weight: bold;
                    font-size:15px;
                    padding: 5px;
                    border-top: 1px dotted #454545;
                }
                div[_h098asdg] {
                    font-weight: bold;
                    font-size: 15px;
                    padding: 5px;
                    border-bottom: 1px dotted #454545;
                }
                div[_h098asdh]{
                    /*background-color:#e0e0e0;*/
                    font-weight: bold;
                    font-size:15px;
                    margin-bottom:15px;
                    padding: 5px;
                    border-top: 1px dotted #454545;
                    border-bottom: 1px dotted #454545;
                }
                div[_d9283dsc]{
                    padding-bottom:25px;
                    border-bottom: 1px solid black;
                    margin-bottom: 15px;
                }
                table[_a584de]{
                    width: 100%;
                    text-align:center;
                }
                table[_a584de] thead{
                    font-weight:bold;
                }
                table[_a584de] td{
                    padding: 3px;
                    border: 1px solid transparent;
                    font-size: 15px;
                }
                table[_t92sadbc2]{
                    width: 100%;
                }
                table[_t92sadbc2] td{
                    padding: 2px;
                    font-size: 15px;
                }
            `;
      document.head.appendChild(this.style);
    },
    convertNumberToWords(amountToWord) {
      var words = new Array();
      words[0] = "";
      words[1] = "One";
      words[2] = "Two";
      words[3] = "Three";
      words[4] = "Four";
      words[5] = "Five";
      words[6] = "Six";
      words[7] = "Seven";
      words[8] = "Eight";
      words[9] = "Nine";
      words[10] = "Ten";
      words[11] = "Eleven";
      words[12] = "Twelve";
      words[13] = "Thirteen";
      words[14] = "Fourteen";
      words[15] = "Fifteen";
      words[16] = "Sixteen";
      words[17] = "Seventeen";
      words[18] = "Eighteen";
      words[19] = "Nineteen";
      words[20] = "Twenty";
      words[30] = "Thirty";
      words[40] = "Forty";
      words[50] = "Fifty";
      words[60] = "Sixty";
      words[70] = "Seventy";
      words[80] = "Eighty";
      words[90] = "Ninety";
      amount = amountToWord == null ? "0.00" : amountToWord.toString();
      var atemp = amount.split(".");
      var number = atemp[0].split(",").join("");
      var n_length = number.length;
      var words_string = "";
      if (n_length <= 9) {
        var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
        var received_n_array = new Array();
        for (var i = 0; i < n_length; i++) {
          received_n_array[i] = number.substr(i, 1);
        }
        for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
          n_array[i] = received_n_array[j];
        }
        for (var i = 0, j = 1; i < 9; i++, j++) {
          if (i == 0 || i == 2 || i == 4 || i == 7) {
            if (n_array[i] == 1) {
              n_array[j] = 10 + parseInt(n_array[j]);
              n_array[i] = 0;
            }
          }
        }
        value = "";
        for (var i = 0; i < 9; i++) {
          if (i == 0 || i == 2 || i == 4 || i == 7) {
            value = n_array[i] * 10;
          } else {
            value = n_array[i];
          }
          if (value != 0) {
            words_string += words[value] + " ";
          }
          if (
            (i == 1 && value != 0) ||
            (i == 0 && value != 0 && n_array[i + 1] == 0)
          ) {
            words_string += "Crores ";
          }
          if (
            (i == 3 && value != 0) ||
            (i == 2 && value != 0 && n_array[i + 1] == 0)
          ) {
            words_string += "Lakhs ";
          }
          if (
            (i == 5 && value != 0) ||
            (i == 4 && value != 0 && n_array[i + 1] == 0)
          ) {
            words_string += "Thousand ";
          }
          if (
            i == 6 &&
            value != 0 &&
            n_array[i + 1] != 0 &&
            n_array[i + 2] != 0
          ) {
            words_string += "Hundred and ";
          } else if (i == 6 && value != 0) {
            words_string += "Hundred ";
          }
        }
        words_string = words_string.split("  ").join(" ");
      }
      return words_string + " only";
    },
    async print() {
      let invoiceContent = document.querySelector("#invoiceContent").innerHTML;
      let printWindow = window.open(
        "",
        "PRINT",
        `width=${screen.width}, height=${screen.height}, left=0, top=0`
      );
      if (this.currentBranch.print_type == "3") {
        printWindow.document.write(`
                    <html>
                        <head>
                            <title>Invoice</title>
                            <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                            <style>
                                body, table{
                                    font-size:11px;
                                }
                                @media print{
                                    #transport{
                                        display:none;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                            <div style="text-align:center;">
                                <!-- <img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_org}" alt="Logo" style="height:80px;margin:0px;" /><br> -->
                                <strong style="text-transform:uppercase;font-size:26px;">${this.currentBranch.Company_Name}</strong><br>
                                <p style="white-space:pre-line;font-size:18px;">${this.currentBranch.Repot_Heading}</p>
                                <strong>Phone: </strong><strong style="font-size:12px;">${this.currentBranch.Company_phone}</strong><br>
                                <strong>Email: </strong><strong style="font-size:12px;">${this.currentBranch.Company_email}</strong><br>
                            </div>
                            ${invoiceContent}
                        </body>
                    </html>
                `);
      } else if (this.currentBranch.print_type == "2") {
        printWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Invoice</title>
                        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                        <style>
                            html, body{
                                width:500px!important;
                            }
                            body, table{
                                font-size: 13px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="row">
                            <div class="col-xs-2"><img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_org}" alt="Logo" style="height:80px;" /></div>
                            <div class="col-xs-10" style="padding-top:20px;">
                                <strong style="font-size:18px;">${this.currentBranch.Company_Name}</strong><br>
                                <p style="white-space:pre-line;">${this.currentBranch.Repot_Heading}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div style="border-bottom: 4px double #454545;margin-top:7px;margin-bottom:7px;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                ${invoiceContent}
                            </div>
                        </div>
                    </body>
                    </html>
				`);
      } else {
        printWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <meta http-equiv="X-UA-Compatible" content="ie=edge">
                        <title>Invoice</title>
                        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                        <style>
                            body, table{
                                font-size: 13px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <table style="width:100%;">
                                <thead>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-2"><img src="/uploads/company_profile_thum/${
                                                  this.currentBranch
                                                    .Company_Logo_org
                                                }" alt="Logo" style="height:80px;" /></div>
                                                <div class="col-xs-10" style="padding-top:20px;">
                                                    <strong style="font-size:18px;">${
                                                      this.currentBranch
                                                        .Company_Name
                                                    }</strong><br>
                                                    <p style="white-space:pre-line;">${
                                                      this.currentBranch
                                                        .Repot_Heading
                                                    }</p>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <div style="border-bottom: 4px double #454545;margin-top:7px;margin-bottom:7px;"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    ${invoiceContent}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <div style="width:100%;height:50px;">&nbsp;</div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="row" style="border-bottom:1px solid #ccc;margin-bottom:5px;padding-bottom:6px;">
                                <div class="col-xs-6">
                                    <span style="text-decoration:overline;">Received by</span><br><br>
                                    ** THANK YOU FOR YOUR BUSINESS **
                                </div>
                                <div class="col-xs-6 text-right">
                                    <span style="text-decoration:overline;">Authorized by</span>
                                </div>
                            </div>
                            <div style="position:fixed;left:0;bottom:15px;width:100%;">
                                <div class="row" style="font-size:12px;">
                                    <div class="col-xs-6">
                                        Print Date: ${moment().format(
                                          "DD-MM-YYYY h:mm a"
                                        )}, Printed by: ${this.sales.AddBy}
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        Developed by: Link-Up Technologoy, Contact no: 01911978897
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </body>
                    </html>
				`);
      }
      let invoiceStyle = printWindow.document.createElement("style");
      invoiceStyle.innerHTML = this.style.innerHTML;
      printWindow.document.head.appendChild(invoiceStyle);
      printWindow.moveTo(0, 0);

      printWindow.focus();
      await new Promise((resolve) => setTimeout(resolve, 1000));
      printWindow.print();
      printWindow.close();
    },
  },
});
