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
  
  if ($_GET["action"]=="save"){
      $pReporte->actualizarGestionCliente($_POST, $_POST['id_gestion'], $_POST['elastix_user']);
      $arrDatosGestion = $pReporte->getDatosGestion($_POST['id_gestion']);
      $historialGestion = $pReporte->getHistorialAuditGestion($_POST['id_gestion']);
      echo "<b>Datos Actualizados</b>";
  }else{
      $arrDatosGestion = $pReporte->getDatosGestion($_GET['id_gestion']);
      $historialGestion = $pReporte->getHistorialAuditGestion($_GET['id_gestion']);
  }
  echo "<form action=?action=save method=\"post\"\>";
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
      echo "<td class=\"table_data\"> <input type=\"text\" value=\"" . $regGestion['valor'] . "\" name=\"".$regGestion['etiqueta']."\"></td>";
      echo "</tr>";
  }
  echo "<input type=\"hidden\" name=\"id_gestion\" value=\"".$_GET['id_gestion']."\">";
  echo "<input type=\"hidden\" name=\"elastix_user\" value=\"".$_GET['user']."\">";
  if (!empty($_GET['id_gestion'])){
    echo "<tr><td colspan=2>";

    echo "<input type=\"submit\" name=\"guardar\" value=\"Guardar\">";
    echo "</tr></td>";
  }
  echo "</table>";
  if(!empty($historialGestion)){
    echo "<table width=\"100%\" border=\"1\"  cellspacing=\"0\" cellpadding=\"2\" align=\"center\">";
    echo "<tr class=\"table_title_row\">";
    echo "<td class=\"table_title_row\">Usuario</td>";
    echo "<td class=\"table_title_row\">Datos</td>";
    echo "</tr>";
    foreach($historialGestion as $regGestion){
        echo "<tr class=\"table_data\">";
        echo "<td class=\"table_data\">" . $regGestion['usuario'] . "</td>";
        echo "<td class=\"table_data\"> ". $regGestion['data'] ."</td>";
        echo "</tr>";
    }
    echo "</table>";
  }
  echo "</td>";
  echo "</tr>";
  echo "</table>";
  echo "</form>";
function _pre($array)
{
  echo "<pre>";
  print_r($array);
  echo "</pre>";
}

?>
</body>
</html>