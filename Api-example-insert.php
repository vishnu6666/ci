
<?php 
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
