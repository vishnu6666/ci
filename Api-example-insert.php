<?php 
//////////////////////////////////////////////  EXAMPLE WITHOUT Authantication   ////////////////////////////////////////////////
//Controller

public function submit_mobile_recharge()
    {
        if ($_SERVER['CONTENT_TYPE'] == 'application/json' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents("php://input");
            $input = json_decode($inputJSON, TRUE);

            $data = array(
               'sim_type' => $input['sim_type'],
               'mobile_number' => $input['mobile_number'],
               'mobile_operator' => $input['mobile_operator'],
               'mobile_circle' => $input['mobile_circle'],
               'mobilerecharge_amount' => $input['mobilerecharge_amount'],
               'cdate' => date('Y-m-d H:i:s'),
            );
            $insertResult = $this->api_model->insert_mobile_data($data);
            $getUserData = $this->api_model->get_mobile_data();
             if($insertResult != FALSE){
                    $status = "Success";
                    $message = "Sucessfully Mobile Recharge.";
                    $data = array("Common" => array("Title" => "Online Mobile Recharge", 'version' => '1.0', 'Description' => 'Online Mobile Recharge API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Success', "Data" => $getUserData));
                    print(json_encode($data, JSON_UNESCAPED_UNICODE));
                } else {
                    $status = "Fail";
                    $message = "No Record Found.";
                    $data = array("Common" => array("Title" => "Online Mobile Recharge", 'version' => '1.0', 'Description' => 'Online Mobile Recharge API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Fail'));
                    print(json_encode($data, JSON_UNESCAPED_UNICODE));
                }
        } else {
            $status = "Fail";
            $message = "Invalid Request.";

            $data = array("Common" => array("Title" => "Online Mobile Recharge", 'version' => '1.0', 'Description' => 'Online Mobile Recharge API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Fail'));
            print(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

// Model

public function insert_mobile_data($data)
    {
        $this->db->insert('tbl_mobile_rechage', $data);
        return true;
    }

public function get_mobile_data()
    {
       $this->db->select('*')
            // ->where('category_id',12)
             ->from('tbl_mobile_rechage');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        }else{
            return false;
        } 
    }

//////////////////////////////////////////////  EXAMPLE WITH Basic Authantication   ////////////////////////////////////////////////
//Controller


public function signup()
    {
        if ((($_SERVER['CONTENT_TYPE'] == 'application/json; charset=utf-8') || ($_SERVER['CONTENT_TYPE'] == 'application/json')) && ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SERVER['PHP_AUTH_USER'] == authUserName && $_SERVER['PHP_AUTH_PW'] == authPassword )) {
            $inputJSON = file_get_contents("php://input");
            $input = json_decode($inputJSON, TRUE);

            if((isset($input['prefixName'])) && (isset($input['name'])) && (!empty($input['country'])) && (!empty($input['martialStatus'])) && (!empty($input['countryCode'])) && (!empty($input['email'])) && (!empty($input['dateOfBirth'])) && (!empty($input['mobileNumber'])) && (!empty($input['annualIncome']))&& (!empty($input['city']))&& (!empty($input['password'])) ){

                $UserData = array(
                                    'user_title'=>$input['prefixName'],
                                    'user_name'=>$input['name'],
                                    'country_id'=>$input['country'],
                                    'marital_status'=>$input['martialStatus'],
                                    'phone_code'=>$input['countryCode'],
                                    'user_email'=>$input['email'],
                                    'user_dob'=>$input['dateOfBirth'],
                                    'user_mobile'=>$input['mobileNumber'],
                                    'annual_income'=>$input['annualIncome'],
                                    'city_id'=>$input['city'],
                                    'user_password'=>$input['password'],
                                    'user_status' => 1
                                   // 'active_status' => 0
                            );

                $userDetails = $this->Apis_model->signupData($UserData);

                if(!empty($userDetails)){
                    $status = "Success";
                    $message = "signup successful.";
                    $data = array("Common" => array("Title" => "User signup API", 'version' => '1.0', 'Description' => 'User signup API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("UserDetails" => $userDetails));
                    print(json_encode($data, JSON_UNESCAPED_UNICODE));
                }else{
                    $status = "Fail";
                    $message = "signup Fail .";
                    $data = array("Common" => array("Title" => "User signup API", 'version' => '1.0', 'Description' => 'User signup API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'signup fail.'));
                    print(json_encode($data, JSON_UNESCAPED_UNICODE));
                }

            }else{
                $status = "Fail";
                $message = "Empty parameters.";
                $data = array("Common" => array("Title" => "User signup API", 'version' => '1.0', 'Description' => 'User signup API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Empty parameters.'));
                print(json_encode($data, JSON_UNESCAPED_UNICODE));
            }

        } else {
            $status = "Fail";
            $message = "Invalid request.";
            $data = array("Common" => array("Title" => "User Singup API", 'version' => '1.0', 'Description' => 'User Singup API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Invalid request.'));
            print(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

// Model 

    public function signupData($UserData)
    {
        $this->db->insert('tbl_reg_user',$UserData);
        $lastid = $this->db->insert_id();

        if(($lastid != '')){
            $this->db->select('*');
            $this->db->from('tbl_reg_user');
            $this->db->where('user_status', 1);
            $this->db->where('user_id', $lastid);
            return $result = $this->db->get()->row_array();
        }else{
            return FALSE;
        }
    }


