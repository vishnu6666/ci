<?php 

public function login()
    {
        if ($this->session->userdata('logged_in') == true) {
            redirect('dashboard');
        }
        if (!empty($this->input->post())) {
            $username = $this->input->post('email');
            $password = $this->input->post('password');
            $user_check = $this->auth_model->user_check($username);
            if ($user_check) {
                $user_data = $this->auth_model->login_user($username, $password);
                if ($user_data) {
                    $session_data = array(
                        "user_id" => $user_data['id'],
                        "uuid"=> $user_data['uuid'],
                        "status" => $user_data['status'],
                        "first_name" => $user_data['first_name'],
                        "last_name" => $user_data['last_name'],
                        "email" => $user_data['email'],
                        "avatar" => $user_data['avatar'],
                        "type" => $user_data['type'],
                        "flag_password" => $user_data['flag_password'],
                        "logged_in" => true,
                    );
                    $this->session->set_userdata($session_data);
                    if ($user_data['type'] == 0 || $user_data['type'] == 1) {
                
                        if ($user_data['flag_password'] == 1) {
                            redirect(base_url() . 'change-password-user');
                        } else {
                            if ($user_data['type'] == 1) {
                                redirect(base_url() . 'dashboard');
                            } else {
                                redirect(base_url() . 'dashboard');
                            }
                        }
                    } else {
                    
                        redirect(base_url() . 'dashboard');
                    }
                } else {
                    $this->session->set_flashdata('error_msg', 'Password Invalid');
                    redirect('login');
                }
            } else {
                $this->session->set_flashdata('error_msg', 'User not found.');
                redirect('login');
            }
        }

        $this->template->title = 'Login ';
        $this->template->content->view('login');
        $this->template->publish();
        //$this->load->view('welcome_message');
    }

    public function user_check($username)
    {
        $this->db->select('*')
            ->from('accounts')
            ->where('email', $username)
            ->or_where('number', $username);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function login_user($username, $password)
    {
        $this->db->select('*')
            ->from('accounts')
            ->group_start()
            ->where('email', $username)
            ->or_where('number', $username)
            ->group_end()
            ->where('password', $password);
        if ($query = $this->db->get()) {
            return $query->row_array();
        } else {
            return false;
        }
    }

// Sql
    
    CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(120) NOT NULL,
  `number` char(20) NOT NULL,
  `type` tinyint(1) DEFAULT '0' COMMENT '-1= Administrator , 0 = Customer , 1 = Reseller',
  `user_name` varchar(50) NOT NULL,
  `password` char(100) NOT NULL DEFAULT '',
  `email` char(80) NOT NULL DEFAULT '',
  `first_name` char(40) NOT NULL DEFAULT '',
  `last_name` char(40) NOT NULL DEFAULT '',
  `company_name` char(40) NOT NULL DEFAULT '',
  `telephone` char(20) NOT NULL DEFAULT '',
  `address` char(80) NOT NULL DEFAULT '',
  `city` char(20) NOT NULL DEFAULT '',
  `postal_code` char(12) NOT NULL DEFAULT '',
  `province` char(20) NOT NULL DEFAULT '',
  `country_id` int(3) NOT NULL DEFAULT '0' COMMENT 'Country table id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1:active,0:inactive',
  `currency` int(50) NOT NULL,
  `timezone_id` int(3) NOT NULL DEFAULT '0' COMMENT 'timezone table id',
  `avatar` varchar(120) NOT NULL DEFAULT 'default.jpg',
  `phone_number` varchar(50) NOT NULL,
  `fax_number` varchar(50) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=deleted',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reseller_id` varchar(50) DEFAULT '0' COMMENT 'if 0 = reseller  account  , NULL  = admin customers , uuid = reseller  customers ',
  `balance` bigint(255) NOT NULL,
  `creation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enc_password` varchar(11) NOT NULL,
  `flag_password` tinyint(4) NOT NULL DEFAULT '0',
  `industry_id` int(11) NOT NULL,
  `salesforce_client_id` varchar(220) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `number` (`number`),
  KEY `reseller` (`reseller_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;


INSERT INTO `accounts` (`id`, `uuid`, `number`, `type`, `user_name`, `password`, `email`, `first_name`, `last_name`, `company_name`, `telephone`, `address`, `city`, `postal_code`, `province`, `country_id`, `status`, `is_recording`, `ratecard`, `currency`, `timezone_id`, `avatar`, `phone_number`, `fax_number`, `deleted`, `created_at`, `updated_at`, `reseller_id`, `balance`, `creation`, `enc_password`, `flag_password`, `industry_id`, `salesforce_client_id`) VALUES
(112, 'ee5bc1ea-0ea8-4ab7-b3ac-9ee2845ceff4', '', 1, 'Test', '123456', 'reseller@gmail.com', 'vishnu', 'prajapati', 'magictechnolabs', '', 'ahmedabad', 'ahmedabad', '787878', 'gujarat', 85, 1, 0, 28, 114, 6, 'default.jpg', '', '', 0, '2018-08-18 12:57:30', '2018-08-18 14:02:23', '0', 1870, '0000-00-00 00:00:00', '', 0, 0, ''),
(120, '116b63ad-ef11-4d0e-8b84-79e7fe96438c', '', -1, 'vishal', 'e10adc3949ba59abbe56e057f20f883e', 'vishal@gmail.com', 'vishal', 'runi', '', '', '', '', '', '', 0, 1, 0, 0, 0, 0, 'default.jpg', '', '', 0, '2019-03-03 05:07:28', '2019-05-05 06:02:29', '0', 0, '0000-00-00 00:00:00', '', 0, 0, '');
