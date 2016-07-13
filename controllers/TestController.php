<?php
class TestController extends ControllerBase
{
	/*******************************************************************************
	* TESTING CONTROLS
	*******************************************************************************/

    public function ajaxTestLoad()
    {
        //Incluye el modelo que corresponde
        require_once 'models/AdminModel.php';

        //Creamos una instancia de nuestro "modelo"
        $model = new AdminModel();
        
        
        /*
         * Build up dynamic query
         */
        /******************* Table Name */
        $sTable = $model->getTableName();
        
        /******************* Columns */
        $aColumns = array('USUARIO'
            , 'FECHA'
            , 'MODIFICACION'
            , 'IP_CLIENTE'
            , 'HOST_NAME'
            , 'MODULO'
        );

        /******************** Indexed column */
        $sIndexColumn = "ID";
        
        /******************** Paging */
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
            $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".mysql_real_escape_string( $_GET['iDisplayLength'] );
        
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
                                        mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
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
                $sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
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

                $sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
            }
        }
        
        /********************** Create Query */
        $sql = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM $sTable
            $sWhere
            $sOrder
            $sLimit
            ";
        
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

                for ( $i=0 ; $i<count($aColumns) ; $i++ )
                {
                    $row[] = utf8_encode($aRow[ $i ]);
                }

                $output['aaData'][] = $row;

                $k++;
        }
        
        #echo $sql;
        echo json_encode( $output );
    }

        /*
         * Models Datatable new version
         * DEVELOPMENT PURPOSES ONLY!
         */
	public function devModelsDt($error_flag = 0)
	{
		//Incluye el modelo que corresponde
		require_once 'models/BrandsModel.php';
                require_once 'models/CategoriesModel.php';
                require_once 'models/SegmentsModel.php';

		//Creamos una instancia de nuestro "modelo"
		$modelBra = new BrandsModel();
                $modelCat = new CategoriesModel();
                $modelSeg = new SegmentsModel();

		//Le pedimos al modelo todos los items
		$lista_brands = $modelBra->getAllBrands();
		$lista_bu = $modelCat->getAllBu();
                $lista_category = $modelCat->getAllCategory();
                $lista_gbu = $modelCat->getAllGbu();
                $lista_segments = $modelSeg->getAllSegments();

		//Pasamos a la vista toda la información que se desea representar
		$data['lista_brands'] = $lista_brands;
                $data['lista_bu'] = $lista_bu;
                $data['lista_category'] = $lista_category;
                $data['lista_gbu'] = $lista_gbu;
                $data['lista_segments'] = $lista_segments;

		//Titulo pagina
		$data['titulo'] = "DEV MODELOS";

                $data['controller'] = "models";
                $data['action'] = "modelsEditForm";
                
		//Posible error
		$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("dev_models_dt.php", $data);
	}

        /*
         * jquery dran n drop
         */
        public function dragndrop()
	{
		//Titulo pagina
		#$data['titulo'] = "DRAG N DROP";

                #$data['controller'] = "models";
                #$data['action'] = "modelsEditForm";
                
		//Posible error
		#$data['error_flag'] = $this->errorMessage->getError($error_flag);
		
		//Finalmente presentamos nuestra plantilla
		$this->view->show("test_dragndrop.php", $data);
	}
}
?>