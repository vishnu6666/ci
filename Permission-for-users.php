
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
//Sql 

CREATE TABLE IF NOT EXISTS `userlevels` (
  `userlevelid` int(11) NOT NULL,
  `userlevelname` varchar(15) NOT NULL,
  `module_permissions` varchar(255) NOT NULL,
  PRIMARY KEY (`userlevelid`),
  KEY `userlevelname` (`userlevelname`),
  KEY `module_permissions` (`module_permissions`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `userlevels` (`userlevelid`, `userlevelname`, `module_permissions`) VALUES
(-1, 'Administrator', '3,20,22,23,24,25,26,28,30,37,38'),
(0, 'Customer', ''),
(1, 'Reseller', '');

CREATE TABLE IF NOT EXISTS `menu_modules` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `menu_label` varchar(25) NOT NULL,
  `module_name` varchar(25) NOT NULL,
  `module_url` varchar(100) NOT NULL,
  `menu_title` varchar(20) NOT NULL,
  `menu_image` varchar(25) NOT NULL,
  `menu_subtitle` varchar(20) NOT NULL DEFAULT '0',
  `priority` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;


INSERT INTO `menu_modules` (`id`, `menu_label`, `module_name`, `module_url`, `menu_title`, `menu_image`, `menu_subtitle`, `priority`) VALUES
(1, 'Customers', 'customers', 'accounts/customers', 'Accounts', 'ti-user', '0', 1),
(2, 'Resellers', 'resellers', 'accounts/resellers', 'Accounts', 'ti-user', '0', 2),
(3, 'Admins', 'admins', 'accounts/admins', 'Accounts', 'ti-user', '0', 3),
(4, 'Numbers List', 'numbers-list', 'numbers/numbers-list', 'Numbers', 'ti-layout-grid3', '0', 21),
(12, 'Companyprofile', 'companyprofile', 'accounts/companyprofile', 'Accounts', 'ti-user', '0', 4),
(16, 'Numbers', 'number-list', 'numbers/number-list', 'Numbers', 'ti-layout-grid3', '0', 5),
(20, 'Category Gujarati', 'category-list', 'category/category-list', 'Category', 'ti-layout-grid3', '0', 22),
(21, 'Sub Category List', 'sub-category-list', 'subcategory/sub-category-list', 'Subcategory', 'ti-layout-grid3', '0', 29),
(22, 'Que bank guj', 'questionbank-list', 'questionbank/questionbank-list', 'Questionbank', 'ti-layout-grid3', '0', 27),
(23, 'Category Hindi', 'category-hindi-list', 'category/category-hindi-list', 'Category', 'ti-layout-grid3', '0', 24),
(24, 'Category Hindi QUE', 'category-hindi-que-list', 'category/category-hindi-que-list', 'Category', 'ti-layout-grid3', '0', 25),
(25, 'Category Guj QUE', 'category-guj-que-list', 'category/category-guj-que-list', 'Category', 'ti-layout-grid3', '0', 23),
(26, 'Que bank hindi', 'questionbank-hindi-list', 'questionbank/questionbank-hindi-list', 'Questionbank', 'ti-layout-grid3', '0', 29),
(28, 'Que bank guj QUE', 'questionbank-guj-que-list', 'questionbank/questionbank-guj-que-list', 'Questionbank', 'ti-layout-grid3', '0', 28),
(30, 'Que bank hindi QUE', 'questionbank-hindi-que-li', 'questionbank/questionbank-hindi-que-list', 'Questionbank', 'ti-layout-grid3', '0', 30),
(31, 'Subject', 'subject-list', 'subject/subject-list', 'Subject', 'ti-layout-grid3', '0', 31),
(32, 'Sub Subject', 'sub-subject-list', 'subject/subsubject-list', 'Subject', 'ti-layout-grid3', '0', 32),
(33, 'Stream', 'stream-list', 'stream/stream-list', 'Stream', 'ti-layout-grid3', '0', 33),
(34, 'Sub Stream', 'substream-list', 'stream/substream-list', 'Stream', 'ti-layout-grid3', '0', 34),
(35, 'State', 'state-list', 'state/state-list', 'State', 'ti-layout-grid3', '0', 35),
(36, 'Sub State', 'substate-list', 'state/substate-list', 'State', 'ti-layout-grid3', '0', 36),
(37, 'Materials Post', 'post-list', 'post/post-list', 'Materials Post', 'ti-layout-grid3', '0', 37),
(38, 'Post Category', 'postcategory-list', 'postcategory/postcategory-list', 'Post Category', 'ti-layout-grid3', '0', 36);
