<script type="text/javascript" src="<?php echo \GocContextPath::getPath()?>javascript/confirm.js"></script>
<!-- onclick="return confirmSubmit()" -->
<div class="rightPageContainer">
    <div style="float: left; text-align: center;">
        <img src="<?php echo \GocContextPath::getPath()?>img/user.png" class="pageLogo" />
    </div>
    <div style="float: left;">
        <h1 style="float: left; margin-left: 0em; padding-bottom: 0.3em;">
            <?php xecho($params['user']->getFullName()) ?>
        </h1>
    </div>

    <!--  Edit User link -->
    <!--  only show this link if we're in read / write mode -->
    <?php
    if(!$params['portalIsReadOnly']) {
    ?>
    <div style="float: right;">
        <?php if($params['ShowEdit']):?>
            <div style="float: right; margin-left: 2em;">
                <a href="index.php?Page_Type=Edit_User&id=<?php echo $params['user']->getId()?>">
                    <img src="<?php echo \GocContextPath::getPath()?>img/pencil.png" height="25px" style="float: right;" />
                    <br />
                    <br />
                    <span>Edit</span>
                </a>
            </div>
            <div style="float: right;">
                <script type="text/javascript" src="<?php echo \GocContextPath::getPath()?>javascript/confirm.js"></script>
                <a onclick="return confirmSubmit()"
                    href="index.php?Page_Type=Delete_User&id=<?php echo $params['user']->getId() ?>">
                    <img src="<?php echo \GocContextPath::getPath()?>img/cross.png" height="25px" style="float: right; margin-right: 0.4em;" />
                    <br />
                    <br />
                    <span>Delete</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
    }
    ?>

    <!-- User Data -->
    <div style="float: left; width: 100%; margin-top: 2em;">
        <!--  User -->
        <div class="tableContainer" style="width: 55%; float: left;">
            <span class="header" style="vertical-align:middle; float: left; padding-top: 0.9em; padding-left: 1em;">User Details</span>
            <img src="<?php echo \GocContextPath::getPath()?>img/contact_card.png" height="25px" style="float: right; padding-right: 1em; padding-top: 0.5em; padding-bottom: 0.5em;" />
            <table style="clear: both; width: 100%; table-layout: fixed;">
                <tr class="site_table_row_1">
                    <td class="site_table" style="width: 30%">E-Mail</td><td class="site_table">
						<a href="mailto:<?php xecho($params['user']->getEmail());?>">
							<?php xecho($params['user']->getEmail()) ?>
						</a>
					</td>
                </tr>
                <tr class="site_table_row_2">
                    <td class="site_table">Telephone</td>
                    <td class="site_table">
                        <?php xecho($params['user']->getTelephone()) ?>
                    </td>
                </tr>
                <tr class="site_table_row_1">
                    <td class="site_table">Identity String</td>
                    <td class="site_table">
                    	<div style="word-wrap: break-word;">
                        	<?php xecho($params['user']->getCertificateDn()) ?>
                        </div>
                    </td>
                </tr>
                <!-- Comment out for now -->
                <!--<tr class="site_table_row_2">
                    <td class="site_table">EGI SSO Username</td>
                    <td class="site_table">
                    	<div style="word-wrap: break-word;">
                        	<?php 
                            //if($params['user']->getusername1() != null){
                            //    echo  'Should this be shown? - TBC'; //$params['user']->getusername1(); 
                            //} else {
                            //    echo 'Not known'; 
                            //}
                            ?>
                        </div>
                    </td>
                </tr>-->
                <?php if(sizeof($params['user']->getHomeSite()) != 0) { ?>
                    <tr class="site_table_row_2">
                        <td class="site_table">Home Site</td>
                        <td class="site_table">
                            <a href="index.php?Page_Type=Site&id=<?php echo $params['user']->getHomeSite()->getId()?>">
                                <?php xecho($params['user']->getHomeSite()->getShortName()) ?>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>


    
    <div class="listContainer">
        <b>Authentication Attributes:</b>
        <br>
        <?php
        foreach ($params['authAttributes'] as $key => $val) {
            $attributeValStr = '';
            foreach ($val as $v) {
                $attributeValStr .= ', '.$v;
            }
            if(strlen($attributeValStr) > 2){$attributeValStr = substr($attributeValStr, 2);}
            xecho('[' . $key . ']  [' . $attributeValStr . ']');
            echo '<br>';
        }
        ?>
    </div>


    <!-- Roles -->
    <div class="listContainer" style="width: 99.5%; float: left; margin-top: 3em; margin-right: 10px;">
        <span class="header" style="vertical-align:middle; float: left; padding-top: 0.9em; padding-left: 1em;">Roles</span>
        <img src="<?php echo \GocContextPath::getPath()?>img/people.png" height="25px" style="float: right; padding-right: 1em; padding-top: 0.5em; padding-bottom: 0.5em;" />
        <table style="clear: both; width: 100%;">
            <tr class="site_table_row_1">
                <th class="site_table">Role Type <!--[roleId] --></th>
                <th class="site_table">Held Over</th>
                <?php if(!$params['portalIsReadOnly']):?>
                    <th class="site_table">Revoke Role</th>
                <?php endif; ?>
            </tr>
            <?php
            $num = 2;
            foreach($params['roles'] as $role) {
            ?>
            <tr class="site_table_row_<?php echo $num ?>">
                <td class="site_table" style="width: 40%">
                    <div style="background-color: inherit;">
                        <img src="<?php echo \GocContextPath::getPath()?>img/person.png" style="vertical-align: middle; padding-right: 1em;" />
                        	<?php xecho($role->getRoleType()->getName())/*.' ['.$role->getId().']'*/ ?>
                    </div>
                </td>
                <td class="site_table">
                    <?php
                    if($role->getOwnedEntity() instanceof \Site) {?>
                        <a style="vertical-align: middle;" href="index.php?Page_Type=Site&id=<?php echo $role->getOwnedEntity()->getId()?>">
                        <?php xecho($role->getOwnedEntity()->getShortName().' [Site]')?>
                    </a>
                    <?php } ?>

                    <?php
                    if($role->getOwnedEntity() instanceof \NGI) {?>
                        <a style="vertical-align: middle;" href="index.php?Page_Type=NGI&id=<?php echo $role->getOwnedEntity()->getId()?>">
                            <?php xecho($role->getOwnedEntity()->getName().' [NGI]')?>
                        </a>
                    <?php } ?>

                    <?php
                    if($role->getOwnedEntity() instanceof \ServiceGroup) {?>
                        <a style="vertical-align: middle;" href="index.php?Page_Type=Service_Group&id=<?php echo $role->getOwnedEntity()->getId()?>">
                            <?php xecho($role->getOwnedEntity()->getName().' [ServiceGroup]')?>
                        </a>
                    <?php } ?>

                    <?php
                    if($role->getOwnedEntity() instanceof \Project) {?>
                       <a style="vertical-align: middle;" href="index.php?Page_Type=Project&id=<?php echo $role->getOwnedEntity()->getId()?>">
                           <?php xecho($role->getOwnedEntity()->getName().' [Project]');?>
                       </a>
                    <?php } ?>
                </td>
                <td class="site_table">
                    <?php if(!$params['portalIsReadOnly'] && $role->getDecoratorObject() != null):?>
                        <form action="index.php?Page_Type=Revoke_Role" method="post"> 
                            <input type="hidden" name="id" value="<?php echo $role->getId()?>" /> 
                            <input id="revokeButton" type="submit" value="Revoke" class="btn btn-sm btn-danger" onclick="return confirmSubmit()" 
                                   title="Your roles allowing revoke: <?php xecho($role->getDecoratorObject()); ?>" >
                        </form> 
                    <?php endif;?>
                </td>
                    
            </tr>
            <?php
            if($num == 1) { $num = 2; } else { $num = 1; }
            }
            ?>
        </table>
    </div>

</div>

 <script type="text/javascript">
    //$(document).ready(function() {
    //    $('#revokeButton').tooltip(); 
    //}); 
</script>