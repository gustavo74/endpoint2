<?php
require_once("GlobalsVarSetEnv.class.php");

/**
 * Class GlobalsVarLocal
 *
 * Clase que genera todas las variables globales estaticas de al app.
 *
 * @package	ley18450v3
 * @author	CNR
 * @copyright	CNR
 * @link	https://www.cnr.cl
 * @since	Version 1.0.0
 * @version 1.0
 * @filesource
 */
class GlobalsVarLocal extends GlobalsVarSetEnv{

    private $protocolo                    = "";
//     private $protocolConvivencia          = ""; //DEPRECADO
    private $nodoConvivencia              = "";
    private $nodoWSerPort                 = "";
    private $expiraCookie                 = "";
    private $seguraCookie                 = "";
    private $pages                        = "";
    private $templates                    = "";
    private $dataTableGrid                = "";
    private $extensionPages               = "";
    private $keywordTunelCrypt            = "";
    private $hashKeyValidNewSystem        = "";
    private $AuxValidator1                = "";
    private $AuxValidator2                = "";
    private $timeRefreshSicroServerClient = "";
    private $sslPem                       = "";
    private $sslPemStatus                 = "";
    private $httpPath                     = "";
    private $httpExpire                   = "";
    private $httpDomain                   = "";
    private $httpSecure                   = "";
    private $serverName                   = "";
    private $serverContextPrefilx         = "";


    private $baseConcurso                 = "";
    private $cnrEstatus                   = "";
    private $baseConocimientos            = "";
    private $manual                       = "";
    private $logoCNR                      = "";




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
     * @return the $protocolo
     */
    public function getLogoCNR()
    {
        return $this->logoCNR = "resources/images/logo.jpg";
    }

    /**
     * @return the $protocolo
     */
    public function getBaseConcurso()
    {
        return $this->baseConcurso = "http://www.cnr.cl/Ley18450/Paginas/Base%20de%20Concursos.aspx";
    }
    
    /**
     * @return the $protocolo
     */
    public function getCnrEstatus()
    {
        return $this->cnrEstatus = "https://cnrstatus.cnr.gob.cl/";
    }
    
    /**
     * @return the $protocolo
     */
    public function getBaseConocimientos()
    {
        return $this->baseConocimientos = "http://www.cnr.cl/Ley18450/Paginas/BaseConocimiento.aspx";
    }

    /**
     * @return the $protocolo
     */
    public function getManual()
    {
        return $this->manual = "http://www.cnr.cl/Ley18450/Paginas/Manual.aspx";
    }

    /**
     * @return the $protocolo
     */
    public function getProtocolo()
    {
        //return $this->protocolo = isset($_SERVER["HTTPS"]) ? 'https' : 'http'; migracion
        return "https";
    }

    /**
     * @return the $nodoConvivencia
     */
    public function getNodoConvivencia()
    {
        return $this->nodoConvivencia = $this->protocolo."://".$_SERVER["SERVER_NAME"].$_SERVER["CONTEXT_PREFIX"];
    }

    /**
     * @return the $nodoWSerPort
     */
    public function getNodoWSerPort()
    {
        return $this->nodoWSerPort = $_SERVER["SERVER_PORT"];
    }

    /**
     * @return the $expiraCookie
     */
    public function getExpiraCookie()
    {
        return $this->expiraCookie = 1;
    }

    /**
     * @return the $seguraCookie
     */
    public function getSeguraCookie()
    {
        return $this->seguraCookie = $this->getProtocolo();
    }

    /**
     * @return the $pages
     */
    public function getPages()
    {
        return $this->pages = "pages";
    }

    /**
     * @return the $templates
     */
    public function getTemplates()
    {
        return $this->templates = "tpl";
    }

    /**
     * @return the $dataTableGrid
     */
    public function getDataTableGrid()
    {
        $jsonDataTable = array(
            "url"                           => "resources/js/datatables/datatables.spanish.json",
            "dataTablesShowRegisterButton"  => "Mostrar %d Registros",
            "dataShowAllText"               => "Mostrando todos los Registro",
            "verRegistro" => array(
                "cien" => "100 registros",
                "dosc" => "200 registros",
                "tres" => "300 registros",
                "cuat" => "400 registros",
                "quin" => "500 registros",
                "all" =>  "Todos"
            )
        );

        $parseToJSONDataTable = json_encode($jsonDataTable);

        return $this->dataTableGrid =$parseToJSONDataTable;
    }

    /**
     * @return the $extensionPages
     */
    public function getExtensionPages()
    {
        return $this->extensionPages = ".php";
    }

    /**
     * @return the $keywordTunelCrypt
     */
    public function getKeywordTunelCrypt()
    {
        return $this->keywordTunelCrypt = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    }

    /**
     * @return the $hashKeyValidNewSystem
     */
    public function getHashKeyValidNewSystem()
    {
        return $this->hashKeyValidNewSystem = rand();
    }

    /**
     * @return the $AuxValidator1
     */
    public function getAuxValidator1()
    {
        return $this->AuxValidator1 = " jQuery.validator.setDefaults({ ignore: ':hidden:not(select)' }); ";
    }

    /**
     * @return the $AuxValidator2
     */
    public function getAuxValidator2()
    {
        return $this->AuxValidator2 = " jQuery.extend(jQuery.validator.messages, { required: 'Debes ingresar/seleccionar una opcion.' }); ";
    }

    /**
     * @return the $timeRefreshSicroServerClient
     */
    public function getTimeRefreshSicroServerClient()
    {
        return $this->timeRefreshSicroServerClient = 300;
    }

    /**
     * @return the $sslPem
     */
    public function getSslPem()
    {
        return $this->sslPem = "/etc/pki/tls/cert.pem";
    }

    /**
     * @return the $sslPemStatus
     */
    public function getSslPemStatus()
    {
        return $this->sslPemStatus = true;
    }

    /**
     * @return the $httpPath
     */
    public function getHttpPath()
    {
        return $this->httpPath = "/";
    }

    /**
     * @return the $httpExpire
     */
    public function getHttpExpire()
    {
        return $this->httpExpire =ini_get("session.cookie_lifetime");
    }

    /**
     * @return the $httpDomain
     */
    public function getHttpDomain()
    {
        return $this->httpDomain = ini_get("session.cookie_domain");
    }

    /**
     * @return the $httpSecure
     */
    public function getHttpSecure()
    {
        return $this->httpSecure = ini_get("session.cookie_secure");
    }

    /**
     * @return the $serverName
     */
    public function getServerName()
    {
        return $this->serverName = $_SERVER["SERVER_NAME"];
    }

    /**
     * @return the $serverContextPrefilx
     */
    public function getServerContextPrefilx()
    {
        return $this->serverContextPrefilx = $_SERVER["CONTEXT_PREFIX"];
    }
}