<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/src/GlobalsVar.class.php';
/**
 * Clase que gestiona la ejecucion de controllers del sistema SEP.
 *
 * @package	ley18450v3
 * @author Gustavo Diaz
 * @copyright CNR
 * @link https://www.cnr.cl
 * @since 02-05-2018
 * @version 1.0
 * @filesource application/libraries/MY_Api.php
*/
class MY_Api
{
	/**
	 * Constructor de la clase.
	 *
	 * @author Gustavo Diaz
	 * @since 02-05-2018
	 * @version 1.0
	 *
	 * @return void
	*/
	public function MY_Api()
	{
		$this->status = true;
	}

	/**
	 * Metodo utilizado para asignar parametros de configuracion de la clase.
	 *
	 * @author Gustavo Diaz
	 * @since 02-05-2018
	 * @version 1.0
	 * @param datos.matriz: Instancia del objeto codeigniter o controller que recibe, ejecuta y gestiona una operacion de obtencion de datos, insercion de datos, modificacion de datos o eliminacion de datos.
	 * 
	 * @return void
	*/
	public function setConfig($datos)
	{
		$this->matriz = $datos['matriz'];
		$aux = explode('::', $datos['dataApi']);
		$this->nameControllerFile = $aux[0];
		$this->nameControllerMethod = $aux[1];
	}

	/**
	 * Metodo que lanza la peticion del controller.
	 *
	 * @author Gustavo Diaz
	 * @since 10-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	public function go($data)
	{
		$this->matriz->goResponse = false;
		if (!isset($data['LOGIN']))
		{
			$encrypt = new MY_Encrypt();
			$this->matriz->session->set_userdata(json_decode($encrypt->decode(call_user_func(array($this->matriz, $_SERVER['REQUEST_METHOD']), 'loginData')), true));
		}
		///////
		$UrlEncode = null;
    	switch ($_SERVER['REQUEST_METHOD'])
    	{
    		case 'GET':
    		{
    			$UrlEncode = isset($_GET['UrlEncode']) ? $_GET['UrlEncode'] : null;
    			break;
    		}
    		case 'POST':
    		{
    			$UrlEncode = isset($_POST['UrlEncode']) ? $_POST['UrlEncode'] : null;
    			break;
    		}
    	}
    	if ($UrlEncode)
    	{
    		for ($i = 0; $i < count($UrlEncode); $i++)
    		{
		    	switch ($_SERVER['REQUEST_METHOD'])
		    	{
		    		case 'GET':
		    		{
		    			$_GET[$UrlEncode[$i]] = urldecode($_GET[$UrlEncode[$i]]);
		    			break;
		    		}
		    		case 'POST':
		    		{
		    			$_POST[$UrlEncode[$i]] = urldecode($_POST[$UrlEncode[$i]]);
		    			break;
		    		}
		    	}
    		}
    	}
		///////
		if (isset($data['dataIn']))
		{
			$dataIO = new DataIO();
	        $dataIO->setConfig(array(
	            'matriz'=>$this->matriz,
	        ));
	        $dataIn = $dataIO->dataCliCon($data['dataIn']);
	        foreach ($dataIn as $llave=>$valor)
	        {
	        	switch ($_SERVER['REQUEST_METHOD'])
	        	{
	        		case 'GET':
	        		{
	        			$_GET[$llave] = $valor;
	        			break;
	        		}
	        		case 'POST':
	        		{
	        			$_POST[$llave] = $valor;
	        			break;
	        		}
	        	}
	        }
		}
		///////
		$pattern = "/(API)(_{$_SERVER['REQUEST_METHOD']})/i";
		$replace = '${2}';
		$nameControllerMethod = preg_replace($pattern, $replace, $this->nameControllerMethod);
		$respuesta = call_user_func(array($this->matriz, $nameControllerMethod), null);
		///////
		if (call_user_func(array($this->matriz, $_SERVER['REQUEST_METHOD']), 'noSeLlama'))
		{
			switch ($_SERVER['REQUEST_METHOD'])
	    	{
	    		case 'GET':
	    		{
					$respuesta["_{$_SERVER['REQUEST_METHOD']}"] = $_GET;
					$respuesta["CI_{$_SERVER['REQUEST_METHOD']}"] = call_user_func(array($this->matriz, $_SERVER['REQUEST_METHOD']), null);
	    			break;
	    		}
	    		case 'POST':
	    		{
	    			$respuesta["_{$_SERVER['REQUEST_METHOD']}"] = $_POST;
					$respuesta["CI_{$_SERVER['REQUEST_METHOD']}"] = call_user_func(array($this->matriz, $_SERVER['REQUEST_METHOD']), null);
	    			break;
	    		}
	    	}
		}
		return $respuesta;
	}

	/**
	 * Metodo que lanza la peticion de la API.
	 *
	 * @author Gustavo Diaz
	 * @since 02-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	public function run()
	{
		$this->globalsVar = new GlobalsVar();
		$this->uriBase = $this->globalsVar->getProtocolo()."://".$this->globalsVar->getServerName().$this->globalsVar->getServerContextPrefilx().$this->globalsVar->getNodoWSer();
		
		$this->controllers = $this->processDataInit($this->preProcessDataInit(call_user_func(array($this->matriz, $_SERVER['REQUEST_METHOD']), null)));
		
		$result = $this->getApi();
		return $result;
	}

	/**
	 * Metodo que focaliza la ejecucion del controller solicitado.
	 *
	 * @author Gustavo Diaz
	 * @since 02-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getApi()
	{
		$this->getApiKeyWEB();
		//$this->getApiKeySEP();
		$respu = null;
		$respuesta = null;
		for ($i = 0; $i < count($this->controllers); $i++)
		{
			switch($this->controllers[$i]['tipo'])
			{
				case 'GET':
				case 'get':
				{
					$respu = $this->getApiGet($this->controllers[$i]);
					break;
				}
				case 'POST':
				case 'post':
				{
					$respu = $this->getApiPost($this->controllers[$i]);
					break;
				}
			}
			/*
			if ($respu['success'] && !isset($respu['curlError']))
			{
			*/
				$this->dataPropagationControl(array(
					'controller'=>$this->controllers[$i],
					'response'=>$respu
				));
				if (isset($this->controllers[$i]['noSeLlama']) ? $this->controllers[$i]['noSeLlama'] : false)
				{
					$respuesta["{$this->controllers[$i]['nombre']}->{$this->controllers[$i]['metodo']}"] = $respu;
				}
			/*
			}
			else
			{
				$this->status = false;
				$respuesta = $respu;
				break;
			}
			*/
		}
		return $respuesta;
	}

	/**
	 * Metodo que que propaga la data response de un controller segun parametrizacion asociada en el cliente, esto es: la salida de un controller se convierte en la entrada de uno o mas controllers
	 *
	 * @author Gustavo Diaz
	 * @since 08-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function dataPropagationControl($data)
	{
		if (isset($data['controller']['dataOut']))
		{
			foreach ($data['controller']['dataOut'] AS $llave => $valor)
			{
				$valor0 = explode('|', $valor);
				for ($i = 0; $i < count($valor0); $i++)
				{
					$valor1 = explode('%', $valor0[$i]);
					$dataOutController = $valor1[1];
					$valor2 = explode('.', $valor1[0]);
					$nombreController = $valor2[0];
					$nombreMetodoController = $valor2[1];
					for ($j = 0; $j < count($this->controllers); $j++)
					{
						if ($this->controllers[$j]['nombre'] == $nombreController && $this->controllers[$j]['metodo'] == $nombreMetodoController)
						{
							$auxLlave = explode('-', $llave);
							$aux = array_shift($auxLlave);
							$this->controllers[$j]['dataIn'][$dataOutController] = $this->iterateInVariablesResponse($data['response'][$aux], $auxLlave);
						}
					}
				}
			}
		}
	}

	private function iterateInVariablesResponse($response, $arreglo)
	{
		if (count($arreglo) == 0)
		{
			return $response;
		}
		else
		{
			$aux = array_shift($arreglo);
			return $this->iterateInVariablesResponse($response[$aux], $arreglo);
		}
	}

	/**
	 * metodo que ejecuta la peticion contenida en controllers segun tipo, en este caso GET.
	 *
	 * @author Gustavo Diaz
	 * @since 03-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getApiGet($controllers)
	{
		$respuesta = null;
		$dataAux = $controllers['dataIn'];
		array_push($dataAux, array('UrlEncode'=>$controllers['UrlEncode']));
		if (isset($controllers['noSeLlama']))
		{
			$dataAux['noSeLlama'] = $controllers['noSeLlama'];
		}
		$dataAux2 = $this->build_get_fields($this->build_post_fields($dataAux));
	    $urlServices = "{$this->uriBase}/{$controllers['nombre']}/{$controllers['metodo']}?{$dataAux2}";
	    $ch = curl_init();
	    curl_setopt_array(
	    	$ch, 
	    	array(
	    		CURLOPT_FAILONERROR=>true,
	    		CURLOPT_RETURNTRANSFER=>true,
	    		CURLOPT_URL=>$urlServices,
	    		CURLOPT_HTTPHEADER=>array(
			        CURLOPT_RETURNTRANSFER => true,
			        "X-API-KEY: {$this->apiKey}",
			        'Authorization: ' . $this->globalsVar->getRestAuthUser()
	    		)
	    	)
	   	);
	   	$respuesta = curl_exec($ch);
	   	if ($respuesta === false)
	   	{
			$respuesta = $this->catchErrorCurl($ch);
	   	}
	   	curl_close($ch);
	   	return json_decode($respuesta, true);
	}

	/**
	 * metodo que ejecuta la peticion contenida en controllers segun tipo, en este caso POST.
	 *
	 * @author Gustavo Diaz
	 * @since 03-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getApiPost($controllers)
	{
		$respuesta = null;
		$dataAux = $controllers['dataIn'];
		array_push($dataAux, array('UrlEncode'=>$controllers['UrlEncode']));
		if (isset($controllers['noSeLlama']))
		{
			$dataAux['noSeLlama'] = $controllers['noSeLlama'];
		}
	    $ch = curl_init();
	    curl_setopt_array(
	    	$ch, 
	    	array(
	    		CURLOPT_FAILONERROR=>true,
	    		CURLOPT_POST=>true,
	    		CURLOPT_POSTFIELDS=>$this->build_post_fields($dataAux),
		        CURLOPT_RETURNTRANSFER=>true,
		        CURLOPT_URL=>"{$this->uriBase}/{$controllers['nombre']}/{$controllers['metodo']}",
	    		CURLOPT_HTTPHEADER=>array(
			        CURLOPT_RETURNTRANSFER=>true,
			        "X-API-KEY: {$this->apiKey}",
			        'Authorization: ' . $this->globalsVar->getRestAuthUser()
	    		)
	    	)
	   	);
	    $respuesta = curl_exec($ch);
	    if ($respuesta === false)
	    {
			return $this->catchErrorCurl($ch, $controllers);
	    }
	    curl_close($ch);
	    return json_decode($respuesta, true);
	}

	/**
	 * Metodo que se gatilla en caso de que la conexion curl dispare un error de conectividad.
	 *
	 * @author Gustavo Diaz
	 * @since 08-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function catchErrorCurl($ch, $controllers)
	{
		$res = array(
			'curlError'=>array(
				'error'=>curl_error($ch),
				'number'=>curl_errno($ch),
				'descripcion'=>curl_multi_strerror(curl_errno($ch)),
				'descripcion2'=>curl_strerror(curl_errno($ch)),
				'descripcion3'=>$this->getErrorCurlByNumber(curl_errno($ch)),
				'controller'=>$controllers
			)
		);
		return $res;
	}

	/**
	 * Metodo que prepara los datos para ser enviados por GET.
	 *
	 * @author Gustavo Diaz
	 * @since 03-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function build_get_fields($data)
	{
	    $respuesta = '';
	    foreach ($data as $llave=>$valor)
	    {
	    	$respuesta .= "{$llave}={$valor}&";
	    }
	    return $respuesta;
	}

	/**
	 * Metodo que prepara los datos para ser enviados por POST.
	 *
	 * @author Gustavo Diaz
	 * @since 03-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function build_post_fields($data, $existingKeys='', &$returnArray=[])
	{
	    if(($data instanceof CURLFile) or !(is_array($data) or is_object($data)))
	    {
	        $returnArray[$existingKeys] = $data;
	        return $returnArray;
	    }
	    else
	    {
	        foreach ($data as $key => $item)
	        {
	            $this->build_post_fields($item, $existingKeys ? $existingKeys."[$key]" : $key, $returnArray);
	        }
	        return $returnArray;
	    }
	}

	/**
	 * Metodo que codifica los caracteres especiales para enviar a curl.
	 *
	 * @author Gustavo Diaz
	 * @since 08-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function preProcessDataInit($data)
	{
		for ($i = 0; $i < count($data['controllers']); $i++)
		{
			$UrlEncode = array();
			foreach($data['controllers'][$i]['dataIn'] as $llave=>$valor)
			{
				if (is_array($valor) || is_object($valor))
				{
				}
				else
				{
					switch($valor)
					{
						case '%inMY_ApiControllerLogin%':
						case '%inMY_ApiControllerConstant%':
						{
							break;
						}
						default:
						{
							array_push($UrlEncode, $llave);
							$data['controllers'][$i]['dataIn'][$llave] = urlencode($valor);

							break;
						}
					}
				}
			}
			$data['controllers'][$i]['UrlEncode'] = $UrlEncode;
		}
		return $data;
	}

	/**
	 * Metodo que asigna valor a variables que son constantes segun logica del controller api que se invoque.
	 *
	 * @author Gustavo Diaz
	 * @since 08-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function processDataInit($data)
	{
		for ($i = 0; $i < count($data['controllers']); $i++)
		{
			foreach ($data['controllers'][$i]['dataIn'] as $llave=>$valor)
			{
				switch($valor)
				{
					case '%inMY_ApiControllerConstant%':
					{
						$data['controllers'][$i]['dataIn'][$llave] = call_user_func(array($this->nameControllerFile, "setConstantData{$this->nameControllerMethod}"), $llave);
						break;
					}
					case '%inMY_ApiControllerLogin%':
					{
						$data['controllers'][$i]['dataIn'][$llave] = $this->getDataLogin($llave);
						break;
					}
					/*default:
					{
						break;
					}*/
				}
			}
		}
		return $data['controllers'];
	}

	/**
	 * Metodo que asocia los datos de conectividad para hacer login en el sistema SEP.
	 *
	 * @author Gustavo Diaz
	 * @since 07-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getDataLogin($data)
	{
		switch($data)
		{
			case 'txtStrUsuario':
			{
				return base64_encode('99999999');
				break;
			}
			case 'passStrUsuario':
			{
				$encrypt = new MY_Encrypt();
				return $encrypt->encode(base64_encode('Externo_01'));
				break;
			}
		}
	}

	/**
	 * Metodo que solicita una nueva apiKey al servidor SEP.
	 *
	 * @author Gustavo Diaz
	 * @since 02-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getApiKeySEP()
	{
		$dataKey = array(
			'miApiKey'=>'1'
		);
	    curl_setopt_array(
	    	$this->ch, 
	    	array(
	    		CURLOPT_POST=>true,
	    		CURLOPT_POSTFIELDS=>$dataKey,
	    		CURLOPT_SSL_VERIFYPEER=>$this->globalsVar->getSslPemStatus(),
	    		CURLOPT_CAINFO=>$this->globalsVar->getSslPem(),
		        CURLOPT_RETURNTRANSFER=>true,
		        CURLOPT_URL=>"{$this->uriBase}/Register/newapikey",
		        CURLOPT_USERPWD=>$this->globalsVar->getRestAuthUser()
	    	)
	   	);
	    $this->apiKey = curl_exec($this->ch);
	}

	/**
	 * Metodo que recupera la apiKey enviada por el cliente web.
	 *
	 * @author Gustavo Diaz
	 * @since 02-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getApiKeyWEB()
	{
	    $this->apiKey = $_SERVER['HTTP_X_API_KEY'];
	}

	/**
	 * Metodo que devuelve una descripcion, sacada del web (https://curl.haxx.se/libcurl/c/libcurl-errors.html), del error dado un numero de error.
	 *
	 * @author Gustavo Diaz
	 * @since 09-05-2018
	 * @version 1.0
	 * @param 
	 * 
	 * @return void
	*/
	private function getErrorCurlByNumber($number)
	{
		switch($number)
		{
			case 0:
			{
				$nombre = 'CURLE_OK';
				$descripcion = 'All fine. Proceed as usual.';
				break;
			}
			case 1:
			{
				$nombre = 'CURLE_UNSUPPORTED_PROTOCOL';
				$descripcion = 'The URL you passed to libcurl used a protocol that this libcurl does not support. The support might be a compile-time option that you didn\'t use, it can be a misspelled protocol string or just a protocol libcurl has no code for.';
				break;
			}
			case 2:
			{
				$nombre = 'CURLE_FAILED_INIT';
				$descripcion = 'Very early initialization code failed. This is likely to be an internal error or problem, or a resource problem where something fundamental couldn\'t get done at init time.';
				break;
			}
			case 3:
			{
				$nombre = 'CURLE_URL_MALFORMAT';
				$descripcion = 'The URL was not properly formatted.';
				break;
			}
			case 4:
			{
				$nombre = 'CURLE_NOT_BUILT_IN';
				$descripcion = 'A requested feature, protocol or option was not found built-in in this libcurl due to a build-time decision. This means that a feature or option was not enabled or explicitly disabled when libcurl was built and in order to get it to function you have to get a rebuilt libcurl.';
				break;
			}
			case 5:
			{
				$nombre = 'CURLE_COULDNT_RESOLVE_PROXY';
				$descripcion = 'Couldn\'t resolve proxy. The given proxy host could not be resolved.';
				break;
			}
			case 6:
			{
				$nombre = 'CURLE_COULDNT_RESOLVE_HOST';
				$descripcion = 'Couldn\'t resolve host. The given remote host was not resolved.';
				break;
			}
			case 7:
			{
				$nombre = 'CURLE_COULDNT_CONNECT';
				$descripcion = 'Failed to connect() to host or proxy.';
				break;
			}
			case 8:
			{
				$nombre = 'CURLE_FTP_WEIRD_SERVER_REPLY';
				$descripcion = 'The server sent data libcurl couldn\'t parse. This error code is used for more than just FTP and is aliased as CURLE_WEIRD_SERVER_REPLY since 7.51.0.';
				break;
			}
			case 9:
			{
				$nombre = 'CURLE_REMOTE_ACCESS_DENIED';
				$descripcion = 'We were denied access to the resource given in the URL. For FTP, this occurs while trying to change to the remote directory.';
				break;
			}
			case 10:
			{
				$nombre = 'CURLE_FTP_ACCEPT_FAILED';
				$descripcion = 'While waiting for the server to connect back when an active FTP session is used, an error code was sent over the control connection or similar.';
				break;
			}
			case 11:
			{
				$nombre = 'CURLE_FTP_WEIRD_PASS_REPLY';
				$descripcion = 'After having sent the FTP password to the server, libcurl expects a proper reply. This error code indicates that an unexpected code was returned.';
				break;
			}
			case 12:
			{
				$nombre = 'CURLE_FTP_ACCEPT_TIMEOUT';
				$descripcion = 'During an active FTP session while waiting for the server to connect, the CURLOPT_ACCEPTTIMEOUT_MS (or the internal default) timeout expired.';
				break;
			}
			case 13:
			{
				$nombre = 'CURLE_FTP_WEIRD_PASV_REPLY';
				$descripcion = 'libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command. The server is flawed.';
				break;
			}
			case 14:
			{
				$nombre = 'CURLE_FTP_WEIRD_227_FORMAT';
				$descripcion = 'FTP servers return a 227-line as a response to a PASV command. If libcurl fails to parse that line, this return code is passed back.';
				break;
			}
			case 15:
			{
				$nombre = 'CURLE_FTP_CANT_GET_HOST';
				$descripcion = 'An internal failure to lookup the host used for the new connection.';
				break;
			}
			case 16:
			{
				$nombre = 'CURLE_HTTP2';
				$descripcion = 'A problem was detected in the HTTP2 framing layer. This is somewhat generic and can be one out of several problems, see the error buffer for details.';
				break;
			}
			case 17:
			{
				$nombre = 'CURLE_FTP_COULDNT_SET_TYPE';
				$descripcion = 'Received an error when trying to set the transfer mode to binary or ASCII.';
				break;
			}
			case 18:
			{
				$nombre = 'CURLE_PARTIAL_FILE';
				$descripcion = 'A file transfer was shorter or larger than expected. This happens when the server first reports an expected transfer size, and then delivers data that doesn\'t match the previously given size.';
				break;
			}
			case 19:
			{
				$nombre = 'CURLE_FTP_COULDNT_RETR_FILE';
				$descripcion = 'This was either a weird reply to a \'RETR\' command or a zero byte transfer complete.';
				break;
			}
			case 20:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 21:
			{
				$nombre = 'CURLE_QUOTE_ERROR';
				$descripcion = 'When sending custom "QUOTE" commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command.';
				break;
			}
			case 22:
			{
				$nombre = 'CURLE_HTTP_RETURNED_ERROR';
				$descripcion = 'This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400.';
				break;
			}
			case 23:
			{
				$nombre = 'CURLE_WRITE_ERROR';
				$descripcion = 'An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback.';
				break;
			}
			case 24:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 25:
			{
				$nombre = 'CURLE_UPLOAD_FAILED';
				$descripcion = 'Failed starting the upload. For FTP, the server typically denied the STOR command. The error buffer usually contains the server\'s explanation for this.';
				break;
			}
			case 26:
			{
				$nombre = 'CURLE_READ_ERROR';
				$descripcion = 'There was a problem reading a local file or an error returned by the read callback.';
				break;
			}
			case 27:
			{
				$nombre = 'CURLE_OUT_OF_MEMORY';
				$descripcion = 'A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs.';
				break;
			}
			case 28:
			{
				$nombre = 'CURLE_OPERATION_TIMEDOUT';
				$descripcion = 'Operation timeout. The specified time-out period was reached according to the conditions.';
				break;
			}
			case 29:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 30:
			{
				$nombre = 'CURLE_FTP_PORT_FAILED';
				$descripcion = 'The FTP PORT command returned error. This mostly happens when you haven\'t specified a good enough address for libcurl to use. See CURLOPT_FTPPORT.';
				break;
			}
			case 31:
			{
				$nombre = 'CURLE_FTP_COULDNT_USE_REST';
				$descripcion = 'The FTP REST command returned error. This should never happen if the server is sane.';
				break;
			}
			case 32:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 33:
			{
				$nombre = 'CURLE_RANGE_ERROR';
				$descripcion = 'The server does not support or accept range requests.';
				break;
			}
			case 34:
			{
				$nombre = 'CURLE_HTTP_POST_ERROR';
				$descripcion = 'This is an odd error that mainly occurs due to internal confusion.';
				break;
			}
			case 35:
			{
				$nombre = 'CURLE_SSL_CONNECT_ERROR';
				$descripcion = 'A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others.';
				break;
			}
			case 36:
			{
				$nombre = 'CURLE_BAD_DOWNLOAD_RESUME';
				$descripcion = 'The download could not be resumed because the specified offset was out of the file boundary.';
				break;
			}
			case 37:
			{
				$nombre = 'CURLE_FILE_COULDNT_READ_FILE';
				$descripcion = 'A file given with FILE:// couldn\'t be opened. Most likely because the file path doesn\'t identify an existing file. Did you check file permissions?';
				break;
			}
			case 38:
			{
				$nombre = 'CURLE_LDAP_CANNOT_BIND';
				$descripcion = 'LDAP cannot bind. LDAP bind operation failed.';
				break;
			}
			case 39:
			{
				$nombre = 'CURLE_LDAP_SEARCH_FAILED';
				$descripcion = 'LDAP search failed.';
				break;
			}
			case 40:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 41:
			{
				$nombre = 'CURLE_FUNCTION_NOT_FOUND';
				$descripcion = 'Function not found. A required zlib function was not found.';
				break;
			}
			case 42:
			{
				$nombre = 'CURLE_ABORTED_BY_CALLBACK';
				$descripcion = 'Aborted by callback. A callback returned "abort" to libcurl.';
				break;
			}
			case 43:
			{
				$nombre = 'CURLE_BAD_FUNCTION_ARGUMENT';
				$descripcion = 'Internal error. A function was called with a bad parameter.';
				break;
			}
			case 44:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 45:
			{
				$nombre = 'CURLE_INTERFACE_FAILED';
				$descripcion = 'Interface error. A specified outgoing interface could not be used. Set which interface to use for outgoing connections\' source IP address with CURLOPT_INTERFACE.';
				break;
			}
			case 46:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 47:
			{
				$nombre = 'CURLE_TOO_MANY_REDIRECTS';
				$descripcion = 'Too many redirects. When following redirects, libcurl hit the maximum amount. Set your limit with CURLOPT_MAXREDIRS.';
				break;
			}
			case 48:
			{
				$nombre = 'CURLE_UNKNOWN_OPTION';
				$descripcion = 'An option passed to libcurl is not recognized/known. Refer to the appropriate documentation. This is most likely a problem in the program that uses libcurl. The error buffer might contain more specific information about which exact option it concerns.';
				break;
			}
			case 49:
			{
				$nombre = 'CURLE_TELNET_OPTION_SYNTAX';
				$descripcion = 'A telnet option string was Illegally formatted.';
				break;
			}
			case 50:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 51:
			{
				$nombre = 'CURLE_PEER_FAILED_VERIFICATION';
				$descripcion = 'The remote server\'s SSL certificate or SSH md5 fingerprint was deemed not OK.';
				break;
			}
			case 52:
			{
				$nombre = 'CURLE_GOT_NOTHING';
				$descripcion = 'Nothing was returned from the server, and under the circumstances, getting nothing is considered an error.';
				break;
			}
			case 53:
			{
				$nombre = 'CURLE_SSL_ENGINE_NOTFOUND';
				$descripcion = 'The specified crypto engine wasn\'t found.';
				break;
			}
			case 54:
			{
				$nombre = 'CURLE_SSL_ENGINE_SETFAILED';
				$descripcion = 'Failed setting the selected SSL crypto engine as default!';
				break;
			}
			case 55:
			{
				$nombre = 'CURLE_SEND_ERROR';
				$descripcion = 'Failed sending network data.';
				break;
			}
			case 56:
			{
				$nombre = 'CURLE_RECV_ERROR';
				$descripcion = 'Failure with receiving network data.';
				break;
			}
			case 57:
			{
				$nombre = '';
				$descripcion = '';
				break;
			}
			case 58:
			{
				$nombre = 'CURLE_SSL_CERTPROBLEM';
				$descripcion = 'problem with the local client certificate.';
				break;
			}
			case 59:
			{
				$nombre = 'CURLE_SSL_CIPHER';
				$descripcion = 'Couldn\'t use specified cipher.';
				break;
			}
			case 60:
			{
				$nombre = 'CURLE_SSL_CACERT';
				$descripcion = 'Peer certificate cannot be authenticated with known CA certificates.';
				break;
			}
			case 61:
			{
				$nombre = 'CURLE_BAD_CONTENT_ENCODING';
				$descripcion = 'Unrecognized transfer encoding.';
				break;
			}
			case 62:
			{
				$nombre = 'CURLE_LDAP_INVALID_URL';
				$descripcion = 'Invalid LDAP URL.';
				break;
			}
			case 63:
			{
				$nombre = 'CURLE_FILESIZE_EXCEEDED';
				$descripcion = 'Maximum file size exceeded.';
				break;
			}
			case 64:
			{
				$nombre = 'CURLE_USE_SSL_FAILED';
				$descripcion = 'Requested FTP SSL level failed.';
				break;
			}
			case 65:
			{
				$nombre = 'CURLE_SEND_FAIL_REWIND';
				$descripcion = 'When doing a send operation curl had to rewind the data to retransmit, but the rewinding operation failed.';
				break;
			}
			case 66:
			{
				$nombre = 'CURLE_SSL_ENGINE_INITFAILED';
				$descripcion = 'Initiating the SSL Engine failed.';
				break;
			}
			case 67:
			{
				$nombre = 'CURLE_LOGIN_DENIED';
				$descripcion = 'The remote server denied curl to login (Added in 7.13.1)';
				break;
			}
			case 68:
			{
				$nombre = 'CURLE_TFTP_NOTFOUND';
				$descripcion = 'File not found on TFTP server.';
				break;
			}
			case 69:
			{
				$nombre = 'CURLE_TFTP_PERM';
				$descripcion = 'Permission problem on TFTP server.';
				break;
			}
			case 70:
			{
				$nombre = 'CURLE_REMOTE_DISK_FULL';
				$descripcion = 'Out of disk space on the server.';
				break;
			}
			case 71:
			{
				$nombre = 'CURLE_TFTP_ILLEGAL';
				$descripcion = 'Illegal TFTP operation.';
				break;
			}
			case 72:
			{
				$nombre = 'CURLE_TFTP_UNKNOWNID';
				$descripcion = 'Unknown TFTP transfer ID.';
				break;
			}
			case 73:
			{
				$nombre = 'CURLE_REMOTE_FILE_EXISTS';
				$descripcion = 'File already exists and will not be overwritten.';
				break;
			}
			case 74:
			{
				$nombre = 'CURLE_TFTP_NOSUCHUSER';
				$descripcion = 'This error should never be returned by a properly functioning TFTP server.';
				break;
			}
			case 75:
			{
				$nombre = 'CURLE_CONV_FAILED';
				$descripcion = 'Character conversion failed.';
				break;
			}
			case 76:
			{
				$nombre = 'CURLE_CONV_REQD';
				$descripcion = 'Caller must register conversion callbacks.';
				break;
			}
			case 77:
			{
				$nombre = 'CURLE_SSL_CACERT_BADFILE';
				$descripcion = 'Problem with reading the SSL CA cert (path? access rights?)';
				break;
			}
			case 78:
			{
				$nombre = 'CURLE_REMOTE_FILE_NOT_FOUND';
				$descripcion = 'The resource referenced in the URL does not exist.';
				break;
			}
			case 79:
			{
				$nombre = 'CURLE_SSH';
				$descripcion = 'An unspecified error occurred during the SSH session.';
				break;
			}
			case 80:
			{
				$nombre = 'CURLE_SSL_SHUTDOWN_FAILED';
				$descripcion = 'Failed to shut down the SSL connection.';
				break;
			}
			case 81:
			{
				$nombre = 'CURLE_AGAIN';
				$descripcion = 'Socket is not ready for send/recv wait till it\'s ready and try again. This return code is only returned from curl_easy_recv and curl_easy_send (Added in 7.18.2)';
				break;
			}
			case 82:
			{
				$nombre = 'CURLE_SSL_CRL_BADFILE';
				$descripcion = 'Failed to load CRL file (Added in 7.19.0)';
				break;
			}
			case 83:
			{
				$nombre = 'CURLE_SSL_ISSUER_ERROR';
				$descripcion = 'Issuer check failed (Added in 7.19.0)';
				break;
			}
			case 84:
			{
				$nombre = 'CURLE_FTP_PRET_FAILED';
				$descripcion = 'The FTP server does not understand the PRET command at all or does not support the given argument. Be careful when using CURLOPT_CUSTOMREQUEST, a custom LIST command will be sent with PRET CMD before PASV as well. (Added in 7.20.0)';
				break;
			}
			case 85:
			{
				$nombre = 'CURLE_RTSP_CSEQ_ERROR';
				$descripcion = 'Mismatch of RTSP CSeq numbers.';
				break;
			}
			case 86:
			{
				$nombre = 'CURLE_RTSP_SESSION_ERROR';
				$descripcion = 'Mismatch of RTSP Session Identifiers.';
				break;
			}
			case 87:
			{
				$nombre = 'CURLE_FTP_BAD_FILE_LIST';
				$descripcion = 'Unable to parse FTP file list (during FTP wildcard downloading).';
				break;
			}
			case 88:
			{
				$nombre = 'CURLE_CHUNK_FAILED';
				$descripcion = 'Chunk callback reported error.';
				break;
			}
			case 89:
			{
				$nombre = 'CURLE_NO_CONNECTION_AVAILABLE';
				$descripcion = '(For internal use only, will never be returned by libcurl) No connection available, the session will be queued. (added in 7.30.0)';
				break;
			}
			case 90:
			{
				$nombre = 'CURLE_SSL_PINNEDPUBKEYNOTMATCH';
				$descripcion = 'Failed to match the pinned key specified with CURLOPT_PINNEDPUBLICKEY.';
				break;
			}
			case 91:
			{
				$nombre = 'CURLE_SSL_INVALIDCERTSTATUS';
				$descripcion = 'Status returned failure when asked with CURLOPT_SSL_VERIFYSTATUS.';
				break;
			}
			case 92:
			{
				$nombre = 'CURLE_HTTP2_STREAM';
				$descripcion = 'Stream error in the HTTP/2 framing layer.';
				break;
			}
			case 93:
			{
				$nombre = 'CURLE_RECURSIVE_API_CALL';
				$descripcion = 'An API function was called from inside a callback.';
				break;
			}
		}
		return "{$nombre}: {$descripcion}";
	}
}
?>