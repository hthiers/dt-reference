<?php
class CategoriesModel extends ModelBase
{
	/*******************************************************************************
	* BU
	*******************************************************************************/
	
	//GET ALL 
	public function getAllBu()
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        COD_BU
                        , NAME_BU
                    FROM t_bu 
                    ORDER BY NAME_BU
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewBuCode()
	{
            $consulta = $this->db->prepare("SELECT COD_BU 
                    FROM t_bu
                    WHERE COD_BU NOT LIKE '%N/A%' 
                    ORDER BY COD_BU DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA
	public function addNewBu($code, $name)
	{
            $session = FR_Session::singleton();

            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_bu VALUES '$code', '$name'";

            $consulta = $this->db->prepare("
                    INSERT INTO t_bu 
                            (COD_BU
                            , NAME_BU) 
                    VALUES 
                            ('$code'
                            ,'$name')
                    ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'BU');

            return $consulta;
	}
        
        //Edit estado
        public function editBu($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_bu WHERE COD_BU ='$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_bu
                        SET 
                            NAME_BU = '$name'
                        WHERE COD_BU = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'BU');

            return $consulta;
	}
        
        
        /*******************************************************************************
	* CATEGORY
	*******************************************************************************/
	
	//GET ALL 
	public function getAllCategory()
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        A.COD_CATEGORY
                        , B.COD_BU AS BU_COD_BU
                        , A.NAME_CATEGORY
                        , B.NAME_BU AS BU_NAME_BU
                    FROM t_category A
                    INNER JOIN t_bu B
                    ON A.COD_BU = B.COD_BU
                    ORDER BY A.NAME_CATEGORY
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET ALL 
	public function getAllCategorySimple()
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        A.COD_CATEGORY
                        , A.NAME_CATEGORY
                    FROM t_category A
                    ORDER BY A.NAME_CATEGORY
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewCategoryCode()
	{
            $consulta = $this->db->prepare("SELECT COD_CATEGORY 
                    FROM t_category
                    WHERE COD_CATEGORY NOT LIKE '%N/A%' 
                    ORDER BY COD_CATEGORY DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA
	public function addNewCategory($code, $code_b, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_category VALUE COD_CATEGORY ='$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_category 
                            (COD_CATEGORY
                            , COD_BU
                            , NAME_CATEGORY) 
                    VALUES 
                            ('$code'
                            ,'$code_b'
                            ,'$name')
                    ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'CATEGORY');

            return $consulta;
	}
        
        //Edit estado
        public function editCategory($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_category WHERE COD_CATEGORY ='$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_category
                        SET 
                            NAME_CATEGORY = '$name'
                        WHERE COD_CATEGORY = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'CATEGORY');

            return $consulta;
	}
        
        
        /*******************************************************************************
	* GBU
	*******************************************************************************/
	
	//GET ALL 
	public function getAllGbu()
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        A.COD_GBU
                        , B.COD_CATEGORY AS CAT_COD_CATEGORY
                        , A.NAME_GBU
                        , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                    FROM t_gbu A
                    INNER JOIN t_category B
                    ON A.COD_CATEGORY = B.COD_CATEGORY
                    ORDER BY A.COD_GBU
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET ALL 
	public function getAllGbuByCategory($cod_category)
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        A.COD_GBU
                        , B.COD_CATEGORY AS CAT_COD_CATEGORY
                        , A.NAME_GBU
                        , B.NAME_CATEGORY AS CAT_NAME_CATEGORY
                    FROM t_gbu A
                    INNER JOIN t_category B
                    ON A.COD_CATEGORY = B.COD_CATEGORY
                    WHERE A.COD_CATEGORY = '$cod_category'
                    ORDER BY A.COD_GBU
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET GBU BY BU 
	public function getGbuByBu($code)
	{
            $consulta = $this->db->prepare("
                    SELECT 
                        A.COD_GBU
                    FROM t_gbu A
                    INNER JOIN t_category B
                    ON A.COD_CATEGORY = B.COD_CATEGORY
                    INNER JOIN t_bu C
                    ON B.COD_BU = C.COD_BU
                    WHERE C.COD_BU = '$code'
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewGbuCode()
	{
            $consulta = $this->db->prepare("SELECT COD_GBU 
                    FROM t_gbu
                    WHERE COD_GBU NOT LIKE '%N/A%' 
                    ORDER BY COD_GBU DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA
	public function addNewGbu($code, $code_b, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_gbu VALUES COD_GBU ='$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_gbu 
                            (COD_GBU
                            , COD_CATEGORY
                            , NAME_GBU) 
                    VALUES 
                            ('$code'
                            ,'$code_b'
                            ,'$name')
                    ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'GBU');

            return $consulta;
	}
        
        //Edit estado
        public function editGbu($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_gbu WHERE COD_GBU ='$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_gbu
                        SET 
                            NAME_GBU = '$name'
                        WHERE COD_GBU = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'GBU');

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
}