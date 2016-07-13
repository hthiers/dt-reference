<?php
/**
 * Controller class for models
 * @author hernan thiers 
 */
class ModelsController extends ControllerBase
{
	/*******************************************************************************
	* MODELS
	*******************************************************************************/
	
	/*
         * Models Datatable
         */
	public function modelsDt($error_flag = 0, $message = "")
	{
		//Incluye el modelo que corresponde
		require_once 'models/BrandsModel.php';
                require_once 'models/CategoriesModel.php';
                require_once 'models/SegmentsModel.php';
                require_once 'models/UsersModel.php';
                require_once 'models/ModelsModel.php';

		//Creamos una instancia de nuestro "modelo"
		$modelBra = new BrandsModel();
                $modelCat = new CategoriesModel();
                $modelSeg = new SegmentsModel();
                $modelUser = new UsersModel();
                $modelModels = new ModelsModel();

                $session = FR_Session::singleton();

		//Le pedimos al modelo todos los items
		$lista_brands = $modelBra->getAllBrands();
		$lista_bu = $modelCat->getAllBu();
                #$lista_category = $modelCat->getAllCategorySimple();
                #$lista_gbu = $modelCat->getAllGbu();
                $lista_segments = $modelSeg->getAllSegmentsSimple();
                $lista_estados = $modelModels->getAllEstados();

                $data['permiso_editar'] = 0;
                $data['permiso_exportar'] = 0;
                $data['permiso_mod_estado'] = 0;
                $data['permiso_mod_premium'] = 0;
                
                // Permisos edicion
                $permisos = $modelUser->getUserModulePrivilegeByModule($session->id, 4);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC))
                    $data['permiso_editar'] = $row['EDITAR'];

                // Permisos exportar
                $permisos = $modelUser->getUserModulePrivilegeByModule($session->id, 12);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC))
                    $data['permiso_exportar'] = $row['VER'];

                // Permisos mod estado
                $permisos = $modelUser->getUserModulePrivilegeByModule($session->id, 13);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC))
                    $data['permiso_mod_estado'] = $row;
                
                // Permisos mod premium (atributo)
                $permisos = $modelUser->getUserModulePrivilegeByModule($session->id, 14);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC))
                    $data['permiso_mod_premium'] = $row;

                //Extraer solo los CATEGORY necesarios
                $sql = "
                    SELECT 
                        A.COD_CATEGORY
                        , A.NAME_CATEGORY
                    FROM t_category A
                    WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                    ORDER BY A.NAME_CATEGORY";
                
                $lista_category = $modelCat->goCustomQuery($sql);
                
                //Extraer solo los GBU necesarios
                $sql = "
                    SELECT 
                        A.COD_GBU
                        , B.COD_CATEGORY AS CAT_COD_CATEGORY
                        , A.NAME_GBU
                        , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                    FROM t_gbu A
                    INNER JOIN t_category B
                    ON A.COD_CATEGORY = B.COD_CATEGORY
                    WHERE A.COD_CATEGORY NOT IN ('AT','ML')
                      AND A.NAME_GBU NOT LIKE '%install%'
                    ORDER BY A.NAME_GBU";

                $lista_gbu = $modelCat->goCustomQuery($sql);
                
                //Lista de targets GFK (atributos)
                $lista_targets = $modelModels->getAllAtributesByGroup(1);

		//Pasamos a la vista toda la información que se desea representar
		$data['lista_brands'] = $lista_brands;
                $data['lista_bu'] = $lista_bu;
                $data['lista_category'] = $lista_category;
                $data['lista_gbu'] = $lista_gbu;
                $data['lista_segments'] = $lista_segments;
                $data['lista_estados'] = $lista_estados;
                $data['lista_targets'] = $lista_targets;

		//Titulo pagina
		$data['titulo'] = "MODELOS";
                
                $data['controller'] = "models";
                $data['action'] = "modelsEditForm";
                $data['action_exp_excel'] = "exportToExcel";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag, $message);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("models_dt.php", $data);
	}

        /**
         * Get product models for ajax dynamic query
         * AJAX
         * @return json
         */
        public function ajaxModelsDt()
        {
            //Incluye el modelo que corresponde
            require_once 'models/ModelsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ModelsModel();

            /*
            * Building dynamic query
            */
            $sTable = $model->getTableName();
            
            $aColumns = array('A.COD_MODEL'
                        , 'A.COD_MODEL_SUFFIX'
                        , 'D.NAME_SEGMENT'
                        , 'E.NAME_SUB_SEGMENT'
                        , 'F.NAME_MICRO_SEGMENT'
                        , 'B.NAME_GBU'
                        , 'C.NAME_BRAND'
                        , 'G.NAME_ESTADO'
                        , 'A.COD_GBU'
                        , 'H.COD_ATRIBUTE');

            $sIndexColumn = "COD_MODEL";
            $aTotalColumns = count($aColumns);

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
                    $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
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
            if( isset($_GET['filBrand']) && $_GET['filBrand'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_BRAND LIKE '%".$_GET['filBrand']."%' ";
            }
            if( isset($_GET['filBu']) && $_GET['filBu'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_BU LIKE '%".$_GET['filBu']."%' ";
            }
            if( isset($_GET['filCategory']) && $_GET['filCategory'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_CATEGORY LIKE '%".$_GET['filCategory']."%' ";
            }
            if( isset($_GET['filGbu']) && $_GET['filGbu'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_GBU LIKE '%".$_GET['filGbu']."%' ";
            }
            if( isset($_GET['filSegment']) && $_GET['filSegment'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_SEGMENT LIKE '%".$_GET['filSegment']."%' ";
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

                $sWhere .= " G.NAME_ESTADO = '".$_GET['filEstado']."' ";
            }
            if( isset($_GET['filPremium']) && $_GET['filPremium'] == '1')
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_MODEL IN (SELECT HH.COD_MODEL 
				FROM t_product_has_t_product_atribute HH 
				WHERE HH.COD_ATRIBUTE = 1) ";
            }
            /*
             * Filtro de atributo:
             * Solo filtra un atributo a la vez, para filtrar de a varios
             * es necesario crear tantas condiciones como cantidad de filtros
             * por atributo a la vez.
             */
            if( isset($_GET['filAtribute']) && $_GET['filAtribute'] != '')
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_MODEL IN (SELECT HI.COD_MODEL 
				FROM t_product_has_t_product_atribute HI 
				WHERE HI.COD_ATRIBUTE = '".$_GET['filAtribute']."') ";
            }

            /********************** Create Query */
            
            //PATCH
            unset($aColumns[9]);    // replace column by group
            $aColumns[9] = 'GROUP_CONCAT(H.COD_ATRIBUTE)';
            
            $sql = "
                SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
                FROM $sTable A
                INNER JOIN T_GBU B
                ON A.COD_GBU = B.COD_GBU
                INNER JOIN T_BRAND C 
                ON A.COD_BRAND = C.COD_BRAND 
                LEFT OUTER JOIN T_SEGMENT D
                ON (A.COD_SEGMENT = D.COD_SEGMENT 
                AND A.COD_GBU = D.COD_GBU)
                LEFT OUTER JOIN T_SUB_SEGMENT E 
                ON (A.COD_SUB_SEGMENT = E.COD_SUB_SEGMENT 
                AND A.COD_GBU = E.COD_GBU)
                LEFT OUTER JOIN T_MICRO_SEGMENT F 
                ON (A.COD_MICRO_SEGMENT = F.COD_MICRO_SEGMENT 
                AND A.COD_GBU = F.COD_GBU)
                INNER JOIN T_PRODUCT_ESTADO G
                ON A.COD_ESTADO = G.COD_ESTADO
                LEFT OUTER JOIN t_product_has_t_product_atribute H
		ON A.COD_MODEL = H.COD_MODEL
                $sWhere
                GROUP BY
                    A.COD_MODEL
                    , A.COD_MODEL_SUFFIX
                    , D.NAME_SEGMENT
                    , E.NAME_SUB_SEGMENT
                    , F.NAME_MICRO_SEGMENT
                    , B.NAME_GBU
                    , C.NAME_BRAND
                    , G.NAME_ESTADO
                    , A.COD_GBU
                $sOrder
                $sLimit";
            
            #print($sql);
            
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

                for($i=0;$i<$aTotalColumns;$i++)
                {
                    // FORCE UTF8
                    #$row[] = utf8_encode($aRow[ $i ]);
                    $row[] = $aRow[$i];
                }

                $output['aaData'][] = $row;

                $k++;
            }

            echo json_encode($output);
        }

	/*
         * SHOW MODELS ADD FORM
         * DEACTIVATED
         */
	public function modelsAddForm($error_flag = 0)
	{
            /*
		require_once 'models/ModelsModel.php';
                require_once 'models/CategoriesModel.php';
                require_once 'models/SegmentsModel.php';
                require_once 'models/BrandsModel.php';

                //Models objects
                $model = new ModelsModel();
                $modelCategories = new CategoriesModel();
                $modelSegments = new SegmentsModel();
                $modelBrands = new BrandsModel();

                $data['listado'] = $model->getModelByCodigo($code);
                $data['lista_bu'] = $modelCategories->getAllBu();
                $data['lista_category'] = $modelCategories->getAllCategory();
                $data['lista_gbu'] = $modelCategories->getAllGbu();
                $data['lista_estados'] = $model->getAllEstados();
                $data['lista_segments'] = $modelSegments->getAllSegments();
                $data['lista_subsegments'] = $modelSegments->getAllSubSegments();
                $data['lista_microsegments'] = $modelSegments->getAllMicroSegments();
                $data['lista_brands'] = $modelBrands->getAllBrands();

                //Finalmente presentamos nuestra plantilla
                $data['titulo'] = "MODELOS > Edición";

                $data['controller'] = "models";
                $data['action'] = "modelsAdd";
                $data['action_b'] = "modelsDt";		
		
		//Finalmente presentamos nuestra plantilla
		$data['titulo'] = "MODELOS > NUEVO";

		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);

		$this->view->show("models_new.php", $data);
             * 
             */
            
            $this->modelsDt();
	}
	
	//PROCESS
	public function modelsAdd()
	{
            /*
		//Parametros login form
		if($_POST)
		{
			#isset($_POST['txtcodigo'], $_POST['txtnombre'], $_POST['txtgbu']);
			$cod_tienda = $this->utils->cleanQuery($_POST['cod_tienda']);
			$cod_btk = $this->utils->cleanQuery($_POST['cod_btk']);
			$name = $this->utils->cleanQuery($_POST['name']);
			$direccion = $this->utils->cleanQuery($_POST['direccion']);
			$cod_cliente = $this->utils->cleanQuery($_POST['cod_cliente']);
			$cod_region = $this->utils->cleanQuery($_POST['cod_region']);
			$cod_ciudad = $this->utils->cleanQuery($_POST['cod_ciudad']);
			$cod_comuna = $this->utils->cleanQuery($_POST['cod_comuna']);
			$cod_zona = $this->utils->cleanQuery($_POST['cod_zona']);
			$cod_tipo = $this->utils->cleanQuery($_POST['cod_tipo']);
			$cod_agrupacion = $this->utils->cleanQuery($_POST['cod_agrupacion']);
			$cod_estado = $this->utils->cleanQuery($_POST['cod_estado']);
			
			//Incluye el modelo que corresponde
			require_once 'models/TiendasModel.php';
			
			//Creamos una instancia de nuestro "modelo"
			$model = new TiendasModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->addNewTienda($cod_tienda, $cod_btk, $name, $direccion, $cod_cliente, $cod_region, $cod_ciudad, $cod_comuna, $cod_zona, $cod_tipo, $cod_agrupacion, $cod_estado);
			
			if($result->rowCount() > 0)
			{
				//Destroy POST
				unset($_POST);
				
				$this->tiendasDt(1);
			}
			else
			{
				//Destroy POST
				unset($_POST);
				
				$this->tiendasDt(2);
			}
		}
		else
		{
			$this->tiendasDt(2);
		}
             * 
             */
            
            $this->modelsDt();
	}

	// EDIT FORM
	public function modelsEditForm()
	{
		if($_POST)
		{
			$code = $_POST['code'];
			#$cod_cliente = $this->utils->cleanQuery($_POST['cod_cliente']);
			#$cod_region = $this->utils->cleanQuery($_POST['cod_region']);
			#$cod_ciudad = $this->utils->cleanQuery($_POST['cod_ciudad']);
			#$cod_comuna = $this->utils->cleanQuery($_POST['cod_comuna']);
			#$cod_zona = $this->utils->cleanQuery($_POST['cod_zona']);
			#$cod_tipo = $this->utils->cleanQuery($_POST['cod_tipo']);
			#$cod_agrupacion = $this->utils->cleanQuery($_POST['cod_agrupacion']);
			#$cod_estado = $this->utils->cleanQuery($_POST['cod_estado']);
			
			require_once 'models/ModelsModel.php';
			require_once 'models/CategoriesModel.php';
                        require_once 'models/SegmentsModel.php';
                        require_once 'models/BrandsModel.php';
		
			//Models objects
			$model = new ModelsModel();
			$modelCategories = new CategoriesModel();
                        $modelSegments = new SegmentsModel();
                        $modelBrands = new BrandsModel();
		
			$data['listado'] = $model->getModelByCodigo($code);
			$data['lista_bu'] = $modelCategories->getAllBu();
			$data['lista_category'] = $modelCategories->getAllCategory();
			$data['lista_gbu'] = $modelCategories->getAllGbu();
                        $data['lista_estados'] = $model->getAllEstados();
                        $data['lista_segments'] = $modelSegments->getAllSegments();
                        $data['lista_subsegments'] = $modelSegments->getAllSubSegments();
                        $data['lista_microsegments'] = $modelSegments->getAllMicroSegments();
                        $data['lista_brands'] = $modelBrands->getAllBrands();
			
			//Finalmente presentamos nuestra plantilla
			$data['titulo'] = "MODELOS > Edici&Oacute;n";
			
                        //time value for submit control
                        $data['orig_timestamp'] = microtime(true); //debug
                        $session->orig_timestamp = $data['orig_timestamp'];
                        
                        $data['controller'] = "models";
                        $data['action'] = "modelsEdit";
                        $data['action_b'] = "modelsDt";
                        
			$this->view->show("models_edit.php", $data);
		}
		else
		{
			$this->modelsDt(2);
		}
	}

	public function modelsEdit()
	{
            $session = FR_Session::singleton();

            $code = $_POST['code'];
            $code_suffix = $_POST['code_suffix'];
            $cod_estado = $_POST['cod_estado'];
            $cod_segment = $_POST['cod_segment'];
            $cod_sub_segment = $_POST['cod_sub_segment'];
            $cod_micro_segment = $_POST['cod_micro_segment'];
            $cod_bu = $_POST['cod_bu'];
            $cod_category = $_POST['cod_category'];
            $cod_gbu = $_POST['cod_gbu'];
            $cod_brand = $_POST['cod_brand'];

            //Incluye el modelo que corresponde
            require_once 'models/ModelsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ModelsModel();

            //Le pedimos al modelo todos los items
            $result = $model->editModel($code, $code_suffix, $cod_gbu, $cod_category, $cod_bu, $cod_segment, $cod_sub_segment, $cod_micro_segment, $cod_brand, $cod_estado);

            //catch errors
            $error = $result->errorInfo();

            if($error[0] == 00000)
                $this->modelsDt(1);
            else
                $this->modelsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
	}

        public function ajaxModelsEdit()
        {
            //Incluye el modelo que corresponde
            require_once 'models/ModelsModel.php';

            //Ajax requested vars
            $newcode = $_REQUEST['value'];
            $suffix = $_REQUEST['suffix'];
            $cod_model = $_REQUEST['idData'];
            $gbu = $_REQUEST['gbu'];
            $target_requested = $_REQUEST['target_col'];

            if($target_requested == 1)
                $target_column = "COD_SEGMENT";
            else if($target_requested == 2)
                $target_column = "COD_SUB_SEGMENT";
            else if($target_requested == 3)
                $target_column = "COD_MICRO_SEGMENT";
            #else if($target_requested == 4)
            #   $target_column = "COD_GBU";
            else if($target_requested == 5)
                $target_column = "COD_BRAND";
            else if($target_requested == 6)
                $target_column = "COD_ESTADO";

            //Update table (EVITAR CAMBIO SUBSEGMENTO PARA MOBILES)
            if($target_requested == 2 && $gbu == "MST"){
                echo "nothing to do...";
            }
            else{
                $model = new ModelsModel();
                $result = $model->editModelSpecificColumn($cod_model, $suffix, $target_column, $newcode, $gbu);
                
                //catch errors for debugging
                $error = $result->errorInfo();
                
                #if($error[0] == 00000)
                #    echo "success!";
                #else
                #    echo $error[1];
            }
        }

        
        /*******************************************************************************
	* ESTADOS
	*******************************************************************************/
        
        //FORM
        public function estadosDt($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/ModelsModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new ModelsModel();
	
		//Le pedimos al modelo todos los items
		$listado = $model->getAllEstados();

		//Pasamos a la vista toda la información que se desea representar
		$data['listado'] = $listado;
		
                // Obtener permisos de edición
                require_once 'models/UsersModel.php';
                $userModel = new UsersModel();
                
                $session = FR_Session::singleton();
                
                $permisos = $userModel->getUserModulePrivilegeByModule($session->id, 4);
                if($row = $permisos->fetch(PDO::FETCH_ASSOC)){
                    $data['permiso_editar'] = $row['EDITAR'];
                }
                else
                    $data['permiso_editar'] = 0;
                
		//Titulo pagina
		$data['titulo'] = "estados modelo";
		
                $data['controller'] = "models";
                $data['action'] = "estadosEditForm";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("models_estados_dt.php", $data);
	}
	
	//SHOW
	public function estadosAddForm($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/ModelsModel.php';
		
		//Creamos una instancia de nuestro "modelo"
		$model = new ModelsModel();
	
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
		$data['titulo'] = "ESTADOS modelo > NUEVO";
                
                $data['controller'] = "models";
                $data['action'] = "estadosAdd";
                $data['action_b'] = "estadosDt";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		$this->view->show("models_estados_new.php", $data);
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
                        require_once 'models/ModelsModel.php';

                        //Creamos una instancia de nuestro "modelo"
                        $model = new ModelsModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->addNewEstado($code, $name);
			
			if($result->rowCount() > 0)
			{
				//Destroy POST
				unset($_POST);
				
				$this->estadosDt(1);
			}
			else
			{
				//Destroy POST
				unset($_POST);
				
				$this->estadosDt(2);
			}
				
		}
		else
		{
			$this->estadosDt(2);
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
			$data['titulo'] = "estados modelo > EDICI&Oacute;N";
                        
                        $data['controller'] = "models";
                        $data['action'] = "estadosEdit";
                        $data['action_b'] = "estadosDt";
                        
			$this->view->show("models_estados_edit.php", $data);
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
                        require_once 'models/ModelsModel.php';

                        //Creamos una instancia de nuestro "modelo"
                        $model = new ModelsModel();
			
			//Le pedimos al modelo todos los items
			$result = $model->editEstado($code, $name);
			
			if($result->rowCount() > 0)
			{
				//Destroy POST
				unset($_POST);
				
				$this->estadosDt(1);
			}
			else
			{
				//Destroy POST
				unset($_POST);
				
				$this->estadosDt(2);
			}
		}
		else
		{
			$this->estadosDt(2);
		}
	}
        
        public function listEstadosJSON()
        {
            //Incluye el modelo que corresponde
            require_once 'models/ModelsModel.php';

            //Creamos una instancia de nuestro "modelo"
            $model = new ModelsModel();

            $listado = $model->getAllEstados();
            
            $output = array();
            
            while ($row = $listado->fetch(PDO::FETCH_ASSOC))
            {
                $output[$row['COD_ESTADO']] = $row['NAME_ESTADO'];
            }
            
            $output['selected'] = $_GET['current'];
            
            echo json_encode( $output );
        }

        /*
         * Models full dt export
         */
        public function exportToExcel()
        {
            if($_GET['column']){
                $cols = $_GET['column'];

                require_once 'models/ReportsModel.php';
                $model = new ReportsModel();
                #$result = $model->exportModelsSelection($cols);
                $result = $model->exportModelsToCsvCustom();
                
                // debug
                print($result);
            }
            else
                print("nothing to do...");
        }

        /*
         * Models filtered dt export
         */
        public function exportToExcelFiltered(){
            $sTable = "t_product";
            $col_list = $_GET['column'];
            $large = count($col_list);

            // Columns structure
            $sql_headers = "";
            $sql_values = "";
            $sql_joins = "";
            
            foreach($col_list as $i => $value){
                switch ($value) {
                    case 'SEGMENTS':
                        $sql_headers .= "'COD_SEGMENT'";
                        $sql_headers .= ", 'NAME_SEGMENT'";
                        $sql_headers .= ", 'COD_SUB_SEGMENT'";
                        $sql_headers .= ", 'NAME_SUB_SEGMENT'";
                        $sql_headers .= ", 'COD_MICRO_SEGMENT'";
                        $sql_headers .= ", 'NAME_MICRO_SEGMENT'";
                        
                        $sql_values .= "D.COD_SEGMENT";
                        $sql_values .= ", D.NAME_SEGMENT";
                        $sql_values .= ", E.COD_SUB_SEGMENT";
                        $sql_values .= ", E.NAME_SUB_SEGMENT";
                        $sql_values .= ", F.COD_MICRO_SEGMENT";
                        $sql_values .= ", F.NAME_MICRO_SEGMENT";
                        
                        $sql_joins .= "
                        INNER JOIN T_SEGMENT SG 
                            ON (SG.COD_SEGMENT = PD.COD_SEGMENT
                            AND SG.COD_GBU = PD.COD_GBU)
                        INNER JOIN T_SUB_SEGMENT SS
                            ON (SS.COD_SUB_SEGMENT = PD.COD_SUB_SEGMENT 
                            AND SS.COD_GBU = PD.COD_GBU)
                        INNER JOIN T_MICRO_SEGMENT MS
                            ON (MS.COD_MICRO_SEGMENT = PD.COD_MICRO_SEGMENT
                            AND MS.COD_GBU = PD.COD_GBU)";
                        
                        break;
                    case 'COD_GBU':
                        $sql_headers .= "'COD_GBU'";
                        $sql_headers .= ", 'NAME_GBU'";
                        
                        $sql_values .= "B.COD_GBU";
                        $sql_values .= ", B.NAME_GBU";
                        
                        $sql_joins .= "
                        LEFT OUTER JOIN T_GBU LGBU
                            ON (LGBU.COD_GBU = PD.COD_GBU)";

                        break;
                    case 'COD_BRAND':
                        $sql_headers .= "'COD_BRAND'";
                        $sql_headers .= ", 'NAME_BRAND'";
                        
                        $sql_values .= "C.COD_BRAND";
                        $sql_values .= ", C.NAME_BRAND";

                        $sql_joins .= "
                        LEFT OUTER JOIN T_BRAND BR 
                            ON BR.COD_BRAND=PD.COD_BRAND";

                        break;
                    case 'COD_ESTADO':
                        $sql_headers .= "'COD_ESTADO'";
                        $sql_headers .= ", 'NAME_ESTADO'";
                        
                        $sql_values .= "G.COD_ESTADO";
                        $sql_values .= ", G.NAME_ESTADO";

                        $sql_joins .= "
                        INNER JOIN T_PRODUCT_ESTADO ST
                            ON ST.COD_ESTADO=PD.COD_ESTADO";
                        
                        break;
                    case 'ATRIBUTE':
                        $sql_headers .= "'ATRIBUTE'";
                        
                        $sql_values .= "IFNULL(GROUP_CONCAT(I.name_atribute), '') as ATRIBUTE";

                        $sql_joins .= "
                        LEFT OUTER JOIN t_product_has_t_product_atribute PA
                            on PD.cod_model = PA.cod_model
                        LEFT OUTER JOIN t_product_atribute PAD
                            on PA.cod_atribute = PAD.cod_atribute";
                        
                        break;
                    default:
                        $sql_headers .= "'".$value."'";
                        $sql_values .= "A.".$value;
                        
                        break;
                }

                if($i < $large-1){
                    $sql_headers .= ", ";
                    $sql_values .= ", ";
                }
            }

            $aColumns = array('A.COD_MODEL'
                        , 'A.COD_MODEL_SUFFIX'
                        , 'D.NAME_SEGMENT'
                        , 'E.NAME_SUB_SEGMENT'
                        , 'F.NAME_MICRO_SEGMENT'
                        , 'B.NAME_GBU'
                        , 'C.NAME_BRAND'
                        , 'G.NAME_ESTADO'
                        , 'A.COD_GBU'
                        , 'H.COD_ATRIBUTE');

            /******************** Filtering */
            $sWhere = "";

            if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
            {
                $sWhere = "WHERE (";
                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    $sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch']."%' OR ";
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
            if( isset($_GET['filBrand']) && $_GET['filBrand'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_BRAND LIKE '%".$_GET['filBrand']."%' ";
            }
            if( isset($_GET['filBu']) && $_GET['filBu'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_BU LIKE '%".$_GET['filBu']."%' ";
            }
            if( isset($_GET['filCategory']) && $_GET['filCategory'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_CATEGORY LIKE '%".$_GET['filCategory']."%' ";
            }
            if( isset($_GET['filGbu']) && $_GET['filGbu'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_GBU LIKE '%".$_GET['filGbu']."%' ";
            }
            if( isset($_GET['filSegment']) && $_GET['filSegment'] != "")
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_SEGMENT LIKE '%".$_GET['filSegment']."%' ";
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

                $sWhere .= " G.NAME_ESTADO = '".$_GET['filEstado']."' ";
            }
            if( isset($_GET['filPremium']) && $_GET['filPremium'] == '1')
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_MODEL IN (SELECT HH.COD_MODEL 
                            FROM t_product_has_t_product_atribute HH 
                            WHERE HH.COD_ATRIBUTE = 1) ";
            }
            /*
             * Filtro de atributo:
             * Solo filtra un atributo a la vez, para filtrar de a varios
             * es necesario crear tantas condiciones como cantidad de filtros
             * por atributo a la vez.
             */
            if( isset($_GET['filAtribute']) && $_GET['filAtribute'] != '')
            {
                if ( $sWhere == "" )
                {
                        $sWhere = "WHERE ";
                }
                else
                {
                        $sWhere .= " AND ";
                }

                $sWhere .= " A.COD_MODEL IN (SELECT HI.COD_MODEL 
                        FROM t_product_has_t_product_atribute HI 
                        WHERE HI.COD_ATRIBUTE = '".$_GET['filAtribute']."') ";
            }
            
            /*
             * Grouping
             * NOTE: AVOID 'IFNULL' COLUMN FOR ATRIBUTES GROUP
             */
            if(strstr($sql_values, ', IFNULL', true))
                $sGroup = "GROUP BY ".strstr($sql_values, ', IFNULL', true);
            else
                $sGroup = "GROUP BY ".$sql_values;

            require_once 'models/ReportsModel.php';
            $model = new ReportsModel();
            $model->exportModelsCustom($sql_values, $sql_headers, $sTable, $sWhere, $sGroup);

//            print($result);
        }
        
        public function modelsPremiumSelection()
	{
            $models = $_POST['item_row'];
            $atribute = $_POST['premium_flag'];
            
            $session = FR_Session::singleton();
            
            require_once 'models/ModelsModel.php';
            $model = new ModelsModel();
            
            foreach ($models as $code) {
                $result = $model->editModelAtribute($code, $atribute);
                
                if($result == null)
                    echo "ERROR";
                else{
                    $error = $result->errorInfo();
                    echo $error[0];
                }
            }
	}
        
        public function modelsEditSelection()
	{
//            print_r($_POST);
//            print_r($_POST['item_row']);
            
            $models = $_POST['item_row'];
//            $suffixes = $_POST['suffix_row'];
            $estado = $_POST['edit_type'];
            
            $session = FR_Session::singleton();
            
            require_once 'models/ModelsModel.php';
            $model = new ModelsModel();
            
            foreach ($models as $code) {
                print($code);
                
                $model->editModelEstado($code, $estado);
            }

            /*
             * el ciclo deberia cargar un array por cada error encontrado!
             */
            
//            echo "success!";
//            
//            $result = $model->editEstado($code, $name);
            
//
//            //catch errors
//            $error = $result->errorInfo();
//
//            if($error[0] == 00000)
//                $this->modelsDt(1);
//            else
//                $this->modelsDt(10, "Ha ocurrido un error: <i>".$error[2]."</i>");
	}
}
?>