<?php
class ReportsModel extends ModelBase
{    
    /**
     * export all tiendas to excel csv file 
     */
    public function exportTiendasToCsv(){
        $Fecha = date('d-m-Y');
        #$Hora = date('H:i:s');
        #$fechahora = "(".$Fecha."-".$Hora.")";

        $consulta = $this->db->prepare("
        SELECT
        'COD_TIENDA'
        ,'COD_BTK'
        ,'TIENDA_NAME'
        ,'DIRECCION'
        ,'COD_CLIENTE'
        ,'CLIENTE_NAME'
        ,'COD_REGION'
        ,'REGION_NAME'
        ,'COD_CIUDAD'
        ,'CIUDAD_NAME'
        ,'COD_COMUNA'
        ,'COMUNA_NAME'
        ,'COD_ZONA'
        ,'ZONA_NAME'
        ,'COD_TIPO'
        ,'TIPO_NAME'
        ,'COD_AGRUPACION'
        ,'AGRUPACION_NAME'
        ,'COD_ESTADO'
        ,'ESTADO_NAME'
        UNION ALL
        SELECT 
        TI.COD_TIENDA,
        TI.COD_BTK,
        TI.NOM_TIENDA,
        IFNULL(TI.DIREC_TIENDA,'') AS DIRECCION,
        CL.COD_CLIENTE,
        CL.NOM_CLIENTE,
        IFNULL(RG.COD_REGION, '') AS COD_REGION,
        IFNULL(RG.NOM_REGION, '') AS NOM_REGION,
        IFNULL(CT.COD_CIUDAD, '') AS COD_CIUDAD,
        IFNULL(CT.NOM_CIUDAD, '') AS NOM_CIUDAD,
        IFNULL(CM.COD_COMUNA, '') AS COD_COMUNA,
        IFNULL(CM.NOM_COMUNA, '') AS NOM_COMUNA,
        IFNULL(ZN.COD_ZONA, '') AS COD_ZONA,
        IFNULL(ZN.NOM_ZONA, '') AS NOM_ZONA,
        IFNULL(TT.COD_TIPO, '') AS COD_TIPO,
        IFNULL(TT.NOM_TIPO, '') AS NOM_TIPO,
        IFNULL(AG.COD_AGRUPACION, '') AS COD_AGRUPACION,
        IFNULL(AG.NOM_AGRUPACION, '') AS NOM_AGRUPACION,
        IFNULL(ET.COD_ESTADO, '') AS COD_ESTADO,
        IFNULL(ET.NOM_ESTADO, '') AS NOM_ESTADO
        FROM T_TIENDA TI
        LEFT OUTER JOIN T_CLIENTE CL ON CL.COD_CLIENTE = TI.CLIENTE_COD_CLIENTE
        LEFT OUTER JOIN T_TIENDA_ESTADO ET ON ET.COD_ESTADO = TI.ESTADO_COD_ESTADO
        LEFT OUTER JOIN T_COMUNA CM ON (CM.COD_COMUNA = TI.COMUNA_COD_COMUNA)
            AND  (CM.CIUDAD_COD_CIUDAD = TI.COMUNA_CIUDAD_COD_CIUDAD)
            AND  (CM.CIUDAD_REGION_COD_REGION = TI.COMUNA_CIUDAD_REGION_COD_REGION)
        LEFT OUTER JOIN T_CIUDAD CT ON CT.COD_CIUDAD = CM.CIUDAD_COD_CIUDAD
        LEFT OUTER JOIN T_REGION RG ON RG.COD_REGION = CM.CIUDAD_REGION_COD_REGION
        LEFT OUTER JOIN T_TIENDA_ZONA ZN ON ZN.COD_ZONA = TI.ZONA_COD_ZONA
        LEFT OUTER JOIN T_TIPO_TIENDA TT ON TT.COD_TIPO = TI.TIPO_TIENDA_COD_TIPO
        LEFT OUTER JOIN T_AGRUPACION AG ON AG.COD_AGRUPACION = TI.AGRUPACION_COD_AGRUPACION 
        WHERE TI.COD_TIENDA NOT LIKE '%N/A%'
          AND TI.COD_BTK NOT LIKE '%N/A%'
        INTO OUTFILE '".$this->apache.$this->root."views/tmp/SOM_TIENDAS_".$Fecha.".csv'
        CHARACTER SET latin1
        FIELDS TERMINATED BY ';'
        LINES TERMINATED BY '\n'");
        
        //OPTIONALLY ENCLOSED BY '\"'
        
        $consulta->execute();

        // hold & go to be sure
        sleep(2);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="SOM_TIENDAS_'.$Fecha.'.csv"');
        header('Cache-Control: max-age=0');
        
        readfile(''.$this->apache.$this->root.'views/tmp/SOM_TIENDAS_'.$Fecha.'.csv');
        unlink(''.$this->apache.$this->root.'views/tmp/SOM_TIENDAS_'.$Fecha.'.csv');
        
//        $error = $consulta->errorInfo();
//        echo $error[2];
    }
    
    /**
     * Export all models to excel csv file 
     */
    public function exportModelsToCsv(){
        $Fecha = date('d-m-Y');
        #$Hora = date('H:i:s');
        #$fechahora = "(".$Fecha."-".$Hora.")";

        $consulta = $this->db->prepare("
        SELECT
        'MODEL'
        ,'MODEL_SUFFIX'
        ,'COD_GBU'
        ,'GBU_NAME'
        ,'COD_BRAND'
        ,'BRAND_NAME'
        ,'COD_SEGMENT'
        ,'SEGMEMT_NAME'
        ,'COD_SUB_SEGMENT'
        ,'SUB_SEGMENT_NAME'
        ,'COD_MICRO_SEGMENT'
        ,'MICRO_SEGMENT_NAME'
        ,'COD_ESTADO'
        ,'ESTADO_NAME'
        UNION ALL
        SELECT 
        PD.COD_MODEL 
        ,PD.COD_MODEL_SUFFIX
        ,PD.COD_GBU 
        ,LGBU.NAME_GBU 
        ,PD.COD_BRAND 
        ,BR.NAME_BRAND 
        ,PD.COD_SEGMENT 
        ,SG.NAME_SEGMENT 
        ,PD.COD_SUB_SEGMENT 
        ,SS.NAME_SUB_SEGMENT 
        ,PD.COD_MICRO_SEGMENT 
        ,MS.NAME_MICRO_SEGMENT 
        ,PD.COD_ESTADO 
        ,ST.NAME_ESTADO 
        FROM T_PRODUCT PD
        INNER JOIN T_GBU LGBU 
            ON (LGBU.COD_GBU = PD.COD_GBU 
                AND LGBU.COD_CATEGORY = PD.COD_CATEGORY)
        INNER JOIN T_SEGMENT SG 
            ON (SG.COD_SEGMENT = PD.COD_SEGMENT
                AND SG.COD_GBU = PD.COD_GBU)
        INNER JOIN T_SUB_SEGMENT SS
            ON (SS.COD_SUB_SEGMENT = PD.COD_SUB_SEGMENT 
                AND SS.COD_GBU = PD.COD_GBU)
        INNER JOIN T_MICRO_SEGMENT MS
            ON (MS.COD_MICRO_SEGMENT = PD.COD_MICRO_SEGMENT
                AND MS.COD_GBU = PD.COD_GBU)
        INNER JOIN T_BRAND BR 
            ON BR.COD_BRAND=PD.COD_BRAND
        INNER JOIN T_PRODUCT_ESTADO ST
            ON ST.COD_ESTADO=PD.COD_ESTADO
        INTO OUTFILE '".$this->apache."htdocs".$this->root."views/tmp/SOM_MODELOS_".$Fecha.".csv'
            CHARACTER SET latin1
            FIELDS TERMINATED BY ';'
            LINES TERMINATED BY '\n'");
        
        $consulta->execute();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="SOM-MODELOS_'.$Fecha.'.csv"');
        header('Cache-Control: max-age=0');
        
        readfile(''.$this->apache.'htdocs'.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
        unlink(''.$this->apache.'htdocs'.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
        
//        $error = $consulta->errorInfo();
//        echo $error[2];
    }

    /**
     * Export all models with selected columns to excel csv file 
     */
    public function exportModelsSelection($col_list){
        $Fecha = date('d-m-Y');        
        $large = count($col_list);
        
        $sql = "SELECT ";
        $sql_joins = "";
        
        // Column labels
        foreach($col_list as $i => $value){
            switch ($value) {
                case 'SEGMENTS':
                    $sql .= "'COD_SEGMENT'";
                    $sql .= ", 'NAME_SEGMENT'";
                    $sql .= ", 'COD_SUB_SEGMENT'";
                    $sql .= ", 'NAME_SUB_SEGMENT'";
                    $sql .= ", 'COD_MICRO_SEGMENT'";
                    $sql .= ", 'NAME_MICRO_SEGMENT'";
                    break;
                case 'COD_GBU':
                    $sql .= "'COD_GBU'";
                    $sql .= ", 'NAME_GBU'";
                    break;
                case 'COD_BRAND':
                    $sql .= "'COD_BRAND'";
                    $sql .= ", 'NAME_BRAND'";
                    break;
                case 'COD_ESTADO':
                    $sql .= "'COD_ESTADO'";
                    $sql .= ", 'NAME_ESTADO'";
                    break;
                case 'xxPREMIUM':
                    $sql .= "'PREMIUM'";
                    break;
                case 'ATRIBUTE':
                    $sql .= "'ATRIBUTE'";
                    break;
                default:
                    $sql .= "'".$value."'";
                    break;
            }

            if($i < $large-1)
                $sql .= ", ";
        }
        
        $sql .= " UNION ALL SELECT ";
        
        // Distinct if necessary
        if(!in_array("COD_MODEL_SUFFIX", $col_list)){
            $sql .= "DISTINCT ";
        }
        
        // Column values
        foreach($col_list as $i => $value){            
            switch ($value) {
                case 'SEGMENTS':
                    $sql .= "PD.COD_SEGMENT";
                    $sql .= ", SG.NAME_SEGMENT";
                    $sql .= ", PD.COD_SUB_SEGMENT";
                    $sql .= ", SS.NAME_SUB_SEGMENT";
                    $sql .= ", PD.COD_MICRO_SEGMENT";
                    $sql .= ", MS.NAME_MICRO_SEGMENT";
                    
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
                    $sql .= "IFNULL(PD.COD_GBU, 'N/A') AS COD_GBU";
                    $sql .= ", IFNULL(LGBU.NAME_GBU, 'NO APLICA') AS NAME_GBU";
                    
                    $sql_joins .= "
                    LEFT OUTER JOIN T_GBU LGBU
                        ON (LGBU.COD_GBU = PD.COD_GBU)";
                    break;
                case 'COD_BRAND':
                    $sql .= "IFNULL(PD.COD_BRAND, 'N/A') AS COD_BRAND";
                    $sql .= ", IFNULL(BR.NAME_BRAND, 'NO APLICA') AS NAME_BRAND";
                    
                    $sql_joins .= "
                    LEFT OUTER JOIN T_BRAND BR 
                        ON BR.COD_BRAND=PD.COD_BRAND";
                    break;
                case 'COD_ESTADO':
                    $sql .= "PD.COD_ESTADO";
                    $sql .= ", ST.NAME_ESTADO";
                    
                    $sql_joins .= "
                    INNER JOIN T_PRODUCT_ESTADO ST
                        ON ST.COD_ESTADO=PD.COD_ESTADO";
                    break;
                case 'PREMIUM':
                    #$sql .= "IFNULL(PA.cod_atribute, 0) as PREMIUM";
                    $sql .= " null";

                    #$sql_joins .= "
                    #    LEFT OUTER JOIN t_product_has_t_product_atribute PA
                    #        on PD.cod_model = PA.cod_model";
                    break;
                 case 'ATRIBUTE':
                    $sql .= "IFNULL(GROUP_CONCAT(PAD.name_atribute), '') as ATRIBUTE";

                    $sql_joins .= "
                        LEFT OUTER JOIN t_product_has_t_product_atribute PA
                            on PD.cod_model = PA.cod_model
                        LEFT OUTER JOIN t_product_atribute PAD
                            on PA.cod_atribute = PAD.cod_atribute";
                    break;
                default:
                    $sql .= "PD.".$value;
                    break;
            }

            if($i < $large-1)
                $sql .= ", ";
        }
        
        $sql .= " FROM T_PRODUCT PD";
        
        $sql .= " ".$sql_joins;
        
//        $sql .= "  WHERE (PA.cod_atribute = 1
//                          OR PA.cod_atribute IS NULL)";
        $sql .= " GROUP BY PD.COD_MODEL
	, PD.COD_MODEL_SUFFIX
	, PD.COD_SEGMENT
	, SG.NAME_SEGMENT
	, PD.COD_SUB_SEGMENT
	, SS.NAME_SUB_SEGMENT
	, PD.COD_MICRO_SEGMENT
	, MS.NAME_MICRO_SEGMENT
	, PD.COD_GBU
	, LGBU.NAME_GBU
	, PD.COD_BRAND
	, BR.NAME_BRAND
	, PD.COD_ESTADO
	, ST.NAME_ESTADO";
        
        $sql .= " INTO OUTFILE '".$this->apache.$this->root."views/tmp/SOM_MODELOS_".$Fecha.".csv'
            CHARACTER SET latin1
            FIELDS TERMINATED BY ';'
            LINES TERMINATED BY '\n'";

        $consulta = $this->db->prepare($sql);

        $consulta->execute();

        // debug
        #return $sql;
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="SOM-MODELOS_'.$Fecha.'.csv"');
        header('Cache-Control: max-age=0');
        
        readfile(''.$this->apache.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
        unlink(''.$this->apache.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
    }
    
    /**
     * Exportar modelos por query dinamica a CSV
     * @param string $sql_values
     * @param string $sTable
     * @param string $sWhere
     * @param string $sGroup
     * @return file csv
     */
    public function exportModelsCustom($sql_values, $sql_headers, $sTable, $sWhere, $sGroup){
        /********************** Create Query */
        $sql = "
            SELECT $sql_headers
            UNION ALL
            SELECT $sql_values
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
            LEFT OUTER JOIN t_product_atribute I
            ON H.COD_ATRIBUTE = I.COD_ATRIBUTE
            $sWhere
            $sGroup";

        $Fecha = date('d-m-Y');
        
        $sql .= " INTO OUTFILE '".$this->apache.$this->root."views/tmp/SOM_MODELOS_".$Fecha.".csv'
        CHARACTER SET latin1
        FIELDS TERMINATED BY ';'
        LINES TERMINATED BY '\n'";
        
//        return $sql;
        
        $consulta = $this->db->prepare($sql);
        $consulta->execute();
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="SOM_MODELOS_'.$Fecha.'.csv"');
        header('Cache-Control: max-age=0');
        
        readfile(''.$this->apache.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
        unlink(''.$this->apache.$this->root.'views/tmp/SOM_MODELOS_'.$Fecha.'.csv');
    }
}
?>