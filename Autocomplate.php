
<div class="col-lg-12">
	<div class="Premiums">
		<div class="Premiums_icon"><i class="fa fa-globe" aria-hidden="true"></i>
		</div>
		<div class="Premiums_icon_country">Country</div>

		<div class="Premiums_input">
			<input name="country" id="country" class="form-control input-number" onkeydown="getCountriesName()" placeholder="Enter Country Name" type="text" value="">
		</div>

	</div>
	<span class="errorBlock" id="errorCountry"></span>
</div>

<script type="text/javascript">
//Get country name on key press
function getCountriesName() {
	$("#country").autocomplete({
		source: function (request, response) {
			$.ajax({
				url: "<?php echo base_url('InsuranceSearch/getCountries'); ?>",
				type: "GET",
				dataType: "json",
				data: { Prefix: request.term },
				success: function (data) {
					response($.map(data, function (item)
					{
						return { label: item.country_name , value: item.country_name };
					}))
				}
			});
		},

		select: function (event, ui) {
			$('#country').attr('value',ui.item.label);
		}
	});
}
</script>


<!-- Controller -->
<?php 
// Get country name on search
    public function getCountries(){
        $prefix = $this->input->get('Prefix');
        $country = $this->Country_model->getCountry($prefix);
        print(json_encode($country, JSON_UNESCAPED_UNICODE));
    }
?>

<!-- Model -->
<?php 
public function getCountry($prefix) {
        $this->db->select('*');
        $this->db->from('country tc');
        $this->db->like('tc.country_name', $prefix, 'after');
        return $query = $this->db->get()->result_array();
    }
?>
