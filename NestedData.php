<?php 

    $broker_users_data=$this->BrokersClaims_model->loadbrokerClaimsData();
        $key=0;
        if (!empty($broker_users_data)) {
            foreach ($broker_users_data as $broker_user) {
                $inqeurydata = $this->BrokersClaims_model->get_inquery_by_plan_purchase_id($broker_user['plan_purchase_id']);
                foreach ($inqeurydata as $key1 => $value) {
                   $nestedData[$key][$key1]=$value;
                }
                $nestedData[$key]['claim_id']         = $broker_user['claim_id'];
                $nestedData[$key]['incident_type']    = $broker_user['incident_type'];
                $nestedData[$key]['assigned_to']      = $broker_user['assigned_to'];
                $nestedData[$key]['client']           = $broker_user['client'];
                $nestedData[$key]['incident_type']    = $broker_user['incident_type'];
                $key++;
            }
        }
        $data['broker_users'] = @$nestedData;

    public function loadbrokerClaimsData()
    {
        $this->db->select('*');
        $this->db->from('tbl_user_claim');
        $this->db->join('tbl_plan_purchase', 'tbl_user_claim.plan_purchase_id = tbl_plan_purchase.plan_purchase_id');
        $this->db->where('tbl_plan_purchase.broker_id',$this->session->userdata('id'));
        $this->db->order_by('tbl_user_claim.claim_id','DESC');
       return $query = $this->db->get()->result_array();
    }

    function get_inquery_by_plan_purchase_id($plan_purchase_id)
    {
        $this->db->select('inquiry_id');
        $this->db->from('tbl_plan_purchase');
        $this->db->where('plan_purchase_id',$plan_purchase_id);
        $this->db->where('broker_id',$this->session->userdata('id'));
        $query = $this->db->get()->result_array();
        $inquiry_id = $query[0]['inquiry_id'];

        $this->db->select('*');
        $this->db->from('tbl_two_wheeler_plan_inquiries');
        $this->db->where('two_wheeler_inquiry_id',$inquiry_id);
       $query_inquery = $this->db->get()->result_array();
       return $query_inquery[0];
    }

?>

