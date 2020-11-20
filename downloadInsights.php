<div id="congressContainerDIV" class='mainDIV'>

<?php

if ($user['level'] < 2)
{
    echo "Unauthorized Access!";
    exit();
}

echo getBGDiv();
echo getHeaderDiv($user);

$congresses = getAllCongressesWithInsights($connection);
$method = isset($_GET['action']) && $_GET['action'] == POST_VIEW_INSIGHTS ? 'view' : 'download';
$title = $method == 'view' ? 'View Insights' : 'Pull Insights';
$submitName = $method == 'view' ? POST_VIEW_INSIGHTS : POST_DOWNLOAD_INSIGHTS;

if ($congresses)
{
    $filterData = array(
        'includeSort' => 0,
        'includeFilter' => 1,
        'includeApplyButton' => 0,
        'filters' => array(
            array(
                'type' => 'select',
                'types' => array(
                    ['value' => 'none', 'label' => 'None'],
                    ['value' => 'type', 'label' => 'Item Type']
                ),
                'options' => 0,
            ),
            array(
                'type' => 'date',
                'types' => array(
                    ['value' => 'none', 'label' => 'None'],
                    ['value' => 'date', 'label' => 'Date']
                ),
                'options' => 0
            )
        )
    );
    
    echo "
<div class='contentDIV'>
    <div class='pageTitleDIV whiteBG'>" . $title . "</div>";
    
    echo getSortFilterBlock($filterData);
    
    echo "
    <div id='congressListDIV'>";
    foreach ($congresses as $congress)
    {
        echo "
        <div class='shortCongressBlockDIV'";
        
        if ($congress["imageURL"] != "")
        {
            echo " style='background-image: linear-gradient(to right, rgba(70, 99, 32, 0.7), rgba(60, 101, 124, 0.7)), url(\"" . CONGRESS_IMAGES_PATH . $congress['imageURL'] . "\");opacity:1;'";
        }
        
        echo "><a class='clickable'>
            <label for='downloadInsights_" . $congress['id'] . "'/>
            <div class='shortCongressShortName'>" . $congress['shortName'] . "</div>
            <div class='shortCongressShortDates'>" . congressDatesForHtmlShortFormat($congress) . "</div>
            </a>
        </div>
        <form name='insightsDownload_" . $congress['id'] . "' method='post' action='" .  HOME . "'>
            <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
            <input class='typeFilterInput' name='typeFilter' type='text' hidden='true'/>
            <input class='dateFilterInput' name='dateFilter' type='text' hidden='true'/>
            <input id='downloadInsights_" . $congress['id'] . "' type='submit' name='" . $submitName . "' style='display:none;' value=''/>
        </form>";
    }
    echo "
    </div>
</div>";
}
else
{
    echo "
<div class='contentDIV'>
    <div class='emptyListDIV'>There are no congresses that currently have activated insights.</div>
</div>";
}

?>

</div>

<script>

    $('#filter1A').change(function()
    {
        var html = "";
        switch($(this).val())
        { 
            case "type":
                html += "<select id='filter1B' name='filter1B'>";
                html += "<option value='<?php echo _BREAK; ?>'><?php echo convertAgendaTermForDisplay(_BREAK); ?></option>";
                html += "<option value='<?php echo EXHIBIT; ?>'><?php echo convertAgendaTermForDisplay(EXHIBIT); ?></option>";
                html += "<option value='<?php echo EXPO_HOURS; ?>'><?php echo convertAgendaTermForDisplay(EXPO_HOURS); ?></option>";
                html += "<option value='<?php echo INTERNAL; ?>'><?php echo convertAgendaTermForDisplay(INTERNAL); ?></option>";
                html += "<option value='<?php echo POSTER; ?>'><?php echo convertAgendaTermForDisplay(POSTER); ?></option>";
                html += "<option value='<?php echo PRESENTATION; ?>'><?php echo convertAgendaTermForDisplay(PRESENTATION); ?></option>";
                html += "<option value='<?php echo RECEPTION; ?>'><?php echo convertAgendaTermForDisplay(RECEPTION); ?></option>";
                html += "</select>";
                $('#filter1BDIV').addClass('selectDIV');
                break;
            default:
                $('#filter1BDIV').removeClass('selectDIV');
                $('.typeFilterInput').val("");
                break;
        }
        $('#filter1BDIV').html(html);
        
        $('#filter1BDIV select').change(function()
        {
           $('.typeFilterInput').val($(this).val());
        });
    });
    
    $('#filter2A').change(function()
    {
        var html = "";
        switch($(this).val())
        {
            case "date":
                html += "<input type='date' name='filterDate'/>";
                break;
            default:
                $('.dateFilterInput').val("");
                break;
        }
        $('#filter2BDIV').html(html);
        
        $('#filter2BDIV input').change(function()
        {
           $('.dateFilterInput').val($(this).val());
        });
        
    });
    
    

</script>