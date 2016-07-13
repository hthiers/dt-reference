<?php
class LugaresController extends ControllerBase
{
	/*******************************************************************************
	* REGIONES
	*******************************************************************************/

	//FORM
        public function regionesDt($error_flag = 0, $message = null)
	{
		//Incluye el modelo que corresponde
		require_once 'models/RegionesModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new RegionesModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllRegiones();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 3);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }

		//Titulo pagina
		$data['titulo'] = "regiones";
                
                //Controller
		$data['controller'] = "lugares";
                
                //Action edit
		$data['action'] = "regionesEditForm";
		
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("regiones_dt.php", $data);
	}
	
	//SHOW
	public function regionesAddForm($error_flag = 0)
	{
		//Import models
		require_once 'models/RegionesModel.php';
		
		//Models objects
		$model = new RegionesModel();
	
		//Extraer ultimo codigo de segmento existente
		$new_code = $model->getNewRegionCode();
		
		if($code = $new_code->fetch(PDO::FETCH_ASSOC))
		{
			//Crear un nuevo codigo: anterior+1
			$NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_REGION']);
			$LETRAS = preg_replace("/[0-9]/", "", $code['COD_REGION']);  
			$NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
			$LEER = strlen($NUEVO_CODIGO);
			
			if($LEER > 1)
				$CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
			else
				$CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;
			
			$data['new_code'] = $CODIGOFINAL;
		}
		else
			$data['new_code'] = "RG01";
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "regiones > NUEVO";
                
                //Controller
		$data['controller'] = "lugares";
                //Action edit
		$data['action'] = "regionesAdd";
                //Action back
		$data['action_b'] = "regionesDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("regiones_new.php", $data);
	}
	
	//PROCESS
	public function regionesAdd()
	{
            $session = FR_Session::singleton();
            
            //Parametros login form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);
                
                $code = $_POST['code'];
                $name = $_POST['name'];

                //Incluye el modelo que corresponde
                require_once 'models/RegionesModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new RegionesModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewRegion($code, $name);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->regionesDt(1);
                else
                    $this->regionesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");

            }
            else
            {
                $this->regionesDt(2);
            }
	}

	//SHOW
	public function regionesEditForm()
	{
            if($_POST)
            {
                    $data['code'] = $_POST['code'];
                    $data['name'] = $_POST['name'];

                    //Finalmente presentamos nuestra plantilla
                    $data['titulo'] = "regiones > EDICI&Oacute;N";

                    //Controller
                    $data['controller'] = "lugares";
                    //Action edit
                    $data['action'] = "regionesEdit";
                    //Action back
                    $data['action_b'] = "regionesDt";

                    $this->view->show("regiones_edit.php", $data);
            }
            else
            {
                    $this->regionesDt(2);
            }
	}

	//PROCESS
	public function regionesEdit()
	{
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    $code = $_POST['code'];
                    $name = $_POST['name'];
                    #$old_code = $_POST['old_code'];

                    //Incluye el modelo que corresponde
                    require_once 'models/RegionesModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $model = new RegionesModel();

                    //Le pedimos al modelo todos los items
                    $result = $model->editRegion($code, $name);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->regionesDt(1);
                    else
                        $this->regionesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                    $this->regionesDt(2);
            }
	}


	/*******************************************************************************
	* CIUDADES
	*******************************************************************************/
	
	//FORM
        public function ciudadesDt($error_flag = 0, $message = null)
	{
            //Incluye el modelo que corresponde
            require_once 'models/CiudadesModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new CiudadesModel();

            //Le pedimos al modelo todos los items
            $listado = $model->getAllCiudades();

            //Pasamos a la vista toda la información que se desea representar
            $data['listado'] = $listado;

            // Obtener permisos de edición
            require_once 'models/UsersModel.php';
            $userModel = new UsersModel();

            $session = FR_Session::singleton();

            $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 3);
            if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                $data['permiso_editar'] = $row['EDITAR'];
            }

            //Titulo pagina
            $data['titulo'] = "ciudades";
            //Controller
            $data['controller'] = "lugares";
            //Action edit
            $data['action'] = "ciudadesEditForm";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

            //Finalmente presentamos nuestra plantilla
            $this->view->show("ciudades_dt.php", $data);
	}
	
	//SHOW
	public function ciudadesAddForm($error_flag = 0)
	{
            //Import models
            require_once 'models/CiudadesModel.php';
            require_once 'models/RegionesModel.php';

            //Models objects
            $model = new CiudadesModel();
            $model_b = new RegionesModel();

            $data['lista_regiones'] = $model_b->getAllRegiones();

            //Extraer ultimo codigo de segmento existente
            $new_code = $model->getNewCiudadCode();

            if($code = $new_code->fetch(PDO::FETCH_ASSOC))
            {
                //Crear un nuevo codigo: anterior+1
                $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_CIUDAD']);
                $LETRAS = preg_replace("/[0-9]/", "", $code['COD_CIUDAD']);  
                $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                $LEER = strlen($NUEVO_CODIGO);

                if($LEER > 2)
                        $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                else
                        $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

                $data['new_code'] = $CODIGOFINAL;
            }
            else
                $data['new_code'] = "CT001";

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "ciudades > NUEVO";

            //Controller
            $data['controller'] = "lugares";
            //Action edit
            $data['action'] = "ciudadesAdd";
            //Action back
            $data['action_b'] = "ciudadesDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag);

            $this->view->show("ciudades_new.php", $data);
	}
	
	//PROCESS
	public function ciudadesAdd()
	{
            $session = FR_Session::singleton();
            
            //Parametros login form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);
                
                $code = $_POST['code'];
                $name = $_POST['name'];
                $code_b = $_POST['code_b'];

                //Incluye el modelo que corresponde
                require_once 'models/CiudadesModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new CiudadesModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewCiudad($code, $name, $code_b);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->ciudadesDt(1);
                else
                    $this->ciudadesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");

            }
            else
            {
                $this->ciudadesDt(2);
            }
	}

	//SHOW
	public function ciudadesEditForm()
	{
            if($_POST)
            {
                $data['code'] = $_POST['code'];
                $data['name'] = $_POST['name'];
                $data['code_b'] = $_POST['code_b'];

                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "ciudades > EDICI&Oacute;N";

                //Controller
                $data['controller'] = "lugares";
                //Action edit
                $data['action'] = "ciudadesEdit";
                //Action back
                $data['action_b'] = "ciudadesDt";

                $this->view->show("ciudades_edit.php", $data);
            }
            else
            {
                $this->ciudadesDt(2);
            }
	}
	
	//PROCESS
	public function ciudadesEdit()
	{
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);

                $code = $_POST['code'];
                $name = $_POST['name'];
                $code_b = $_POST['code_b'];

                //Incluye el modelo que corresponde
                require_once 'models/CiudadesModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new CiudadesModel();

                //Le pedimos al modelo todos los items
                $result = $model->editCiudad($code, $name, $code_b);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->ciudadesDt(1);
                else
                    $this->ciudadesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                    $this->ciudadesDt(2);
            }
	}
	
	
	/*******************************************************************************
	* COMUNAS
	*******************************************************************************/
	
	//FORM
        public function comunasDt($error_flag = 0, $message = null)
	{
		//Incluye el modelo que corresponde
		require_once 'models/ComunasModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new ComunasModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllComunas();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 3);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "comunas";
                //Controller
		$data['controller'] = "lugares";
                //Action edit
		$data['action'] = "comunasEditForm";
		
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("comunas_dt.php", $data);
	}
	
	//SHOW
	public function comunasAddForm($error_flag = 0)
	{
            //Import models
            require_once 'models/CiudadesModel.php';
            require_once 'models/RegionesModel.php';
            require_once 'models/ComunasModel.php';

            //Models objects
            $model = new CiudadesModel();
            $model_b = new RegionesModel();
            $model_c = new ComunasModel();

            $data['lista_regiones'] = $model_b->getAllRegiones();
            $data['lista_ciudades'] = $model->getAllCiudades();

            //Extraer ultimo codigo de segmento existente
            $new_code = $model_c->getNewComunaCode();

            if($code = $new_code->fetch(PDO::FETCH_ASSOC))
            {
                //Crear un nuevo codigo: anterior+1
                $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_COMUNA']);
                $LETRAS = preg_replace("/[0-9]/", "", $code['COD_COMUNA']);  
                $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                $LEER = strlen($NUEVO_CODIGO);

                if($LEER > 2)
                        $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                else
                        $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

                $data['new_code'] = $CODIGOFINAL;
            }
            else
                $data['new_code'] = "CM001";

            //Finalmente presentamos nuestra plantilla
            $data['titulo'] = "comunas > NUEVO";

            //Controller
            $data['controller'] = "lugares";
            //Action edit
            $data['action'] = "comunasAdd";
            //Action back
            $data['action_b'] = "comunasDt";

            //Posible error
            $data['error_flag'] = $this->errorMessage->getError($error_flag);

            $this->view->show("comunas_new.php", $data);
	}
	
	//PROCESS
	public function comunasAdd()
	{
            $session = FR_Session::singleton();
            
            //Parametros login form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);
                
                $code = $_POST['code'];
                $name = $_POST['name'];
                $code_b = $_POST['code_b'];
                $code_c = $_POST['code_c'];

                //Incluye el modelo que corresponde
                require_once 'models/ComunasModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new ComunasModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewComuna($code, $name, $code_b, $code_c);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->comunasDt(1);
                else
                    $this->comunasDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                $this->comunasDt(2);
            }
	}

	//SHOW
	public function comunasEditForm()
	{
            if($_POST)
            {
                $data['code'] = $_POST['code'];
                $data['name'] = $_POST['name'];
                $data['code_b'] = $_POST['code_b'];
                $data['code_c'] = $_POST['code_c'];

                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "comunas > EDICI&Oacute;N";

                //Controller
                $data['controller'] = "lugares";
                //Action edit
                $data['action'] = "comunasEdit";
                //Action back
                $data['action_b'] = "comunasDt";

                $this->view->show("comunas_edit.php", $data);
            }
            else
            {
                $this->comunasDt(2);
            }
	}
	
	//PROCESS
	public function comunasEdit()
	{
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);

                $code = $_POST['code'];
                $name = $_POST['name'];
                $code_b = $_POST['code_b'];
                $code_c = $_POST['code_c'];

                //Incluye el modelo que corresponde
                require_once 'models/ComunasModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new ComunasModel();

                //Le pedimos al modelo todos los items
                $result = $model->editComuna($code, $name, $code_b, $code_c);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->comunasDt(1);
                else
                    $this->comunasDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                $this->comunasDt(2);
            }
	}
}
?>