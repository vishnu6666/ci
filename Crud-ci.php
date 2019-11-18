
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Freeswitch extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('freeswitch_model');
         $this->load->library('permission');
        if ($this->session->userdata('logged_in') != true) {
            redirect('login');
        }
    }

//GET FREESWITCH DATA FOR LISTING
 public function get_freeswitch_data()
    {
        $columns = array(
            0 => 'id',
            1 => 'freeswitch_host',
            2 => 'freeswitch_password',
            3 => 'freeswitch_port',
            4 => 'status',
            5 => 'creation_date',
            6 => 'last_modified_date',
            7 => 'uuid',
        );
        $limit = $this->input->post('length');
        $start = $this->input->post('start');
        $order = $columns[$this->input->post('order')[0]['column']];

        $dir = $this->input->post('order')[0]['dir'];

        $total_freeswitch = $this->freeswitch_model->get_freeswitch_count();

        $total_filter = $total_freeswitch;

        if (empty($this->input->post('search')['value'])) {
            $freeswitch = $this->freeswitch_model->get_freeswitch( $limit, $start, $order, $dir);
        } else {
            $search = $this->input->post('search')['value'];
            $freeswitch = $this->freeswitch_model->freeswitch_search( $limit, $start, $search, $order, $dir);

            $total_filter = $this->freeswitch_model->freeswich_search_count( $search);
        }

        $data = array();

        if (!empty($freeswitch)) {
            foreach ($freeswitch as $freeswitchs) {
                $nestedData['host']     = $freeswitchs->freeswitch_host;
                $nestedData['password'] = $freeswitchs->freeswitch_password;
                $nestedData['port']     = $freeswitchs->freeswitch_port;
                $nestedData['status']       = $freeswitchs->status ? '<div class="text-center table-actions"><a class="table-actions" href="' . base_url() . 'freeswitch/status_check/' . $freeswitchs->uuid . '"><i class="btn-success btn">InActive</i></a></div>':'<div class="text-center table-actions"><a class="table-actions" href="' . base_url() . 'freeswitch/status_check/' . $freeswitchs->uuid . '"><i class="btn-info btn">Active</i></a></div>'; 
                
                $nestedData['creation_date']         = $freeswitchs->creation_date;
                $nestedData['last_modified_date']    = $freeswitchs->last_modified_date;
                
                $nestedData['actions']  = '<div class="text-center table-actions"><a class="table-actions" href="' . base_url() . 'Freeswitch/edit_freeswitch/' . $freeswitchs->uuid . '"><button class="btn btn-primary"><i class="ti-pencil-alt"></i></button></a><button onclick="deletefreeswitch(' . "'" . $freeswitchs->uuid . "'" . ')" class="btn btn-danger"><i class="ti-trash"></i></button></div>';
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"              => intval($this->input->post('draw')),
            "recordsTotal"      => intval($total_freeswitch),
            "recordsFiltered"   => intval($total_filter),
            "data"              => $data,
        );
        echo json_encode($json_data);
    }


// ADD FREESWITCH DATA 

    public function create_freeswitch()
    {
        if (!empty($this->input->post())) {
            $config = array(
                array(
                    'field' => 'host',
                    'label' => 'Host',
                    'rules' => 'required|trim',
                ),
                 array(
                    'field' => 'password',
                    'label' => 'Password',
                    'rules' => 'required|trim|min_length[6]',
                ),
                array(
                    'field' => 'port',
                    'label' => 'Port',
                    'rules' => 'required|trim',
                ),
            );

            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() == false) {
                $data['freeswitch'] = $this->input->post();
            } else {
                $freeswitch_data = array(
                    'uuid'                  => $this->uuid->v4(),
                    'freeswitch_host'       => $this->input->post('host'),
                    'freeswitch_password'   => $this->input->post('password'),
                    'freeswitch_port'       => $this->input->post('port'),
                    'creation_date'         => date('Y-m-d H:i:s'),
                    'status'                => $this->input->post('status'),
                );
                $create = $this->freeswitch_model->create_freeswitch($freeswitch_data);
                if ($create) {
                    $this->session->set_flashdata('success_msg', 'Freeswitch  Added Successfully');
                    redirect('freeswitch/freeswitch_list');
                }
            }
        }
        $this->template->title = 'Create Freeswitch';
        $this->template->content->view('create-freeswitch');
        $this->template->publish();
    }


//LIST FREESWITCH FORM
    public function freeswitch_list()
    {
        $this->template->title = 'Freeswitch List ';
        $this->template->content->view('list-freeswitch');
        $this->template->publish();
    }

//DELETE FREESWITCH DATA
    public function delete_freeswitch($freeswitch_id)
    {
        $this->freeswitch_model->delete_freeswitch($freeswitch_id);
    }

//EDIT FREESWITCH DATA
    public function edit_freeswitch($uuid)
    {
        if (!empty($this->input->post())) {
           $config = array(
                array(
                    'field' => 'host',
                    'label' => 'Host',
                    'rules' => 'required|trim',
                ),
                 array(
                    'field' => 'password',
                    'label' => 'Password',
                    'rules' => 'required|trim|min_length[6]',
                ),
                array(
                    'field' => 'port',
                    'label' => 'Port',
                    'rules' => 'required|trim',
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() == false) {
                $data['freeswitch_data'] = $this->input->post();
            } else {
                $freeswitch_id = $this->input->post('uuid');
                $freeswitch_data = array(
                    'freeswitch_host'       => $this->input->post('host'),
                    'freeswitch_password'   => $this->input->post('password'),
                    'freeswitch_port'       => $this->input->post('port'),
                    'last_modified_date'    => date('Y-m-d H:i:s'),
                    'status'                => $this->input->post('status'),
                );
                $update_freeswitch = $this->freeswitch_model->update_freeswitch($freeswitch_id, $freeswitch_data);
                if ($update_freeswitch) {
                    $this->session->set_flashdata('success_msg', 'Freeswitch Updated Successfully');
                    redirect('Freeswitch/freeswitch_list');
                } else {
                    $this->session->set_flashdata('error_msg', 'Error Occured Try Again!');
                    redirect('freeswitch/edit_freeswitch/' . $freeswitch_id);
                }
            }
        }

        $data['freeswitch_data'] = $this->freeswitch_model->get_freeswitch_data($uuid);
        $this->template->title = 'Edit Freeswitch ';
        $this->template->content->view('edit-freeswitch', $data);
        $this->template->publish();
    }

    public function status_check($uuid)
    {
        $status = $this->freeswitch_model->gate_status($uuid);
        if($status->status == '0')
        {
           $this->freeswitch_model->update_status($uuid ,$status='1');
           redirect('Freeswitch/freeswitch_list');
        }elseif($status->status == '1')
        {
          $this->freeswitch_model->update_status($uuid , $status='0');
          redirect('Freeswitch/freeswitch_list');
        }
        
    }   

}


// In Model 
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Freeswitch_model extends CI_Model
{
    public function create_freeswitch($freeswitch_data)
    {
        $this->db->insert('freeswich_servers', $freeswitch_data);
        return true;
    }


   public function get_freeswitch_count()
    {
        $this->db->select('*');
        $query = $this->db->get('freeswich_servers');
        return $query->num_rows();
    }

    public function get_freeswitch($limit, $start, $order, $dir)
    {
        $this->db->select('uuid,freeswitch_host,freeswitch_password,freeswitch_port,status,creation_date,last_modified_date')
            ->from('freeswich_servers')
            ->limit($limit, $start)
            ->order_by($order,'DESC', $dir);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function freeswitch_search($limit, $start, $search, $order, $dir)
    {
        $this->db->select('uuid,freeswitch_host,freeswitch_password,freeswitch_port,status,creation_date,last_modified_date')
                 ->like('id', $search)
                 ->or_like('freeswitch_host', $search)
                 ->or_like('freeswitch_password',$search)
                 ->or_like('freeswitch_port',$search)
                 ->or_like('creation_date',$search)
                 ->or_like('last_modified_date',$search)
                 ->limit($limit, $start)
                 ->order_by($order, $dir);
        $query = $this->db->get('freeswich_servers');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }

    public function freeswich_search_count($search)
    {
        $this->db->like('id', $search)
                 ->or_like('freeswitch_host', $search)
                 ->or_like('freeswitch_password',$search)
                 ->or_like('freeswitch_port',$search)
                 ->or_like('creation_date',$search)
                 ->or_like('last_modified_date',$search);
        $query = $this->db->get('freeswich_servers');

        return $query->num_rows();
    }

    public function delete_freeswitch($freeswitch_id)
    {

        $this->db->where('uuid', $freeswitch_id)
                 ->delete('freeswich_servers');
               //->update('freeswich_servers', ['deleted' => 1]);
        return true;
    }


    public function update_freeswitch($freeswitch_id, $freeswitch_data)
    {

        $this->db->where('uuid', $freeswitch_id)
                 ->update('freeswich_servers', $freeswitch_data);
        return true;
    }

    public function get_freeswitch_data($uuid)
    {
        $this->db->select('*')
                 ->where('uuid', $uuid);
               //->where('deleted!=', 1);
        $query = $this->db->get('freeswich_servers');
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

      public function gate_status($uuid)
        {
            $this->db->select('uuid,status')
                     ->where('uuid', $uuid);
            $query = $this->db->get('freeswich_servers');
            if ($query->num_rows() > 0) {
                return $query->row();
            } else {
                return null;
            }
        }

     public function update_status($uuid,$status)
     {
         $this->db->where('uuid', $uuid)
              ->update('freeswich_servers', ['status' => $status]);
        return true;
     }

}

// Create Page 

<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-block">
                                    <h5 class="m-b-10">Create Freeswitch </h5>
                                    <br>
                                     <form method="post" action="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="host" class="block">Host *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input id="host" name="host" type="text" class="form-control" value="<?php if(isset($freeswitch_data['host'])){ echo $freeswitch_data['host']; }?>">
                                                            <?php echo form_error('host','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="password" class="block">Password *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input id="password" name="password" type="text" class="form-control" value="<?php if(isset($freeswitch_data['password'])){ echo $freeswitch_data['password']; }?>">
                                                            <?php echo form_error('password','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="port" class="block">Port *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input id="port" name="port" type="port" class="form-control" value="<?php if(isset($freeswitch_data['port'])){ echo $freeswitch_data['port']; }?>">
                                                            <?php echo form_error('port','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="status" class="block">Status *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <select id="status" name="status" class="form-control">
                                                                <option value="0">Active</option>
                                                                <option value="1">In Active</option>
                                                            </select>
                                                            <?php echo form_error('status','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                        
                                              <input type="button" class="btn btn-info" value="Close" onClick="document.location.href='<?php echo base_url('freeswitch/freeswitch_list') ?>';" />&nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="submit" class="btn btn-primary"><i class="ti-save"></i> Create Freeswitch</button>
                                        </form>                     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

//Edit Page 


<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-block">
                                    <h5 class="m-b-10">Create Freeswitch </h5>
                                    <br>
                                     <form method="post" action="">
                                         <input type="text" name="uuid" value="<?php if(isset($freeswitch_data->uuid)){ echo $freeswitch_data->uuid; }?>" hidden>
                                           
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="host" class="block">Host *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input id="host" name="host" type="text" class="form-control" value="<?php if(isset($freeswitch_data->freeswitch_host)){ echo $freeswitch_data->freeswitch_host; }?>">
                                                            <?php echo form_error('host','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="password" class="block">Password *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input id="password" name="password" type="text" class="form-control" value="<?php if(isset($freeswitch_data->freeswitch_password)){ echo $freeswitch_data->freeswitch_password; }?>">
                                                            <?php echo form_error('password','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="port" class="block">Port *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <input id="port" name="port" type="port" class="form-control" value="<?php if(isset($freeswitch_data->freeswitch_port)){ echo $freeswitch_data->freeswitch_port; }?>">
                                                            <?php echo form_error('port','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <label for="status" class="block">Status *</label>
                                                        </div>
                                                        <div class="col-sm-12">
                                                            <select id="status" name="status" class="form-control">
                                                                <option value="0" <?php if($freeswitch_data->status == "0"){ echo "selected";} ?>>Active</option>
                                                                <option value="1" <?php if($freeswitch_data->status == "1"){ echo "selected";} ?>>In Active</option>
                                                            </select>
                                                            <?php echo form_error('status','<p class="text-danger error">','</p>'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                             <input type="button" class="btn btn-info" value="Close" onClick="document.location.href='<?php echo base_url('freeswitch/freeswitch_list') ?>';" />&nbsp;&nbsp;&nbsp;&nbsp;
                                            <button type="submit" class="btn btn-primary"><i class="ti-save"></i> Update Freeswitch</button>
                                        </form>                     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

//List apage

<div class="pcoded-content">
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-block">
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <h5 class="m-b-10">Freeswitch </h5>
                                        </div>
                                         <div class="float-right">
                                            <?php if($this->session->userdata('type')=='-1'){?>
                                            <a href="<?php echo base_url('Freeswitch/create_freeswitch')?>"><button class="btn btn-primary"><i class="ti-user"></i> Add Freeswitch</button></a>
                                            <?php } ?>
                                        </div>  
                                    </div>                                    
                                    <br>
                                    <?php
                                        $success_msg=$this->session->flashdata('success_msg');
                                        $error_msg=$this->session->flashdata('error_msg');
                                        if($error_msg){
                                    ?>
                                        <div class="alert alert-danger background-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="ti-close" style="color:#fff"></i></button>
                                            <small><?php echo $error_msg; ?></small>
                                        </div>
                                        <?php }elseif($success_msg){?>
                                        <div class="alert alert-success background-success" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><i class="ti-close" style="color:#fff"></i></button>
                                            <small><?php echo $success_msg; ?></small>
                                        </div>
                                        <?php } ?>
                                    <table id="freeswitch" class="table table-striped table-bordered nowrap dataTable dt-responsive" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Host</th>
                                                <th>Password</th>
                                                <th>Port</th>
                                                <th>Status</th>
                                                <th>Created Date</th>
                                                <th>Updated Date</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#freeswitch').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":{
                "url": "<?php echo base_url('freeswitch/get_freeswitch_data') ?>",
                "dataType": "json",
                "type": "POST",
                "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
            },
            "columns": [
                
                { "data": "host" },
                { "data": "password" },
                { "data": "port" },
                { "data": "status" },
                { "data": "creation_date" },
                { "data": "last_modified_date" },
                { "data": "actions" },
            ]
        });    
    });

//  Delete freeswitch 

function deletefreeswitch(freeswitch_id){
    swal({
        title: "Are you sure?",
        text: "You will not be able to recover this Freeswitch.",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes, delete!",
        cancelButtonText: "No",
        closeOnConfirm: false,
        closeOnCancel: false
    }, function (isConfirm) {
        if (isConfirm) {
           // alert(freeswitch_id);
            $.ajax({
                url: "<?php echo base_url('freeswitch/delete_freeswitch/')?>" + freeswitch_id,
                type: "POST",
                success: function (data) {
                    swal({
                        title: 'Success!',
                        type: 'success',
                        focusConfirm: false,
                        timer: 2000,
                    });
                    window.setTimeout(function () {
                        location.reload();
                    }, 2000);
                },
                error: function () {
                    alert('Not Closed');
                }
            });
        } else {
            swal("Cancelled", "Your Freeswitch is safe :)", "error");
        }
    });
}


window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 3000);

</script>



