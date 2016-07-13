<?php
class RegionesModel extends ModelBase
{
	/*******************************************************************************
	* REGIONES
	*******************************************************************************/
	
	//GET ALL REGIONES
	public function getAllRegiones()
	{
            $consulta = $this->db->prepare("
                    SELECT COD_REGION, NOM_REGION, COD_REGION_GFK, NOM_REGION_GFK 
                    FROM t_region ORDER BY COD_REGION");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewRegionCode()
	{
            $consulta = $this->db->prepare("SELECT COD_REGION FROM t_region 
                    WHERE COD_REGION NOT LIKE '%N/A%' 
                    ORDER BY COD_REGION DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA estado
	public function addNewRegion($code, $name, $codegfk = 'N/A', $namegfk = 'NO APLICA')
	{
            $consulta = $this->db->prepare("
                    INSERT INTO t_region 
                            (COD_REGION
                            , NOM_REGION
                            , COD_REGION_GFK
                            , NOM_REGION_GFK) 
                    VALUES 
                            ('$code'
                            ,'$name'
                            ,'$codegfk'
                            ,'$namegfk')
                    ");

            $consulta->execute();

            return $consulta;
	}
        
        //Edit estado
        public function editRegion($code, $name, $codegfk = '', $namegfk = '')
	{
            $consulta = $this->db->prepare("UPDATE t_region
                        SET 
                            NOM_REGION = '$name'
                        WHERE COD_REGION = '$code'
                            ");

            $consulta->execute();

            return $consulta;
	}
}