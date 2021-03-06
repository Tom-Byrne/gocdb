<div class="rightPageContainer">
    <div style="float: left;">
        <img src="<?php echo \GocContextPath::getPath()?>img/ngi/fullSize/<?php echo $params['ngi']->getName() ?>.jpg" class="pageLogo" />
    </div>
    <div style="float: left;">
        <h1 style="float: left; margin-left: 0em;">
                NGI: <?php xecho($params['ngi']->getName()) ?>
        </h1>
        <span style="clear: both; float: left; padding-bottom: 0.4em;">
            <?php xecho($params['ngi']->getDescription()) ?>
            <br /><br />
            <a style="padding-top: 0.2em;" href="http://www.egi.eu/about/glossary/glossary_N.html">What is an NGI?</a>
        </span>
    </div>

    <!--  Edit NGI link -->
    <!--  only show this link if we're in read / write mode -->
   <?php if(!$params['portalIsReadOnly']):?>
        <div style="float: right;">
            <?php if($params['UserIsAdmin']):?>
                <div style="float: right; margin-left: 2em; text-align:center;">
                    <script type="text/javascript" src="<?php echo \GocContextPath::getPath()?>javascript/confirm.js"></script>
                    <a onclick="return confirmSubmit()" 
                       href="index.php?Page_Type=Admin_Delete_NGI&id=<?php echo $params['ngi']->getId() ?>">
                        <img src="<?php echo \GocContextPath::getPath()?>img/cross.png" height="25px" />
                        <br />
                        <span>Admin<br>Delete</span>
                    </a>
                </div>
            <?php endif; ?>
            <?php if($params['ShowEdit']):?>
                <div style="float: right; margin-left: 2em;">
                    <a href="index.php?Page_Type=Edit_NGI&id=<?php echo $params['ngi']->getId() ?>">
                        <img src="<?php echo \GocContextPath::getPath()?>img/pencil.png" height="25px" style="float: right;" />
                        <br />
                        <br />
                        <span>Edit</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- NGI Contacts & Projects -->
    <div style="float: left; width: 100%; margin-top: 2em;">
        <!--  Contacts (left) -->
        <div class="tableContainer" style="width: 55%; float: left;">
            <span class="header" style="vertical-align:middle; float: left; padding-top: 0.9em; padding-left: 1em;">Contacts</span>
            <img src="<?php echo \GocContextPath::getPath()?>img/contact_card.png" class="titleIcon"/>
                <table style="clear: both; width: 100%; table-layout: fixed;">
                    <tr class="site_table_row_1">
                        <td class="site_table" style="width: 30%">E-Mail</td><td class="site_table">
                            <?php if($params['authenticated']) { ?>
                            <a href="mailto:<?php xecho($params['ngi']->getEmail()) ?>">
                                <?php xecho($params['ngi']->getEmail()) ?>
                            </a>
                            <?php } else {echo('PROTECTED - Auth Required');} ?>
                        </td>
                    </tr>
                    <tr class="site_table_row_2">
                        <td class="site_table" style="width: 30%">ROD E-Mail</td><td class="site_table">
                            <?php if($params['authenticated']) { ?>
                            <a href="mailto:<?php xecho($params['ngi']->getRodEmail()) ?>">
                            <?php xecho($params['ngi']->getRodEmail()) ?>
                            </a>
                            <?php } else {echo('PROTECTED - Auth Required');} ?>
                        </td>
                    </tr>
                    <tr class="site_table_row_1">
                        <td class="site_table" style="width: 30%">Helpdesk E-Mail</td><td class="site_table">
                            <?php if($params['authenticated']) { ?>
                            <a href="mailto:<?php xecho($params['ngi']->getHelpdeskEmail()) ;?>">
                            <?php xecho($params['ngi']->getHelpdeskEmail()) ?>
                            </a>
                            <?php } else {echo('PROTECTED - Auth Required');} ?>
                        </td>
                    </tr>
                    <tr class="site_table_row_2">
                        <td class="site_table" style="width: 30%">Security E-Mail</td><td class="site_table">
                            <?php if($params['authenticated']) { ?>
                            <a href="mailto:<?php echo $params['ngi']->getSecurityEmail() ?>">
                            <?php xecho($params['ngi']->getSecurityEmail()) ?>
                            </a>
                            <?php } else {echo('PROTECTED - Auth Required');} ?>
                        </td>
                    </tr>
                    <tr class="site_table_row_1">
                        <td class="site_table" style="width: 30%">GGUS Support Unit</td><td class="site_table">
                            <?php if($params['authenticated']) { ?>
                            <?php xecho($params['ngi']->getGgus_Su()) ?>
                            <?php } else {echo('PROTECTED - Auth Required');} ?>
                        </td>
                    </tr>
                </table>
        </div>
        
        <!-- Project memberships (Top right) -->
        <div class="tableContainer" style="width: 42%; float: right;" >
            <span class="header" style="vertical-align:middle; float: left; padding-top: 0.9em; padding-left: 1em;">Project memberships</span>
            <img src="<?php echo \GocContextPath::getPath()?>img/project.png" class="titleIcon"/>
            <table style="clear: both; width: 100%; table-layout: fixed;">
                <?php if(!empty($params['Projects'])) {
                        $num = 1; 
                        foreach($params['Projects'] as $project) { ?>
                        <tr class="site_table_row_<?php echo $num ?>">
                            <td class="site_table">
                                <a href="index.php?Page_Type=Project&id=<?php echo $project->getId()?>"><?php xecho($project->getName())?></a>
                            </td>
                        </tr>
                        <?php if($num == 1) { $num = 2; } else { $num = 1; } } ?>
                    
                <?php } ?>
            </table>
        </div>
       
        <!-- Scopes (bottom right) -->
        <div class="tableContainer" style="width: 42%; float: right; margin-top: 1.6em" >
            <span class="header" style="vertical-align:middle; float: left; padding-top: 0.9em; padding-left: 1em; word-wrap: normal">Scope(s)</span>
            <table style="clear: both; width: 100%; table-layout: fixed;">

                        <tr class="site_table_row_1">
                            <td class="site_table" >
                                <span style="float: left;">
                                    <input type="text" value="<?php echo $params['ngi']->getScopeNamesAsString() ?>" readonly>
                                </span>
                                <span style="float: right">
                                    <a href="index.php?Page_Type=Scope_Help">?</a>&nbsp
                                </span>
                            </td>
                            
                        </tr>
            </table>
        </div>
        
    </div>


    <!--  Sites -->
    <div class="listContainer">
        <span class="header listHeader">
            <?php echo sizeof($params['ngi']->getSites()) ?> Site<?php if(sizeof($params['ngi']->getSites()) != 1) echo "s"?>
        </span>
        <img src="<?php echo \GocContextPath::getPath()?>img/site.png" class="decoration" />
        <table class="vSiteResults" id="selectedSETable">
            <tr class="site_table_row_1">
                <th class="site_table">Name</th>
                <th class="site_table">Certification Status</th>
                <th class="site_table">Production Status</th>
                <th class="site_table"><a href="index.php?Page_Type=Scope_Help">Scope(s)</a></th>
            </tr>
            <tr>
              <td  colspan="4"> 
                  Note, Scope values marked with (x) indicate the parent NGI does not share that scope 
              </td>
            </tr>
            <?php
            $num = 2;
            if(sizeof($params['ngi']->getSites()) > 0) {
                foreach($params['SitesAndScopes'] as $siteAndScopes) {
                    $site = $siteAndScopes['Site'];
                    $scopes=$siteAndScopes['Scopes'];
            ?>
                <tr class="site_table_row_<?php echo $num ?>">
                    <td class="site_table" style="width: 30%">
                        <div style="background-color: inherit;">
                            <span style="vertical-align: middle;">
                                <a href="index.php?Page_Type=Site&id=<?php echo $site->getId() ?>">
                                    <span>&nbsp;&nbsp;</span><?php xecho($site->getShortName()); ?>
                                </a>
                            </span>
                        </div>
                    </td>

                    <td class="site_table">
                        <?php xecho($site->getCertificationStatus()->getName()) ?>
                    </td>

                    <td class="site_table">
                        <?php xecho($site->getInfrastructure()->getName()) ?>
                    </td>
                     <td class="site_table">
                                 <?php
                                 $count = 0;
                                 $numScopes = sizeof($scopes);
                                 $scopeString = ''; 
                                 foreach ($scopes as $scopeName => $sharedWithParent) {
                                     if ($sharedWithParent) {
                                         $scopeString .= $scopeName;
                                     } else {
                                         $scopeString .= $scopeName . '(x)';
                                     }
                                     if (++$count != $numScopes) {
                                         $scopeString .= ", ";
                                     }
                                 } ?>   
                          <input type="text" value="<?php xecho($scopeString); ?>" readonly>
                    </td>

                    
                </tr>
                <?php
                    if($num == 1) { $num = 2; } else { $num = 1; }
                    } // End of the foreach loop iterating over sites
            }
            ?>
        </table>
    </div>

    <!--  Users and Roles -->
    <div class="listContainer">
        <span class="header listHeader">
           Users (Click on name to manage roles) 
        </span>
        <img src="<?php echo \GocContextPath::getPath()?>img/user.png" class="decoration" />
        <table class="vSiteResults" id="selectedSETable">
            <tr class="site_table_row_1">
                <th class="site_table">Name</th>
                <th class="site_table">Role</th>
            </tr>
            <?php
            $num = 2;
            if(sizeof($params['roles']) > 0) {
                foreach($params['roles'] as $role) {
                ?>
                <tr class="site_table_row_<?php echo $num ?>">
                    <td class="site_table" style="width: 30%">
                        <div style="background-color: inherit;">
                            <span style="vertical-align: middle;">
                                <?php if($params['authenticated']) { ?>
                                <a href="index.php?Page_Type=User&id=<?php echo $role->getUser()->getId() ?>">
                                    <span>&nbsp;&nbsp;</span><?php xecho($role->getUser()->getFullName())/*.' ['.$role->getUser()->getId().']' */?>
                                </a>
                                <?php } else {echo 'PROTECTED'; } ?>
                            </span>
                        </div>
                    </td>

                    <td class="site_table" style="width: 30%">
                        <div style="background-color: inherit;">
                            <span style="vertical-align: middle;">
                                <?php if($params['authenticated']) { ?>
                                <span>&nbsp;&nbsp;</span><?php xecho($role->getRoleType()->getName())?>
                                <?php } else {echo 'PROTECTED'; } ?>
                            </span>
                        </div>
                    </td>
                </tr>
                <?php
                    if($num == 1) { $num = 2; } else { $num = 1; }
                    } // End of the foreach loop iterating over sites
            }
            ?>
        </table>
        <!-- Don't show role request in read only mode -->
        <?php if(!$params['portalIsReadOnly'] && $params['authenticated']):?>
            <div style="padding: 1em; padding-left: 1.4em; overflow: hidden;">
                <a href="index.php?Page_Type=Request_Role&id=<?php echo $params['ngi']->getId();?>">
                    <img src="<?php echo \GocContextPath::getPath()?>img/add.png" height="20px" style="float: left; vertical-align: middle; padding-right: 1em;">
                    <span class="header" style="vertical-align:middle; float: left; padding-top: 0.2em;">
                            Request Role
                    </span>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Show RoleActionRecords if user has permissions over this NGI -->
    <?php if ($params['ShowEdit']){
        require_once __DIR__ . '/../fragments/viewRoleActionsTable.php'; 
    } ?>
    

</div>