<?php

if ($user['level'] > 1)
{
    echo "<div id='adminContainerDIV' class='mainDIV'>";
    echo getBGDiv();
    echo getHeaderDiv($user) . "
<div class='contentDIV buttonListDIV'>
    <div class='pageTitleDIV'>Admin</div>
    <div><a href='" . HOME . "?action=" . POST_ADD_CONGRESS . "'><div class='button'>Add Congress</div></a></div>
    <div><a href='" . HOME . "?page=" . POST_MODIFY_CONGRESSES . "'><div class='button'>Modify Congress</div></a></div>
    <div><a href='" . HOME . "?page=" . POST_REMOVE_CONGRESS . "'><div class='button'>Remove Congress</div></a></div>
    <div><a href='" . HOME . "?page=" . POST_ADD_USER . "'><div class='button'>Add User</div></a></div>
    <div><a href='" . HOME . "?page=" . POST_MODIFY_USERS . "'><div class='button'>Modify User</div></a>
    <div><a href='" . HOME . "?page=" . POST_REMOVE_USER . "'><div class='button'>Remove User</div></a></div>
    <div><a href='" . HOME . "?page=" . POST_VIEW_REQUESTS . "'><div class='button'>View Requests</div></a></div>
    <div><a href='" . HOME . "?action=" . POST_VIEW_INSIGHTS . "'><div class='button'>View Insights</div></a></div>
    <div><a href='" . HOME . "?action=" . POST_MODIFY_HOTELS . "'><div class='button'>Modify Existing Hotel</div></a></div>
</div>";

}
else
{
    echo "
<div class='emptyListDIV'>Unauthorized Access</div>";
}

echo "</div>";
