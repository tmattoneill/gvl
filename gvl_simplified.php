<?php

    $src = "https://vendorlist.consensu.org/v2/vendor-list.json";
    $gvl_json = @file_get_contents($src);

    if ( $gvl_json == false) {
        echo "Invalid source file.";
        die;
    } else {
        $gvl_data = json_decode($gvl_json, true);
    }

    # meta information
    $gvlSpecificationVersion = $gvl_data['gvlSpecificationVersion'];
    $vendorListVersion = $gvl_data['vendorListVersion'];
    $tcfPolicyVersion = $gvl_data['tcfPolicyVersion'];
    $lastUpdated = $gvl_data['lastUpdated'];

    # look up data
    $purposes = $gvl_data['purposes'];
    $specialPurposes = $gvl_data['specialPurposes'];
    $features = $gvl_data['features'];
    $specialFeatures = $gvl_data['specialFeatures'];
    $stacks = $gvl_data['stacks'];

    # vendor list
    $vendors = $gvl_data['vendors'];

    function data_lookup($dataset, $id) {
        return $dataset[$id];
    }

    function build_select($dataset, $field_value, $field_text, $selected_value=null) {
        $return_string = '<select style="" name=' . $field_value . '_select id=' . $field_value . '>' . "\n";

        $name = array_column($dataset, 'name');
        array_multisort($name, SORT_ASC, $dataset);

        foreach ($dataset as $item) {
            $return_string .= '<option value="' . $item[$field_value] . '"';
            $return_string .= $item[$field_value] == $selected_value ? " selected >" : ">";
            $return_string .= $item[$field_text] . '</option>' . "\n";
        }

        $return_string .= '</select>';

        return $return_string;
    }

    function single_array($dataset, $field_to_flatten){
        $the_array = [];

        foreach($dataset as $item) {
            array_push($the_array, ["value" => $item["id"], "label" => $item[$field_to_flatten]]);
        }

        return json_encode($the_array);
    }
?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <title>GVL Lookup</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="bower_components/jquery-ui/themes/humanity/jquery-ui.css">
</head>
<body class="bg-gray-200 p-5">
    <h2 class="font-bold text-2xl">Select a Vendor</h2>
    <div id="selector"><?= build_select($vendors, "id", "name") ?></div>
    <div class="ui-widget">
        <label for="vendor">Vendor Search: </label>
        <input id="vendor">
        <input type="hidden" id="vendor_id" value="">
    </div>

    <! -- Scripts & jQuery -->
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#vendor" ).autocomplete({
                source: <?= single_array($vendors, "name") ?>,
                minLength: 3,
                select: function (event, ui) {
                    event.preventDefault();
                    $('#vendor_id').val(ui.item.value);
                    $('#vendor').val(ui.item.label);
                }
            });
        } );

        $(function() {
            $("#vendor").change( function() {
                this.val("123")
            });
        })
    </script>
</body>
</html>