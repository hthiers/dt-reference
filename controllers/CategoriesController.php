<?php
class CategoriesController extends ControllerBase
{
	/*******************************************************************************
	* BU
	*******************************************************************************/

	//FORM
        public function buDt($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/CategoriesModel.php';
                
		//Creamos una instancia de nuestro "modelo"
		$model = new CategoriesModel();

		//Le pedimos al modelo todos los items
		$listado = $model->getAllBu();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;

                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 6);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "bu";

                //Controller
		$data['controller'] = "categories";

                //Action edit
		$data['action'] = "buEditForm";

		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);

		//Finalmente presentamos nuestra plantilla
		$this->view->show("bu_dt.php", $data);
	}
	
	//SHOW
	public function buAddForm($error_flag = 0)
	{
		//Import
		require_once 'models/CategoriesModel.php';

		//Models objects
		#$model = new CategoriesModel();
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "bu > NUEVO";
                
                //Controller
		$data['controller'] = "categories";
                //Action edit
		$data['action'] = "buAdd";
                //Action back
		$data['action_b'] = "buDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("bu_new.php", $data);
	}
	
	//PROCESS
	public function buAdd()
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
                require_once 'models/CategoriesModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new CategoriesModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewBu($code, $name);

                if($result->rowCount() > 0)
                {
                        //Destroy POST
                        unset($_POST);

                        $this->buDt(1);
                }
                else
                {
                        //Destroy POST
                        unset($_POST);

                        $this->buDt(2);
                }

            }
            else
            {
                $this->buDt();
            }
	}

	//SHOW
	public function buEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
			$data['name'] = $_POST['name'];
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "bu > EDICI&Oacute;N";
			
                        //Controller
                        $data['controller'] = "categories";
                        //Action edit
                        $data['action'] = "buEdit";
                        //Action back
                        $data['action_b'] = "buDt";
                        
			$this->view->show("bu_edit.php", $data);
		}
		else
		{
			$this->buDt(2);
		}
	}
	
	//PROCESS
	public function buEdit()
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
			require_once 'models/CategoriesModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new CategoriesModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editBu($code, $name);
			
			if($result->rowCount() > 0)
			{
				//Destroy POST
				unset($_POST);
				
				$this->buDt(1);
			}
			else
			{
				//Destroy POST
				unset($_POST);
				
				$this->buDt(2);
			}
		}
		else
		{
			$this->buDt();
		}
	}
        
        
        /*******************************************************************************
	* CATEGORY
	*******************************************************************************/

	//FORM
        public function categoryDt($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/CategoriesModel.php';

		//Creamos una instancia de nuestro "modelo"
		$model = new CategoriesModel();

		//Le pedimos al modelo todos los items
		$listado = $model->getAllCategory();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;

                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 6);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "category";

                //Controller
		$data['controller'] = "categories";

                //Action edit
		$data['action'] = "categoryEditForm";

		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);

		//Finalmente presentamos nuestra plantilla
		$this->view->show("category_dt.php", $data);
	}
	
	//SHOW
	public function categoryAddForm($error_flag = 0)
	{
		//Import
		require_once 'models/CategoriesModel.php';

		//Models objects
		$model = new CategoriesModel();
                
                $data['lista_gbu'] = $model->getAllGbu();
                $data['lista_bu'] = $model->getAllBu();
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "category > NUEVO";
		
                //Controller
		$data['controller'] = "categories";
                //Action edit
		$data['action'] = "categoryAdd";
                //Action back
		$data['action_b'] = "categoryDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("category_new.php", $data);
	}
	
	//PROCESS
	public function categoryAdd()
	{
            $session = FR_Session::singleton();
            
            //Parametros login form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);
                
                $code = $_POST['code'];
                $code_b = $_POST['code_b'];
                $name = $_POST['name'];

                #print($code.", ");
                #print($code_b.", ");
                #print_r($name);
                
                //Incluye el modelo que corresponde
                require_once 'models/CategoriesModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new CategoriesModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewCategory($code, $code_b, $name);

                if($result->rowCount() > 0)
                {
                        //Destroy POST
                        unset($_POST);

                        $this->categoryDt(1);
                }
                else
                {
                        //Destroy POST
                        unset($_POST);
                        
                        #print_r($result->errorInfo());
                        $this->categoryDt(2);
                }

            }
            else
            {
                $this->categoryDt();
            }
	}

	//SHOW
	public function categoryEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
                        $data['code_b'] = $_POST['code_b'];
			$data['name'] = $_POST['name'];
			
                        //Incluye el modelo que corresponde
                        require_once 'models/CategoriesModel.php';

                        //Creamos una instancia de nuestro "modelo"
                        $model = new CategoriesModel();

                        //Le pedimos al modelo todos los items
                        $data['lista_bu'] = $model->getAllBu();
                        $data['lista_gbu'] = $model->getAllGbu();
                        
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "category > EDICI&Oacute;N";
			
                        //Controller
                        $data['controller'] = "categories";
                        //Action edit
                        $data['action'] = "categoryEdit";
                        //Action back
                        $data['action_b'] = "categoryDt";
                        
			$this->view->show("category_edit.php", $data);
		}
		else
		{
			$this->categoryDt(2);
		}
	}
	
	//PROCESS
	public function categoryEdit()
	{
                $session = FR_Session::singleton();
            
		//Parametros form
		if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
		{
                        //Avoid resubmit
                        $session->orig_timestamp = microtime(true);
                    
			$code = $_POST['code'];
			$name = $_POST['name'];
			
			//Incluye el modelo que corresponde
			require_once 'models/CategoriesModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new CategoriesModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editCategory($code, $name);
			
			if($result->rowCount() > 0)
			{
				//Destroy POST
				unset($_POST);
				
				$this->categoryDt(1);
			}
			else
			{
				//Destroy POST
				unset($_POST);
				
				$this->categoryDt(2);
			}
		}
		else
		{
			$this->categoryDt();
		}
	}

        
        /*******************************************************************************
	* GBU
	*******************************************************************************/

	//FORM
        public function gbuDt($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/CategoriesModel.php';

		//Creamos una instancia de nuestro "modelo"
		$model = new CategoriesModel();

		//Le pedimos al modelo todos los items
		$listado = $model->getAllGbu();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;

                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 6);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "gbu";

                //Controller
		$data['controller'] = "categories";

                //Action edit
		$data['action'] = "gbuEditForm";

		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);

		//Finalmente presentamos nuestra plantilla
		$this->view->show("gbu_dt.php", $data);
	}
	
	//SHOW
	public function gbuAddForm($error_flag = 0)
	{
		//Import
		require_once 'models/CategoriesModel.php';

		//Models objects
		$model = new CategoriesModel();
                
                $data['lista_categoria'] = $model->getAllCategory();
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "gbu > NUEVO";

                //Controller
		$data['controller'] = "categories";
                //Action edit
		$data['action'] = "gbuAdd";
                //Action back
		$data['action_b'] = "gbuDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("gbu_new.php", $data);
	}
	
	//PROCESS
	public function gbuAdd()
	{
            $session = FR_Session::singleton();
            
            //Parametros login form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);
                
                $code = $_POST['code'];
                $code_b = $_POST['code_b'];
                $name = $_POST['name'];

                //Incluye el modelo que corresponde
                require_once 'models/CategoriesModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new CategoriesModel();

                //Le pedimos al modelo todos los items
                $result = $model->addNewGbu($code, $code_b, $name);

                if($result->rowCount() > 0)
                {
                        //Destroy POST
                        unset($_POST);

                        $this->gbuDt(1);
                }
                else
                {
                        //Destroy POST
                        unset($_POST);

                        $this->gbuDt(2);
                }

            }
            else
            {
                $this->gbuDt();
            }
	}

	//SHOW
	public function gbuEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
                        $data['code_b'] = $_POST['code_b'];
			$data['name'] = $_POST['name'];
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "gbu > EDICI&Oacute;N";
			
                        //Controller
                        $data['controller'] = "categories";
                        //Action edit
                        $data['action'] = "gbuEdit";
                        //Action back
                        $data['action_b'] = "gbuDt";
                        
			$this->view->show("gbu_edit.php", $data);
		}
		else
		{
			$this->buDt(2);
		}
	}
	
	//PROCESS
	public function gbuEdit()
	{
                $session = FR_Session::singleton();
            
		//Parametros form
		if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
		{
                        //Avoid resubmit
                        $session->orig_timestamp = microtime(true);
                    
			$code = $_POST['code'];
                        $code_b = $_POST['code_b'];
			$name = $_POST['name'];
			#$old_code = $_POST['old_code'];
			
			//Incluye el modelo que corresponde
			require_once 'models/CategoriesModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new CategoriesModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editGbu($code, $name);
			
			if($result->rowCount() > 0)
			{				
				$this->gbuDt(1);
			}
			else
			{
				$this->gbuDt(2);
			}
		}
		else
		{
			$this->gbuDt();
		}
	}
        
        /*
         * Verify existent attribute
         * AJAX
         */
        public function verifyCodeCategory()
        {
            if($_REQUEST['code'])
            {
                $input = $_REQUEST['code'];
                
                if($_REQUEST['target'] == 1)
                    $sql = "SELECT cod_bu FROM t_bu WHERE cod_bu = '$input'";
                elseif($_REQUEST['target'] == 2)
                    $sql = "SELECT cod_category FROM t_category WHERE cod_category = '$input'";
                elseif($_REQUEST['target'] == 3)
                    $sql = "SELECT cod_gbu FROM t_gbu WHERE cod_gbu = '$input'";

                //Incluye el modelo que corresponde
                require_once 'models/CategoriesModel.php';
                $model = new CategoriesModel();
                $result = $model->goCustomQuery($sql);

                if($result->rowCount() > 0)
                    echo "false";
                else
                    echo "true";
            }
            else
                echo "false";
        }
        
        /*
         * Verify existent attribute
         * AJAX
         */
        public function verifyNamecategory()
        {
            if($_REQUEST['name'])
            {
                $input = $_REQUEST['name'];
                
                if($_REQUEST['target'] == 1)
                    $sql = "SELECT cod_bu FROM t_bu WHERE name_bu = '$input'";
                elseif($_REQUEST['target'] == 2)
                    $sql = "SELECT cod_category FROM t_category WHERE name_category = '$input'";
                elseif($_REQUEST['target'] == 3)
                    $sql = "SELECT cod_gbu FROM t_gbu WHERE name_gbu = '$input'";

                //Incluye el modelo que corresponde
                require_once 'models/CategoriesModel.php';
                $model = new CategoriesModel();
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