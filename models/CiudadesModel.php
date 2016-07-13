<?php
class CiudadesModel extends ModelBase
{
	/*******************************************************************************
	* CIUDADES
	*******************************************************************************/
	
	//GET ALL CIUDADES
	public function getAllCiudades()
	{
            $consulta = $this->db->prepare("
                    SELECT COD_CIUDAD, NOM_CIUDAD, REGION_COD_REGION 
                    FROM t_ciudad ORDER BY NOM_CIUDAD
            ");

            $consulta->execute();

            return $consulta;
	}
	
	//GET ALL CIUDADES BY REGION
	public function getAllCiudadesByRegion($code = 'N/A')
	{
            $consulta = $this->db->prepare("
                    SELECT COD_CIUDAD, NOM_CIUDAD, REGION_COD_REGION 
                    FROM t_ciudad 
                    WHERE REGION_COD_REGION LIKE '%$code%'
                    ORDER BY NOM_CIUDAD
            ");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewCiudadCode()
	{
            $consulta = $this->db->prepare("SELECT COD_CIUDAD FROM t_ciudad 
                    WHERE COD_CIUDAD NOT LIKE '%N/A%' 
                    ORDER BY COD_CIUDAD DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA estado
	public function addNewCiudad($code, $name, $code_b)
	{
            #$session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_ciudad 
                            (COD_CIUDAD
                            , NOM_CIUDAD
                            , REGION_COD_REGION) 
                    VALUES 
                            ('$code'
                            ,'$name'
                            ,'$code_b')
                    ");

            $consulta->execute();

            return $consulta;
	}
        
        //Edit estado
        public function editCiudad($code, $name, $code_b)
	{
            #$session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_ciudad
                        SET 
                            NOM_CIUDAD = '$name'
                        WHERE COD_CIUDAD = '$code'
                            AND REGION_COD_REGION = '$code_b'
                            ");

            $consulta->execute();

            return $consulta;
	}
}
?>