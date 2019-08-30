<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	/*
	 *	@author 	: Ajit Dhanawade / 8108702999
	 *	date		: 30 May, 2017
	 *	Laundry Management Application
	 *	ajitrajyash@gmail.com
	*/
 
	public function __construct()
    {   parent::__construct();
        $this->load->helper(array('form','url','html'));
        $this->load->library(array('session', 'form_validation'));
        $this->load->database();
      	$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Sat, 11 Jun 1983 05:00:00 GMT");
		
    }
	
	public function index()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','dashboard');
		
		$this->db->from('customer_order');
		$this->db->order_by("total_paid", "desc");
		$desktop['order_data'] = $this->db->get();
		
		$desktop['userdata'] = $this->db->get('users');
		$desktop['today']=date('d-m-Y');
		$this->load->view('admin/header');
		$this->load->view('admin/main',$desktop);
		$this->load->view('admin/footer');
	}
	
	
	// Customer Controller = = >
	
	function customers()
	{	
		if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','customers');
		$data['userdata'] = $this->db->get('users');
		$data['last_id']=0;	
		$result=$this->db->select('*')->from('users')->order_by('id',"desc")->limit(1)->get()->result();
		if (count($result) > 0)	$data['last_id'] = $result[0]->id;
		$this->load->view('admin/users',$data);
	}

	// Customer Order = = >
	
	function Order()
	{	
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','order');
		$data['result_customer']=$this->db->select('*')->from('users')->get();
		$data['result_employee']=$this->db->select('*')->from('employee')->get();
		$data['result_package']=$this->db->select('*')->from('services')->get();
		$data['result']=$this->db->select('*')->from('customer_order')->get();
		$this->load->view('admin/order',$data);
	}

	function order_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$customer_id=$this->input->post('customer_id');
			$order_date=$this->input->post('order_date');
			$qty=$this->input->post('qty');
			$discount=$this->input->post('discount');
			$tax=$this->input->post('tax');
			$total_paid=$this->input->post('total_paid');
			$status=$this->input->post('status');

			$invoice=$this->input->post('invoice');
			$order_month=$this->input->post('month');
			$delivery_date=$this->input->post('delivery');
			$desc=$this->input->post('desc');
			$paid_to=$this->input->post('paidto');
			// $cloth_code=$this->input->post('cloth_code');
			$data = array(
				'customer_id' => $customer_id,
				'order_date' => $order_date, 
				'total_qty' => $qty,
				'discount' => $discount,
				'service_tax' => $tax,
				'total_paid' => $total_paid,
				'order_status' => $status,
				
				'invoice_no' => $invoice,
				'discount' => $order_month,
				'delivery_date' => $delivery_date,
				'remarks' => $desc,
				'amt_paidby' => $paid_to
			 );
			if($this->db->insert('customer_order', $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Added Successfully"); </script>
			<?php
			redirect('admin/order','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$query['cloths_edit'] = $this->db->get_where('customer_order' , array('invoice_no' => $param2) )->result();
			$this->load->view('admin/order_update',$query);
		}
		
		if ($param1 == 'modify') 
		{   $data['invoice_no']= $this->input->post('cloth_name');
            $data['order_status'] = $this->input->post('cloth_code');
			$this->db->where('invoice_no' , $param2);
			
			if($this->db->update('customer_order' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/order','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('invoice_no' , $param2);
            if($this->db->delete('customer_order')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/order','refresh');
			}	
		}
	}

	// Monthly Order = = >
	
	function M_order()
	{	
		$data['result_customer']=$this->db->select('*')->from('users')->get();
		$data['result_employee']=$this->db->select('*')->from('employee')->get();
		$data['result']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->get();

		$data['daily_order']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->where('order_date', 'NOW()')->count_all_results();
		$data['weekly_order']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->where('order_date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()')->count_all_results();
		$data['monthly_order']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->where('order_date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()')->count_all_results();
		$data['lifetime_order']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->count_all_results();
		
		$data['daily_income']=$this->db->select('SUM(total_paid) as total')->from('customer_order')->where('order_date', 'NOW()')->where('order_status', 'Paid')->get();
		$data['weekly_income']=$this->db->select('SUM(total_paid) as total')->from('customer_order')->where('order_date BETWEEN DATE_SUB(NOW(), INTERVAL 7 DAY) AND NOW()')->where('order_status', 'Paid')->get();
		$data['monthly_income']=$this->db->select('SUM(total_paid) as total')->from('customer_order')->where('order_date BETWEEN DATE_SUB(NOW(), INTERVAL 31 DAY) AND NOW()')->where('order_status', 'Paid')->get();
		// $data['monthly_order']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->get();
		// $data['lifetime_order']=$this->db->select('*')->from('customer_order')->where('order_status', 'Paid')->get();

		$data['lifetime_income']=$this->db->select('SUM(total_paid) as total')->from('customer_order')->where('order_status', 'Paid')->get();
		// $data['result'] = $this->db->get_where('customer_order' , array('order_status' => 'Not Paid') )->result();
		$this->load->view('admin/Monthly_order',$data);
	}

	function M_order_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$customer_id=$this->input->post('customer_id');
			$order_date=$this->input->post('order_date');
			$qty=$this->input->post('qty');
			$discount=$this->input->post('discount');
			$tax=$this->input->post('tax');
			$total_paid=$this->input->post('total_paid');
			$status=$this->input->post('status');

			$invoice=$this->input->post('invoice');
			$order_month=$this->input->post('month');
			$delivery_date=$this->input->post('delivery');
			$desc=$this->input->post('desc');
			$paid_to=$this->input->post('paidto');
			// $cloth_code=$this->input->post('cloth_code');
			$data = array(
				'customer_id' => $customer_id,
				'order_date' => $order_date, 
				'total_qty' => $qty,
				'discount' => $discount,
				'service_tax' => $tax,
				'total_paid' => $total_paid,
				'order_status' => $status,
				
				'invoice_no' => $invoice,
				'discount' => $order_month,
				'delivery_date' => $delivery_date,
				'remarks' => $desc,
				'amt_paidby' => $paid_to
			 );
			if($this->db->insert('customer_order', $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Added Successfully"); </script>
			<?php
			redirect('admin/order','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$query['cloths_edit'] = $this->db->get_where('customer_order' , array('invoice_no' => $param2) )->result();
			$this->load->view('admin/order_update',$query);
		}
		
		if ($param1 == 'modify') 
		{   $data['invoice_no']= $this->input->post('cloth_name');
            $data['order_status'] = $this->input->post('cloth_code');
			$this->db->where('invoice_no' , $param2);
			
			if($this->db->update('customer_order' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/order','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('invoice_no' , $param2);
            if($this->db->delete('customer_order')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/order','refresh');
			}	
		}
	}
	
	// customer Profile Controller --->
	
	function customer_profile($CustID='')
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','customers');
		
		$UserID=$CustID;
		$data['userdata'] = $this->db->get_where('users' , array('id' => $UserID) );
		
		//$data['userdata'] = $this->db->get('users');
		$this->load->view('admin/customer_profile',$data);
		
	}
	
	
	function customer_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$join_date=$this->input->post('join_date');
			$first_name=ucwords($this->input->post('first_name'));
			$last_name=ucwords($this->input->post('last_name'));
			$email_id=$this->input->post('email');
			$phone=$this->input->post('phone');
			$daddress=$this->input->post('address');
			$password=$this->input->post('password');
			$status=$this->input->post('status');
			
			$customer_data = array('join_date' => $join_date, 'first_name' => $first_name, 'last_name' => $last_name, 'address' => $daddress, 'email_id' => $email_id, 'mobile' => $phone, 'password' => $password, 'status' => $status);
			if($this->db->insert('users', $customer_data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert("Record Added Successfully"); </script>
			<?php
			redirect('admin/customers','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$query['custmoer_edit'] = $this->db->get_where('users' , array('id' => $param2) )->result();
			$this->load->view('admin/customer_update',$query);
		}
		
		
		if ($param1 == 'modify') 
		{  
            $data['join_date'] = $this->input->post('join_date');
            $data['first_name'] = $this->input->post('first_name');
            $data['last_name'] = $this->input->post('last_name');
            $data['email_id'] = $this->input->post('email');
            $data['mobile'] = $this->input->post('phone');
            $data['address'] = $this->input->post('address');
			$data['password']= $this->input->post('password');
            $data['status'] = $this->input->post('status');
			$this->db->where('id' , $param2);
			
			if($this->db->update('users' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/customers','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('id' , $param2);
            if($this->db->delete('users')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/customers','refresh');
			}	
		}
	}
	
		
	
	
	
	// Employee Controller = = >
	
	function employer()
	{
		if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','employee');
		$data['empdata'] = $this->db->get('employee');
		$data['last_id']=0;	
		$result=$this->db->select('*')->from('employee')->order_by('emp_id',"desc")->limit(1)->get()->result();
		if (count($result) > 0)	$data['last_id'] = $result[0]->emp_id;
		$this->load->view('admin/employee',$data);
	}
	
	function employee_crud($param1='', $param2='')
	{	if($param1=='new')
		{	$join_date=$this->input->post('join_date');
			$first_name=ucwords($this->input->post('first_name'));
			$last_name=ucwords($this->input->post('last_name'));
			$email_id=$this->input->post('email');
			$gender=$this->input->post('gender');
			$birth_date=$this->input->post('birth_date');
			$phone=$this->input->post('phone');
			$address=$this->input->post('address');
			$password=$this->input->post('password');
			$status=$this->input->post('status');
			
			$database_data = array('join_date' => $join_date, 'first_name' => $first_name, 'last_name' => $last_name, 'mobile' => $phone, 'email_id' => $email_id, 'address' => $address,  'birth_date' => $birth_date,  'gender' => $gender, 'password' => $password, 'status' => $status);
			if($this->db->insert('employee', $database_data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert("Employee Added Successfully"); </script>
			<?php
			redirect('admin/employer','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$query['employee_edit'] = $this->db->get_where('employee' , array('emp_id' => $param2) )->result();
			$this->load->view('admin/employee_update',$query);
		}
		
		
		if ($param1 == 'modify') 
		{  
            $data['join_date'] = $this->input->post('join_date');
            $data['first_name'] = $this->input->post('first_name');
            $data['last_name'] = $this->input->post('last_name');
            $data['email_id'] = $this->input->post('email');
            $data['mobile'] = $this->input->post('phone');
            $data['address'] = $this->input->post('address');
			$data['password']= $this->input->post('password');
            $data['status'] = $this->input->post('status');
			$this->db->where('emp_id' , $param2);
			
			if($this->db->update('employee' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Employee Updated Successfully"); </script>
			<?php
			redirect('admin/employer','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('emp_id' , $param2);
            if($this->db->delete('employee')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Employee Deleted Successfully"); </script>
			<?php
			redirect('admin/employer','refresh');
			}	
		}
	}
	
	// Cloth Controller ==>
	function cloth_type()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','cloths');
		$data['cloth'] = $this->db->get('cloths');
		$data['last_id']=0;	
		$result=$this->db->select('*')->from('cloths')->order_by('id',"desc")->limit(1)->get()->result();
		if (count($result) > 0)	$data['last_id'] = $result[0]->id;
		$this->load->view('admin/cloth',$data);
	}
	
	function cloth_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$cloth_type=$this->input->post('cloth_name');
			$cloth_code=$this->input->post('cloth_code');
			$data = array('cloth_type' => $cloth_type, 'cloth_code' => $cloth_code );
			if($this->db->insert('cloths', $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Added Successfully"); </script>
			<?php
			redirect('admin/cloth_type','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$query['cloths_edit'] = $this->db->get_where('cloths' , array('id' => $param2) )->result();
			$this->load->view('admin/cloths_update',$query);
		}
		
		if ($param1 == 'modify') 
		{   $data['cloth_type']= $this->input->post('cloth_name');
            $data['cloth_code'] = $this->input->post('cloth_code');
			$this->db->where('id' , $param2);
			
			if($this->db->update('cloths' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/cloth_type','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('id' , $param2);
            if($this->db->delete('cloths')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/cloth_type','refresh');
			}	
		}
	}
	
	// Expeses Type Controller ==>
	
	function expenses_type()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','expensestype');
		$data['expenstype'] = $this->db->get('expense_type');
		$data['last_id']=0;	
		$result=$this->db->select('*')->from('expense_type')->order_by('exps_id',"desc")->limit(1)->get()->result();
		if (count($result) > 0)	$data['last_id'] = $result[0]->exps_id;
		$this->load->view('admin/expenses_type',$data);
	}
	
	function expenses_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$expse_type=$this->input->post('expse_type');
			$expse_code=$this->input->post('expse_code');
			$data = array('exps_type' => $expse_type, 'exps_code' => $expse_code );
			if($this->db->insert('expense_type', $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Added Successfully"); </script>
			<?php
			redirect('admin/expenses_type','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$query['exps_edit'] = $this->db->get_where('expense_type' , array('exps_id' => $param2) )->result();
			$this->load->view('admin/exps_type_update',$query);
		}
		
		if ($param1 == 'modify') 
		{   $data['exps_type']= $this->input->post('expse_type');
            $data['exps_code'] = $this->input->post('expse_code');
			$this->db->where('exps_id' , $param2);
			
			if($this->db->update('expense_type' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/expenses_type','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('exps_id' , $param2);
            if($this->db->delete('expense_type')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/expenses_type','refresh');
			}	
		}
	}

	// Services Controller ==>
	function laundry_services()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','services');
		$data['service'] = $this->db->get('services');
		$data['last_id']=0;	
		$result=$this->db->select('*')->from('services')->order_by('id',"desc")->limit(1)->get()->result();
		if (count($result) > 0)	$data['last_id'] = $result[0]->id;
		$this->load->view('admin/services',$data);
	}
	
	function service_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$service_name=$this->input->post('service_name');
			$service_code=$this->input->post('service_code');
			$price=$this->input->post('price');
			$duration=$this->input->post('duration');
			$data = array('service_name' => $service_name, 'service_code' => $service_code, 'duration' => $duration, 'price_kg' => $price );
			if($this->db->insert('services', $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Added Successfully"); </script>
			<?php
			redirect('admin/laundry_services','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	$Serviice['service_edit'] = $this->db->get_where('services' , array('id' => $param2) )->result();
			$this->load->view('admin/service_update',$Serviice);
		}
		
		if ($param1 == 'modify') 
		{   $data['service_name']= $this->input->post('service_name');
            $data['service_code'] = $this->input->post('service_code');
			$this->db->where('id' , $param2);
			
			if($this->db->update('services' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/laundry_services','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('id' , $param2);
            if($this->db->delete('services')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/laundry_services','refresh');
			}	
		}
	}
	
	
	// Assing Cloth Service Prices --->
	
	function cloth_prices()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','master');
		$this->session->set_userdata('submenu','pricelist');
		
		$this->load->view('admin/price_list');
	}
	
	// Exepnsese List ----------->
	
	function expenses()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','job_order');
		$this->session->set_userdata('submenu','expenses');
		$data['expensetype'] = $this->db->get('expense_type');
		$data['expenses'] = $this->db->get('expenses');
		$data['last_id']=0;	
		$result=$this->db->select('*')->from('expenses')->order_by('exp_id',"desc")->limit(1)->get()->result();
		if (count($result) > 0)	$data['last_id'] = $result[0]->exp_id;
		$this->load->view('admin/expenses_list',$data);
	}
	
	function expense_crud($param1='', $param2='')
	{	if($param1=='create')
		{	$ExpDate=$this->input->post('exp_date');
			$PayeeName=$this->input->post('payee_name');
			$ExpType=$this->input->post('exp_type');
			$ExpAmt=$this->input->post('exp_amt');
			$ExpAmtPaidBy=$this->input->post('exp_amt_paid_by');
			$ExpChequeNo=$this->input->post('exp_cheque_no');
			$ExpChequeDate=$this->input->post('exp_cheque_date');
			$ExpRemark=$this->input->post('exp_remark');
			
			$data = array('exps_date' => $ExpDate, 'exp_payee_name' => $PayeeName, 'exp_type' => $ExpType, 'exp_amt' => $ExpAmt, 'exp_paidby' => $ExpAmtPaidBy, 'exp_chequeno' => $ExpChequeNo, 'exp_cheque_date' => $ExpChequeDate, 'exp_remarks' => $ExpRemark );
			if($this->db->insert('expenses', $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Added Successfully"); </script>
			<?php
			redirect('admin/expenses','refresh');
			}	
		}
		
		if($param1=='do_update')
		{	
			$query['expensetype'] = $this->db->get('expense_type');
			$query['expenses_edit'] = $this->db->get_where('expenses' , array('exp_id' => $param2) )->result();
			$this->load->view('admin/expenses_edit',$query);
		}
		
		if ($param1 == 'modify') 
		{   $data['exps_date']= $this->input->post('exp_date');
            $data['exp_payee_name'] = $this->input->post('payee_name');
            $data['exp_type'] = $this->input->post('exp_type');
            $data['exp_amt'] = $this->input->post('exp_amt');
            $data['exp_paidby'] = $this->input->post('exp_amt_paid_by');
            $data['exp_chequeno'] = $this->input->post('exp_cheque_no');
            $data['exp_cheque_date'] = $this->input->post('exp_cheque_date');
            $data['exp_remarks'] = $this->input->post('exp_remark');
			$this->db->where('exp_id' , $param2);
			
			if($this->db->update('expenses' , $data)===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Updated Successfully"); </script>
			<?php
			redirect('admin/expenses','refresh');
			}
		}
		
		if($param1=='delete')
		{	$this->db->where('exp_id' , $param2);
            if($this->db->delete('price_list')===TRUE)		// using direct parameter
			{
			?>
			<script> alert(" Record Deleted Successfully"); </script>
			<?php
			redirect('admin/expenses','refresh');
			}	
		}
	}
	
	
	// Admin Profile Controller --->
	
	function profile()
	{	if ($this->session->userdata('admin_login') == 0) redirect('login/logout');
		$this->session->set_userdata('menu','settings');
		$this->session->set_userdata('submenu','adminprofile');
		$data['admindata'] = $this->db->get('admin');
		$this->load->view('admin/admin_profile',$data);
		
	}
	
	function edit_profile($id="")
	{	
		$data['admindata'] = $this->db->get('admin');
		$this->load->view('admin/edit_profile',$data);
		
	}
	
	function update_profile($id="")
	{	
			$admin['admin_name']= $this->input->post('full_name');
            $admin['mobile'] = $this->input->post('mobile');
            $admin['email_id'] = $this->input->post('email');
            $admin['username'] = $this->input->post('username');
            $NewPass=$this->input->post('new_passwd');
			
			if($NewPass!="")
			{
			   $admin['password'] = sha1($this->input->post('new_passwd'));
			}	
           
            $this->db->where('id' , $id);
			
			if($this->db->update('admin' , $admin)===TRUE)		// using direct parameter
			{
			?>
			<script> confirm(" Are you sure update this information"); </script>
			<?php
			redirect('login/logout','refresh');
			}
			
		
	}
	
	
	
	
	
	
		
}
