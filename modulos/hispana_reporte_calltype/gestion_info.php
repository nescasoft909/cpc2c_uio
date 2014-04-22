<html>
    <head>
<title>Información de Calltypes
</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
        <link rel="stylesheet" href="../../themes/elastixwave/styles.css" />
        <link rel="stylesheet" href="../../themes/elastixwave/help.css" />
    </head>
<body>
<?php
  require_once "/var/www/html/libs/smarty/libs/Smarty.class.php";
  require_once "/var/www/html/libs/paloSantoDB.class.php";
  include_once "/var/www/html/modules/hispana_reporte_calltype/libs/paloSantoReportedeCalltypes.class.php";
  include_once "/var/www/html/modules/hispana_reporte_calltype/configs/default.conf.php";

  $smarty = new Smarty();
  $smarty->template_dir = "/var/www/html/themes/elastixwave/";
  $smarty->compile_dir =  "/var/www/html/var/templates_c/";
  $smarty->config_dir =   "/var/www/html/configs/";
  $smarty->cache_dir =    "/var/www/html/var/cache/";

  $pDB = new paloDB($arrConfModule['dsn_conn_database']); // viene de default.conf.php
  $pReporte = new paloSantoReportedeCalltypes($pDB);
  $arrDatosGestion = $pReporte->getDatosGestion($_GET['id_gestion']);

  echo "<table width=\"100%\" border=\"0\" class=\"tabForm\">";
  echo "<tr><td><font size=3><b>Información de Gestión</font></td></tr>";
  echo "<tr><td>";
  
  echo "<table width=\"100%\" border=\"1\"  cellspacing=\"0\" cellpadding=\"2\" align=\"center\">";
  echo "<tr class=\"table_title_row\">";
  echo "<td class=\"table_title_row\">Campo</td>";
  echo "<td class=\"table_title_row\">Valor</td>";
  echo "</tr>";

  foreach($arrDatosGestion as $regGestion){
      echo "<tr class=\"table_data\">";
      echo "<td class=\"table_data\">" . $regGestion['etiqueta'] . "</td>";
      echo "<td class=\"table_data\">" . $regGestion['valor'] . "</td>";
      echo "</tr>";
  }

  echo "</table>";
  echo "</td>";
  echo "</tr>";
  echo "</table>";

function _pre($array)
{
  echo "<pre>";
  print_r($array);
  echo "</pre>";
}

?>
</body>
</html>