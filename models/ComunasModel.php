<?php
class ComunasModel extends ModelBase
{
	/*******************************************************************************
	* COMUNAS
	*******************************************************************************/
	
	//GET ALL ESTADOS
	public function getAllComunas()
	{
            $consulta = $this->db->prepare("
                    SELECT COD_COMUNA, NOM_COMUNA, CIUDAD_COD_CIUDAD, CIUDAD_REGION_COD_REGION 
                    FROM t_comuna ORDER BY NOM_COMUNA");

            $consulta->execute();

            return $consulta;
	}
        
        //GET ALL CIUDADES BY REGION
	public function getAllComunasByRegion($code = 'N/A')
	{
            $consulta = $this->db->prepare("
                    SELECT COD_COMUNA, NOM_COMUNA, CIUDAD_COD_CIUDAD, CIUDAD_REGION_COD_REGION
                    FROM t_comuna 
                    WHERE CIUDAD_REGION_COD_REGION LIKE '%$code%'
                    ORDER BY NOM_COMUNA");

            $consulta->execute();

            return $consulta;
	}
        
        //GET ALL COMUNAS BY CIUDAD
        public function getAllComunasByCiudad($code = 'N/A')
	{
            $consulta = $this->db->prepare("
                    SELECT COD_COMUNA, NOM_COMUNA, CIUDAD_COD_CIUDAD, CIUDAD_REGION_COD_REGION
                    FROM t_comuna 
                    WHERE CIUDAD_COD_CIUDAD LIKE '%$code%'
                    ORDER BY NOM_COMUNA");

            $consulta->execute();

            return $consulta;
	}
        
        
        //GET LAST CODE
	public function getNewComunaCode()
	{
            $consulta = $this->db->prepare("SELECT COD_COMUNA FROM t_comuna 
                    WHERE COD_COMUNA NOT LIKE '%N/A%' 
                    ORDER BY COD_COMUNA DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA estado
	public function addNewComuna($code, $name, $code_b, $code_c)
	{
            #$session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_comuna 
                            (COD_COMUNA
                            , NOM_COMUNA
                            , CIUDAD_COD_CIUDAD
                            , CIUDAD_REGION_COD_REGION) 
                    VALUES 
                            ('$code'
                            ,'$name'
                            ,'$code_b'
                            ,'$code_c')");

            $consulta->execute();

            return $consulta;
	}
        
        //Edit estado
        public function editComuna($code, $name, $code_b, $code_c)
	{
            #$session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_comuna
                        SET 
                            NOM_COMUNA = '$name'
                        WHERE COD_COMUNA = '$code'
                            AND CIUDAD_COD_CIUDAD = '$code_b'
                            AND CIUDAD_REGION_COD_REGION = '$code_c'");

            $consulta->execute();

            return $consulta;
	}
}
?>