<?php
/**
 * Class GlobalsVar
 *
 * Clase que genera todas las variables globales dinamicas de al app, esas pueden ser de entornos o locales.
 *
 * @package	ley18450v3
 * @author	CNR
 * @copyright	CNR
 * @link	https://www.cnr.cl
 * @since	Version 1.0.0
 * @version 1.0
 * @filesource
 */
class GlobalsVarSetEnv{

    private $nameSiteOfSystems            = "";
    private $nameSiteOfOldSystems         = "";
    private $nodoWSer                     = "";
    private $restAuthUser                 = "";
    private $environment                  = "";
    private $dbUser                       = "";
    private $dbPass                       = "";
    private $dbInstance                   = "";
    private $dbPort                       = "";
    private $dbUrl                        = "";
    private $uploadPathFolder             = "";
    private $originSia                    = "";
    private $digitalURL                   = "";
    private $pathDigital                  = "";
    private $userDigital                  = "";
    private $passDigital                  = "";

    /**
     * Constructor
     *
     * @author	CNR
     * @since	Version 1.0.0
     * @version 1.0
     * @param	N/A
     * @return	void
     */
    public function __construct(){

    }
    
    /**
     * @return the $nameSiteOfSystems
     */
    public function getOriginSia()
    {
        return $this->originSia = getenv("ORIGINSIA");
    }

    /**
     * @return the $nameSiteOfSystems
     */
    public function getUploadPathFolder()
    {
        return $this->uploadPathFolder = getenv("UPLOADPATHFOLDER");
    }

    /**
     * @return the $nameSiteOfSystems
     */
    public function getNameSiteOfSystems()
    {
        return $this->nameSiteOfSystems = getenv("SIAWEB");
    }

    /**
     * @return the $nameSiteOfOldSystems
     */
    public function getNameSiteOfOldSystems()
    {
        return $this->nameSiteOfOldSystems = getenv("SIACONVIVENCIA");
    }

    /**
     * @return the $nodoWSer
     */
    public function getNodoWSer()
    {
        return $this->nodoWSer = getenv("SIASERVICIOS");
    }

    /**
     * @return the $digitalURL
     */
    public function getUrlDigitalizacion()
    {
        return $this->digitalURL = getenv("DIGITALURL");
    }

    /**
     * @return the $pathDigital
     */
    public function getPathDigitalizacion()
    {
        return $this->pathDigital = getenv("PATHDIGITAL");
    }

    /**
     * @return the $userDigital
     */
    public function getUserDigitalizacion()
    {
        return $this->userDigital = getenv("USERDIGITAL");
    }

    /**
     * @return the $passDigital
     */
    public function getPassDigitalizacion()
    {
        return $this->passDigital = getenv("PASSDIGITAL");
    }

    /**
     * @return the $restAuthUser
     */
    public function getRestAuthUser()
    {
        return $this->restAuthUser = 'Basic '.base64_encode(getenv("RESTUSER").":".getenv("RESTPASSWORD"));
    }

    /**
     * @return the $environment
     */
    public function getEnvironment()
    {
        return $this->environment = getenv("ENVIRONMENT");
    }

    /**
     * @return the $dbUser
     */
    public function getDbUser()
    {
        return $this->dbUser = getenv("DBUSER");
    }

    /**
     * @return the $dbPass
     */
    public function getDbPass()
    {
        return $this->dbPass = getenv("DBPASS");
    }

    /**
     * @return the $dbInstance
     */
    public function getDbInstance()
    {
        return $this->dbInstance = getenv("DBINSTANCE");
    }

    /**
     * @return the $dbPort
     */
    public function getDbPort()
    {
        return $this->dbPort = getenv("DBPORT");
    }

    /**
     * @return the $dbUrl
     */
    public function getDbUrl()
    {
        return $this->dbUrl = getenv("DBURL");
    }
}