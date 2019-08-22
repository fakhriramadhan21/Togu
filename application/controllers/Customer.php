<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

	/*
	 *	@author 	: Ajit Dhanawade / 8108702999
	 *	date		: 09 June, 2017
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
	{	if ($this->session->userdata('user_login') == 0) redirect('customer/logout');
		$this->session->set_userdata('menu','profile');
		$this->session->set_userdata('submenu','adminprofile');
		$UserID=$this->session->userdata('user_id');
		$data['userdata'] = $this->db->get_where('users' , array('id' => $UserID) );
		
		//$data['userdata'] = $this->db->get('users');
		$this->load->view('customer/customer_profile',$data);
		
	}
	
	// Customer Controller = = >
	
	function login()
	{	
			$mobile=$this->input->post('email');
			$pwd=$this->input->post('password');
			
			$this->db->like('mobile', $mobile);
			$this->db->like('password', $pwd);
			$custresult = $this->db->get('users');
		
						
			if (count($custresult->result()) > 0)
			{	
					$this->db->like('mobile', $mobile);
					$this->db->like('status', 'disable');
					$check = $this->db->get('users');
				 
					if(count($check->result()) > 0)		// using direct parameter
					{
					?>
					<script> alert("Your account is Deactived \nPlease Contact Administrator "); </script>
					<?php
					redirect('../../','refresh');
					}	
			
				
				//echo "Login Successfully ==> Redirecting";
				$this->session->set_userdata('user_login', 1);
				if ($this->session->userdata('user_login') == 1)
				{	
					foreach($custresult->result() as $getcustrow): 
					$CustId=$getcustrow->id;
					$this->session->set_userdata('user_id',$CustId);
					endforeach; 
			
				?> <script> alert("Hi, <?php echo $mobile; ?> Your Login Successfully \nRedirecting Dashboard ..."); </script> <?php	
				redirect('customer/index','refresh');
				}
			}
			else
			{	$this->session->set_userdata('user_login', 0);
				echo "<script> alert('Username and Password Invalid...'); </script>"; 	
				redirect('../../','refresh');
				//echo "Error :";
			}
			
	}
		
	
	function register()
	{	
			$join_date=date('d-m-Y');
			$first_name=ucwords($this->input->post('first_name'));
			$last_name=ucwords($this->input->post('last_name'));
			$email_id=$this->input->post('email');
			$phone=$this->input->post('phone');
			$password='demo'; 
			$status='enable';
			
			$reg_users = array('join_date' => $join_date, 'first_name' => $first_name, 'last_name' => $last_name, 'email_id' => $email_id, 'mobile' => $phone, 'password' => $password, 'status' => $status);
			if($this->db->insert('users', $reg_users)===TRUE)		// using direct parameter
			{
			?>
			<script> alert("Your Registration Successfully\n Your Temp Password : demo "); </script>
			<?php
			redirect('../../','refresh');
			}
			
	}
	
	function logout() {
        $this->session->set_userdata('user_login', 0);
		$this->session->sess_destroy();
        $this->session->set_flashdata('logout_notification', 'logged_out');
		?>
			<script> alert("Your are Logging Out"); </script>
		<?php
		redirect('../../','refresh');
    }
	
	
	
	// customer Profile Controller --->
	
	function profile()
	{	if ($this->session->userdata('user_login') == 0) redirect('customer/logout');
		$this->session->set_userdata('menu','profile');
		$this->session->set_userdata('submenu','adminprofile');
		$UserID=$this->session->userdata('user_id');
		$data['userdata'] = $this->db->get_where('users' , array('id' => $UserID) );
		
		//$data['userdata'] = $this->db->get('users');
		$this->load->view('customer/customer_profile',$data);
		
	}
	
	function edit_profile($id="")
	{	
		$UserID=$this->session->userdata('user_id');
		$data['userdata'] = $this->db->get_where('users' , array('id' => $UserID) );
		$this->load->view('customer/edit_profile',$data);
		
	}
	
	function update_profile($id="")
	{	
			$customer['mobile'] = $this->input->post('mobile');
            $customer['email_id'] = $this->input->post('email');
            $customer['address'] = $this->input->post('address');
            $NewPass=$this->input->post('new_passwd');
			
			if($NewPass!="")
			{
			    $customer['password'] = $this->input->post('new_passwd');
			}	
           
            $this->db->where('id' , $id);
			
			if($this->db->update('users' , $customer)===TRUE)		// using direct parameter
			{
			?>
				<script> confirm("Are you sure update this information \n After update automatically logout"); </script>
			<?php
			redirect('customer/logout','refresh');
			}
			
	}
	
	function demo_ver()
	{
		$this->load->view('customer/demo');
	}
	
}	