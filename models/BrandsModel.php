<?php
class BrandsModel extends ModelBase
{
	/*******************************************************************************
	* REGIONES
	*******************************************************************************/
	
	//GET ALL 
	public function getAllBrands()
	{
            $consulta = $this->db->prepare("
                    SELECT COD_BRAND, NAME_BRAND
                    FROM t_brand ORDER BY NAME_BRAND
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewBrandCode()
	{
            $consulta = $this->db->prepare("SELECT COD_BRAND 
                    FROM t_brand
                    WHERE COD_BRAND NOT LIKE '%N/A%' 
                    ORDER BY COD_BRAND DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA
	public function addNewBrand($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_brand VALUES '$code', '$name'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_brand 
                            (COD_BRAND
                            , NAME_BRAND) 
                    VALUES 
                            ('$code'
                            ,'$name')
                    ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'BRANDS');

            return $consulta;
	}
        
        //Edit estado
        public function editBrand($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_brand WHERE COD_BRAND = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_brand
                        SET 
                            NAME_BRAND = '$name'
                        WHERE COD_BRAND = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'BRANDS');

            return $consulta;
	}
        
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