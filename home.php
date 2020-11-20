
<div id='homeContainerDIV' class='mainDIV'>
    <?php
        echo getBGDiv();
        echo getHomeHeaderDiv($user);
        
        $unreadMessageCount = getUnreadMessageCount($user, $connection);
        $unviewedCongressCount = getUnviewedCongressCount($user, $connection);
        $unviewedReservationCount = getUnviewedReservationCount($user, $connection);
        
    ?>
    <div class='contentDIV'>
        
        <div class='homeMenuItemDIV itemCongress'><a href='<?php echo HOME; ?>?page=congresses'>
            <div>CONGRESSES</div>
                <?php
                
                if ($unviewedCongressCount)
                {
                    echo "
                        <div class='homeItemAlert'>" . $unviewedCongressCount . "</div>";
                }
                
                ?>
        </a></div>
        
        <div class='homeMenuItemDIV itemReservations'><a href='<?php echo HOME; ?>?page=reservations'>
            <div>RESERVATIONS</div>
                <?php
                
                if ($unviewedReservationCount)
                {
                    echo "
                        <div class='homeItemAlert'>" . $unviewedReservationCount . "</div>";
                }
                
                ?>
        </a></div>
        
        <div class='homeMenuItemDIV itemSchedule'><a href='<?php echo HOME; ?>?page=schedule'>
            <div>SCHEDULE</div>
        </a></div>
        
        <div class='homeMenuItemDIV itemBookAFlight'><a href='<?php echo CANYON_CREEK_URL; ?>' target="_blank">
            <div>BOOK A FLIGHT</div>
        </a></div>
            
        <div class='homeMenuItemDIV itemMessages'>
            <a href='<?php echo HOME; ?>?page=messages'>
                <div>MESSAGES</div>
                <?php
                
                if ($unreadMessageCount)
                {
                    echo "
                        <div class='homeItemAlert'>" . $unreadMessageCount . "</div>";
                }
                
                ?>
            </a>
        </div>
            
        <div class='homeMenuItemDIV itemAccount'><a href='<?php echo HOME; ?>?page=account'>
            <div>MY ACCOUNT</div>
        </a></div>
            
        <div class='homeMenuItemDIV itemSupport'><a href='<?php echo HOME; ?>?page=support'>
            <div>SUPPORT/HELP</div>
        </a></div>

        <?php

        if ($user['level'] > 1)
        {
            echo "
        <div class='homeMenuItemDIV itemAdmin'><a href='" . HOME . "?page=admin'>
            <div>ADMIN</div>";
            
            $pRs = getAllPendingRequests($user, $connection);
            if ($pRs)
            {
                $unhandledRequests = count($pRs);
                if ($unhandledRequests)
                {
                    echo "
                        <div class='homeItemAlert'>" . $unhandledRequests . "</div>";
                }
            }
            echo "
        </a></div>";
        }

        ?>

        <div class='homeMenuItemDIV itemSignout'><a href='<?php echo HOME; ?>?action=<?php echo POST_SIGN_OUT; ?>'>
            <div>SIGN OUT</div>
        </a></div>

    </div>
</div>