<div id='congressAdminContainerDIV' class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);
echo "
<div class='contentDIV'>
    <div class='pageTitleDIV'>Requests</div>";

$congresses = getAllCongresses($connection);

$sortType = isset($_GET['sortType']) ? $_GET['sortType'] : 'timestamp';
$sortType = $sortType != 'timestamp' && $sortType != 'congress' ? 'timestamp' : $sortType;
$sortDirection = isset($_GET['sortDirection']) ? $_GET['sortDirection'] : 0;
$filterType = isset($_GET['filterTpye']) ? $_GET['filterType'] : 'none';
$congressID = isset($_GET['congress']) ? $_GET['congress'] : 0;
$congress = getCongressById($congressID, $connection);

$allPendingRequests = getAllPendingRequests($user, $connection);
$pendingRequests = 0;
if ($allPendingRequests != 0)
{
    foreach ($allPendingRequests as $pR)
    {
        $passesFilter = true;
        if ($congress)
        {
            $c = parseCongressFromRecord($pR['userRecord'], $connection);
            $passesFilter = $c['id'] == $congress['id'] ? true : false;
        }
        if ($passesFilter)
        {
            if (!$pendingRequests){ $pendingRequests = []; }
            array_push($pendingRequests, $pR);
        }
    }
}

$sortOptionsB = 0;
if ($sortType == 'congress')
{
    $sortOptionsB = array(
        ['value' => '0', 'label' => 'A > Z'],
        ['value' => '1', 'label' => 'Z > A']
    );
}
else
{
    $sortOptionsB = array(
        ['value' => '0', 'label' => 'Old > New'],
        ['value' => '1', 'label' => 'New > Old']
    );
}

$sortData = array(
    'includeSort' => 1,
    'includeFilter' => 1,
    'includeApplyButton' => 1,
    'sortA' => array(
        'options' => array(
            ['value' => 'timestamp', 'label' => 'Date'],
            ['value' => 'congress', 'label' => 'Congress']
        )
    ),
    'sortB' => array(
        'options' => $sortOptionsB
    ),
    'filters' => array(
        array(
            'type' => 'select',
            'types' => array(
                ['value' => 'none', 'label' => 'None'],
                ['value' => 'congress', 'label' => 'Congress']
            ),
            'options' => 0
        )
    )
);

echo getSortFilterBlock($sortData);
    
if ($sortType == 'timestamp' && $sortDirection)
{
    $newPRs = array();
    foreach ($pendingRequests as $pR)
    {
        $timestamp = $pR['userRecord']['timeStamp'];
        $pR['timeStamp'] = $timestamp;
        array_push($newPRs, $pR);
    }
    $pendingRequests = $newPRs;
    $timestamps = array_column($pendingRequests, 'timeStamp');
    if ($sortDirection)
    {
        array_multisort($timestamps, SORT_DESC, $pendingRequests);
    }
    else
    {
        array_multisort($timestamps, SORT_ASC, $pendingRequests);
    }
}
else if ($sortType == 'congress')
{
    $newPRs = array();
    foreach ($pendingRequests as $pR)
    {
        $congress = parseCongressFromRecord($pR['userRecord'], $connection);
        $pR['congressName'] = $congress['shortName'];
        array_push($newPRs, $pR);
    }
    $pendingRequests = $newPRs;
    $congressNames = array_column($pendingRequests, 'congressName');
    if ($sortDirection)
    {
        array_multisort($congressNames, SORT_DESC, $pendingRequests);
    }
    else
    {
        array_multisort($congressNames, SORT_ASC, $pendingRequests);
    }
}

if ($pendingRequests)
{
    echo "    
</div>
<div id='reservationsListDIV'>";
    foreach ($pendingRequests as $pR)
    {
        echo getShortRequestBlock($pR, $connection);
    }
}
else
{
    echo "
    <div class='emptyListDIV'>This query produced zero pending requests.</div>";
}

?>

</div>
</div>

<script>
    
    $('#sortA').change(function()
    {
        var html = "";
        switch($(this).val())
        {
            case "congress":
                html += "<option value='0'>A > Z</option>";
                html += "<option value='1'>Z > A</option>";
                break;
            case "timestamp":
                html += "<option value='0'>Old > New</option>";
                html += "<option value='1'>New > Old</option>";
                break;  
        }
        $('#sortB').html(html);
    });
    
    $('#filter1A').change(function()
    {
        var html = "";
        switch($(this).val())
        {
            case "congress":
                html += "<select id='filter1B' name='filter1B'>";
<?php

    foreach ($congresses as $c)
    {
        echo "
                html += \"<option value='" . $c['id'] . "'>" . $c['shortName'] . "</option>\";";
    }

?>
                html += "</select>";
                $('.filterBDIV').addClass('selectDIV');
                break;
            default:
                $('.filterBDIV').removeClass('selectDIV');
                break;
        }
        $('.filterBDIV').html(html);
    });
    
    $("#applyFilters").click(function()
    {
        var type = $('#sortA').val();
        var dir = $('#sortB').val();
        var filterType = $('#filter1A').val();
        var location = "<?php echo HOME; ?>?action=viewRequests&sortType=" + type + "&sortDirection=" + dir;
        if (filterType != "none")
        {
            location += "&congress=" + $('#filter1B').val();
        }
        window.location.href = location;
    });
    
</script>

