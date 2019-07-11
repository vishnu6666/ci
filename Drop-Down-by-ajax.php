<!-- View Section -->
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
</head>

    <div class="col-lg-12">
        <div class="Premiums">
            <div class="Premiums_icon"><i class="fa fa-file-text" aria-hidden="true"></i>
            </div>
            <div class="Premiums_icon_country">Data</div>
            <div class="Premiums_select_one1">
                <select id="categoryList">
                    <option>Select One</option>
                </select>
            </div>
        </div>
    </div>  
    <script type="text/javascript">

        $(document).ready(function(){
            getPolicyCategory();
        });

        function getPolicyCategory(){
            $.ajax({
                type: "POST",
                contentType: "application/json",
                dataType: "json",
                url: "<?php echo base_url('/Ajax/getData');?>",
                success: function (result) {
                    var objToString = JSON.stringify(result);
                    var stringToArray = [];
                    stringToArray.push(objToString);
                    var jsonObj = $.parseJSON(stringToArray);
                    var message = jsonObj.Common.Message;

                    if (message == "Data Found.") {
                        $('#categoryList').empty();
                        for(var i=0; i < jsonObj.Response.CatData.length; i++){
                            var info = '';
                            info += '<option value="'+jsonObj.Response.CatData[i].pc_id+","+jsonObj.Response.CatData[i].category_name+'">'+jsonObj.Response.CatData[i].category_name+'</option>';

                            $('#categoryList').append(info);
                        }
                    }
                }
            });
        }
    </script>     


<!-- Controller Section--> 
<?php 

    public function index()
    {
        $this->load->view('ajax/ajaxfeatch');
    }

    public function getData()
    {
        if ($_SERVER['CONTENT_TYPE'] == 'application/json' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $inputJSON = file_get_contents("php://input");
            $input = json_decode($inputJSON, TRUE);
            $this->load->model('Modeldata');
            $getPolicyCategory = $this->Modeldata->loadData();

            if(count($getPolicyCategory)>0){
                $status = "Success";
                $message = "Data Found.";
                $data = array("Common" => array("Title" => "Get Data", 'version' => '1.0', 'Description' => 'Get Data', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("CatData" => $getPolicyCategory));
                print(json_encode($data, JSON_UNESCAPED_UNICODE));

            }else{
                $status = "Fail";
                $message = "Data Not Found.";
                $data = array("Common" => array("Title" => "Get Data", 'version' => '1.0', 'Description' => 'Get Data', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Phone code not found.'));
                print(json_encode($data, JSON_UNESCAPED_UNICODE));
            }
        } else {
            $status = "Fail";
            $message = "Invalid request.";
            $data = array("Common" => array("Title" => "Get Data", 'version' => '1.0', 'Description' => 'Get Data', 'Method' => 'POST', 'Status' => $status, 'Message' => $message), "Response" => array("Value" => 'Invalid request'));
            print(json_encode($data, JSON_UNESCAPED_UNICODE));
        }
    }

/* Model Section */  
    public function loadData()
    {
        $this->db->select('*');
        $this->db->from('tbl_policy_category');
        $this->db->order_by('cdate');
        return $query = $this->db->get()->result_array();
    }

?>

