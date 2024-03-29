<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Order extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->sbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
        $this->load->model('SMS_model', 'sms', true);
    }

    public function index($serviceOrProduct = 'product')
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $this->cart->destroy();
        $this->session->unset_userdata('cheque');
        $data['title'] = "Product Order Sales";

        $invoice = $this->mt->generateSalesInvoice();

        $data['isService'] = $serviceOrProduct == 'product' ? 'false' : 'true';
        $data['salesId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/order/product_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addOrder()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $data->sales->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_salesmaster where SaleMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generateSalesInvoice();
            }

            $customerId = $data->sales->customerId;
            if (isset($data->customer) && $data->customer->display_name != 'New Customer') {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                $customer['Customer_Code']     = $this->mt->generateCustomerCode();
                $customer['status']            = 'a';
                $customer['AddBy']             = $this->session->userdata("FullName");
                $customer['AddTime']           = date("Y-m-d H:i:s");
                $customer['Customer_brunchid'] = $this->session->userdata("BRANCHid");

                $this->db->insert('tbl_customer', $customer);
                $customerId = $this->db->insert_id();
            }
            if (isset($data->customer) && $data->customer->display_name == 'New Customer') {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['Customer_Type']);
                $customer['Customer_Code']         = $this->mt->generateCustomerCode();
                $customer['area_ID']               = 1;
                $customer['Customer_Credit_Limit'] = 100000;
                $customer['status']                = 'a';
                $customer['Customer_Type']         = $data->sales->salesType;
                $customer['AddBy']                 = $this->session->userdata("FullName");
                $customer['AddTime']               = date("Y-m-d H:i:s");
                $customer['Customer_brunchid']     = $this->session->userdata("BRANCHid");

                $this->db->insert('tbl_customer', $customer);
                $customerId = $this->db->insert_id();
            }

            $sales = array(
                'SaleMaster_InvoiceNo'           => $invoice,
                'SalseCustomer_IDNo'             => $customerId,
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_PaidAmount'          => $data->sales->paid,
                'SaleMaster_cashPaid'            => $data->sales->cashPaid,
                'SaleMaster_bankPaid'            => $data->sales->bankPaid,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                'Status'                         => 'p',
                'is_order'                       => 'true',
                'is_service'                     => $data->sales->isService,
                "AddBy"                          => $this->session->userdata("FullName"),
                'AddTime'                        => date("Y-m-d H:i:s"),
                'SaleMaster_branchid'            => $this->session->userdata("BRANCHid")
            );

            $this->db->insert('tbl_salesmaster', $sales);

            $salesId = $this->db->insert_id();

            foreach ($data->cart as $cartProduct) {
                $saleDetails = array(
                    'SaleMaster_IDNo'           => $salesId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate'             => $cartProduct->purchaseRate,
                    'SaleDetails_Rate'          => $cartProduct->salesRate,
                    'SaleDetails_Tax'           => $cartProduct->vat,
                    'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'Status'                    => 'p',
                    'AddBy'                     => $this->session->userdata("FullName"),
                    'AddTime'                   => date('Y-m-d H:i:s'),
                    'SaleDetails_BranchId'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_saledetails', $saleDetails);
            }
            if (count($data->banks) > 0) {
                foreach ($data->banks as $bank) {
                    $salesBank = array(
                        'salesId'       => $salesId,
                        'account_id'    => $bank->account_id,
                        'amount'        => $bank->amount,
                        'charge_amount' => $bank->charge_amount,
                        'lastDigit'     => $bank->bankDigit,
                        'status'        => 'a',
                    );

                    $this->db->insert('tbl_salesmaster_account', $salesBank);
                }
            }

            // $currentDue = $data->sales->previousDue + ($data->sales->total - $data->sales->paid);
            // //Send sms
            // $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row();
            // $sendToName = $customerInfo->owner_name != '' ? $customerInfo->owner_name : $customerInfo->Customer_Name;
            // $currency = $this->session->userdata('Currency_Name');

            // $message = "Dear {$sendToName},\nYour bill is {$currency} {$data->sales->total}. Received {$currency} {$data->sales->paid} and current due is {$currency} {$currentDue} for invoice {$invoice}";
            // $recipient = $customerInfo->Customer_Mobile;
            // $this->sms->sendSms($recipient, $message);

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Order Success', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function OrderEdit($productOrService, $salesId)
    {
        $data['title'] = "Order Sales update";
        $sales = $this->db->query("select * from tbl_salesmaster where SaleMaster_SlNo = ?", $salesId)->row();
        $data['isService'] = $productOrService == 'product' ? 'false' : 'true';
        $data['salesId'] = $salesId;
        $data['invoice'] = $sales->SaleMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/order/product_order', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function updateOrder()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $salesId = $data->sales->salesId;

            $oldSale = $this->db->query("select * from tbl_salesmaster where SaleMaster_SlNo = ?", [$salesId])->row();

            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                $customer['UpdateBy'] = $this->session->userdata("FullName");
                $customer['UpdateTime'] = date("Y-m-d H:i:s");

                $this->db->where('Customer_SlNo', $data->sales->customerId)->update('tbl_customer', $customer);
            }

            $sales = array(
                'SalseCustomer_IDNo'             => $data->sales->customerId,
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_TotalSaleAmount'     => count($data->is_exchange) > 0 ? $oldSale->SaleMaster_TotalSaleAmount : $data->sales->total,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'SaleMaster_SubTotalAmount'      => count($data->is_exchange) > 0 ? $oldSale->SaleMaster_SubTotalAmount : $data->sales->subTotal,
                'SaleMaster_PaidAmount'          => count($data->is_exchange) > 0 ? $oldSale->SaleMaster_PaidAmount : $data->sales->paid,
                'SaleMaster_cashPaid'            => count($data->is_exchange) > 0 ? $oldSale->SaleMaster_cashPaid : $data->sales->cashPaid,
                'SaleMaster_bankPaid'            => count($data->is_exchange) > 0 ? $oldSale->SaleMaster_bankPaid : $data->sales->bankPaid,
                'SaleMaster_bankPaidwithChagre'  => $data->sales->bankPaidwithChagre,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                'Status'                         => $data->sales->Status,
                'exchange_total'                 => $data->sales->exchangeTotal,
                'takeAmount'                     => $data->sales->takeAmount,
                'returnAmount'                   => $data->sales->returnAmount,
                'ex_cash_amount'                 => $data->sales->cashPaid - $oldSale->SaleMaster_cashPaid,
                'ex_bank_amount'                 => $data->sales->bankPaid - $oldSale->SaleMaster_bankPaid,
                'is_order'                       => 'true',
                'is_service'                     => $data->sales->isService,
                "UpdateBy"                       => $this->session->userdata("FullName"),
                'UpdateTime'                     => date("Y-m-d H:i:s"),
                'exchangeDate'                   => count($data->is_exchange) > 0 ? date("Y-m-d H:i:s") : null,
                'SaleMaster_branchid'            => $this->session->userdata("BRANCHid")
            );

            $this->db->where('SaleMaster_SlNo', $salesId);
            $this->db->update('tbl_salesmaster', $sales);

            if (isset($data->sales->Status) && $data->sales->Status != 'p') {
                $currentSaleDetails = $this->db->query("select * from tbl_saledetails where SaleMaster_IDNo = ?", $salesId)->result();
                foreach ($currentSaleDetails as $product) {
                    $this->db->query("
                    update tbl_currentinventory 
                    set sales_quantity = sales_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                    ", [$product->SaleDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
                }
            }
            $this->db->query("delete from tbl_saledetails where SaleMaster_IDNo = ?", $salesId);


            foreach ($data->cart as $cartProduct) {
                if ($cartProduct->is_exchange == 'true') {
                    $exchange = array(
                        'sale_id'    => $salesId,
                        'product_id' => $cartProduct->productId,
                        'rate'       => $cartProduct->salesRate,
                        'quantity'   => $cartProduct->quantity,
                        'total'      => $cartProduct->total,
                        'status'     => 'a',
                        'added_by'   => $this->session->userdata('FullName'),
                        'added_date' => date('Y-m-d H:i:s'),
                        'branch_id'  => $this->session->userdata('BRANCHid'),
                    );
                    $this->db->insert('tbl_exchange', $exchange);
                } else {
                    $saleDetails = array(
                        'SaleMaster_IDNo'           => $salesId,
                        'Product_IDNo'              => $cartProduct->productId,
                        'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                        'Purchase_Rate'             => $cartProduct->purchaseRate,
                        'SaleDetails_Rate'          => $cartProduct->salesRate,
                        'SaleDetails_Tax'           => $cartProduct->vat,
                        'SaleDetails_TotalAmount'   => $cartProduct->total,
                        'Status'                    => $data->sales->Status,
                        'AddBy'                     => $this->session->userdata("FullName"),
                        'AddTime'                   => date('Y-m-d H:i:s'),
                        'SaleDetails_BranchId'      => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_saledetails', $saleDetails);
                }
            }

            $this->db->query("UPDATE tbl_salesmaster_account SET status = 'd' WHERE salesId = '$salesId'");
            if (count($data->banks) > 0) {
                foreach ($data->banks as $bank) {
                    $olddata = $this->db->query("SELECT * FROM tbl_salesmaster_account WHERE salesId = ? AND account_id = ?", [$salesId, $bank->account_id])->row();
                    if (empty($olddata)) {
                        $salesBank = array(
                            'salesId'              => $salesId,
                            'account_id'           => $bank->account_id,
                            'amount'               => count($data->is_exchange) > 0 ? 0 : $bank->amount,
                            'exchange_bank_amount' => count($data->is_exchange) > 0 ? $bank->amount : 0,
                            'charge_amount'        => $bank->charge_amount,
                            'lastDigit'            => $bank->bankDigit,
                            'status'               => 'a',
                        );
                    } else {
                        $salesBank = array(
                            'salesId'              => $salesId,
                            'account_id'           => $bank->account_id,
                            'amount'               => count($data->is_exchange) > 0 ? $olddata->amount : $bank->amount,
                            'exchange_bank_amount' => count($data->is_exchange) > 0 ? $bank->amount - $olddata->amount : 0,
                            'charge_amount'        => $bank->charge_amount,
                            'lastDigit'            => $bank->bankDigit,
                            'status'               => 'a',
                        );
                    }

                    $this->db->insert('tbl_salesmaster_account', $salesBank);
                }
            }
            $this->db->query("DELETE FROM tbl_salesmaster_account WHERE salesId = '$salesId' AND status = 'd'");

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Order Updated', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }
        echo json_encode($res);
    }

    public function order_invoice()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Order Sales Invoice";
        $data['content'] = $this->load->view('Administrator/order/order_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function orderInvoicePrint($saleId)
    {
        $invoice = $this->db->query("SELECT
                        sm.*,
                        c.Customer_Code,
                        c.Customer_Name,
                        c.Customer_Mobile
                    FROM tbl_salesmaster sm
                    LEFT JOIN tbl_customer c ON c.Customer_SlNo = sm.SalseCustomer_IDNo
                    WHERE sm.SaleMaster_SlNo = '$saleId'")->row();
        $data['title'] = "Order Sales Invoice";
        $data['salesId'] = $saleId;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/order/orderAndreport', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getOrders()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        if (isset($data->customerType) && $data->customerType != '') {
            $clauses .= " and c.Customer_Type = '$data->customerType'";
        }

        if (isset($data->salesId) && $data->salesId != 0 && $data->salesId != '') {
            $clauses .= "and sm.SaleMaster_SlNo = '$data->salesId'";
            $saleDetails = $this->db->query("
                                select 
                                sd.*,
                                p.Product_Code,
                                p.Product_Name,
                                p.per_unit_convert,
                                pc.ProductCategory_Name,
                                u.Unit_Name
                            from tbl_saledetails sd
                            left join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                            left join tbl_unit u on u.Unit_SlNo = p.Unit_ID
                            where sd.SaleMaster_IDNo = ?
                            and sd.Status != 'd'
                            ", $data->salesId)->result();

            $res['saleDetails'] = $saleDetails;

            $exchange = $this->db->query("
                select 
                    e.*,
                    p.Product_Code,
                    p.Product_Name,
                    un.Unit_Name
                from tbl_exchange e
                join tbl_product p on p.Product_SlNo = e.product_id
                left join tbl_unit un on un.Unit_SlNo = p.Unit_ID
                where e.status = 'a'
                and e.sale_id = ?
            ", $data->salesId)->result();
            $res['exchanges'] = $exchange;

            $banks = $this->db->query("
                select 
                    sb.*,
                    a.account_id,
                    a.account_name,
                    a.bank_name
                from tbl_salesmaster_account sb
                join tbl_bank_accounts a on a.account_id = sb.account_id
                where sb.salesId = ?
            ", $data->salesId)->result();
            $res['banks'] = $banks;
        }

        $sales = $this->db->query("
                            select 
                            concat(sm.SaleMaster_InvoiceNo, ' - ', c.Customer_Name) as invoice_text,
                            sm.*,
                            c.Customer_Code,
                            c.Customer_Name,
                            c.Customer_Mobile,
                            c.Customer_Address,
                            c.Customer_Type,
                            e.Employee_Name,
                            br.Brunch_name
                            from tbl_salesmaster sm
                            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
                            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
                            where sm.SaleMaster_branchid = '$branchId'
                            and sm.Status != 'd'
                            and sm.is_order = 'true'
                            $clauses
                            order by sm.SaleMaster_SlNo desc
                        ")->result();

        $res['sales'] = $sales;

        echo json_encode($res);
    }


    function order_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Order Sales Record";
        $data['content'] = $this->load->view('Administrator/order/order_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getOrderRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        $sales = $this->db->query("
            select 
                sm.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile,
                c.Customer_Address,
                e.Employee_Name,
                br.Brunch_name
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            where sm.SaleMaster_branchid = '$branchId'
            and sm.Status != 'd'
            and sm.is_order = 'true'
            $clauses
            order by sm.SaleMaster_SlNo desc
        ")->result();

        foreach ($sales as $sale) {
            $sale->saleDetails = $this->db->query("
                select 
                    sd.*,
                    p.Product_Name,
                    pc.ProductCategory_Name
                from tbl_saledetails sd
                left join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                where sd.SaleMaster_IDNo = ?
                and sd.Status != 'd'
            ", $sale->SaleMaster_SlNo)->result();
        }

        echo json_encode($sales);
    }

    public function getOrderDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and c.Customer_SlNo = '$data->customerId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $saleDetails = $this->db->query("
                select 
                sd.*,
                p.Product_Code,
                p.Product_Name,
                p.ProductCategory_ID,
                pc.ProductCategory_Name,
                sm.SaleMaster_InvoiceNo,
                sm.SaleMaster_SaleDate,
                c.Customer_Code,
                c.Customer_Name
            from tbl_saledetails sd
            join tbl_product p on p.Product_SlNo = sd.Product_IDNo
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sd.Status != 'd'
            and sm.is_order = 'true'
            and sm.SaleMaster_branchid = ?
            $clauses
        ", $this->sbrunch)->result();

        echo json_encode($saleDetails);
    }

    public function  deleteOrder()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

            $sale = $this->db->select('*')->where('SaleMaster_SlNo', $saleId)->get('tbl_salesmaster')->row();
            // if ($sale->Status != 'a') {
            //     $res = ['success' => false, 'message' => 'Sale not found'];
            //     echo json_encode($res);
            //     exit;
            // }

            $returnCount = $this->db->query("select * from tbl_salereturn sr where sr.SaleMaster_InvoiceNo = ? and sr.Status = 'a'", $sale->SaleMaster_InvoiceNo)->num_rows();

            if ($returnCount != 0) {
                $res = ['success' => false, 'message' => 'Unable to delete. Sale return found'];
                echo json_encode($res);
                exit;
            }

            /*Get Sale Details Data*/
            $saleDetails = $this->db->select('Product_IDNo, SaleDetails_TotalQuantity')->where('SaleMaster_IDNo', $saleId)->get('tbl_saledetails')->result();

            if ($sale->Status == 'a') {
                foreach ($saleDetails as $detail) {
                    /*Get Product Current Quantity*/
                    $totalQty = $this->db->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $sale->SaleMaster_branchid])->get('tbl_currentinventory')->row()->sales_quantity;

                    /* Subtract Product Quantity form  Current Quantity  */
                    $newQty = $totalQty - $detail->SaleDetails_TotalQuantity;

                    /*Update Sales Inventory*/
                    $this->db->set('sales_quantity', $newQty)->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $sale->SaleMaster_branchid])->update('tbl_currentinventory');

                    // update color wise stock
                    $this->db->query("
                    update tbl_color_size
                    set stock = stock + ?
                    where product_id = ?
                    and color_id = ? 
                    and size_id = ? 
                    and branch_id = ?
                ", [$detail->SaleDetails_TotalQuantity, $detail->Product_IDNo, $detail->Product_colorId, $detail->Product_sizeId, $this->session->userdata('BRANCHid')]);
                }
            }

            /*Delete Sale Details*/
            $this->db->set('Status', 'd')->where('SaleMaster_IDNo', $saleId)->update('tbl_saledetails');

            /*Delete Sale Master Data*/
            $this->db->set('Status', 'd')->where('SaleMaster_SlNo', $saleId)->update('tbl_salesmaster');

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Sale deleted'];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function  OrderStatusChange()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

            $sale = $this->db->select('*')->where('SaleMaster_SlNo', $saleId)->get('tbl_salesmaster')->row();
            // if ($sale->Status != 'p') {
            //     $res = ['success' => false, 'message' => 'Order not found'];
            //     echo json_encode($res);
            //     exit;
            // }


            if ($data->Status == 'a') {
                /*Get Sale Details Data*/
                $saleDetails = $this->db->select('Product_IDNo, SaleDetails_TotalQuantity, Product_colorId, Product_sizeId')->where('SaleMaster_IDNo', $saleId)->get('tbl_saledetails')->result();

                foreach ($saleDetails as $product) {
                    $stock = $this->mt->productStock($product->Product_IDNo);
                    if ($product->SaleDetails_TotalQuantity > $stock) {
                        $res = ['success' => false, 'message' => 'Stock Unavailable'];
                        echo json_encode($res);
                        exit;
                    }
                }

                // update salesmaster
                $updateSale = array(
                    'SaleMaster_bankPaid'   => $sale->SaleMaster_bankPaid + $sale->SaleMaster_DueAmount,
                    'SaleMaster_PaidAmount' => $sale->SaleMaster_PaidAmount + $sale->SaleMaster_DueAmount,
                    'SaleMaster_DueAmount'  => 0
                );
                $salesBank = array(
                    'salesId'       => $sale->SaleMaster_SlNo,
                    'account_id'    => 1,
                    'amount'        => $sale->SaleMaster_bankPaid + $sale->SaleMaster_DueAmount,
                    'charge_amount' => 0,
                    'lastDigit'     => 0,
                    'status'        => 'a',
                );
                $this->db->insert('tbl_salesmaster_account', $salesBank);
                
                $this->db->where('SaleMaster_SlNo', $sale->SaleMaster_SlNo);
                $this->db->update('tbl_salesmaster', $updateSale);

            }

            /*deliver order Details*/
            $this->db->set('Status', $data->Status)->where('SaleMaster_IDNo', $saleId)->update('tbl_saledetails');

            /*deliver Sale Master Data*/
            $this->db->set('Status', $data->Status)->where('SaleMaster_SlNo', $saleId)->update('tbl_salesmaster');

            if ($data->Status == 'a') {
                foreach ($saleDetails as $detail) {
                    /*Update Sales Inventory*/
                    $this->db->query("
                    update tbl_currentinventory 
                    set sales_quantity = sales_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$detail->SaleDetails_TotalQuantity, $detail->Product_IDNo, $this->session->userdata('BRANCHid')]);

                    // update color wise stock
                    $this->db->query("
                                update tbl_color_size 
                                set stock = stock - ?
                                where product_id = ?
                                and color_id = ? 
                                and size_id = ? 
                                and branch_id = ?
                            ", [$detail->SaleDetails_TotalQuantity, $detail->Product_IDNo, $detail->Product_colorId, $detail->Product_sizeId, $this->session->userdata('BRANCHid')]);
                }
            }
            if ($data->Status == 'process') {
                $res = ['success' => true, 'message' => 'Order processing success'];
            }
            if ($data->Status == 'cancel') {
                $res = ['success' => true, 'message' => 'Order cancel success'];
            }
            if ($data->Status == 'a') {
                $res = ['success' => true, 'message' => 'Order delivery success'];
            }
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deliveryStatusChange()
    {
        $data = json_decode($this->input->raw_input_stream);
        // update salesmaster
        $updateSale = array(
            'delivery_status' => $data->delivery_status
        );
        $this->db->where('SaleMaster_SlNo', $data->saleId);
        $this->db->update('tbl_salesmaster', $updateSale);
        $res = ['success' => true, 'message' => 'Order delivery success'];
        echo json_encode($res);
    }
    public function getAllOrderFilter()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";
        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }
        if (isset($data->dateFrom) && $data->dateFrom != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $res = $this->db->query("SELECT
                                sm.*,
                                c.Customer_Name,
                                em.Employee_Name
                            FROM tbl_salesmaster sm
                            LEFT JOIN tbl_customer c ON c.Customer_SlNo = sm.SalseCustomer_IDNo
                            LEFT JOIN tbl_employee em ON em.Employee_SlNo = sm.employee_id
                            WHERE sm.Status = 'a'
                            AND sm.is_order = 'true'
                            AND sm.SaleMaster_branchid = '$this->sbrunch' $clauses")->result();
        echo json_encode($res);
    }

    public function PendingOrder()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Pending Order Record";
        $data['content'] = $this->load->view('Administrator/order/order_pending_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function ProcessingOrder()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Processing Order Record";
        $data['content'] = $this->load->view('Administrator/order/order_processing_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function DeleveredOrder()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Delivery Order Record";
        $data['content'] = $this->load->view('Administrator/order/order_delivery_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function CancelOrder()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Delivery Cancel Record";
        $data['content'] = $this->load->view('Administrator/order/order_cancel_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getAllOrder()
    {
        $query = $this->db->query("
            select 
                sm.*,
                c.Customer_Code,
                c.Customer_Name,
                c.Customer_Mobile,
                c.Customer_Address,
                c.Customer_Type,
                e.Employee_Name,
                br.Brunch_name
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            where sm.SaleMaster_branchid = ?
            and sm.Status != 'd'
            and sm.is_order = 'true'
            order by sm.SaleMaster_SlNo desc
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($query);
    }
}
