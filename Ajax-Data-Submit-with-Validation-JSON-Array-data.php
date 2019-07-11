<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<style type="text/css">
    .errorBlock {
        padding-top: 10px;
        color: #FF0000;
        font-weight: normal;
        display: none;
        text-align: left;
        font-size: 12px;
        bottom: 0px;
        float: left;
        margin-top: -7px;
    }
</style>

<div class="col-lg-12" id="assistanceDiv">
    <div class="col-lg-12">
        <div class="Premiums">
            <div class="Premiums_icon"><i class="fa fa-user" aria-hidden="true"></i>
            </div>
            <div class="Premiums_icon_country">Name</div>
            <div class="Premiums_select_one13">
            </div>
            <div class="Premiums_input">
                <input name="name1" id="name1" class="form-control input-number" min="1"
                max="10" placeholder="Name" type="text"
                onkeypress='return ((event.charCode === 8) || (event.charCode === 9) || (event.charCode === 16) || (event.charCode === 13) || (event.charCode === 20) || (event.charCode === 37) || (event.charCode === 39) || (event.charCode === 46) || (event.charCode === 32) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode >= 65 && event.charCode <= 90))'
                value="">
            </div>
        </div>
        <span class="errorBlock" id="errorName1"></span><br>
    </div>

    <div class="col-lg-12">
        <div class="Premiums">
            <div class="Premiums_icon"><i class="fa fa-envelope" aria-hidden="true"></i>
            </div>
            <div class="Premiums_icon_country">Email</div>
            <div class="Premiums_input">
                <input name="email1" id="email1" class="form-control input-number" min="1"
                max="10" placeholder="Email Id" type="text"
                value="">
            </div>
        </div>
        <span class="errorBlock" id="errorEmail1"></span><br>
    </div>

    <div class="col-lg-12">
        <div class="Premiums">
            <div class="Premiums_icon"><i class="fa fa-phone" aria-hidden="true"></i>
            </div>
            <div class="Premiums_icon_country">Mobile</div>

            <div class="Premiums_input">
                <input name="mobile1" id="mobile1" class="form-control input-number"
                min="1" max="10" placeholder="Enter Mobile Number" type="text"
                onkeypress='return ((event.charCode >= 48 && event.charCode <= 57) || (event.charCode === 0))'
                value="">
            </div>
        </div>
        <span class="errorBlock" id="errorMobile1"></span><br>
    </div>


    <div class="col-md-12" id="SlidediDiv1">
        <button class="btn btn-primary" id="btnleft" onclick="submitData()" >
            Request Add Data
        </button>
    </div>
</div>

<script type="text/javascript">
    function divOneValidation() {

        if ($('#name1').val().length > 0) {
            var nameRegex = /^[a-zA-Z ]+$/.test($('#name1').val());
            if (nameRegex === true) {
                $("#errorName1").css("display", "none");
            } else {
                $("#errorName1").css("display", "block");
                $('#errorName1').html('Only characters are allowed.');
            }

        } else {
            $("#errorName1").css("display", "block");
            $('#errorName1').html('Name is required.');
        }

        if ($('#email1').val().length > 0) {
            var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($('#email1').val());
            if (emailRegex === true) {
                $("#errorEmail1").css("display", "none");
            } else {
                $("#errorEmail1").css("display", "block");
                $('#errorEmail1').html('Please enter valid email address.');
            }
        } else {
            $("#errorEmail1").css("display", "block");
            $('#errorEmail1').html('Email address is required.');
        }

        if ($('#mobile1').val().length > 0) {
            var mobileRegex = /^[0-9\-().\s]{8,10}$/.test($('#mobile1').val());
            if (mobileRegex === true) {
                $("#errorMobile1").css("display", "none");
            } else {
                $("#errorMobile1").css("display", "block");
                $('#errorMobile1').html('Please enter valid mobile number.');
            }
        } else {
            $("#errorMobile1").css("display", "block");
            $('#errorMobile1').html('Mobile number is required.');
        }

        var nullCounter = $(".errorBlock:visible").length;
        if(nullCounter === 0){
            return('True');
        }else{
            return('False');
        }
    }
</script>

<script type="text/javascript">

    function submitData() {

        var isDivOneValidate = divOneValidation();
        if (isDivOneValidate === 'True') {

            var name = $('#name1').val();
            var email = $('#email1').val();
            var mobile = $('#mobile1').val();

            var dataValues = {
                'name': name,
                'email': email,
                'mobile': mobile
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
    }
</script> 

    <!-- Controller Section -->   
    <?php
    public function index()
    {
        $this->load->view('ajax/ajaxfeatch');
    }

    public function submitdata(){

        if ($_SERVER['CONTENT_TYPE'] == 'application/json' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents("php://input");
            $input = json_decode($inputJSON, TRUE);

            $inputData = array(
                'name'              =>$input['name'],
                'email'             =>$input['email'],
                'mobile'            =>$input['mobile'],
                'status'            => 1,
                'cdate'             => date('Y-m-d H:i:s')
            );

            $datarequest = $this->Modeldata->submit_data($inputData);

            if(count($datarequest)>0){
                $status = "Success";
                $message = "Record inserted.";
                $data = array("Common" => array("Title" => "Insert data Request API", 'version' => '1.0', 'Description' => 'Insert data Request API', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("RequestData" => $datarequest));
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

    public function submit_data($inputData)
    {
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