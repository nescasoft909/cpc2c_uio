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
  include_once "/var/www/html/modules/hispana_interfaz_agente/libs/paloSantoInterfazdeAgente.class.php";
  include_once "/var/www/html/modules/hispana_interfaz_agente/configs/default.conf.php";

  $smarty = new Smarty();
  $smarty->template_dir = "/var/www/html/themes/elastixwave/";
  $smarty->compile_dir =  "/var/www/html/var/templates_c/";
  $smarty->config_dir =   "/var/www/html/configs/";
  $smarty->cache_dir =    "/var/www/html/var/cache/";

  $pDB = new paloDB($arrConfModule['dsn_conn_database']); // viene de default.conf.php
  $pInterfazdeAgente = new paloSantoInterfazdeAgente($pDB);
  $arrCalltypes = $pInterfazdeAgente->getCalltypeInfo($_GET['id_campania']);

echo "<table width=\"100%\" border=\"0\" class=\"tabForm\">";
echo "<tr><td><font size=3><b>Información de Calltypes</font></td></tr>";
echo "<tr><td>";
  
  echo "<table width=\"100%\" border=\"1\"  cellspacing=\"0\" cellpadding=\"2\" align=\"center\">";
  echo "<tr class=\"table_title_row\">";
  echo "<td class=\"table_title_row\">Clase</td>";
  echo "<td class=\"table_title_row\">Calltype</td>";
  echo "<td class=\"table_title_row\">Definición</td>";
  echo "</tr>";

foreach($arrCalltypes as $regCalltype){
    echo "<tr class=\"table_data\">";
    echo "<td class=\"table_data\">" . $regCalltype['clase'] . "</td>";
    echo "<td class=\"table_data\">" . $regCalltype['descripcion'] . "</td>";
    echo "<td class=\"table_data\">" . $regCalltype['definicion'] . "</td>";
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