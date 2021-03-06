<?php
namespace org\gocdb\services;

/*
 * Copyright © 2011 STFC Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0 Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
*/
require_once __DIR__ . '/QueryBuilders/ExtensionsQueryBuilder.php';
require_once __DIR__ . '/QueryBuilders/ExtensionsParser.php';
require_once __DIR__ . '/QueryBuilders/ScopeQueryBuilder.php';
require_once __DIR__ . '/QueryBuilders/ParameterBuilder.php';
require_once __DIR__ . '/QueryBuilders/Helpers.php';
require_once __DIR__ . '/IPIQuery.php';


/** 
 * Return an XML document that encodes the site contacts.
 * Optionally provide an associative array of query parameters with values 
 * used to restrict the results. Only known parameters are honoured while 
 * unknown params produce an error doc.
 * <pre>
 * 'sitename', 'roc', 'country', 'roletype', 'scope', 'scope_match' 
 * (where scope refers to Site scope) 
 * </pre>
 * 
 * @author James McCarthy
 * @author David Meredith 
 */
class GetSiteContacts implements IPIQuery{
    protected $query;
    protected $validParams;
    protected $em;
    private $helpers;
    private $roleT;
    private $sites;
    
    /** Constructor takes entity manager which is then used by the
     *  query builder
     *
     * @param EntityManager $em
     */
    public function __construct($em){
        $this->em = $em;
        $this->helpers=new Helpers();
    }
    
    /** Validates parameters against array of pre-defined valid terms
     *  for this PI type
     * @param array $parameters
     */
    public function validateParameters($parameters){
    
        // Define supported parameters and validate given params (die if an unsupported param is given)
        $supportedQueryParams = array (
                'sitename',
                'roc',
                'country',
                'roletype',
                'scope',
                'scope_match'
        );
    
        $this->helpers->validateParams ( $supportedQueryParams, $parameters );
        $this->validParams = $parameters;
    }
    
    /** Creates the query by building on a queryBuilder object as
     *  required by the supplied parameters
     */
    public function createQuery() {
        $parameters = $this->validParams;
        $binds= array();
        $bc=-1;
    
        $qb = $this->em->createQueryBuilder();
    
        //Initialize base query
		$qb	->select('s', 'n', 'r', 'u', 'rt')
		->from('Site', 's')		
		->leftJoin('s.ngi', 'n')
		->leftJoin('s.country', 'c')		
        ->leftJoin('s.roles', 'r')        
        ->leftJoin('r.user', 'u')        
        ->leftJoin('r.roleType', 'rt')        
		->orderBy('s.shortName', 'ASC');
    
        /**This is used to filter the reults at the point
         * of building the XML to only show the correct roletypes.
        * Future work could see this build into the query.
        */
        if(isset($parameters['roletype'])) {
            $this->roleT = $parameters['roletype'];
        } else {
            $this->roleT = '%%';
        }
    
        /*Pass parameters to the ParameterBuilder and allow it to add relevant where clauses
         * based on set parameters.
        */
        $parameterBuilder = new ParameterBuilder($parameters, $qb, $this->em, $bc);
        //Get the result of the scope builder
        $qb = $parameterBuilder->getQB();
        $bc = $parameterBuilder->getBindCount();
        //Get the binds and store them in the local bind array - only runs if the returned value is an array
        foreach((array)$parameterBuilder->getBinds() as $bind){
            $binds[] = $bind;
        }
    
    
        //Run ScopeQueryBuilder regardless of if scope is set.
        $scopeQueryBuilder = new ScopeQueryBuilder(
                (isset($parameters['scope'])) ? $parameters['scope'] : null,
                (isset($parameters['scope_match'])) ? $parameters['scope_match'] : null,
                $qb,
                $this->em,
                $bc,
                'Site',
                's'
        );
    
        //Get the result of the scope builder
        $qb = $scopeQueryBuilder->getQB();
        $bc = $scopeQueryBuilder->getBindCount();
    
        //Get the binds and store them in the local bind array only if any binds are fetched from scopeQueryBuilder
        foreach((array)$scopeQueryBuilder->getBinds() as $bind){
            $binds[] = $bind;
        }
    
        //Bind all variables
        $qb = $this->helpers->bindValuesToQuery($binds, $qb);
    
        //Get the dql query from the Query Builder object
        $query = $qb->getQuery();
    
        $this->query = $query;
        return $this->query;
    }
    
    /**
     * Executes the query that has been built and stores the returned data
     * so it can later be used to create XML, Glue2 XML or JSON.
     */
    public function executeQuery(){
        $this->sites = $this->query->execute();
        return $this->sites;
    }
	
	/** Returns proprietary GocDB rendering of the site contacts data 
	 *  in an XML String
	 * @return String
	 */
	public function getXML(){
		$helpers = $this->helpers;
	
		$sites = $this->sites;
		$xml = new \SimpleXMLElement("<results />");
		foreach ( $sites as $site ) {
			$xmlSite = $xml->addChild ( 'SITE' );
			$xmlSite->addAttribute ( 'ID', $site->getId () . "G0" );
			$xmlSite->addAttribute ( 'PRIMARY_KEY', $site->getPrimaryKey () );
			$xmlSite->addAttribute ( 'NAME', $site->getShortName () );
			
			$xmlSite->addChild ( 'PRIMARY_KEY', $site->getPrimaryKey () );
			$xmlSite->addChild ( 'SHORT_NAME', $site->getShortName () );
			foreach ( $site->getRoles () as $role ) {
				if ($role->getStatus () == "STATUS_GRANTED") { // Only show users who are granted the role, not pending
					
					$rtype = $role->getRoleType ()->getName ();
					if ($this->roleT == '%%' || $rtype == $this->roleT) {
						$user = $role->getUser ();
						$xmlContact = $xmlSite->addChild ( 'CONTACT' );
						$xmlContact->addAttribute ( 'USER_ID', $user->getId () . "G0" );
						$xmlContact->addAttribute ( 'PRIMARY_KEY', $user->getId () . "G0" );
						$xmlContact->addChild ( 'FORENAME', $user->getForename () );
						$xmlContact->addChild ( 'SURNAME', $user->getSurname () );
						$xmlContact->addChild ( 'TITLE', $user->getTitle () );
						$xmlContact->addChild ( 'EMAIL', $user->getEmail () );
						$xmlContact->addChild ( 'TEL', $user->getTelephone () );
						$xmlContact->addChild ( 'CERTDN', $user->getCertificateDn () );
						$xmlContact->addChild ( 'ROLE_NAME', $role->getRoleType ()->getName () );
					}
				}
			}
		}

		$dom_sxe = dom_import_simplexml($xml);
		$dom = new \DOMDocument('1.0');
		$dom->encoding='UTF-8';
		$dom_sxe = $dom->importNode($dom_sxe, true);
		$dom_sxe = $dom->appendChild($dom_sxe);
		$dom->formatOutput = true;
		$xmlString = $dom->saveXML();
		return $xmlString;
	}
	
	/** Returns the site contact data in Glue2 XML string.
	 * 
	 * @return String
	 */
	public function getGlue2XML(){
	
	}
	
	/** Not yet implemented, in future will return the site contact
	 *  data in JSON format
	 * @throws LogicException
	 */
	public function getJSON(){
		$query = $this->query;		
		throw new LogicException("Not implemented yet");
	}
}