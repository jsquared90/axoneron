<div id="registrationContainerDIV" class='mainDIV'>
    
<?php echo getBGDiv(); ?>

<!-- need to add jQuery validation -->

<div class='contentDIV'>
    <div class='pageTitleDIV'>Complete Registration :</div>
    <form name='validateUserRegistration' id='validateUserRegistration' onSubmit = 'return validate(this)' method='post' action='<?php echo HOME; ?>'>
        
        <div>
            <div class='formLabel'>First Name :*</div>
            <div><input type='text' name='first' value='<?php echo cleanForValueField($user['first']); ?>'/></div>
        </div>
        
        <div>
            <div class='formLabel'>Last Name :*</div>
            <div><input type='text' name='last' value='<?php echo cleanForValueField($user['last']); ?>'/></div>
        </div>
        
        <div>
            <div class='formLabel'>Password :*</div>
            <div><input class='password' type='password' name='pw1' id='pw1' /></div>
        </div>
        
        <div>
            <div class='formLabel'>Retype Password :*</div>
            <div><input class='password' type='password' name='pw2' id='pw2' /></div>
        </div>
        
        <div>
            <div class='formLabel'>Phone :</div>
            <div><input type='tel' name='phone' size='20' value='<?php echo $user['phone']; ?>'/></div>
        </div>
        
        <div>
            <div class='formLabel'>Title :*</div>
            <div><input type='text' name='title' value='<?php echo cleanForValueField($user['title']); ?>'/></div>
        </div>
        
        <div class='selectDIV'>
            <div class='formLabel'>Role :*</div>
            <select id='role' name='role' value='<?php echo $user['role']; ?>'>
                
                
                <?php
                
                if ($user['level'] > 2)
                {
                    $selected = $user['role'] == "admin" ? " selected" : "";
                    echo "<option id='admin' value='admin'" . $selected . ">Administration</option>";
                    $selected = $user['role'] == "support" ? " selected" : "";
                    echo "<option id='support' value='support'" . $selected . ">Support</option>";
                }
                else if ($user['level'] > 1)
                {
                    echo "<option id='admin' value='admin'>Administration</option>";
                }
                else
                {
                    $selected = $user['role'] == "commercial" ? " selected" : "";
                    echo "<option id='commercial' value='commercial'" . $selected . ">Commercial</option>";
                    $selected = $user['role'] == "medical" ? " selected" : "";
                    echo "<option id='medical' value='medical'" . $selected . ">Medical</option>";
                }
                
                ?>
                
            </select>
        </div>
        
        <input name='id' hidden='true' type='number' value='<?php echo $user['id']; ?>'/>
        
        <br/>
        <input type='submit' name='<?php echo POST_REGISTER_USER; ?>' value='Submit' />
    </form>
</div>

</div>

<script>
    
    //Validate function
    $(document).ready(function()
    {
        //window.alert("we have successfully called this function");
        $("#validateUserRegistration").validate(
        {
            rules:
            {
                first:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 20,
                    letterswithbasicpunc: true
                },
                last:
                {
                    required: true,
                    minlength: 2,
                    maxlength: 30,
                    letterswithbasicpunc: true
                },
                pw1:
                {
                    required: true,
                    minlength: 6,
                    maxlength: 64
                },
                pw2:
                {
                    required: true,
                    equalTo: '#pw1'
                },
                phone:
                {
                    required: false,
                    minlength: 9,
                    maxlength: 16,
                    phoneUS: true
                },
                title:
                {
                    required: true,
                    maxlength: 32
                },
                role:
                {
                    required: true,
                    maxlength: 20
                }
            },
            messages:
            {

            }
        });
    });
    
</script>

