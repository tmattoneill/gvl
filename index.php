<?php
session_start();
require("./gvl.php");

$gvl = new GVL();

if (isset($_GET['vendor'])) {
   $show_info = true;
   $company = $gvl->get_record("vendors", $_GET['vendor']);
}


// Helpers
function element_list($collection) {
    global $company; // set on page load based on query params
    global $gvl;
    $collection_string = "";

    if (!isset($company)) {
        return false;
    }

    $elements = $company[$collection];

    foreach ($elements as $elem_idx) {
        $element = $gvl->get_record($collection, $elem_idx);
        $collection_string .= "<strong>$elem_idx: </strong>" . $element['name'] . ":" . $element['description'] . "<br>";
    }

    return $collection_string;
}

function tmt_csv_to_array($file='data/tmt_lookup.csv') {
    $csv = array_map('str_getcsv', file($file));
    array_walk($csv, function(&$a) use ($csv) {
        $a = array_combine($csv[0], $a);
    });

    array_shift($csv);

    return $csv;
}

function get_tmt_gid($id) {
    foreach (tmt_csv_to_array() as $vendor) {
        if ($vendor['iab_id'] == $id && $vendor['gid']) {
            return $vendor['gid'];
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>GVL Lookup Tool</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <style>
        .info {
            line-height: .85em;
            font-size: .85em;
        }
        #company-info p{
            margin-left: 3em;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>GVL Manager</h1>
    <h3>Vendor list Information</h3>
    <pre class="info">
        GVL Specific Version: <?= $gvl->get_gvlSpecificVersion(); ?><br>
        Vendor List Version : <?= $gvl->get_vendorListVersion(); ?><br>
        TCF Policy Version  : <?= $gvl->get_tcfPolicyVersion(); ?><br>
        Last Updated        : <?= $gvl->get_lastUpdated(); ?><br>
        Source file         : <?= $gvl->get_gvl_json() ?>
    </pre>

    <!-- Query / Search Form -->
    <form name="company" method="get" action="index.php">
        <div class="auto_submit">
            <!-- Vendor Selection Dropdown -->
            <label for="vendor">Company Name</label>
            <select id="vendor" name="vendor">
                <option value="" disabled selected>Select Vendor</option>
                <?= $gvl->get_options("vendors") ?>
            </select>
        </div>
    </form>
    <!-- END FORM -->
    <?php if (isset($company)): ?>
        <div id="company-info">
            <hr>
            <img alt="logo" style="float:right" src="//logo.clearbit.com/<?= parse_url($company['policyUrl'])['host'] ?>?size=120">
            <h2><?= $company['name'] ?> (ID: <?= $company['id'] ?>)</h2>
            <?php if (get_tmt_gid($company['id']) != 'null'): ?>
                <p>
                    TMT GID: <?= get_tmt_gid($company['id']) ?>
                    <img style="display: inline-block; width:20px" src="img/tmt-shield.png" alt="TMT">
                </p>
            <?php endif; ?>
            <p>Link to Privacy Policy: <a href="<?= $company['policyUrl'] ?>"><?= $company['policyUrl'] ?></a></p>
            <!-- Purposes -->
            <?php if ($elems = element_list("purposes")): ?>
                <h3>Legal Purposes</h3>
                <p><?= $elems; ?></p>
            <?php endif; ?>
            <?php if ($elems = element_list("specialPurposes")): ?>
                <h3>Special Purposes</h3>
                <p><?= $elems; ?></p>
            <?php endif; ?>
            <?php if ($elems = element_list("features")): ?>
                <h3>Features</h3>
                <p><?= $elems; ?></p>
            <?php endif; ?>
            <?php if ($elems = element_list("specialFeatures")): ?>
                <h3>Special Features</h3>
                <p><?= $elems; ?></p>
            <?php endif; ?>
        </div>
    <?php endif;?>
</div>

<script src="bower_components/jquery/dist/jquery.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"
        integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd"
        crossorigin="anonymous"></script>
<script>
    let url_params = new URLSearchParams(window.location.search);

    // set values in search form if set from query
    if (url_params.has('vendor')) {
        $('#vendor option[value=' + url_params.get('vendor') + ']').attr('selected', 'selected');
        $('#' +  url_params.get('version')).attr('checked', 'checked');
    }
    // auto-update on change from the dropdown
    $(function() {
        $(".auto_submit").change(function() {
            $("form").submit();
        });
    });
</script>
</body>
</html>
