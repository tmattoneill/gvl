<?php

class GVL
{
    /* {
            gvlSpecificationVersion: 2,
            vendorListVersion: 59,
            tcfPolicyVersion: 2,
            lastUpdated: "2020-10-08T16:05:25Z",
            purposes: {},
            specialPurposes: {},
            features: {},
            specialFeatures: {},
            stacks: {},
            vendors: {}
        }
     */

    private $gvl_json = "https://vendorlist.consensu.org/v2/vendor-list.json";
	private $gvl;
	private $gvl_data;
	private $vendors, $purposes;

	function __construct() {

		$this->gvl = @file_get_contents($this->gvl_json);

        if ($this->gvl === FALSE) {
            echo 'There was a problem loading the JSON file: ', $this->gvl_json, "\n";
            die;
        } else {
            $this->gvl_data = json_decode($this->gvl, true);
        }
	}

	public function get_gvl_json() {
	    return $this->gvl_json;
    }
	public function get_gvlSpecificVersion() {
		return $this->gvl_data['gvlSpecificationVersion'];
	}

	public function get_vendorListVersion() {
		return $this->gvl_data['vendorListVersion'];
	}

	public function get_tcfPolicyVersion() {
		return $this->gvl_data['tcfPolicyVersion'];
	}

	function get_lastUpdated() {
		return $this->gvl_data['lastUpdated'];
	}

	public function get_record($collection, $id) {
	// takes a string of the name of the collection and returns a StdObj
	// of the associated record. If nothing is found, returns NULL
		$dataset = $this->gvl_data[$collection];
		
		foreach ($dataset as $record) {
			if ($record['id'] == $id) {
				return $record;
			}
		}
		return NULL;
	}

	public function get_options($collection) {
	    // Takes as input the name (string) of a collection in json
        // and returns a string of the <option> values for a select dropdown
        // sorted in alphbetical order.
        $dataset = $this->gvl_data[$collection];
	    $option_string = "";
        $values = array();

        foreach ($dataset as $record) {
            $values[$record['id']] = $record['name'];
        }

        asort($values, SORT_NATURAL | SORT_FLAG_CASE);

		foreach ($values as $id=>$name) {
			$option_string .= "<option value=\"" . $id . "\">" . $name . "</option>\n";
		}

		return $option_string;
	}

	function find_vendors($search_field, $search_array) {
		$vendors = $this->gvl_data['vendors'];
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

	    foreach ($this->gvl_data['vendors'] as $vendor) {
	        array_push($vendor_array, ["idx" => $cnt, "id"=>$vendor['id'], "name"=>$vendor['name']]);
	        $cnt++;
        }
        return json_encode($vendor_array);
    }

}
