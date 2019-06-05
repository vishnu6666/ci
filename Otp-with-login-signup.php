<?php 
public function otp_check_signup()
    {
            $registered_id = $this->session->userdata('registered_id');
            $code_id = $this->session->userdata('code_id');
            $otp_check_signup = $this->input->post('otp_check_signup');

            $result = 0;
            $user_data = $this->api_model->varifyOtpCodeSignup($registered_id,$otp_check_signup,$code_id);
       
            if ($user_data) { 
                 $otpStatus = $this->api_model->updateOtpStatusSignup($user_data);
                 $session_data = array(
                        "fname"         => $user_data['fname'],
                        "lname"         => $user_data['lname'],
                        "email"         => $user_data['email'],
                        "mobile_number" => $user_data['mobile_number'],
                        "birth_date"    => $user_data['birth_date'],
                        "gender"        => $user_data['gender'],
                        "verified"      => $user_data['verified'],
                        "logged_in"     => true,
                    );
                $this->session->set_userdata($session_data);
                $result = 2;
           }else{
                $result = 1;
            }
           echo $result;
        }
    public function signup()
    {
        $mobile_no = $this->input->post("mobile_no_signup");
        $email = $this->input->post("email_signup");
        $check_old_edit = $this->api_model->checkUserRegistration($mobile_no);

        $count = $check_old_edit['cnt'];
        $result = 0;
        if ($count > 0) {
             $getUser = $this->api_model->getMobileDataByMobileno($mobile_no);

               $Phonecode = rand(1111, 9999);
                $data_otp_phone = array(
                    'registered_id' => $getUser['registered_id'],
                    'code'    => $Phonecode,
                    'cdate'   => date('Y-m-d H:i:s'),
                    'status'  => '1',
                );
                //$this->sendMail($getUser['email'], $Phonecode);
                $res_otp = $this->api_model->insertOtpCode($data_otp_phone);
                $code_id_last = $this->db->insert_id();

                if ($res_otp === TRUE) {

                 $session_data = array(
                    'registered_id' => $getUser['registered_id'],
                    'code_id'       => $code_id_last,
                    );
                    $session_id = $this->session->set_userdata($session_data);
                    $result = 1;
             } else {
                    $result = 0;
                }
          } else {
            $data = array(
                'mobile_number' => $mobile_no,
                'email' => $email,
                'cdate' => date('Y-m-d H:i:s'),
                'udate' => date('Y-m-d H:i:s'),
            );
            $insertResult = $this->api_model->insertMobileData($data);
            if ($insertResult === TRUE) {

                $cid = $this->db->insert_id();

                $Phonecode = rand(1111, 9999);
                $data_otp_phone = array(
                    'registered_id' => $cid,
                    'code'    => $Phonecode,
                    'cdate'   => date('Y-m-d H:i:s'),
                    'status'  => '1',
                );
                //$getUser = $this->api_model->getuserdata_by_id($cid);

                //$this->sendMail($getUser['email'], $Phonecode);

                $res_otp = $this->api_model->insertOtpCode($data_otp_phone);
                $code_id_last = $this->db->insert_id();

                if ($res_otp === TRUE) {

                 $session_data = array(
                    'registered_id' => $cid,
                    'code_id'       => $code_id_last,
                    );
                    $session_id = $this->session->set_userdata($session_data);
                    $result = 1;
                } else {
                    $result = 0;
                }
            } else {
                $result = 0;
            }
        }

        echo $result;
    }

    public function login()
    {
            $mobile_no = $this->input->post('mobile_no');
            $result = 0;
            $user_data = $this->api_model->checkExistMobileNumber($mobile_no);
            $registered_id = $user_data['registered_id'];
             if ($user_data) { 
                $Phonecode = rand(1111, 9999);
                $data_otp_phone = array(
                    'registered_id' => $user_data['registered_id'],
                    'code'    => $Phonecode,
                    'cdate'   => date('Y-m-d H:i:s'),
                    'status'  => '1',
                );
                
                //$this->sendMail($user_data['email'], $Phonecode);

                $res_otp = $this->api_model->insertOtpCode_login($data_otp_phone);
                    $cid = $this->db->insert_id();
                if ($res_otp) {
                    $id = $this->session->set_userdata('registered_id',$registered_id);
                    $code_id_login = $this->session->set_userdata('code_id_login',$cid);
                    $result = 3;
                } else {
                    $result = 0;
                }
           }else{
                $result = 1;
            }
           echo $result;
    }

    public function otp_check_login()
    {
            $registered_id = $this->session->userdata('registered_id');
            $code_id_login = $this->session->userdata('code_id_login');
            $otp = $this->input->post('otp');

            $result = 0;
            $user_data = $this->api_model->varifyOtpCodeLogin($registered_id,$otp,$code_id_login);
       
            if ($user_data) { 
                 $otpStatus = $this->api_model->updateOtpStatusLogin($user_data);
                 $session_data = array(
                        "fname"         => $user_data['fname'],
                        "lname"         => $user_data['lname'],
                        "email"         => $user_data['email'],
                        "mobile_number" => $user_data['mobile_number'],
                        "birth_date"    => $user_data['birth_date'],
                        "gender"        => $user_data['gender'],
                        "verified"      => $user_data['verified'],
                        "logged_in"     => true,
                    );
                $this->session->set_userdata($session_data);
                $result = 2;
           }else{
                $result = 1;
            }
           echo $result;
    }

    public function resendOtpLogin()
    {
        $date = date("Y-m-d H:i:s");
        $registered_id = $this->session->userdata('registered_id');
        $code_id_login = $this->session->userdata('code_id_login');
        $result = 0;
        $data = array(
            'status' => 0,
            'cdate' => $date
        );
        $updateResult = $this->api_model->updateOtpData_login($registered_id, $code_id_login, $data);

        if ($updateResult === TRUE) {
            $otpCode = rand(1111, 9999);
            $data_otp_phone = array(
                'registered_id' => $registered_id,
                'code' => $otpCode,
                'cdate' => date("Y-m-d H:i:s")
            );
            $res_otp = $this->api_model->insertOtpCode_login($data_otp_phone);
             $insert_id = $this->db->insert_id();
            if ($res_otp == TRUE) {
                $code_id_login = $this->session->set_userdata('code_id_login',$insert_id);
                $result = 1;
            } else {
                $result = 0;
            }
        } else {
            $result = 0;
        }
        echo $result;
    }

    public function resendOtpSignup()
    {
        $date = date("Y-m-d H:i:s");
        $registered_id = $this->session->userdata('registered_id');
        $code_id = $this->session->userdata('code_id');
        $result = 0;
        $data = array(
            'status' => 0,
            'cdate' => $date
        );
        $updateResult = $this->api_model->updateOtpData_signup($registered_id, $code_id, $data);

        if ($updateResult === TRUE) {
            $otpCode = rand(1111, 9999);
            $data_otp_phone = array(
                'registered_id' => $registered_id,
                'code' => $otpCode,
                'cdate' => date("Y-m-d H:i:s")
            );
            $res_otp = $this->api_model->insertOtpCode($data_otp_phone);
             $insert_id = $this->db->insert_id();
            if ($res_otp == TRUE) {
                $code_id = $this->session->set_userdata('code_id',$insert_id);
                $result = 1;
            } else {
                $result = 0;
            }
        } else {
            $result = 0;
        }
        echo $result;
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }


// Model


public function checkUserRegistration($mobile)
    {
        $this->db->select('count(*) as cnt');
        $this->db->from('tbl_registered_user');
        $this->db->where('mobile_number', $mobile);
        $query = $this->db->get();
        return $result = $query->row_array();
    }

    function getMobileDataByMobileno($mobile){
        $this->db->select('*');
        $this->db->from('tbl_registered_user');
        $this->db->where('mobile_number',$mobile);
        return $this->db->get()->row_array();
    }

    function getuserdata_by_id($lastid){
        $this->db->select('*');
        $this->db->from('tbl_registered_user');
        $this->db->where('id',$lastid);
        return $this->db->get()->row_array();
    }

    function insertOtpCode($data){
        return $this->db->insert('tbl_otp_code', $data);
    }

    function insertMobileData($data){
        return $this->db->insert('tbl_registered_user', $data);
    }

    function varifyOtpCodeSignup($registered_id,$otp_check_signup,$code_id){
        $this->db->select('*');
        $this->db->from('tbl_otp_code');
        $this->db->join('tbl_registered_user', 'tbl_otp_code.registered_id = tbl_registered_user.registered_id');
        $this->db->where('tbl_otp_code.registered_id',$registered_id);
        $this->db->where('tbl_otp_code.code',$otp_check_signup);
        $this->db->where('tbl_otp_code.code_id',$code_id);
        $this->db->where('tbl_otp_code.status',1);
        return $this->db->get()->row_array();
    }

    function updateOtpStatusSignup($data) {
        $this->db->where('registered_id', $data['registered_id']);
        return $this->db->update('tbl_otp_code', ['status' => 0]);
    }

    function checkExistMobileNumber($mobileNumber){
        $this->db->select('*');
        $this->db->from('tbl_registered_user');
        $this->db->where('mobile_number',$mobileNumber);
        // $this->db->where('verified',1);
        return $this->db->get()->row_array();
    }

    function updateOtpData_signup($registered_id, $code_id, $data) {
        $this->db->where('registered_id',$registered_id);
        $this->db->where('code_id', $code_id);
        $this->db->where('status', 1);
        return $this->db->update('tbl_otp_code', $data);
    }

    function insertOtpCode_login($data){
        return $this->db->insert('tbl_otp_login', $data);
    }

    function varifyOtpCodeLogin($id,$code,$code_id_login){
        $this->db->select('*');
        $this->db->from('tbl_otp_login');
        $this->db->join('tbl_registered_user', 'tbl_otp_login.registered_id = tbl_registered_user.registered_id');
        $this->db->where('tbl_otp_login.registered_id',$id);
        $this->db->where('tbl_otp_login.code',$code);
        $this->db->where('tbl_otp_login.code_id_login',$code_id_login);
        $this->db->where('tbl_otp_login.status',1);
        return $this->db->get()->row_array();
    }

    function updateOtpStatusLogin($data) {
        $this->db->where('registered_id', $data['registered_id']);
        return $this->db->update('tbl_otp_login', ['status' => 0]);
    }

    function updateOtpData_login($registered_id, $code_id_login, $data) {
        $this->db->where('registered_id',$registered_id);
        $this->db->where('code_id_login', $code_id_login);
        $this->db->where('status', 1);
        return $this->db->update('tbl_otp_login', $data);
    }

    public function insert_mobile_data($data)
    {
        $this->db->insert('tbl_mobile_rechage', $data);
        return true;
    }

?>

<script>
  function otpCheckSignup() {
  var otp_check_signup = $("#otp_check_signup").val();
  var numpattern = /^[0-9]*$/;
  var a = 0;
  if (otp_check_signup == '') {
    $("#errorotpSignup").text('Enter the OTP Code.');
    $("#errorotpSignup").css("display", "block");
    a = 0
  }

  if (a == 0) {
    $.ajax
    ({
      type: "POST",
      data: {otp_check_signup: otp_check_signup},
      url: "<?php echo base_url('api/otp_check_signup');?>",
      success: function (result) {
        if (result == 2) {
          window.location.href = "<?php echo base_url('paystore'); ?>";
        }
        else if (result == 1) {
          swal({
            title: "",
            text: "Please Enter Valid OTP",
            icon: "warning",
            confirmButtonColor: 'error'
          })
        }
      }
    });
  }
  else {
    return false;
  }
}

  function signup() {
    var mobile_no_signup = $("#mobile_no_signup").val();
    var email_signup = $("#email_signup").val();
    alert(mobile_no_signup);
    alert(email_signup);
    var numpattern = /^[0-9]*$/;
    var a = 0;
    if (mobile_no_signup == '') {
      $("#errorphone_signup").text('Enter the mobile number.');
      $("#errorphone_signup").css("display", "block");
      a = 1
    }
    else {
      if (!mobile_no_signup.match(numpattern)) {
        $("#errorphone_signup").text('Enter Only number.');
        $("#errorphone_signup").css("display", "block");
        a = 1;
      }
      else {
        if (mobile_no_signup.length < 8 || mobile_no_signup.length > 10) {
          $("#errorphone_signup").text('Please enter valid mobile number.');
          $("#errorphone_signup").css("display", "block");
          a = 1;
        }
        else {
          $("#errorphone_signup").text('');
          $("#errorphone_signup").css("display", "none");
          a = 0;
        }
      }
    }
    if (a == 0) {
      /*var datavalues = {
        'oldNumber' : Cookies.get('oldMobile'),
        'mobileNumber' : mobileNumber,
        'password' : password,
        'email' : email
      };*/
      $.ajax
      ({
        type: "POST",
        data: {email_signup: email_signup, mobile_no_signup: mobile_no_signup},
        url: "<?php echo base_url('api/signup');?>",
        success: function (result) {
          if (result == 4) {
            swal({
              title: "",
              text: "Already Register,Please Login",
              icon: "warning",
              confirmButtonColor: 'error'
            })
          }
          else if (result == 1) {
            $(".Verify_otp_signup").show();
            $(".signUpOTP").hide();
                    // $(".loginOTP").hide();
                  }

                }
              });
    }
    else {
      return false;
    }
  }

  function login() {
    var mobile_no = $("#mobile_no").val();
    var numpattern = /^[0-9]*$/;
    var a = 0;
    if (mobile_no == '') {
      $("#errorphone").text('Enter the mobile number.');
      $("#errorphone").css("display", "block");
      a = 1
    }
    else {
      if (!mobile_no.match(numpattern)) {
        $("#errorphone").text('Enter Only number.');
        $("#errorphone").css("display", "block");
        a = 1;
      }
      else {
        if (mobile_no.length < 8 || mobile_no.length > 10) {
          $("#errorphone").text('Please enter valid mobile number.');
          $("#errorphone").css("display", "block");
          a = 1;
        }
        else {
          $("#errorphone").text('');
          $("#errorphone").css("display", "none");
          a = 0;
        }
      }
    }
    if (a == 0) {
      $.ajax
      ({
        type: "POST",
        data: {mobile_no: mobile_no},
        url: "<?php echo base_url('api/login'); ?>",
        success: function (result) {
          if (result == 1) {
            swal({
              title: "",
              text: "Please Register Your Number",
              icon: "warning",
              confirmButtonColor: 'error'
            })
          }
          else if (result == 3) {
            $(".Verify_otp_login").show();
            $(".loginOTP").hide();
                    // $(".signUpOTP").hide();
                  }
                }
              });
    }
    else {
      return false;
    }
  }

  function otp_check_login() {
    var otp = $("#otp").val();
    var numpattern = /^[0-9]*$/;
    var a = 0;
    if (otp == '') {
      $("#errorOtpLogin").text('Enter the OTP Code.');
      $("#errorOtpLogin").css("display", "block");
      a = 0
    }

    if (a == 0) {
      $.ajax
      ({
        type: "POST",
        data: { otp: otp},
        url: "<?php echo base_url('api/otp_check_login');?>",
        success: function (result) {
          if (result == 2) {
            window.location.href = "<?php echo site_url(); ?>";
          }
          else if (result == 1) {
            swal({
              title: "",
              text: "Please Enter Valid OTP",
              icon: "warning",
              confirmButtonColor: 'error'
            })
          }
        }
      });
    }
    else {
      return false;
    }
  }

</script>


// SQL file 

///////////////////////////////////////////////////////////////////////
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `tbl_otp_code` (
  `code_id` int(11) NOT NULL AUTO_INCREMENT,
  `registered_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT '1',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;


INSERT INTO `tbl_otp_code` (`code_id`, `registered_id`, `code`, `status`, `cdate`) VALUES
(1, 8, '3084', '1', '2019-05-28 08:15:37'),
(2, 9, '2509', '0', '2019-05-28 08:21:20');


///////////////////////////////////////////////////////////////////////
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `tbl_otp_login` (
  `code_id_login` int(11) NOT NULL AUTO_INCREMENT,
  `registered_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `status` varchar(2) NOT NULL DEFAULT '1',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`code_id_login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `tbl_otp_login` (`code_id_login`, `registered_id`, `code`, `status`, `cdate`) VALUES
(1, 10, '8072', '0', '2019-05-28 08:45:25'),
(2, 10, '5377', '0', '2019-05-28 08:45:35');

/////////////////////////////////////////////////////////////////////////

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `tbl_registered_user` (
  `registered_id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `birth_date` varchar(50) NOT NULL,
  `gender` varchar(15) NOT NULL,
  `verified` int(2) NOT NULL DEFAULT '0',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `udate` varchar(50) NOT NULL,
  PRIMARY KEY (`registered_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

INSERT INTO `tbl_registered_user` (`registered_id`, `fname`, `lname`, `mobile_number`, `password`, `email`, `birth_date`, `gender`, `verified`, `cdate`, `udate`) VALUES
(1, '', '', '1111111111', '', 'demo@gmail.com', '', '', 0, '2019-04-29 11:58:05', '2019-04-29 11:58:05'),
(6, '', '', '9974725810', '', 'admin@gmail.com', '', '', 0, '2019-05-27 15:10:54', '2019-05-27 15:10:54'),
(8, '', '', '9974777777', '', 'test@gmail.com', '', '', 0, '2019-05-28 08:15:37', '2019-05-28 08:15:37'),
(10, '', '', '9974725555', '', 'test3@gmail.com', '', '', 0, '2019-05-28 08:30:46', '2019-05-28 08:30:46'),
(12, '', '', '9974725888', '', 'test20@gmail.com', '', '', 0, '2019-05-28 09:40:42', '2019-05-28 09:40:42'),
(13, '', '', '9977445566', '', 'testii@gmail.com', '', '', 0, '2019-05-28 10:36:35', '2019-05-28 10:36:35');
