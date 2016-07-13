<?php
class TiendasController extends ControllerBase
{
	/*******************************************************************************
	* TIENDAS
	*******************************************************************************/

	//SHOW
	public function tiendasDt($error_flag = 0, $message = "")
	{
                $session = FR_Session::singleton();
            
		//Incluye el modelo que corresponde
		require_once 'models/ClientesModel.php';
		require_once 'models/RegionesModel.php';
                require_once 'models/UsersModel.php';
                require_once 'models/TiendasModel.php';

		//Creamos una instancia de nuestro "modelo"
		#$modelTiendas = new TiendasModel();
		$modelClientes = new ClientesModel();
		$modelRegiones = new RegionesModel();
                $userModel = new UsersModel();
                $modelTiendas = new TiendasModel();

		//Le pedimos al modelo todos los items
		$listado_clientes = $modelClientes->getAllClientes();
		$listado_regiones = $modelRegiones->getAllRegiones();
                $listado_estados = $modelTiendas->getAllEstados();

		//Pasamos a la vista toda la información que se desea representar
		#$data['listado'] = $listado;
		$data['listado_clientes'] = $listado_clientes;
		$data['listado_regiones'] = $listado_regiones;
                $data['permiso_editar'] = 0;
                $data['permiso_exportar'] = 0;
                $data['listado_estados'] = $listado_estados;

                // Permisos edicion
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 1);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC))
                    $data['permiso_editar'] = $row['EDITAR'];
                
                // Permisos exportar
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 12);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC))
                    $data['permiso_exportar'] = $row['VER'];                    
                
		//Titulo pagina
		$data['titulo'] = "TIENDAS";
		
                $data['controller'] = "tiendas";
                $data['action'] = "tiendasEditForm";
                $data['action_b'] = "tiendasDt";
                $data['action_exp_excel'] = "exportToExcel";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("tiendas_dt.php", $data);
	}
        
        /**
         * Get tiendas for ajax dynamic query
         * AJAX
         * @return json
         */
        public function ajaxTiendasDt()
        {
            //Incluye el modelo que corresponde
            require_once 'models/TiendasModel.php';
            $model = new TiendasModel();

            /*
            * Build up dynamic query
            */
            $sTable = $model->getTableName();
            $aColumns = array('a.COD_TIENDA'
				, 'a.NOM_TIENDA'
				, 'a.DIREC_TIENDA'
				, 'b.NOM_CLIENTE'
				, 'c.NOM_AGRUPACION'
				, 'd.NOM_TIPO'
				, 'e.NOM_COMUNA'
				, 'f.NOM_CIUDAD'
				, 'g.NOM_REGION'
				, 'h.NOM_ZONA'
				, 'i.NOM_ESTADO'
                                , 'a.COD_BTK');
            $sIndexColumn = "COD_TIENDA";

            /******************** Paging */
            if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
                $sLimit = "LIMIT ".$_GET['iDisplayStart'].", ".$_GET['iDisplayLength'];

            /******************** Ordering */
            $sOrder = "";
            if ( isset( $_GET['iSortCol_0'] ) )
            {
                    $sOrder = "ORDER BY  ";
                    for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
                    {
                            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                            {
                                    $sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
                                            $_GET['sSortDir_'.$i].", ";
                            }
                    }

                    $sOrder = substr_replace( $sOrder, "", -2 );
                    if ( $sOrder == "ORDER BY" )
                    {
                            $sOrder = "";
                    }
            }

            /******************** Filtering */
            $sWhere = "";

            if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
            {                
                $sWhere = "WHERE (";
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    $sWhere .= "".$aColumns[$i]." LIKE '%".utf8_decode($_GET['sSearch'])."%' OR ";
                }

                $sWhere = substr_replace( $sWhere, "", -3 );
                $sWhere .= ')';
            }

            /********************* Individual column filtering */
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
                {
                    if ( $sWhere == "" )
                    {
                        $sWhere = "WHERE ";
                    }
                    else
                    {
                        $sWhere .= " AND ";
                    }

                    $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
                }
            }
            
            /******************** Custom Filtering */
            if( isset($_GET['filCliente']) && $_GET['filCliente'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                #$sWhere .= " b.NOM_CLIENTE LIKE '%".$_GET['filCliente']."%' ";
                $sWhere .= " b.COD_CLIENTE LIKE '%".$_GET['filCliente']."%' ";
            }
            if( isset($_GET['filRegion']) && $_GET['filRegion'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " g.NOM_REGION LIKE '%".$_GET['filRegion']."%' ";
            }
            if( isset($_GET['filEstado']) && $_GET['filEstado'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " i.NOM_ESTADO LIKE '%".$_GET['filEstado']."%' ";
            }
//            if( isset($_GET['filTipo']) && $_GET['filTipo'] != "")
//            {
//                if ( $sWhere == "" )
//                {
//                        $sWhere = "WHERE ";
//                }
//                else
//                {
//                        $sWhere .= " AND ";
//                }
//
//                $sWhere .= " a.TIPO_TIENDA_COD_TIPO = '".$_GET['filEstado']."' ";
//            }

            /********************** Create Query */
            $sql = "
                SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
                FROM $sTable a
                left outer join t_cliente b
                on a.CLIENTE_COD_CLIENTE = b.COD_CLIENTE
                left outer join t_agrupacion c
                on a.AGRUPACION_COD_AGRUPACION = c.COD_AGRUPACION
                left outer join t_tipo_tienda d
                on a.TIPO_TIENDA_COD_TIPO = d.COD_TIPO
                left outer join t_comuna e
                on a.COMUNA_COD_COMUNA = e.COD_COMUNA
                left outer join t_ciudad f
                on a.COMUNA_Ciudad_COD_CIUDAD = f.COD_CIUDAD
                left outer join t_region g
                on a.COMUNA_Ciudad_Region_COD_REGION = g.COD_REGION
                left outer join t_tienda_zona h
                on a.ZONA_COD_ZONA = h.COD_ZONA
                left outer join t_tienda_estado i
                on a.ESTADO_COD_ESTADO = i.COD_ESTADO
                $sWhere
		GROUP BY 
		a.COD_TIENDA
		, a.NOM_TIENDA
		, a.DIREC_TIENDA
		, b.NOM_CLIENTE
		, c.NOM_AGRUPACION
		, d.NOM_TIPO
		, e.NOM_COMUNA
		, f.NOM_CIUDAD
		, g.NOM_REGION
		, h.NOM_ZONA
		, i.NOM_ESTADO
                $sOrder
                $sLimit";

            $result_data = $model->goCustomQuery($sql);

            $found_rows = $model->goCustomQuery("SELECT FOUND_ROWS()");

            $total_rows = $model->goCustomQuery("SELECT COUNT(`".$sIndexColumn."`) FROM $sTable");

            /*
            * Output
            */
            $iTotal = $total_rows->fetch(PDO::FETCH_NUM);
            $iTotal = $iTotal[0];

            $iFilteredTotal = $found_rows->fetch(PDO::FETCH_NUM);
            $iFilteredTotal = $iFilteredTotal[0];

            #echo $iFilteredTotal." ";

            $output = array(
                    "sEcho" => intval($_GET['sEcho']),
                    "iTotalRecords" => $iTotal,
                    "iTotalDisplayRecords" => $iFilteredTotal,
                    "aaData" => array()
            );

            $k = 1;
            while($aRow = $result_data->fetch(PDO::FETCH_NUM))
            {
                    $row = array();

                    for ( $i=0 ; $i<count($aColumns) ; $i++ )
                    {
                        // FORCE UTF8 IF NEEDED
                        #$row[] = utf8_encode($aRow[ $i ]);
                        $row[] = $aRow[ $i ];
                    }

                    $output['aaData'][] = $row;

                    $k++;
            }

            echo json_encode( $output );
        }

	//SHOW
	public function tiendasAddForm($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/TiendasModel.php';
		require_once 'models/ClientesModel.php';
		require_once 'models/RegionesModel.php';
		require_once 'models/CiudadesModel.php';
		require_once 'models/ComunasModel.php';

		//Creamos una instancia de nuestro "modelo"
		$modelTiendas = new TiendasModel();
		$modelClientes = new ClientesModel();
		$modelRegiones = new RegionesModel();
		$modelCiudades = new CiudadesModel();
		$modelComunas = new ComunasModel();
                
		//Le pedimos al modelo todos los items
		$data['lista_clientes'] = $modelClientes->getAllClientes("CL1");
		$data['lista_regiones'] = $modelRegiones->getAllRegiones();
		$data['lista_ciudades'] = $modelCiudades->getAllCiudades();
		$data['lista_comunas'] = $modelComunas->getAllComunas();
		$data['lista_zonas'] = $modelTiendas->getAllZonas();
		$data['lista_tipos'] = $modelTiendas->getAllTipos();
		$data['lista_agrupaciones'] = $modelTiendas->getAllAgrupaciones();
		$data['lista_estados'] = $modelTiendas->getAllEstados();		

		//Extraer ultimo codigo existente
		$newCode = $modelTiendas->getLastTiendaCode();
                $newBtk = $modelTiendas->getLastTiendaBTK();

		if($code = $newCode->fetch(PDO::FETCH_ASSOC))
		{
                    //Crear un nuevo codigo: anterior+1
                    $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_TIENDA']);
                    $LETRAS = preg_replace("/[0-9]/", "", $code['COD_TIENDA']);  
                    $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                    $LEER = strlen($NUEVO_CODIGO);

                    if($LEER > 3)
                            $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                    else
                            $CODIGOFINAL = $LETRAS."0".$NUEVO_CODIGO;

                    $data['codigo_nuevo'] = $CODIGOFINAL;
		}
		else
                {
                    $data['codigo_nuevo'] = "ST0001";
                    $data['error'] = $newCode;
                }
                if($code = $newBtk->fetch(PDO::FETCH_ASSOC))
		{
                    //Crear un nuevo codigo: anterior+1
                    $NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_BTK']);
                    $LETRAS = preg_replace("/[0-9]/", "", $code['COD_BTK']);  
                    $NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
                    $LEER = strlen($NUEVO_CODIGO);

                    if($LEER > 3)
                            $CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
                    else
                            $CODIGOFINAL = $LETRAS."00".$NUEVO_CODIGO;

                    $data['codigo_btk_nuevo'] = $CODIGOFINAL;
		}
		else
                {
                    $data['codigo_btk_nuevo'] = "X001";
                    $data['error'] = $newBtk;
                }

		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "TIENDAS > NUEVO";

                $data['controller'] = "tiendas";
                $data['action'] = "tiendasAdd";
                $data['action_b'] = "tiendasDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("tiendas_new.php", $data);
	}
	
	//PROCESS
	public function tiendasAdd()
	{
                $session = FR_Session::singleton();

		//Parametros login form
		if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
                {
                    //Avoid resubmit
                    $session->orig_timestamp = microtime(true);

                    #isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
                    $cod_tienda = $_POST['cod_tienda'];
                    $cod_btk = $_POST['cod_btk'];
                    $name_suffix = $_POST['name'];
                    $direccion = $_POST['direccion'];
                    $cod_cliente = $_POST['cod_cliente'];
                    $cod_region = $_POST['cod_region'];
                    $cod_ciudad = $_POST['cod_ciudad'];
                    $cod_comuna = $_POST['cod_comuna'];
                    $cod_zona = $_POST['cod_zona'];
                    $cod_tipo = $_POST['cod_tipo'];
                    $cod_agrupacion = $_POST['cod_agrupacion'];
                    $cod_estado = $_POST['cod_estado'];

                    //Incluye el modelo que corresponde
                    require_once 'models/TiendasModel.php';
                    require_once 'models/ClientesModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $model = new TiendasModel();
                    $modelCliente = new ClientesModel();

                    //Concatenar cliente + sufijo para nombre de tienda
                    $result = $modelCliente->getClienteByCode($cod_cliente);
                    if($result->rowCount() > 0){
                        $name_cliente = $result->fetch(PDO::FETCH_ASSOC);
                        $name = $name_cliente['NOM_CLIENTE']."-".$name_suffix;
                    }
                    else
                        $name = $name_suffix;

                    //Le pedimos al modelo todos los items
                    $result = $model->addNewTienda($cod_tienda, $cod_btk, $name, $direccion, $cod_cliente, $cod_region, $cod_ciudad, $cod_comuna, $cod_zona, $cod_tipo, $cod_agrupacion, $cod_estado);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->tiendasDt(1);
                    else
                        $this->tiendasDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
				
		}
		else
		{
                    $this->tiendasDt();
		}
			
	}

	// EDIT FORM
	public function tiendasEditForm()
	{
            if($_POST)
            {
                $cod_tienda = $_POST['cod_tienda'];

                require_once 'models/TiendasModel.php';
                require_once 'models/ClientesModel.php';
                require_once 'models/RegionesModel.php';
                require_once 'models/CiudadesModel.php';
                require_once 'models/ComunasModel.php';

                //Models objects
                $model = new TiendasModel();
                $modelClientes = new ClientesModel();
                $modelRegiones = new RegionesModel();
                $modelCiudades = new CiudadesModel();
                $modelComunas = new ComunasModel();

                $data['listado'] = $model->getTiendaByCodigo($cod_tienda);
                $data['lista_cliente'] = $modelClientes->getAllClientes('CL1');
                $data['listado_regiones'] = $modelRegiones->getAllRegiones();
                $data['listado_ciudades'] = $modelCiudades->getAllCiudades();
                $data['listado_comunas'] = $modelComunas->getAllComunas();
                $data['listado_zonas'] = $model->getAllZonas();
                $data['listado_tipos'] = $model->getAllTipos();
                $data['listado_agrupaciones'] = $model->getAllAgrupaciones();
                $data['listado_estados'] = $model->getAllEstados();

                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "TIENDAS > Edici&Oacute;n";

                $data['controller'] = "tiendas";
                $data['action'] = "tiendasEdit";
                $data['action_b'] = "tiendasDt";

                $this->view->show("tiendas_edit.php", $data);
            }
            else
            {
                    $this->tiendasDt(2);
            }
	}

	//PROCESS
	public function tiendasEdit()
	{
            $session = FR_Session::singleton();

            //Parametros form
            if(strval($_POST['form_timestamp']) == strval($session->orig_timestamp))
            {
                //Avoid resubmit
                $session->orig_timestamp = microtime(true);

                $code = $_POST['txtcodigo'];
                $code_btk = $_POST['txtcodigobtk'];

                $prename = $_POST['prename'];
                $postname = $_POST['name'];
                $name = $prename."-".$postname;


                $direccion = $_POST['txtdireccion'];
                $cod_cliente = $_POST['cod_cliente'];
                $cod_region = $_POST['cod_region'];
                $cod_ciudad = $_POST['cod_ciudad'];
                $cod_comuna = $_POST['cod_comuna'];
                $cod_tipo = $_POST['cod_tipo'];
                $cod_agrupacion = $_POST['cod_agrupacion'];
                $cod_estado = $_POST['cod_estado'];
                $cod_zona = $_POST['cod_zona'];

                //Incluye el modelo que corresponde
                require_once 'models/TiendasModel.php';

                //Creamos una instancia de nuestro "modelo"
                $model = new TiendasModel();

                //Le pedimos al modelo todos los items
                $result = $model->editTienda($code, $code_btk, $name, $direccion, $cod_cliente, $cod_region, $cod_ciudad, $cod_comuna, $cod_tipo, $cod_agrupacion, $cod_estado, $cod_zona);

                //catch errors
                $error = $result->errorInfo();

                if($error[0] == 00000)
                    $this->tiendasDt(1);
                else
                    $this->tiendasDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
            }
            else
            {
                $this->tiendasDt();
            }
	}
        
        
        /*******************************************************************************
	* ZONAS
	*******************************************************************************/
        
        //FORM
        public function zonasDt($error_flag = 0, $message = "")
	{
		//Incluye el modelo que corresponde
		require_once 'models/TiendasModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new TiendasModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllZonas();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 1);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "ZONAS";
		
                $data['controller'] = "tiendas";
                $data['action'] = "zonasEditForm";
                $data['action_b'] = "zonasDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("zonas_dt.php", $data);
	}
	
	//SHOW
	public function zonasAddForm($error_flag = 0)
	{
		//Import models
		require_once 'models/TiendasModel.php';
		
		//Models objects
		$model = new TiendasModel();
	
		//Extraer ultimo codigo de segmento existente
		$segment_code = $model->getNewZonaCode();
		
		if($code = $segment_code->fetch(PDO::FETCH_ASSOC))
		{
			//Crear un nuevo codigo: anterior+1
			$NUEVO_CODIGO = $code['COD_ZONA'];
			$NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
			
			$data['new_code'] = $NUEVO_CODIGO;
		}
		else
			$data['new_code'] = "1";
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "ZONAS > NUEVA";
                
                $data['controller'] = "tiendas";
                $data['action'] = "zonasAdd";
                $data['action_b'] = "zonasDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("zonas_new.php", $data);
	}
	
	//PROCESS
	public function zonasAdd()
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
                    require_once 'models/TiendasModel.php';

                    //Creamos una instancia de nuestro "modelo"
                    $model = new TiendasModel();

                    //Le pedimos al modelo todos los items
                    $result = $model->addNewZona($code, $name);

                    //catch errors
                    $error = $result->errorInfo();

                    if($error[0] == 00000)
                        $this->zonasDt(1);
                    else
                        $this->zonasDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
		}
		else
		{
                    $this->zonasDt();
		}
	}

	//SHOW
	public function zonasEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
			$data['name'] = $_POST['name'];
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "ZONAS > EDICI&Oacute;N";
                        
                        $data['controller'] = "tiendas";
                        $data['action'] = "zonasEdit";
                        $data['action_b'] = "zonasDt";
                        
			$this->view->show("zonas_edit.php", $data);
		}
		else
		{
			$this->zonasDt(2);
		}
	}
	
	//PROCESS
	public function zonasEdit()
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
			require_once 'models/TiendasModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editZona($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();
                        
                        if($error[0] == 00000)
                            $this->zonasDt(1);
                        else
                            $this->zonasDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
		}
		else
		{
			$this->zonasDt();
		}
	}
        
        
        /*******************************************************************************
	* TIPOS
	*******************************************************************************/
        
        //FORM
        public function tiposDt($error_flag = 0, $message = "")
	{
		//Incluye el modelo que corresponde
		require_once 'models/TiendasModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new TiendasModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllTipos();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 1);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "tipos";
		
                $data['controller'] = "tiendas";
                $data['action'] = "tiposEditForm";
                $data['action_b'] = "tiposDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("tipos_dt.php", $data);
	}
	
	//SHOW
	public function tiposAddForm($error_flag = 0)
	{
		//Import models
		require_once 'models/TiendasModel.php';
		
		//Models objects
		$model = new TiendasModel();
	
		//Extraer ultimo codigo de segmento existente
		$new_code = $model->getNewTipoCode();
		
		if($code = $new_code->fetch(PDO::FETCH_ASSOC))
		{
			//Crear un nuevo codigo: anterior+1
			$NUEVO_CODIGO = $code['COD_TIPO'];
			$NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
			
			$data['new_code'] = $NUEVO_CODIGO;
		}
		else
			$data['new_code'] = "1";
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "TIPOS > NUEVO";
                
                $data['controller'] = "tiendas";
                $data['action'] = "tiposAdd";
                $data['action_b'] = "tiposDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("tipos_new.php", $data);
	}
	
	//PROCESS
	public function tiposAdd()
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
			require_once 'models/TiendasModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->addNewTipo($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();
                        
                        if($error[0] == 00000)
                            $this->tiposDt(1);
                        else
                            $this->tiposDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        
//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->tiposDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->tiposDt(2);
//			}
				
		}
		else
		{
			$this->tiposDt();
		}
	}

	//SHOW
	public function tiposEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
			$data['name'] = $_POST['name'];

			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "tipos > EDICI&Oacute;N";
                        
                        $data['controller'] = "tiendas";
                        $data['action'] = "tiposEdit";
                        $data['action_b'] = "tiposDt";

			$this->view->show("tipos_edit.php", $data);
		}
		else
		{
			$this->tiposDt(2);
		}
	}
	
	//PROCESS
	public function tiposEdit()
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
			require_once 'models/TiendasModel.php';

			//Creamos una instancia de nuestro "modelo"
			$model = new TiendasModel();

			//Le pedimos al modelo todos los items
			$result = $model->editTipo($code, $name);

                        //catch errors
                        $error = $result->errorInfo();

                        if($error[0] == 00000)
                            $this->tiposDt(1);
                        else
                            $this->tiposDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");

//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->tiposDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->tiposDt(2);
//			}
		}
		else
		{
			$this->tiposDt();
		}
	}
        
        
        /*******************************************************************************
	* AGRUPACIONES
	*******************************************************************************/
        
        //FORM
        public function agrupacionesDt($error_flag = 0, $message = "")
	{
		//Incluye el modelo que corresponde
		require_once 'models/TiendasModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new TiendasModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllAgrupaciones();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 1);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "agrupaciones";
		
                $data['controller'] = "tiendas";
                $data['action'] = "agrupacionesEditForm";
                $data['action_b'] = "agrupacionesDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("agrupaciones_dt.php", $data);
	}
	
	//SHOW
	public function agrupacionesAddForm($error_flag = 0)
	{
		//Import models
		require_once 'models/TiendasModel.php';
		
		//Models objects
		$model = new TiendasModel();
	
		//Extraer ultimo codigo de segmento existente
		$new_code = $model->getNewAgrupacionCode();
		
		if($code = $new_code->fetch(PDO::FETCH_ASSOC))
		{
			//Crear un nuevo codigo: anterior+1
			$NUEVO_CODIGO = preg_replace("/[A-Za-z]/", "", $code['COD_AGRUPACION']);
			$LETRAS = preg_replace("/[0-9]/", "", $code['COD_AGRUPACION']);  
			$NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
			$LEER = strlen($NUEVO_CODIGO);
			
			$CODIGOFINAL = $LETRAS.$NUEVO_CODIGO;
			
			$data['new_code'] = $CODIGOFINAL;
		}
		else
			$data['new_code'] = "GRP1";
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "AGRUPACIONES > NUEVO";
                
                $data['controller'] = "tiendas";
                $data['action'] = "agrupacionesAdd";
                $data['action_b'] = "agrupacionesDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("agrupaciones_new.php", $data);
	}
	
	//PROCESS
	public function agrupacionesAdd()
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
			require_once 'models/TiendasModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->addNewAgrupacion($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();
                        
                        if($error[0] == 00000)
                            $this->agrupacionesDt(1);
                        else
                            $this->agrupacionesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        
//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->agrupacionesDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->agrupacionesDt(2);
//			}
				
		}
		else
		{
			$this->agrupacionesDt();
		}
	}

	//SHOW
	public function agrupacionesEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
			$data['name'] = $_POST['name'];
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "agrupaciones > EDICI&Oacute;N";
                        
                        $data['controller'] = "tiendas";
                        $data['action'] = "agrupacionesEdit";
                        $data['action_b'] = "agrupacionesDt";
                        
			$this->view->show("agrupaciones_edit.php", $data);
		}
		else
		{
			$this->agrupacionesDt(2);
		}
	}
	
	//PROCESS
	public function agrupacionesEdit()
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
			require_once 'models/TiendasModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editAgrupacion($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();
                        
                        if($error[0] == 00000)
                            $this->agrupacionesDt(1);
                        else
                            $this->agrupacionesDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        
//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->agrupacionesDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->agrupacionesDt(2);
//			}
		}
		else
		{
			$this->agrupacionesDt();
		}
	}
        
        
        /*******************************************************************************
	* ESTADOS
	*******************************************************************************/
        
        //FORM
        public function estadosDt($error_flag = 0, $message = "")
	{
		//Incluye el modelo que corresponde
		require_once 'models/TiendasModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new TiendasModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllEstados();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 1);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                
		//Titulo pagina
		$data['titulo'] = "estados tienda";
		
                $data['controller'] = "tiendas";
                $data['action'] = "estadosEditForm";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("estados_dt.php", $data);
	}
	
	//SHOW
	public function estadosAddForm($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/TiendasModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new TiendasModel();
	
		//Extraer ultimo codigo de segmento existente
		$new_code = $model->getNewEstadoCode();
		
		if($code = $new_code->fetch(PDO::FETCH_ASSOC))
		{
			//Crear un nuevo codigo: anterior+1
			$NUEVO_CODIGO = $code['COD_ESTADO'];
			$NUEVO_CODIGO = (int) $NUEVO_CODIGO + 1;
			
			$data['new_code'] = $NUEVO_CODIGO;
		}
		else
			$data['new_code'] = "1";
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "ESTADOS tienda > NUEVO";
                
                $data['controller'] = "tiendas";
                $data['action'] = "estadosAdd";
                $data['action_b'] = "estadosDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("estados_new.php", $data);
	}
	
	//PROCESS
	public function estadosAdd()
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
                        require_once 'models/TiendasModel.php';

                        //Creamos una instancia de nuestro "modelo"
                        $model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->addNewEstado($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();
                        
                        if($error[0] == 00000)
                            $this->estadosDt(1);
                        else
                            $this->estadosDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        
//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->estadosDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->estadosDt(2);
//			}
				
		}
		else
		{
			$this->estadosDt();
		}
	}

	//SHOW
	public function estadosEditForm()
	{
		if($_POST)
		{
			$data['code'] = $_POST['code'];
			$data['name'] = $_POST['name'];
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "estados tienda > EDICI&Oacute;N";
                        
                        $data['controller'] = "tiendas";
                        $data['action'] = "estadosEdit";
                        $data['action_b'] = "estadosDt";
                        
			$this->view->show("estados_edit.php", $data);
		}
		else
		{
			$this->estadosDt(2);
		}
	}
	
	//PROCESS
	public function estadosEdit()
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
                        require_once 'models/TiendasModel.php';

                        //Creamos una instancia de nuestro "modelo"
                        $model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editEstado($code, $name);
			
                        //catch errors
                        $error = $result->errorInfo();
                        
                        if($error[0] == 00000)
                            $this->estadosDt(1);
                        else
                            $this->estadosDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
                        
//			if($result->rowCount() > 0)
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->estadosDt(1);
//			}
//			else
//			{
//				//Destroy POST
//				unset($_POST);
//				
//				$this->estadosDt(2);
//			}
		}
		else
		{
			$this->estadosDt();
		}
	}
        
        /*
         * Verify BTK Code with Customer
         * AJAX
         */
        public function verifyCodBTKbyCliente()
        {
            if($_REQUEST['cod_btk'] && $_REQUEST['cod_cliente']){
                $inputBtk = $_REQUEST['cod_btk'];
                $inputCliente = $_REQUEST['cod_cliente'];
                
                if($_REQUEST['cod_btk'] != "" && $_REQUEST['cod_cliente'] != ""){
                    $sql = "SELECT cod_btk FROM t_tienda 
                            WHERE cod_btk = '$inputBtk'
                            AND cliente_cod_cliente = '$inputCliente'";

                    //Incluye el modelo que corresponde
                    require_once 'models/TiendasModel.php';
                    $model = new TiendasModel();
                    $result = $model->goCustomQuery($sql);

                    if($result->rowCount() > 0)
                        echo "false";
                    else
                        echo "true";
                }
                else {
                    echo "true";
                }
            }
            else{
               echo "true";
            }
        }
        
        /*
         * Verify Tienda Name
         * AJAX
         */
        public function verifyNameTienda()
        {
            if($_REQUEST['name'] && $_REQUEST['prename'])
            {
                $name = $_REQUEST['prename']."-".$_REQUEST['name'];
                
                $input = utf8_decode($name);
                $sql = "SELECT nom_tienda FROM t_tienda WHERE nom_tienda = '$input'";
            
                //Incluye el modelo que corresponde
                require_once 'models/TiendasModel.php';
                $model = new TiendasModel();
                $result = $model->goCustomQuery($sql);

                if($result->rowCount() > 0)
                    echo "false";
                else
                    echo "true";
            }
            else
                echo "false";
        }
        
        /**
         * Reports export
         */
        public function exportToExcel(){
            //Incluye el modelo que corresponde
            require_once 'models/ReportsModel.php';
            $model = new ReportsModel();
            #$model->exportTiendas();
            $model->exportTiendasToCsv();
        }
        
        /*
         * Change tienda state by one or more selections
         */
        public function tiendasEditSelection()
	{
//            print_r($_POST);
            
            $codigos_inicio = $_POST['item_row'];
            $estado = $_POST['edit_type'];
            
            require_once 'models/TiendasModel.php';
            $model = new TiendasModel();
            
            foreach ($codigos_inicio as $codes) {
//                print($codes."<br>");
                
                $codes = explode("--", $codes);
//                print_r($codes[0]);
//                print("<br>");
                
                $model->editTiendaEstado($codes[0], $codes[1], $estado);
            }
	}
}
?>