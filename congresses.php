<div id='congressContainerDIV' class='mainDIV'>

<?php

    echo getBGDiv();
    echo getHeaderDiv($user) . "
    <div class='contentDIV'>
    ";
    
    $congresses = getAllCongresses($connection);
    
    if ($congresses)
    {
        echo "
        <div class='pageTitleDIV whiteBG'>Select Congress</div>
        <div id='congressListDIV'>
        ";
        
        foreach ($congresses as $congress)
        {
            if (!congressIsInThePast($congress))
            {
                echo getShortFormatCongressBlock($congress);
            }
        }
        
        echo "
        </div>
        ";
    }
    else
    {
        echo "<div class='emptyListDIV'>There are no congresses currently in the database.</div>";
    }

    echo "
    </div>
    ";
    
    ?>

</div>



