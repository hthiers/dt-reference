<?php
class TiendasModel extends ModelBase
{
	/*******************************************************************************
	* TIENDAS
	*******************************************************************************/
	
	/**
         * Get all tiendas
         * @return PDO
         */
	public function getAllTiendas()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            a.COD_TIENDA
                            , a.COD_BTK
                            , a.NOM_TIENDA
                            , a.DIREC_TIENDA
                            , a.CLIENTE_COD_CLIENTE AS COD_CLIENTE
                            , b.NOM_CLIENTE
                            , a.AGRUPACION_COD_AGRUPACION AS COD_AGRUPACION
                            , c.NOM_AGRUPACION
                            , a.TIPO_TIENDA_COD_TIPO AS COD_TIPO
                            , d.NOM_TIPO
                            , a.COMUNA_COD_COMUNA AS COD_COMUNA
                            , e.NOM_COMUNA
                            , a.COMUNA_Ciudad_COD_CIUDAD AS COD_CIUDAD
                            , f.NOM_CIUDAD
                            , a.COMUNA_Ciudad_Region_COD_REGION AS COD_REGION
                            , g.NOM_REGION
                            , a.ZONA_COD_ZONA AS COD_ZONA
                            , h.NOM_ZONA
                            , a.ESTADO_COD_ESTADO AS COD_ESTADO
                            , i.NOM_ESTADO
                    FROM t_tienda a
                    inner join t_cliente b
                    on a.CLIENTE_COD_CLIENTE = b.COD_CLIENTE
                    inner join t_agrupacion c
                    on a.AGRUPACION_COD_AGRUPACION = c.COD_AGRUPACION
                    inner join t_tipo_tienda d
                    on a.TIPO_TIENDA_COD_TIPO = d.COD_TIPO
                    inner join t_comuna e
                    on a.COMUNA_COD_COMUNA = e.COD_COMUNA
                    inner join t_ciudad f
                    on a.COMUNA_Ciudad_COD_CIUDAD = f.COD_CIUDAD
                    inner join t_region g
                    on a.COMUNA_Ciudad_Region_COD_REGION = g.COD_REGION
                    inner join t_tienda_zona h
                    on a.ZONA_COD_ZONA = h.COD_ZONA
                    inner join t_tienda_estado i
                    on a.ESTADO_COD_ESTADO = i.COD_ESTADO
                    ORDER BY A.NOM_TIENDA ASC");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	/**
         * Get all tiendas by code
         * @param string $code
         * @return PDO
         */
	public function getTiendaByCodigo($code = '')
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            a.COD_TIENDA
                            , a.COD_BTK
                            , a.NOM_TIENDA
                            , a.DIREC_TIENDA
                            , a.CLIENTE_COD_CLIENTE AS COD_CLIENTE
                            , b.NOM_CLIENTE
                            , a.AGRUPACION_COD_AGRUPACION AS COD_AGRUPACION
                            , c.NOM_AGRUPACION
                            , a.TIPO_TIENDA_COD_TIPO AS COD_TIPO
                            , d.NOM_TIPO
                            , a.COMUNA_COD_COMUNA AS COD_COMUNA
                            , e.NOM_COMUNA
                            , a.COMUNA_Ciudad_COD_CIUDAD AS COD_CIUDAD
                            , f.NOM_CIUDAD
                            , a.COMUNA_Ciudad_Region_COD_REGION AS COD_REGION
                            , g.NOM_REGION
                            , a.ZONA_COD_ZONA AS COD_ZONA
                            , h.NOM_ZONA
                            , a.ESTADO_COD_ESTADO AS COD_ESTADO
                            , i.NOM_ESTADO
                    FROM t_tienda a
                    inner join t_cliente b
                    on a.CLIENTE_COD_CLIENTE = b.COD_CLIENTE
                    inner join t_agrupacion c
                    on a.AGRUPACION_COD_AGRUPACION = c.COD_AGRUPACION
                    inner join t_tipo_tienda d
                    on a.TIPO_TIENDA_COD_TIPO = d.COD_TIPO
                    inner join t_comuna e
                    on a.COMUNA_COD_COMUNA = e.COD_COMUNA
                    inner join t_ciudad f
                    on a.COMUNA_Ciudad_COD_CIUDAD = f.COD_CIUDAD
                    inner join t_region g
                    on a.COMUNA_Ciudad_Region_COD_REGION = g.COD_REGION
                    inner join t_tienda_zona h
                    on a.ZONA_COD_ZONA = h.COD_ZONA
                    inner join t_tienda_estado i
                    on a.ESTADO_COD_ESTADO = i.COD_ESTADO
                    WHERE a.COD_TIENDA = '$code'
                    ORDER BY A.NOM_TIENDA ASC");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
	
	/**
         * Edit a tienda
         * @param string $code
         * @param string $code_btk
         * @param string $name
         * @param string $direccion
         * @param string $cod_cliente
         * @param string $cod_region
         * @param string $cod_ciudad
         * @param string $cod_comuna
         * @param string $cod_tipo
         * @param string $cod_agrupacion
         * @param string $cod_estado
         * @param string $cod_zona
         * @return int 
         */
	public function editTienda($code, $code_btk, $name, $direccion, $cod_cliente, $cod_region, $cod_ciudad, $cod_comuna, $cod_tipo, $cod_agrupacion, $cod_estado, $cod_zona)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_tienda WHERE COD_TIENDA = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_tienda
                            SET 
                                    NOM_TIENDA = '$name'
                                    , DIREC_TIENDA = '$direccion'
                                    , COMUNA_COD_COMUNA = '$cod_comuna'
                                    , COMUNA_CIUDAD_COD_CIUDAD = '$cod_ciudad'
                                    , COMUNA_CIUDAD_REGION_COD_REGION = '$cod_region'
                                    , CLIENTE_COD_CLIENTE = '$cod_cliente'
                                    , AGRUPACION_COD_AGRUPACION = '$cod_agrupacion'
                                    , ZONA_COD_ZONA = '$cod_zona'
                                    , TIPO_TIENDA_COD_TIPO = '$cod_tipo'
                                    , ESTADO_COD_ESTADO = '$cod_estado'
                            WHERE COD_TIENDA = '$code'
                                AND COD_BTK = '$code_btk'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS');

            return $consulta;
	}
        
        public function editTiendaEstado($cod_tienda, $cod_btk, $cod_estado)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_tienda COD_BTK = '$cod_btk'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_tienda
                            SET 
                                ESTADO_COD_ESTADO = '$cod_estado'
                            WHERE COD_TIENDA = '$cod_tienda'
                                AND COD_BTK = '$cod_btk'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS');

            return $consulta;
	}
	
	//NUEVA t_tienda
	public function addNewTienda($cod_tienda, $cod_btk, $name, $direccion, $cod_cliente, $cod_region, $cod_ciudad, $cod_comuna, $cod_zona, $cod_tipo, $cod_agrupacion, $cod_estado)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_tienda COD_TIENDA = '$cod_tienda'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_tienda 
                            (COD_TIENDA
                            , COD_BTK
                            , NOM_TIENDA
                            , DIREC_TIENDA
                            , COMUNA_COD_COMUNA
                            , COMUNA_CIUDAD_COD_CIUDAD
                            , COMUNA_CIUDAD_REGION_COD_REGION
                            , CLIENTE_COD_CLIENTE
                            , AGRUPACION_COD_AGRUPACION
                            , ZONA_COD_ZONA
                            , TIPO_TIENDA_COD_TIPO
                            , ESTADO_COD_ESTADO) 
                    VALUES 
                            ('$cod_tienda'
                            ,'$cod_btk'
                            ,'$name'
                            ,'$direccion'
                            ,'$cod_comuna'
                            ,'$cod_ciudad'
                            ,'$cod_region'
                            ,'$cod_cliente'
                            ,'$cod_agrupacion'
                            ,'$cod_zona'
                            ,'$cod_tipo'
                            ,'$cod_estado')");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS');

            return $consulta;
	}
	
	//GET ULTIMO CODIGO t_tienda
	public function getLastTiendaCode()
	{
            //get last segment
            $consulta = $this->db->prepare("SELECT COD_TIENDA FROM t_tienda 
                    WHERE COD_TIENDA NOT LIKE '%N/A%' ORDER BY COD_TIENDA DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}

        //GET ULTIMO CODIGO BTK t_tienda
	public function getLastTiendaBTK()
	{
            //get last segment
            $consulta = $this->db->prepare("SELECT COD_BTK FROM t_tienda 
                    WHERE COD_BTK NOT LIKE '%N/A%' 
                        AND COD_BTK LIKE 'X%'
                    ORDER BY COD_BTK DESC LIMIT 1");

            $consulta->execute();

            return $consulta;
	}


        /*******************************************************************************
	* ZONAS
	*******************************************************************************/
        
        //GET ZONAS DE TIENDAS
	public function getAllZonas()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            COD_ZONA, NOM_ZONA
                    FROM t_tienda_zona
                    ORDER BY COD_ZONA DESC
            ");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewZonaCode()
	{
            $consulta = $this->db->prepare("SELECT COD_ZONA FROM t_tienda_zona 
                    WHERE COD_ZONA NOT LIKE '%N/A%' ORDER BY COD_ZONA DESC LIMIT 1");
            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA t_tienda_zona
	public function addNewZona($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_tienda_zona COD_ZONA = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_tienda_zona 
                            (COD_ZONA
                            , NOM_ZONA) 
                    VALUES 
                            ('$code'
                            ,'$name')
                    ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-ZONAS');

            return $consulta;
	}
        
        //Edit t_tienda_zona
        public function editZona($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_tienda_zona WHERE COD_ZONA = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_tienda_zona
                        SET 
                            NOM_ZONA = '$name'
                        WHERE COD_ZONA = '$code'");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-ZONAS');

            return $consulta;
	}
        
        
        /*******************************************************************************
	* TIPOS
	*******************************************************************************/
        
        //GET ZONAS DE TIENDAS
	public function getAllTipos()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            COD_TIPO, NOM_TIPO
                    FROM t_tipo_tienda
                    ORDER BY COD_TIPO DESC");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewTipoCode()
	{
            $consulta = $this->db->prepare("SELECT COD_TIPO FROM t_tipo_tienda
                    WHERE COD_TIPO NOT LIKE '%N/A%' ORDER BY COD_TIPO DESC LIMIT 1");
            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA TIPO
	public function addNewTipo($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_tipo_tienda WHERE COD_TIPO = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_tipo_tienda 
                            (COD_TIPO
                            , NOM_TIPO) 
                    VALUES 
                            ('$code'
                            ,'$name')
                    ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-TIPOS');

            return $consulta;
	}
        
        //Edit t_tienda_zona
        public function editTipo($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_tipo_tienda WHERE COD_TIPO = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_tipo_tienda
                        SET 
                            NOM_TIPO = '$name'
                        WHERE COD_TIPO = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-TIPOS');

            return $consulta;
	}
        
        
        /*******************************************************************************
	* AGRUPACIONES
	*******************************************************************************/
        
        //GET AGRUPACIONES DE TIENDAS
	public function getAllAgrupaciones()
	{
            //realizamos la consulta de todos los segmentos
            $consulta = $this->db->prepare("
                    SELECT 
                            COD_AGRUPACION, NOM_AGRUPACION
                    FROM t_agrupacion
                    ORDER BY COD_AGRUPACION DESC");

            $consulta->execute();

            //devolvemos la coleccion para que la vista la presente.
            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewAgrupacionCode()
	{
            $consulta = $this->db->prepare("SELECT COD_AGRUPACION FROM t_agrupacion 
                    WHERE COD_AGRUPACION NOT LIKE '%N/A%' ORDER BY COD_AGRUPACION DESC LIMIT 1");
            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA Agrupacion
	public function addNewAgrupacion($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_agrupacion COD_AGRUPACION = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_agrupacion 
                            (COD_AGRUPACION
                            , NOM_AGRUPACION) 
                    VALUES 
                            ('$code'
                            ,'$name')");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-AGRUPACION');

            return $consulta;
	}
        
        //Edit agrupacion
        public function editAgrupacion($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_agrupacion WHERE COD_AGRUPACION = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_agrupacion
                        SET 
                            NOM_AGRUPACION = '$name'
                        WHERE COD_AGRUPACION = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-AGRUPACION');

            return $consulta;
	}
        
        
        /*******************************************************************************
	* ESTADOS
	*******************************************************************************/
	
	//GET ALL ESTADOS
	public function getAllEstados()
	{
            $consulta = $this->db->prepare("
                    SELECT COD_ESTADO, NOM_ESTADO FROM t_tienda_estado ORDER BY NOM_ESTADO");

            $consulta->execute();

            return $consulta;
	}
        
        //GET LAST CODE
	public function getNewEstadoCode()
	{
            $consulta = $this->db->prepare("SELECT COD_ESTADO FROM t_tienda_estado 
                    WHERE COD_ESTADO NOT LIKE '%N/A%' ORDER BY COD_ESTADO DESC LIMIT 1");
            $consulta->execute();

            return $consulta;
	}
        
        //NUEVA t_tienda_estado
	public function addNewEstado($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "INSERT INTO t_tienda_estado VALUE COD_ESTADO = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("
                    INSERT INTO t_tienda_estado 
                            (COD_ESTADO
                            , NOM_ESTADO) 
                    VALUES 
                            ('$code'
                            ,'$name')");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-ESTADO');

            return $consulta;
	}
        
        //Edit t_tienda_estado
        public function editEstado($code, $name)
	{
            require_once 'AdminModel.php';
            $logModel = new AdminModel();
            $sql = "UPDATE t_tienda_estado WHERE COD_ESTADO = '$code'";

            $session = FR_Session::singleton();

            $consulta = $this->db->prepare("UPDATE t_tienda_estado
                        SET 
                            NOM_ESTADO = '$name'
                        WHERE COD_ESTADO = '$code'
                            ");

            $consulta->execute();

            //Save log event - NOTE THAT IS ACTION IS NOT DEBUGGABLE
            $logModel->addNewEvent($session->usuario, $sql, 'TIENDAS-ESTADO');

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
         * NOTA: Solo por lógica; modelo = tabla
         * @return string 
         */
        public function getTableName()
        {
            $tableName = "t_tienda";
            
            return $tableName;
        }
        
        /**
         * Get database table column names
         * NOTA: Solo por lógica modelo = tabla
         * @return array
         */
        public function getTableColumnNames()
        {
            $columns = array('COD_TIENDA'
                , 'COD_BTK'
                , 'NOM_TIENDA'
                , 'DIREC_TIENDA'
                , 'COMUNA_COD_COMUNA'
                , 'COMUNA_CIUDAD_COD_CIUDAD'
                , 'COMUNA_CIUDAD_REGION_COD_REGION'
                , 'CLIENTE_COD_CLIENTE'
                , 'AGRUPACION_COD_AGRUPACION'
                , 'ZONA_COD_ZONA'
                , 'TIPO_TIENDA_COD_TIPO'
                , 'ESTADO_COD_ESTADO');
            
            return $columns;
        }
}
?>