<?php

class GVL
{
    public $path_to_json;

    private $v1 = "https://vendorlist.consensu.org/vendorlist.json";
    private $v2 = "https://vendorlist.consensu.org/v2/vendor-list.json";
	private $gvl;
	private $gvl_data;
	private $purpose_list;

	function __construct($version) {
	    if ($version == "v1") {
            $this->path_to_json = $this->v1;
        } elseif ($version == "v2") {
	        $this->path_to_json = $this->v2;
        } else {
            $this->path_to_json = $this->v2;
        }

		$this->gvl = @file_get_contents($this->path_to_json);
        if ($this->gvl === FALSE) {
            echo 'There was a problem loading the JSON file: ', "\n";
            die;
        }
		$this->purpose_list = array("purposes"=>"purposes",
			                        "legIntPurposes"=>"purposes",
			                        "flexiblePurposes"=>"purposes",
			                        "specialPurposes"=>"specialPurposes",
                                    "features"=>"features",
                                    "specialFeatures"=>"specialFeatures");
		$this->gvl_data = json_decode($this->gvl);
	}

	public function set_gvl_version($version) {
	    $this->path_to_json = $version == "v1" ? $this->v1 : $this->v2;
    }

	public function get_gvlSpecificVersion() {
		return $this->gvl_data->gvlSpecificationVersion;
	}

	public function get_vendorListVersion() {
		return $this->gvl_data->vendorListVersion;
	}


	public function get_tcfPolicyVersion() {
		return $this->gvl_data->tcfPolicyVersion;
	}

	function get_lastUpdated() {
		return $this->gvl_data->lastUpdated;
	}

	public function get_record($collection, $id) {
	// takes a string of the name of the collection and returns a StdObj
	// of the associated record. If nothing is found, returns NULL
		$dataset = $this->gvl_data->$collection;
		foreach ($dataset as $record) {
			if ($record->id == $id) {
				return $record;
			}
		}
		return NULL;
	}

	public function get_options($collection) {
        $dataset = $this->gvl_data->$collection;
	    $option_string = "";
        $values = array();

        foreach ($dataset as $record) {
            $values[$record->id] = $record->name;
        }

        asort($values, SORT_NATURAL | SORT_FLAG_CASE);

		foreach ($values as $id=>$name) {
			$option_string .= "<option value=\"" . $id . "\">" . $name . "</option>\n";
		}

		return $option_string;
	}

	function find_vendors($search_field, $search_array) {
		$vendors = $this->gvl_data->vendors;
		$results_array = array();

		foreach ($vendors as $vendor) {
			foreach ($search_array as $search_value) {
				if (array_search($search_value, $vendor->$search_field)) {
					array_push($results_array, $vendor->id);
				}
			}		
		}
		return $results_array;
	}

	function get_all_vendors() {
	    $vendor_array = array();
        $cnt = 1;

	    foreach ($this->gvl_data->vendors as $vendor) {
	        array_push($vendor_array, ["idx" => $cnt, "id"=>$vendor->id, "name"=>$vendor->name]);
	        $cnt++;
        }
        return json_encode($vendor_array);
    }

}
