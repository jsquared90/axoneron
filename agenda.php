<div id='agendaContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user);
    
    $agenda = 0;
    $congress = isset($_GET["congress"]) ? getCongressById($_GET["congress"], $connection) : (isset($_POST["congressID"]) ? getCongressById($_POST["congressID"], $connection) : 0);
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 0;
    $pageTitle = "Agenda";
    if ($congress)
    {
        $pageTitle = "Agenda for " . $congress['shortName'];
    }
    else if ($filter == "all")
    {
        $pageTitle = "Complete Schedule";
    }
    else if ($filter == "axoneron")
    {
        $pageTitle = "Schedule of Axoneron Events";
    }
    $genNotesNotSaved = 0;
    $newPostNotSaved = 0;
    $insights = 0;
    
    echo "
    <div class='contentDIV'>
        <div class='pageTitleDIV whiteBG'>". $pageTitle . "</div>";
    
    if ($congress)
    {
        echo getLongFormatCongressBlock($congress);
        
        $item = isset($_GET["item"]) ? getAgendaItemByID($_GET["item"], $congress, $connection) : (isset($_POST["itemID"]) ? getAgendaItemByID($_POST["itemID"], $congress, $connection) : 0);
        if ($item)
        {
            // user has clicked on an agenda item with the intention of expanding for use
            
            $names = "";
            foreach ($item[ASSIGNMENT] as $assignment)
            {
                $u = getUserById($assignment, $connection);
                $names .= $u['first'] . " " . $u['last'];
                if (next($item[ASSIGNMENT]))
                {
                    $names .= ", ";
                }
            }
            
            echo "
        <div id='agendaItemDetailViewDIV'>
            <div class='agendaItemTypeDIV agendaItemDisplay'>" . convertAgendaTermForDisplay($item[TYPE]) . "</div>
            <div class='agendaItemDateDIV agendaItemDisplay'>" . $item[START_DATE] . "</div>
            <div class='agendaItemTitleDIV agendaItemDisplay'>" . $item[TITLE] . "</div>
            <div class='agendaItemSubTitleDIV agendaItemDisplay'>" . $item[SUB_TITLE] . "</div>
            <div class='agendaItemTimeDIV agendaItemDisplay'>" . format1ForSingleTimeDisplay($item[START_TIME], $item[END_TIME]) . "</div>
            <div class='agendaItemPresenterDIV hRow agendaItemDisplay'>
                <div class='agendaItemColumn1'>Presenter(s):</div>
                <div class='agendaItemColumn2'>" . implode(",", $item[PRESENTERS]) . "</div>
            </div>
            <div class='agendaItemLocationDIV hRow'>
                <div class='agendaItemColumn1'>Location:</div>
                <div class='agendaItemColumn2'>" . $item[LOCATION] . "</div>
            </div>
            <div class='agendaItemChairDIV hRow'>
                <div class='agendaItemColumn1'>Chairperson(s):</div>
                <div class='agendaItemColumn2'>" . implode(",", $item[CHAIR]) . "</div>
            </div>
            <div class='agendaItemSessionDIV hRow'>
                <div class='agendaItemColumn1'>Session Name:</div>
                <div class='agendaItemColumn2'>" . $item[SESSION_NAME] . "</div>
            </div>
            <div class='agendaItemAssignmentDIV hRow'>
                <div class='agendaItemColumn1'>Assignment(s):</div>
                <div class='agendaItemColumn2'>" . $names . "</div>
            </div>
            <div class='agendaItemPriorityDIV hRow'>
                <div class='agendaItemColumn1'>Priority:</div>
                <div class='agendaItemColumn2'>" . convertAgendaPriorityForDisplay($item[PRIORITY]) . "</div>
            </div>
        </div>
        <div id='insightsDIV' class='indented'>
            <div class='subTitleDIV'>Insights</div>";
            
            $insights = getInsights($user, $congress, $item);
            $insightsDisplay = "";
            $generalNotes = DEFAULT_NOTEPAD_TEXT;
            if (!$insights)
            {
                $insightsDisplay = "style='display:none;'";
                echo "
            <div id='addInsightsDIV'>
                <form class='itemAddForm' name='insightsAdd' id='insightsAdd' method='post' action='" . HOME . "'>
                    <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
                    <input name='itemID' hidden='true' type='number' value='" . $item['id'] . "'/>
                    <div class='addItem far'><label for='addInsights'>&#xf0fe;</label></div>
                    <input id='addInsights' type='submit' name='" . POST_ADD_INSIGHTS . "' value=''/>
                </form>
            </div>";
            }
            else
            {
                if (isset($_COOKIE[$insights['identifier'] . "_generalNotes"]) && $_COOKIE[$insights['identifier'] . "_generalNotes"] != "null")
                {
                    $genNotesNotSaved = 1;
                    $generalNotes = $_COOKIE[$insights['identifier'] . "_generalNotes"];
                    echo $generalNotes . "<br/>";
                }
                else if ($insights['generalNotes'] != "")
                {
                    $generalNotes = $insights['generalNotes'];
                }
            }
            
            echo "
            <div id='insightsGeneralNotesDIV'" . $insightsDisplay . ">
                <div id='generalNotesCaret' class='caret fa noteCaretContract'>&#xf105;</div>
                <div><span class='bold'>General Notes </span><span>(click to edit)</span></div>
                <div id='generalNotes' style='display:none;'>" . $generalNotes . "</div>
                <div id='insightsGeneralNotesContainer'>" . nl2br($insights['generalNotes']) . "</div>
            </div>
            <div id='insightsPostsDIV'" . $insightsDisplay . ">
            <div class='subTitleDIV'>POSTS</div>";
            
            if ($insights['posts'])
            {
                foreach ($insights['posts'] as $post)
                {
                    echo getShortInsightBlock($post);
                }
            }
            
            echo "
            </div>
            <div id='addInsightDIV' class='addItem far'" . $insightsDisplay . ">&#xf0fe;</div>
            <div id='addInsightPostFormDIV' style='display:none;'>
                <div class='subTitleDIV editInsightSubTitle'>Add Post</div>
                <form name='insightPostAdd' id='insightPostAdd' method='post' action='" .  HOME . "' enctype='multipart/form-data'>
                    <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
                    <input name='itemID' hidden='true' type='number' value='" . $item['id'] . "'/>
                    <input id='imageFile' type='file' name='imageFile' accept='.png,.jpg,.jpeg'/>
                    <div class='insightPostFormInput'>
                        <div class='formLabel'>Title:*</div>
                        <div><input id='insightPostTitle' type='text' name='insightPostTitle'";
            
            if (isset($_COOKIE[$insights['identifier'] . "_insightPostTitle"]) && $_COOKIE[$insights['identifier'] . "_insightPostTitle"] != "null")
            {
                echo " value= '" . $_COOKIE[$insights['identifier'] . "_insightPostTitle"] . "'";
                $newPostNotSaved = 1;
            }
            
            echo "></div>
                    </div>
                    <div class='insightPostFormInput'>
                        <div class='formLabel'>Notes:</div>
                        <div><textarea id='insightPostNotes' rows='4' cols='50' name='insightPostNotes' >";
            
            if (isset($_COOKIE[$insights['identifier'] . "_insightPostNotes"]) && $_COOKIE[$insights['identifier'] . "_insightPostNotes"] != "null")
            {
                echo $_COOKIE[$insights['identifier'] . "_insightPostNotes"];
                $newPostNotSaved = 1;
            }
            
            echo "</textarea></div>
                    </div>
                    <div class='subTitleDIV'>Image:</div>
                    <div id='imageAreaDIV'>
                        <div id='imageAreaDIV2' style='display:none;'>
                            <div class='imageFileName'></div>
                            <div class='edit imageEdit fa'><label id='imageFileLabel1' for='imageFile'>&#xf044;</label></div>
                        </div>
                        <div id='imageAreaDIV3'>
                            <label id='imageFileLabel2' class='button' for='imageFile'>Choose File</label>
                        </div>
                    </div>
                    <div><input class='addInsightButton' type='submit' name='" . POST_ADD_INSIGHT . "' value='Submit Post'";
            
            if (!$newPostNotSaved)
            {
                echo " style='display:none;'";
            }
            
            echo "/></div>
                </form>
            </div>
            <div id='editInsightPostFormDIV' style='display:none;'>
                <div class='subTitleDIV editInsightSubTitle'>Edit Post</div>
                <form name='insightPostEdit' id='insightPostEdit' method='post' action='" .  HOME . "' enctype='multipart/form-data'>
                    <input name='congressID' hidden='true' type='number' value='" . $congress['id'] . "'/>
                    <input name='itemID' hidden='true' type='number' value='" . $item['id'] . "'/>
                    <input id='editFile' type='file' name='editFile' accept='.png,.jpg,.jpeg'/>
                    <input id='previousTitle' name='previousTitle' hidden='true' type='text' value=''/>
                    <div class='insightEditFormInput'>
                        <div class='formLabel'>Title:*</div>
                        <div><input id='insightEditTitle' name='insightEditTitle' type='text'></div>
                    </div>
                    <div class='insightEditFormInput'>
                        <div class='formLabel'>Notes:</div>
                        <div><textarea id='insightEditNotes' name='insightEditNotes' rows='4' cols='50'></textarea></div>
                    </div>
                    <div class='subTitleDIV'>Image:</div>
                    <div id='editAreaDIV'>
                        <div id='editAreaDIV2' style='display:none;'>
                            <div class='editFileName'></div>
                            <div class='edit imageEdit fa'><label id='editFileLabel1' for='editFile'>&#xf044;</label></div>
                        </div>
                        <div id='editAreaDIV3'>
                            <label id='editFileLabel2' class='button' for='editFile'>Choose File</label>
                        </div>
                    </div>
                    <div><input class='editInsightButton' type='submit' name='" . POST_MODIFY_INSIGHT . "' value='Submit Post'/></div>
                </form>
            </div>
        </div>";
            
        }
        else
        {
            // user has clicked on "Agenda" from a specific congress' sub menu
            
            $agenda = getAgendaFromDatabase($congress, $connection);
            echo getAgendaBlockItems($agenda, $connection);
        }
    }
    else if ($filter == "all")
    {
        $agenda = getAllAgendasFromDatabase($connection);
        echo getAgendaBlockItems($agenda, $connection);
    }
    else if ($filter == "axoneron")
    {
        $agenda = getAxoneronAgendaItemsFromDatabase($connection);
        echo getAgendaBlockItems($agenda, $connection);
    }
    else 
    {
        echo "
    <div class='emptyListDIV'>There has been an error retrieving the agenda items based on the search criteria.</div>";
    }
    
    

?>
    </div>
</div>

<script>
    
    window.onload = cleanAllImages();
    
    function cleanAllImages()
    {
        $('.postImage').each(function()
        {
            var id = $(this).attr('id');
            cleanImage(id);
        });
    }
    
    function cleanImage(id)
    {
        var image = document.getElementById(id);
        EXIF.getData(image, function()
        {
            var orientation = EXIF.getTag(this, "Orientation");
            
            if(orientation == 6)
            {
                $(id).addClass("rotate90");
            }
            else if(orientation == 8)
            {
                $(id).addClass("rotate270");
            }
            else if(orientation == 3)
            {
                $(id).addClass("rotate180");
            }
        });
    }
    
    var userID = <?php echo $user['id']; ?>;
    var availableDates = <?php
    
    if ($agenda)
    {
        $startDate = getSoonestAgendaItemDate($agenda);
        $endDate = $startDate ? getLatestAgendaItemDate($agenda) : 0;

        echo "[";

        $i = strtotime($startDate);
        $j = 0;

        $string = '';
        while ($i < strtotime($endDate))
        {
            $date = Date("n/d/y", $i);
            $items = getAgendaItemsForDate($agenda, $date);
            if ($items)
            {
                $string .= "'" . $date . "',";
            }
            $j++;
            $i = strtotime($startDate . "+" . $j . " day");
        }
        if ($string != '')
        {
            $string = substr($string, 0, strlen($string) - 1);
        }
        echo $string;

        echo "];";
    }
    else
    {
        echo "[];";
    }
    
    
    ?>
        
    var selectedDate = availableDates.length > 0 ? availableDates[0] : 0;
    var congressID = '<?php if(isset($congress)){ echo $congress['id']; }else{ echo ""; } ?>';
    var itemID = '<?php if(isset($item)){ echo $item['id']; }else{ echo ""; } ?>';
    var insightTitle = "";
    var genNotesNotSaved = <?php if($genNotesNotSaved){ echo "true"; }else{ echo "false"; } ?>;
        
    const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
    
    $(".leftArrow").click(function()
    {
        $(".dateBlock").each(function()
        {
            var date = $(this).attr('date');
            var rawDate = Date.parse(date);
            var ms = rawDate - <?php echo ONE_DAY; ?>;
            var nextDay = new Date(ms);
            if (nextDay.getHours() == 1)
            {
                // daylight savings. let's subtract an hour and reset next day
                ms -= <?php echo ONE_HOUR; ?>;
                var nextDay = new Date(ms);
            }
            var month = nextDay.getMonth() + 1;
            var year = nextDay.getFullYear().toString().slice(2, 4);
            var day = nextDay.getDate() < 10 ? "0" + nextDay.getDate() : nextDay.getDate();
            var dateString = month + "/" + day + "/" + year;
            $(this).attr('date', dateString);
            $(this).find('.dateBlockMonth').html(months[nextDay.getMonth()]);
            $(this).find('.dateBlockDate').html(nextDay.getDate());
            if (dateString === selectedDate)
            {
                $(this).addClass("selected");
            }
            else
            {
                $(this).removeClass("selected");
            }
            var disabled = true;
            for (var availableDate of availableDates)
            {
                if (dateString === availableDate)
                {
                    disabled = false;
                }
            }
            if (disabled)
            {
                $(this).addClass("disabled");
            }
            else
            {
                $(this).removeClass("disabled");
            }
        });
    });
    
    $(".rightArrow").click(function()
    {
        $(".dateBlock").each(function()
        {
            var date = $(this).attr('date');
            var rawDate = Date.parse(date);
            var ms = rawDate + <?php echo ONE_DAY; ?>;
            var nextDay = new Date(ms);
            if (nextDay.getHours() == 23)
            {
                // daylight savings. let's add an hour and reset next day
                ms += <?php echo ONE_HOUR; ?>;
                var nextDay = new Date(ms);
            }
            var month = nextDay.getMonth()+1;
            var year = nextDay.getFullYear().toString().slice(2, 4);
            var day = nextDay.getDate() < 10 ? "0" + nextDay.getDate() : nextDay.getDate();
            var dateString = month + "/" + day + "/" + year;
            $(this).attr('date', dateString);
            $(this).find('.dateBlockMonth').html(months[nextDay.getMonth()]);
            $(this).find('.dateBlockDate').html(nextDay.getDate());
            if (dateString === selectedDate)
            {
                $(this).addClass("selected");
            }
            else
            {
                $(this).removeClass("selected");
            }
            var disabled = true;
            for (var availableDate of availableDates)
            {
                if (dateString === availableDate)
                {
                    disabled = false;
                }
            }
            if (disabled)
            {
                $(this).addClass("disabled");
            }
            else
            {
                $(this).removeClass("disabled");
            }
        });
        
    });

    $(".button").click(function()
    {
       if ($(this).attr('id') === "allItems")
       {
           $("#allItems").addClass("selected");
           $("#assignedItems").removeClass("selected");
       }
       else
       {

           $("#assignedItems").addClass("selected");
           $("#allItems").removeClass("selected");
       }
       filterAgendaItems();
    });
    
    function filterAgendaItems()
    {
        var all = $("#allItems").hasClass("selected") ? true : false;
        $(".agendaItemDIV").each(function()
        {
           if (all)
           {
               $(this).show();
           }
           else if ($(this).attr('assignment').includes(userID))
           {
               $(this).show();
           }
           else
           {
               $(this).hide();
           }
        });
    }

    $(".dateBlock").click(function()
    {
        if (!$(this).hasClass('selected') && !$(this).hasClass('disabled'))
        {
            $(this).addClass('selected');
            selectedDate = $(this).attr('date');
            $(".dateBlock").each(function()
            {
                if (selectedDate !== $(this).attr('date') && !$(this).hasClass('disabled'))
                {
                   $(this).removeClass('selected');
                }
            });
            $(".dateBlock").each(function()
            {
                if (selectedDate !== $(this).attr('date') && !$(this).hasClass('disabled'))
                {
                   $(this).removeClass('selected');
                }
            });
            $(".agendaGroupDIV").each(function()
            {
                if (selectedDate !== $(this).attr('date'))
                {
                    $(this).hide();
                }
                else
                {
                    $(this).show();
                }
            });
        }
    });
    
    $('#addInsightDIV').click(function()
    {
        insightTitle = $('#insightPostTitle').val();
        if ($('#addInsightDIV').html().charCodeAt(0) == 61766)
        {
            $('#addInsightPostFormDIV').hide();
            $('#editInsightPostFormDIV').hide();
            $('.insightPostDIV').show();
            $('#addInsightDIV').html('&#xf0fe;');
        }
        else if ($('#addInsightPostFormDIV').is(':hidden'))
        {
            $('#addInsightPostFormDIV').show();
            $('#addInsightDIV').html('&#xf146;');
        }
    });
    
    $('#imageFileLabel1,#imageFileLabel2').click(function()
    {
        $('#imageFile').change(function()
        {
            var fileName = $('#imageFile')[0].files[0].name;
            $('.imageFileName').html(fileName);
            $('#imageAreaDIV3').hide();
            $('#imageAreaDIV2').show();
            $('.addInsightButton').show();
        });
    });
    
    $('#editFileLabel1,#editFileLabel2').click(function()
    {
        $('#editFile').change(function()
        {
            var fileName = $('#editFile')[0].files[0].name;
            $('.editFileName').html(fileName);
            $('#editAreaDIV3').hide();
            $('#editAreaDIV2').show();
            $('.editInsightButton').show();
        });
    });
         
    $('.insightEdit').click(function()
    {
        var insightID = $(this).attr('id');
        insightTitle = insightID.replace('editInsight_', '');
        $('#insight_' + insightTitle).hide();
        $('#editInsightPostFormDIV').show();
        $('#addInsightPostFormDIV').hide();
        $('#addInsightDIV').html('&#xf146;');
        var previousTitle = $('#editTitle_' + insightTitle).html();
        $('#previousTitle').val(previousTitle);
        $('#insightEditTitle').val(previousTitle);
        $('#insightEditTitle').html(previousTitle);
        var previousNotes = $('#editNotes_' + insightTitle).html();
        $('#insightEditNotes').val(previousNotes);
        identifier = userID + "_" + congressID + "_" + itemID + "_" + insightTitle;
        if (Cookies.get(identifier) == null || Cookies.get(identifier) == "null")
        {
            $('.editInsightButton').hide();
        }
    });
    
    $('#insightPostTitle').on('input selectionchange propertychange', function()
    {
        identifier = userID + "_" + congressID + "_" + itemID + "_insightPostTitle";
        Cookies.set(identifier, $('#insightPostTitle').val());
        $('.addInsightButton').show();
    });
    
    $('#insightPostNotes').on('input selectionchange propertychange', function()
    {
        identifier = userID + "_" + congressID + "_" + itemID + "_insightPostNotes";
        Cookies.set(identifier, $('#insightPostNotes').val());
        $('.addInsightButton').show();
    });
    
    $('#insightEditNotes').on('input selectionchange propertychange', function()
    {
        identifier = userID + "_" + congressID + "_" + itemID + "_" + insightTitle;
        Cookies.set(identifier, $('#insightEditNotes').val());
        $('#editNotes_' + insightTitle).html($('#insightEditNotes').val());
        $('.editInsightButton').show();
    });
    
    $('#insightEditTitle').on('input selectionchange propertychange', function()
    {
        $('.editInsightButton').show();
    });
    
    $('#generalNotesCaret').click(function()
    {
        if($('#insightsGeneralNotesContainer').hasClass('insightsGeneralNotesExpanded'))
        {
            $('#insightsGeneralNotesContainer').removeClass('insightsGeneralNotesExpanded');
            $('#generalNotesCaret').removeClass('noteCaretExpand');
            $('#generalNotesCaret').addClass('noteCaretContract');
            $('#generalNotesCaret').html('&#xf105;');
        }
        else
        {
            $('#insightsGeneralNotesContainer').addClass('insightsGeneralNotesExpanded');
            $('#generalNotesCaret').removeClass('noteCaretContract');
            $('#generalNotesCaret').addClass('noteCaretExpand');
            $('#generalNotesCaret').html('&#xf107;');
        }
    });
    
    $('#insightsGeneralNotesContainer').click(function()
    {
        invokeNotePad($('#generalNotes').html(),'<?php if ($insights){ echo $insights['identifier']; } ?>_generalNotes');
    });
    
    
    function invokeNotePad(notePadCopy, identifier)
    {
        var html = "<div class='notePadDIV'>";
        html += "<div class='notePadFormDIV'>";
        html += "<form name='generalInsightAdd' id='generalInsightAdd' method='post' action='<?php echo HOME; ?>'>";
        html += "<input name='congressID' hidden='true' type='number' value='" + congressID + "'/>";
        html += "<input name='itemID' hidden='true' type='number' value='" + itemID + "'/>";
        html += "<input name='identifier' hidden='true' type='number' value='" + identifier + "'/>";
        html += "<div class='notePadTextDIV'>";
        html += "<textarea id='notePad' name='notePadData'>" + notePadCopy + "</textarea>";
        html += "</div>";
	html += "<div class='notePadButton'>";
        html += "<input id='notePadSubmit' name='<?php echo POST_ADD_GENERAL_INSIGHT; ?>' type='submit' value='Submit Insights'";
        if (genNotesNotSaved)
        {
            html += "/>";
        }
        else
        {
            html += " style='display:none;'/>";
        }
        html += "</div>";
        html += "<div id='headerBarInnerDIV'>";
        html += "<input id='retainGeneralInsight' type='cross' value='×'/>";
        html += "</div>";
        $('#agendaContainerDIV').hide();
        $(document.body).append(html);
        
        $('#notePad').on('input selectionchange propertychange', function()
        {
            Cookies.set(identifier, $('#notePad').val());
            genNotesNotSaved = true;
            $('#generalNotes').html($('#notePad').val());
            $('#notePadSubmit').show();
        });
        
        $('#retainGeneralInsight').click(function()
        {
            $('.notePadDIV').remove();
            $('#agendaContainerDIV').show();
        });
    }
    
    function confirmDelete(title)
    {
        if (confirm("Are you sure you want delete '" + title + "'? This action cannot be undone!"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#insightPostAdd").validate(
        {
            rules:
            {
                insightPostTitle:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32
                },
                insightPostNotes:
                {
                    required: false,
                    maxlength: 512
                },
                imageFile:
                {
                    required: false,
                    accept: "image/*"
                }
            },
            messages:
            {

            }
        });
    });
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#insightPostEdit").validate(
        {
            rules:
            {
                insightEditTitle:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 32
                },
                insightEditNotes:
                {
                    required: false,
                    maxlength: 512
                },
                editFile:
                {
                    required: false,
                    accept: "image/*"
                }
            },
            messages:
            {

            }
        });
    });

</script>
    