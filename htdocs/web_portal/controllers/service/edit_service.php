<?php
/*______________________________________________________
 *======================================================
 * Author: John Casson, George Ryall, David Meredith
 * Description: Processes an edit service request from the web portal.
 *
 * License information
 *
 * Copyright 2009 STFC
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 /*======================================================*/
require_once __DIR__ . '/../../../../lib/Gocdb_Services/Factory.php';
require_once __DIR__ . '/../../../../htdocs/web_portal/components/Get_User_Principle.php';
require_once __DIR__ . '/../utils.php';

/**
 * Controller for an edit service request
 * @global array $_POST only set if the browser has POSTed data
 * @return null
 */
function edit_service() {
    $dn = Get_User_Principle();
    $user = \Factory::getUserService()->getUserByPrinciple($dn);

    //Check the portal is not in read only mode, returns exception if it is and user is not an admin
    checkPortalIsNotReadOnlyOrUserIsAdmin($user);
    
    if($_POST) {     // If we receive a POST request it's for a new site
		submit($user);
	} else { // If there is no post data, draw the edit site form
		draw($user);
	}
}

/**
 * Processes an edit service request. 
 * @param \User $user Current user
 * @return null
 */
function submit(\User $user = null) {
    $serv = \Factory::getServiceService();
    $newValues = getSeDataFromWeb();
    $se = $serv->getService($newValues['ID']);
    $se = $serv->editService($se, $newValues, $user);
    $params = array('se' => $se);
    show_view('service/service_updated.php', $params);
}

/**
 * Draw the edit site form. 
 * @param \User $user
 * @throws \Exception
 */
function draw(\User $user = null) {
    if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id']) ){
        throw new Exception("An id must be specified");
    }
	$id = $_REQUEST['id'];
	$serv = \Factory::getServiceService();
	$se = $serv->getService($id);

	if(count(Factory::getServiceService()->authorizeAction(\Action::EDIT_OBJECT, $se, $user)) == 0){
       throw new \Exception("You do not have permission over $se."); 
    }
    
    $configservice = \Factory::getConfigService();
    
    //get parent scope ids to generate warning message in view
    $params["parentScopeIds"] = array();
    foreach ($se->getParentSite()->getScopes() as $scope){
        $params["parentScopeIds"][]=$scope->getId();
    }
	$params['se'] = $se;
	$params['serviceTypes'] = $serv->getServiceTypes();
	$params['scopes'] = \Factory::getScopeService()->getScopesSelectedArray($se->getScopes());
    $params['numberOfScopesRequired'] = $configservice->getMinimumScopesRequired('service');
    
	show_view('service/edit_service.php', $params);
}


?>