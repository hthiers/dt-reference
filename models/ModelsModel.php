<?php
/**
 * Model class for models.
 * Note that some functions are not in use because of ajax model script.
 * @author Hernan Thiers
 */
class ModelsModel extends ModelBase
{
	/*******************************************************************************
	* MODELOS
	*******************************************************************************/
	
	//GET ALL tiendas
	public function getAllModels()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            a.COD_MODEL
                            , a.COD_MODEL_SUFFIX
                            , a.COD_GBU
                            , a.COD_SEGMENT
                            , a.COD_SUB_SEGMENT
                            , a.COD_MICRO_SEGMENT
                            , a.COD_BRAND
                            , a.COD_ESTADO
                    FROM t_product a");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	//GET model by CODE
	public function getModelByCodigo($code = '')
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            A.COD_MODEL
                            , A.COD_MODEL_SUFFIX
                            , A.COD_GBU
                            , A.COD_CATEGORY
                            , A.COD_BU
                            , A.COD_SEGMENT
                            , A.COD_SUB_SEGMENT
                            , A.COD_MICRO_SEGMENT
                            , A.COD_BRAND
                            , A.COD_ESTADO
                    FROM t_product A
                    WHERE a.COD_MODEL LIKE '%$code%'
                        OR A.COD_MODEL_SUFFIX LIKE '%$code%'
            ");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	/**
         * Edit a model by its code
         * @param string $code
         * @param string $code_suffix
         * @param string $cod_gbu
         * @param string $cod_category
         * @param string $cod_bu
         * @param string $cod_segment
         * @param string $cod_sub_segment
         * @param string $cod_micro_segment
         * @param string $cod_brand
         * @param int $cod_estado
         * @return int 
         */
	public function editModel($code, $code_suffix, $cod_gbu, $cod_category, $cod_bu, $cod_segment, $cod_sub_segment, $cod_micro_segment, $cod_brand, $cod_estado)
	{
            require_once 'AdminModel.php';

            $logModel = new AdminModel();
            $sql = "UPDATE t_product SET ... WHERE COD_MODEL = '$code' AND COD_MODEL_SUFFIX = '$code_suffix'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_product
                            SET 
                                    COD_GBU = '$cod_gbu'
                                    , COD_CATEGORY = '$cod_category'
                                    , COD_BU = '$cod_bu'
                                    , COD_SEGMENT = '$cod_segment'
                                    , COD_SUB_SEGMENT = '$cod_sub_segment'
                                    , COD_MICRO_SEGMENT = '$cod_micro_segment'
                                    , COD_BRAND = '$cod_brand'
                                    , COD_ESTADO = '$cod_estado'
                            WHERE COD_MODEL = '$code'
                                AND COD_MODEL_SUFFIX = '$code_suffix'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'MODELOS');

            return $consulta;
	}
        
        public function editModelSpecificColumn($cod_model, $cod_model_suffix, $column, $value, $gbu)
        {
            //Log registry
            require_once 'AdminModel.php';
            
            $logModel = new AdminModel();
            $sql = "UPDATE t_product SET $column WHERE COD_MODEL = '$cod_model'";
            $session = FR_Session::singleton();
            
            $consulta = $this->db->prepare("UPDATE t_product
                            SET 
                                $column = '$value'
                            WHERE COD_MODEL = '$cod_model'
                                AND COD_MODEL_SUFFIX = '$cod_model_suffix'");

            $consulta->execute();

            //Save log event
            $logModel->addNewEvent($session->usuario, $sql, 'MODELOS');

            return $consulta;
        }

	//NUEVO MODELO - NO DEBERIA SER USADO POR UN USUARIO!!
	public function addNewModel($code, $code_suffix, $cod_gbu, $cod_category, $cod_bu, $cod_segment, $cod_sub_segment, $cod_micro_segment, $cod_brand, $cod_estado)
	{
            #$session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_product 
                            (COD_MODEL
                            , COD_MODEL_SUFFIX
                            , COD_GBU
                            , COD_CATEGORY
                            , COD_BU
                            , COD_SEGMENT
                            , COD_SUB_SEGMENT
                            , COD_MICRO_SEGMENT
                            , COD_BRAND
                            , COD_ESTADO) 
                    VALUES 
                            ('$code'
                            ,'$code_suffix'
                            ,'$cod_gbu'
                            ,'$cod_category'
                            ,'$cod_bu'
                            ,'$cod_segment'
                            ,'$cod_sub_segment'
                            ,'$cod_micro_segment'
                            ,'$cod_brand'
                            ,'$cod_estado')");

            $consulta->execute();

            return $consulta;
	}
        
        /**
         * Change estado modelo
         * @param varchar $model
         * @param int $estado
         * @return int 
         */
        public function editModelEstado($model, $estado)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_product SET '$estado' WHERE '$model'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_product
                        SET 
                            COD_ESTADO = '$estado'
                        WHERE COD_MODEL = '$model'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'MODELOS');

            return $consulta;
	}
        


        /*******************************************************************************
	* ESTADOS
	*******************************************************************************/

	//GET ALL ESTADOS
	public function getAllEstados()
	{
            $consulta = $this->db->prepare("
                    SELECT COD_ESTADO, NAME_ESTADO FROM t_product_estado");

            $consulta->execute();

            return $consulta;
	}

        //GET LAST CODE
	public function getNewEstadoCode()
	{
            $consulta = $this->db->prepare("SELECT COD_ESTADO FROM t_product_estado 
                    WHERE COD_ESTADO NOT LIKE '%N/A%' ORDER BY COD_ESTADO DESC LIMIT 1");
            $consulta->execute();

            return $consulta;
	}

        //NUEVA t_estado
	public function addNewEstado($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_product_estado VALUES '$code', '$name'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_product_estado 
                            (COD_ESTADO
                            , NAME_ESTADO) 
                    VALUES 
                            ('$code'
                            ,'$name')");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'MODELOS-ESTADOS');

            return $consulta;
	}
        
        //Edit t_estado
        public function editEstado($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_product_estado SET '$name' WHERE '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_product_estado
                        SET 
                            NAME_ESTADO = '$name'
                        WHERE COD_ESTADO = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'MODELOS-ESTADOS');

            return $consulta;
	}
        
        
        /*******************************************************************************
	* OTHERS
	*******************************************************************************/
        
        /**
         * Get PDO object from custom sql query
         * NOTA: Esta función impide tener un control de la consulta sql (depende desde donde se llame).
         * @param string $sql
         * @return PDO 
         */
        public function goCustomQuery($sql)
        {
            $consulta = $this->db->prepare($sql);

            $consulta->execute();

            return $consulta;
        }
        
        /**
         * Get database table name linked to this model
         * NOTA: Solo por lógica modelo = tabla
         * @return string 
         */
        public function getTableName()
        {
            $tableName = "t_product";
            
            return $tableName;
        }
        
        /**
         * Get database table column names
         * NOTA: Solo por lógica modelo = tabla
         * @return array
         */
        public function getTableColumnNames()
        {
            $columns = array('COD_MODEL'
                , 'COD_MODEL_SUFFIX'
                , 'COD_GBU'
                , 'COD_CATEGORY'
                , 'COD_BU'
                , 'COD_SEGMENT'
                , 'COD_SUB_SEGMENT'
                , 'COD_MICRO_SEGMENT'
                , 'COD_BRAND'
                , 'COD_ESTADO'
            );
            
            return $columns;
        }
        
        
        /*******************************************************************************
	* ATRIBUTES
	*******************************************************************************/
        
        /**
         * get one model atribute by atribute code
         * @param int $cod_atribute
         * @param varchar $cod_model
         * @return PDO
         */
        public function getModelAtributeByCodAtribute($cod_atribute, $cod_model){
            $consulta = $this->db->prepare("SELECT
                    C.COD_ATRIBUTE
                    , C.NAME_ATRIBUTE
                    , A.COD_MODEL
            FROM T_PRODUCT A
            INNER JOIN T_PRODUCT_HAS_T_PRODUCT_ATRIBUTE B
            ON A.COD_MODEL = B.COD_MODEL
            INNER JOIN T_PRODUCT_ATRIBUTE C
            ON B.COD_ATRIBUTE = C.COD_ATRIBUTE
            WHERE A.COD_MODEL = '$cod_model'
              AND C.COD_ATRIBUTE = '$cod_atribute'");
            
            $consulta->execute();
            
            return $consulta;
        }
        
        /**
         * get all model atributes by cod model
         * @param varchar $cod_model
         * @return PDO
         */
        public function getAllModelAtributesByCodModel($cod_model){
            $consulta = $this->db->prepare("SELECT
                    C.COD_ATRIBUTE
                    , C.NAME_ATRIBUTE
                    , A.COD_MODEL
            FROM T_PRODUCT A
            INNER JOIN T_PRODUCT_HAS_T_PRODUCT_ATRIBUTE B
            ON A.COD_MODEL = B.COD_MODEL
            INNER JOIN T_PRODUCT_ATRIBUTE C
            ON B.COD_ATRIBUTE = C.COD_ATRIBUTE
            WHERE A.COD_MODEL = '$cod_model'");
            
            $consulta->execute();
            
            return $consulta;
        }
        
        public function getAllAtributesByGroup($cod_group){
            $consulta = $this->db->prepare("SELECT
                    A.COD_ATRIBUTE
                    , A.NAME_ATRIBUTE
                    , A.COD_CATEGORY_GFK
                    , B.COD_GROUP_GFK
                    , B.NAME_GROUP_GFK
            FROM T_PRODUCT_ATRIBUTE A
            INNER JOIN T_GROUP_GFK B
            ON A.COD_GROUP_GFK = B.COD_GROUP_GFK
            WHERE A.COD_GROUP_GFK = '$cod_group'
            ORDER BY A.NAME_ATRIBUTE");
            
            $consulta->execute();
            
            return $consulta;
        }
        
        /**
         * get atribute name by code
         * @param int $cod_atribute
         * @return PDO
         */
        public function getAtributeNameByCodAtribute($cod_atribute){
            $consulta = $this->db->prepare("SELECT
                    A.COD_ATRIBUTE
                    , A.NAME_ATRIBUTE
            FROM T_PRODUCT_ATRIBUTE A
            WHERE A.COD_ATRIBUTE = '$cod_atribute'");
            
            $consulta->execute();
            
            return $consulta;
        }
        
        /**
         * update (add or remove) atribute for a model
         * @param varchar $cod_model
         * @param int $cod_atribute
         * @return PDO
         */
        public function editModelAtribute($cod_model, $cod_atribute){
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE product_atribute SET '$cod_atribute' WHERE '$cod_model'";
            $session = FR_Session::singleton();
            
            // look for atribute on match table
            $consulta = $this->getModelAtributeByCodAtribute($cod_atribute, $cod_model);
            $consulta->execute();
            $atrib_value = $consulta->fetchColumn();
            
            if($atrib_value == null || $atrib_value == ""){
                // no tiene este atributo => agregarlo
                $consulta = $this->db->prepare("INSERT INTO 
                            t_product_has_t_product_atribute
                            (COD_MODEL, COD_ATRIBUTE)
                            VALUES
                            ('$cod_model', '$cod_atribute')");
                
                $consulta->execute();

                //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
                $logModel->addNewEvent($session->usuario, $sql, 'MODELOS');
                
                return $consulta;
            }
            else{
                // ya tiene este atributo => quitarlo
                $consulta = $this->db->prepare("DELETE FROM
                            t_product_has_t_product_atribute
                            WHERE COD_MODEL = '$cod_model'
                              AND COD_ATRIBUTE = '$cod_atribute'");
                
                $consulta->execute();
                
                return $consulta;
            }
            
            return null;
        }
}
?>