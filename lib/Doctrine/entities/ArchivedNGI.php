<?php

/**
 * Keeps a record of deleted NGIs and some information about the deletion.
 *
 * @author George Ryall
 * @author David Meredith 
 * @Entity @Table(name="ArchivedNGIs")
 */
class ArchivedNGI {
   
    /** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;

    /*
     * Note, we define the entity attributes as simple types rather than linking 
     * to related entities because we need to record a history/log, including 
     * recordig information on entitites that may be deleted.  For exammple parent
     * project is stored as a string of the name. These record the name of 
     * that entityu on the day the service group was delted. Similary, we record
     * the user's DN rather than linking to the User object as that User may be
     * deleted in future.  
     */
    
    /**
     * DN of deleting user 
     * @Column(type="string", nullable=false) */
    protected $deletedBy; 
 
    /* DATETIME NOTE:
	 * Doctrine checks whether a date's been updated by doing a byreference comparison.
	 * If you just update an existing DateTime object, Doctrine won't persist it!
	 * Create a new DateTime object and reference that for it to persist during an update.
	 * http://docs.doctrine-project.org/en/2.0.x/cookbook/working-with-datetime.html
	 */
    
    /** @Column(type="datetime", nullable=false) **/
    protected $deletedDate; 

    /**
     * Name of NGI 
     * @Column(type="string", nullable=false) */
    protected $name; 
    
    /**
     * Scopes applied to the NGI at the time it was deleted
     * @Column(type="string", nullable=true) */
    protected $scopes = null;
    
    /**
     * Name of parent projects
     * @Column(type="string", nullable=true) */
    protected $parentProjects = null;
    
   /** @Column(type="datetime", nullable=false) **/
    protected $originalCreationDate; 

    public function __construct() {
        $this->deletedDate =  new \DateTime("now");
	}

    public function getId() {
        return $this->id;
    }

    public function getDeletedBy() {
        return $this->deletedBy;
    }

    public function getDeletedDate() {
        return $this->deletedDate;
    }

    public function getName() {
        return $this->name;
    }

    public function getScopes() {
        return $this->scopes;
    }

    public function getParentProjects() {
        return $this->parentProject;
    }

    public function getOriginalCreationDate() {
        return $this->originalCreationDate;
    }

    public function setDeletedBy($deletedBy) {
        $this->deletedBy = $deletedBy;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setScopes($scopesOnDeletion) {
        $this->scopes = $scopesOnDeletion;
    }

    public function setParentProjects($parentProject) {
        $this->parentProject = $parentProject;
    }

    public function setOriginalCreationDate($originalCreationDate) {
        $this->originalCreationDate = $originalCreationDate;
    }


}