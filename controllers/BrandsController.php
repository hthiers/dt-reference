<?php
class BrandsController extends ControllerBase
{
	/*******************************************************************************
	* BRANDS
	*******************************************************************************/

	//FORM
        public function brandsDt($error_flag = 0, $message = "")
	{
		//Incluye el modelo que corresponde
		require_once 'models/BrandsModel.php';

		//Creamos una instancia de nuestro "modelo"
		$model = new BrandsModel();

		//Le pedimos al modelo todos los items
		$listado = $model->getAllBrands();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;

                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 5);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                else
                    $data['permiso_editar'] = 0;
                
		//Titulo pagina
		$data['titulo'] = "brands";

                //Controller
		$data['controller'] = "brands";
                //Action edit
		$data['action'] = "brandsEditForm";

		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);

		//Finalmente presentamos nuestra plantilla
		$this->view->show("brands_dt.php", $data);
	}
        
	//SHOW
	public function brandsAddForm($error_flag = 0)
	{
		//Import
		require_once 'models/BrandsModel.php';

		//Models objects
		$model = new BrandsModel();

		//Extraer ultimo codigo de segmento existente
		$new_code = $model->getNewBrandCode();

		if($code = $new_code->fetch(PDO::FETCH_ASSOC))
		{
			//Crear un nuevo codigo: anterior+1
			$NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_BRAND']);
			$LETRAS = preg_replace("/[0-9]/", "", $code['COD_BRAND']);  
			$NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
			$LEER = strlen($NUEVO_CODIGO);
			
			if($LEER > 2)
				$CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
			else
				$CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;
			
			$data['new_code'] = $CODIGOFINAL;
		}
		else
			$data['new_code'] = "BD001";
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "brands > NUEVO";
                
                //Controller
		$data['controller'] = "brands";
                //Action edit
		$data['action'] = "brandsAdd";
                //Action back
		$data['action_b'] = "brandsDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("brands_new.php", $data);
	}
	
	//PROCESS
	public function brandsAdd()
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
                require_once 'models/BrandsModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new BrandsModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewBrand($code, $name);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->brandsDt(1);
                else
                    $this->brandsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");

//                if($result->rowCount() > 0)
//                {
//                        //Destroy POST
//                        unset($_POST);
//
//                        $this->brandsDt(1);
//                }
//                else
//                {
//                        //Destroy POST
//                        unset($_POST);
//
//                        $this->brandsDt(2);
//                }
            }
            else
            {
                $this->brandsDt();
            }
	}

	//SHOW
	public function brandsEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
			$data['name'] = $_POST['name'];
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "brands > EDICI&Oacute;N";
                        
                        //Controller
                        $data['controller'] = "brands";
                        //Action edit
                        $data['action'] = "brandsEdit";
                        //Action back
                        $data['action_b'] = "brandsDt";
                        
			$this->view->show("brands_edit.php", $data);
		}
		else
		{
			$this->brandsDt(2);
		}
	}
	
	//PROCESS
	public function brandsEdit()
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
			require_once 'models/BrandsModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new BrandsModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editBrand($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();

                        if($error[0] == 00000)
                            $this->brandsDt(1);
                        else
                            $this->brandsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        
//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->brandsDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->brandsDt(2);
//			}
		}
		else
		{
			$this->brandsDt();
		}
	}
        
        /**
         * Get serialized array of brands
         * @return String JSON 
         */
        public function listBrandsJSON()
        {
            //Incluye el modelo que corresponde
            require_once 'models/BrandsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new BrandsModel();

            $listado = $model->getAllBrands();
            
            $output = array();
            
            while ($row = $listado->fetch(PDO::FETCH_ASSOC))
            {
                $output[$row['COD_BRAND']] = utf8_encode($row['NAME_BRAND']);
            }
            
            $output['selected'] = utf8_encode($_GET['current']);
            
            echo json_encode( $output );
        }

        /*
         * Verify Name
         * AJAX
         */
        public function verifyNameBrand()
        {
            if($_REQUEST['name'])
            {
                $input = $_REQUEST['name'];
                $sql = "SELECT NAME_BRAND FROM t_brand where NAME_BRAND = '$input'";

                //Incluye el modelo que corresponde
                require_once 'models/BrandsModel.php';
                $model = new BrandsModel();
                $result = $model->goCustomQuery($sql);

                if($result->rowCount() > 0)
                    echo "false";
                else
                    echo "true";
            }
            else
                echo "false";
        }
}
?>