<?php
require("GlobalsVarLocal.class.php");

/**
 * Class GlobalsVar
 *
 * Clase que hereda y centraliza todas las variables de la app.
 *
 * @package	ley18450v3
 * @author	CNR
 * @copyright	CNR
 * @link	https://www.cnr.cl
 * @since	Version 1.0.0
 * @version 1.0
 * @filesource
 */
class GlobalsVar extends GlobalsVarLocal {

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
     * Metodo que permite cargar las variables del tipo js en las principales paginas del sistema (login, contenido, contenidopopup)
     * @author	CNR
     * @since	Version 1.0.0
     * @version 1.0
     * @return	string
     *
     */
    public function cargaGlobalVarJs(){
        $string = '<script type="text/javascript">';
        $string .= ' var protocolo = "'.$this->getProtocolo().'";';

        $string .= ' var baseConcurso = "'.$this->getBaseConcurso().'";';
        $string .= ' var cnrEstatus = "'.$this->getCnrEstatus().'";';
        $string .= ' var baseConocimientos = "'.$this->getBaseConocimientos().'";';
        $string .= ' var manual = "'.$this->getManual().'";';
        $string .= ' var logoCNR = "'.$this->getLogoCNR().'";';
        
        $string .= ' var serverName = "'.$this->getServerName().'";';
        $string .= ' var serverContextPrefix = "'.$this->getServerContextPrefilx().'";';
        $string .= ' var nameSiteOfSystems = "'.$this->getNameSiteOfSystems().'";';
        $string .= ' var nameSiteOfOldSystems = "'.$this->getNameSiteOfOldSystems().'";';
        $string .= ' var nodoWSer = "'.$this->getNodoWSer().'";';
        $string .= ' var nodoConvivencia = "'.$this->getNodoConvivencia().'";';
        $string .= ' var sitioConvivencia = "'.$this->getNameSiteOfOldSystems().'";';
        $string .= ' var nodoWSerPort = "'.$this->getNodoWSerPort().'";';
        $string .= ' var sitio = nameSiteOfSystems +  "'.$this->getNodoWSer().'";';
        $string .= ' var web =  "'.$this->getNameSiteOfSystems().'";';
        $string .= ' var pathWSer = protocolo + "://" + serverName  + serverContextPrefix + nodoWSer + "/"; ';
        $string .= ' var pathWeb = protocolo + "://" + serverName   +  serverContextPrefix +"/" + web + "/"; ';
        //$string .= ' var pathWSer = protocolo + "://" + serverName +  ":" + nodoWSerPort + serverContextPrefix +"/" + nodoWSer + "/"; ';
        //$string .= ' var pathWeb = protocolo + "://" + serverName  +  ":" + nodoWSerPort +  serverContextPrefix +"/" + web + "/"; ';

        $string .= ' var globalPathSystem = pathWSer; ';
        $string .= ' var globalPathWebSystem = pathWeb; ';
        $string .= ' var expiraCookie = "'.$this->getExpiraCookie().'";';
        $string .= ' var seguraCookie = "'.$this->getProtocolo().'";';
        $string .= ' var pages = "'.$this->getPages().'";';
        $string .= ' var templates = "'.$this->getTemplates().'";';
        $string .= ' var dataTableGridJS = '.$this->getDataTableGrid().';';
        $string .= ' var extensionPages = "'.$this->getExtensionPages().'";';
        $string .= ' var keywordTunelCrypt = "'.$this->getKeywordTunelCrypt().'";';
        $string .= ' var urlOlSystem = protocolo+nodoConvivencia+sitioConvivencia+"/GestionExterna/NuevoSistemaLey.php? ";';
        $string .= ' var hashKeyValidNewSystem = "'.$this->getHashKeyValidNewSystem().'";';
        $string .= ' '.$this->getAuxValidator1()."; ";
        $string .= ' '.$this->getAuxValidator2()."; ";
        $string .= ' var timeRefreshSicroServerClient = '.$this->getTimeRefreshSicroServerClient()."; ";
        $string .= '</script>';

        return $string;
    }
}