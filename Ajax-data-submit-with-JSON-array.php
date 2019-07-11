<!-- View Section -->
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<div class="col-md-12" id="SlidediDiv1">
    <button class="btn btn-primary"  onclick="submitData()" >
        Submit Data
    </button>
</div>

<script type="text/javascript">
	function submitData() {
        var policyCategoryName = 'test1'; // $('#policyCategoryName').val();
        var registeredUserId = 'test2';
        var policyCategoryId = 'test3';
        var namePrefix = 'test4';
        var name = 'test5';
        var email = 'test6';
        var mobileCountryCode = 'test7';
        var mobile = 'test8';
        var description = 'test9';

        var dataValues = {
            'policyCategoryName': policyCategoryName,
            'registeredUserId': registeredUserId,
            'policyCategoryId':policyCategoryId,
            'namePrefix': namePrefix,
            'name': name,
            'email': email,
            'mobileCountryCode': mobileCountryCode,
            'mobile': mobile,
            'description': description
        };

        $.ajax({
            type: "POST",
            data: JSON.stringify(dataValues),
            contentType: "application/json",
            dataType: "json",
            url: "<?php echo base_url('/Ajax/submitdata');?>",
            success: function (result) {
                var objToString = JSON.stringify(result);
                var stringToArray = [];
                stringToArray.push(objToString);
                var jsonObj = $.parseJSON(stringToArray);
                var message = jsonObj.Common.Message;

                if (message == "Record inserted.") {

                    swal({
                        text: 'Your request is submitted. Thank you.',
                        icon: 'success'
                    }).then(function () {
                        window.location.reload();
                    });

                } else {
                    swal({
                        text: 'Your request is not submitted. Please try again.',
                        icon: 'error'
                    }).then(function () {
                    });
                }
            }
        });
    }

	/* Controller Section */

	<?php
	public function submitdata(){

        if ($_SERVER['CONTENT_TYPE'] == 'application/json' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents("php://input");
            $input = json_decode($inputJSON, TRUE);

            $inputData = array(
                'policy_category_name'=>$input['policyCategoryName'],
                'user_id'           => $input['registeredUserId'],
                'pc_id'             =>$input['policyCategoryId'],
                'name_prefix'       =>$input['namePrefix'],
                'name'              =>$input['name'],
                'email'             =>$input['email'],
                'country_code'      => $input['mobileCountryCode'],
                'mobile'            =>$input['mobile'],
                'description'       =>$input['description'],
                'status'            => 1,
                'cdate'             => date('Y-m-d H:i:s')
            );

            $datarequest = $this->Modeldata->submit_data($inputData);

            if(count($datarequest)>0){
                $status = "Success";
                $message = "Record inserted.";
                $data = array("Common" => array("Title" => "Insert data Request API", 'version' => '1.0', 'Description' => 'Insert data Request API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Requestdata" => $datarequest));
                print(json_encode($data, JSON_UNESCAPED_UNICODE));
            }else{
                $status = "Fail";
                $message = "Record not inserted.";
                $data = array("Common" => array("Title" => "Insert data Request API", 'version' => '1.0', 'Description' => 'Insert data Request API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Record not inserted'));
                print(json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $status = "Fail";
            $message = "Invalid request.";
            $data = array("Common" => array("Title" => "Insert data Request API", 'version' => '1.0', 'Description' => 'Insert Claim data API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Invalid request'));
            print(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

	/* Model Section */

    public function submit_data($inputData){
        $query = $this->db->insert('tbl_request', $inputData);
        $lastRecordedId = $this->db->insert_id();

        if(($query == TRUE) && ($lastRecordedId != '')){
            $this->db->select('*');
            $this->db->from('tbl_request thar');
            $this->db->where('thar.request_id', $lastRecordedId);
            return $result = $this->db->get()->row_array();
        }else{
            return FALSE;
        }
    } 

?>


