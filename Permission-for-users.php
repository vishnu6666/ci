
<?php 

// Controller
	$menu_list = $this->permission->get_module_access($user_data['type']);
	
	$this->session->set_userdata('mode_cur', 'user');


// Library
public function get_module_access($user_type)
    {
        $where = array("userlevelid" => $user_type);
        $modules_arr = $this->CI->DB_model->getSelect("module_permissions", "userlevels", $where);
        //echo"<pre>";print_r($modules_arr);
        if ($modules_arr) {
            //echo "Hi";exit;
            $modules_arr = $modules_arr->result_array();
            $modules_arr = $modules_arr[0]['module_permissions'];
            $menu_arr = $this->CI->DB_model->select("*", "menu_modules", "id IN ($modules_arr)", "priority", "asc", "", "", "");
            //echo"<pre> Hi";print_r($menu_arr->result_array());exit;
            $menu_list = array();
            $permited_modules = array();
            $modules_seq_arr = array();
            $modules_seq_arr = explode(",", $modules_arr);
            $label_arr = array();
            //echo "<pre>";print_r($label_arr);exit;
            foreach ($menu_arr->result_array() as $menu_key => $menu_value) {
                //echo "<pre> Hi";print_r($menu_value);
                if (!isset($label_arr[$menu_value['menu_label']])) {
                    $label_arr[$menu_value['menu_label']] = $menu_value['menu_label'];
                    $menu_value["menu_image"] = ($menu_value["menu_image"] == "") ? "Home.png" : $menu_value["menu_image"];
                    $menu_list[$menu_value["menu_title"]][] = array(
                        "menu_label" => trim($menu_value["menu_label"]),
                        "module_url" => trim($menu_value["module_url"]), 
                        "module" => trim($menu_value["module_name"]),
                        "menu_image" => trim($menu_value["menu_image"]));
                }
                $permited_modules[] = trim($menu_value["module_name"]);
            }
            //echo "<pre> Hi";print_r($menu_list);exit;
            $this->CI->session->set_userdata('permited_modules', serialize($permited_modules));
            $this->CI->session->set_userdata('menuinfo', serialize($menu_list));
            return true;
        }
        // else{
        //     echo "No";exit;
        // }
    }


//Model
   public function getSelect($select, $tableName, $where)
    {
        $this->db->select($select, false);
        $this->db->from($tableName);
        if ($where != '') {
            $this->db->where($where);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query;
    }


    public function select($select, $tableName, $where, $order_by, $order_type, $paging_limit = '', $start_limit = '', $groupby = '')
    {
        $this->db->select($select);
        $this->db->from($tableName);
        if ($where != "") {
            $this->db->where($where);
        }

        if ($paging_limit) {
            $this->db->limit($paging_limit, $start_limit);
        }

        if (!empty($groupby)) {
            $this->db->group_by($groupby);
        }

        if (isset($_GET['sortname']) && $_GET['sortname'] != 'undefined') {
            $this->db->order_by($_GET['sortname'], ($_GET['sortorder'] == 'undefined') ? 'desc' : $_GET['sortorder']);
        } else {
            if ($order_by) {
                $this->db->order_by($order_by, $order_type);
            }

        }
        $query = $this->db->get();
        return $query;
    }
