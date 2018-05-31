<?php
    class Controlador
    {
        public function __constructor(){}
        
        public function init()
        {
            $data = json_decode($_GET['data'], false);
            //var_dump($_POST);
            //var_dump($_GET);
            //var_dump($data);
            //echo $data->funcion;
            switch ($data->funcion)
            {
                case 'saludo':
                {
                    $this->saludo($data->datain);
                    break;
                }
                case 'despedida':
                {
                    $this->despedida($data->datain);
                    break;
                }
            }
        }
        
        private function saludo($data)
        {
            echo "Hola po!, {$data->nombre}, {$data->mensaje}";
        }
        
        private function despedida($data)
        {
            echo "Adios, {$data->nombre}, {$data->mensaje}";
        }
    }
    $instancia = new Controlador();
    $instancia->init();
?>
