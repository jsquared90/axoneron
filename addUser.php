<div id='userAdminContainerDIV' class='mainDIV'>
    
<?php

    $first = $last = $phone = $title = 0;
    $comMedHide = 0;
    $supportHide = 1;
    $pageTitle = 0;
    $submitName = 0;
    $first = 0;
    $last = 0;
    $phone = 0;
    $title = 0;
    $role = 0;

    if (isset($_GET['action']))
    {
        if ($_GET['action'] == POST_MODIFY_ACCOUNT)
        {
            $pageTitle = "Edit Profile";
            $submitName = POST_MODIFY_ACCOUNT;
            $first = $user['first'];
            $last = $user['last'];
            $phone = $user['phone'];
            $title = $user['title'];
            $role = $user['role'];
            if ($user['level'] == 2)
            {
                $comMedHide = 1;
            }
            elseif ($user['level'] == 3)
            {
                $comMedHide = 1;
                $supportHide = 0;
            }
        }
        else if ($_GET['action'] == POST_MODIFY_USER)
        {
            if ($user['level'] < 2)
            {
                echo "Unauthorized Access!";
                exit();
            }
            $pageTitle = "Edit User";
            $submitName = POST_MODIFY_USER;
            $proxyUser = getUserById($_GET['user'], $connection);
            $first = $proxyUser['first'];
            $last = $proxyUser['last'];
            $phone = $proxyUser['phone'];
            $title = $proxyUser['title'];
            $role = $proxyUser['role'];
            if ($proxyUser['level'] == 2)
            {
                $comMedHide = 1;
            }
            elseif ($proxyUser['level'] == 3)
            {
                $comMedHide = 1;
                $supportHide = 0;
            }
        }
        else if ($user['level'] < 2)
        {
            echo "Unauthorized Access!";
            exit();
        }
    }
    else if ($user['level'] < 2)
    {
        echo "Unauthorized Access!";
        exit();
    }
    else
    {
        $pageTitle = "Add User";
        $submitName = POST_ADD_USER;
    }

    echo getBGDiv();
    echo getHeaderDiv($user);

?>

<div class='contentDIV'>
    <div class='pageTitleDIV'><?php echo $pageTitle; ?></div>
</div>

<!-- need to add more jQuery validation -->
<!-- need to apply character restrictions to fields, as dictated by database structure -->

<form class='contentDIV' name='userAdd' id="addUser" method='post' action='<?php echo HOME; ?>' enctype='multipart/form-data'>
    
    <div>
        <div>
            <div class='formLabel'>First Name:</div>
            <div><input type='text' name='newUserFirst' <?php if ($first){ echo " value='" . $first . "'"; } ?>/></div>
        </div>
        <div>
            <div class='formLabel'>Last Name:</div>
            <div><input type='text' name='newUserLast' <?php if ($last){ echo " value='" . $last . "'"; } ?>/></div>
        </div>
        <div>
            <div class='formLabel'>Phone:</div>
            <div><input type='tel' name='newUserPhone' size='20' <?php if ($phone){ echo " value='" . $phone . "'"; } ?>/></div>
        </div>
        
        <?php
            
        if ($submitName == POST_ADD_USER)
        {
            echo "
        <div>
            <div class='formLabel'>Email:*</div>
            <div><input class='newUserEmail' type='email' name='newUserEmail' pattern='[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$' title='Please provide a valid e-mail address.'/></div>
        </div>";
        }
        else if ($submitName == POST_MODIFY_ACCOUNT)
        {
            echo "
        <div class='passwordEditDIV'>
            <div class='formLabel'>Password :</div>
            <div class='edit passwordEdit fa'>&#xf044;</div>
        </div>";
        }

        ?>
        
        
        <div>
            <div class='formLabel'>Title:</div>
            <div><input type='text' name='newUserTitle' <?php if ($title){ echo " value='" . $title . "'"; } ?>/></div>
        </div>
        
        <?php
        
        if ($submitName == POST_ADD_USER || ($submitName == POST_MODIFY_USER))
        {
            echo "
        <div>
            <div class='formLabel'>User Type:*</div>
            <div class='selectDIV'>
                <select id='newUserLevel' name='newUserLevel'";
            
            if ($submitName == POST_MODIFY_USER)
            {
                echo " value='" . $proxyUser['level'] . "'";
            }
            
            echo ">
                    <option value='1'";
            
            if ($submitName == POST_MODIFY_USER)
            {
                if ($proxyUser['level'] == 1)
                {
                    echo " selected";
                }
            }
            
            echo ">Basic User</option>
                    <option value='2'";
            
            if ($submitName == POST_MODIFY_USER)
            {
                if ($proxyUser['level'] == 2)
                {
                    echo " selected";
                }
            }
            
            echo ">Axoneron Administrator</option>";

        if ($user['level'] > 2)
        {
            echo "
                    <option value='3'";
            
            if ($submitName == POST_MODIFY_USER)
            {
                if ($proxyUser['level'] == 3)
                {
                    echo " selected";
                }
            }
            
            echo ">Site Administrator</option>";
        }
        echo "
                </select>
            </div>
        </div>";
        }
        
        ?>
        
        
        <div>
            <div class='formLabel'>Role:*</div>
            <div class='selectDIV'>
                
                <select id='newUserRole' name='newUserRole' <?php if ($role){ echo " value='" . $role . "'"; }else{ echo " value='commercial'"; }?>>
                    <option id='commercial' value='commercial'<?php if($comMedHide){ echo " hidden='true'"; } if ($role === 'commercial'){ echo " selected"; } ?>>Commercial</option>
                    <option id='medical' value='medical'<?php if($comMedHide){ echo " hidden='true'"; } if ($role === 'medical'){ echo " selected"; } ?>>Medical</option>
                    <option id='admin' value='admin'<?php if(!$comMedHide){ echo " hidden='true'"; } if ($role === 'admin'){ echo " selected"; } ?>>Administration</option>
                    <option id='support' value='support'<?php if($supportHide){ echo " hidden='true'"; } if ($role === 'support'){ echo " selected"; } ?>>Support</option>
                </select>
            </div>
        </div>
        
        <?php
        
        if ($submitName == POST_ADD_USER)
        {
            echo "
        <br/>
        <div>
            <div>CC me on notification:<input type='checkbox' name='newUserCC'/></div>
        </div>
        <br/>
        <br/>";
        }
        
        if ($submitName == POST_MODIFY_ACCOUNT)
        {
            echo "
        <div class='subTitleDIV'>Photo:</div>
        <div id='imageAreaDIV'>
            <div><input id='imageFile' type='file' name='imageFile' accept='.png,.jpg,.jpeg'/></div>
            <div id='imageAreaDIV2'";
            
            if($user['imageURL'] == "")
            {
                echo " style='display:none;'";
            }
            
            echo ">
            <div class='imageFileName'>" . rawurldecode($user['imageURL']) . "</div>
                <div class='edit imageEdit fa'><label id='imageFileLabel1' for='imageFile'>&#xf044;</label></div>
            </div>
            <div id='imageAreaDIV3'";
            
            if($user['imageURL'] != "")
            {
                echo " style='display:none;'";
            }
            echo ">
                <div id='fileNameDIV' class='emptyListDIV'>No Photo uploaded</div>
                <label id='imageFileLabel2' class='button' for='imageFile'>Choose File</label>
            </div>
        </div>
        <input id='password' name='newPassword' hidden='true'/>";
        }
        
        if ($submitName == POST_MODIFY_USER)
        {
            echo "
        <input name='userID' hidden='true' value='" . $proxyUser['id'] . "'/>";
        }
        
        ?>
        
        <div>
            <div><input type='submit' name='<?php echo $submitName; ?>' value='Submit'/></div>
        </div>
    </div>
    
</form>

</div>
</div>

<script>
    
    $( "#newUserLevel" ).change(function()
    {
        if ($(this).val() == 1)
        {
            $("#newUserRole").val("commercial");
            $("#commercial").attr("hidden",false);
            $("#medical").attr("hidden",false);
            $("#admin").attr("hidden",true);
            $("#support").attr("hidden",true);
        }
        else if ($(this).val() == 2)
        {
            $("#newUserRole").val("admin");
            $("#commercial").attr("hidden",true);
            $("#medical").attr("hidden",true);
            $("#admin").attr("hidden",false);
            $("#support").attr("hidden",true);
        }
        else
        {
            $("#newUserRole").val("support");
            $("#commercial").attr("hidden",true);
            $("#medical").attr("hidden",true);
            $("#admin").attr("hidden",false);
            $("#support").attr("hidden",false);
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
        });
    });
    
    $('.passwordEdit').click(function()
    {
        var html = "<div id='resetPasswordDIV1'>";
        html += "<div id='resetPasswordDIV2' class='contentDIV'>";
        html += "<div class='pageTitleDIV'>Reset Password</div>";
        html += "<form>";
        html += "<div>";
        html += "<div class='formLabel'>New Password:</div>";
        html += "<div><input id='pw1' class='password' type='password' name='pw1'/></div>";
        html += "</div>";
        html += "<div>";
        html += "<div class='formLabel'>Re-Type Password:</div>";
        html += "<div><input id='pw2' class='password' type='password' name='pw2'/></div>";
        html += "</div>";
        html += "<input id='resetPassword' class='button' value='Reset'/>";
        html += "<input id='cancelPassword' class='button' value='Cancel'/>";
        html += "</div></form></div></div>";
        $(document.body).append(html);
        
        $('#resetPassword').click(function()
        {
            if ($('#pw1').val() !== $('#pw2').val())
            {
                window.alert("Password fields do not match!");
            }
            else
            {
                $('#password').val($('#pw1').val());
                $('#resetPasswordDIV1').remove();
            }
        });
        
        $('#cancelPassword').click(function()
        {
            $('#resetPasswordDIV1').remove();
        });
    });
    
    
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#addUser").validate(
        {
            rules:
            {
                newUserFirst:
                {
                    required: false,
                    minlength: 2,
                    maxlength: 20,
                    letterswithbasicpunc: true
                },
                newUserLast:
                {
                    required: false,
                    minlength: 2,
                    maxlength: 30,
                    letterswithbasicpunc: true
                },
                newUserPhone:
                {
                    required: false,
                    phoneUS: true,
                    minlength: 9,
                    maxlength: 30
                },
                newUserEmail:
                {
                    required: true,
                    email: true,
                    minlength: 2,
                    maxlength: 40
                },
                newUserTitle:
                {
                    required: false,
                    minlength: 2,
                    maxlength: 32
                },
                newUserLevel:
                {
                    required: true,
                    digits: true
                },
                newUserRole:
                {
                    required: true
                },
                imageFile:
                {
                    required: false,
                    accept: "image/*"
                },
                newUserCC:
                {
                    required: false
                }
            },
            messages:
            {

            }
        });
    });

</script>