<?php
 ob_start();

// (c) Xavier Nicolay

// Exemple de g�n�ration de devis/facture PDF



require('Ingreso.php');



session_start();



$lo = $_SESSION["logo"];



require_once "../model/Configuracion.php";



      $objConf = new Configuracion();



      $query_conf = $objConf->Listar();



      $regConf = $query_conf->fetch_object();



require_once "../model/Ingreso.php";



$obIngreso = new Ingreso();



$query_cli = $obIngreso->GetProveedorSucursalIngreso($_GET["id"]);



        $reg_cli = $query_cli->fetch_object();





$f = "";



      if ($_SESSION["superadmin"] == "S") {

        $f = $regConf->logo;

      } else {

        $f = $reg_cli->logo;

      }



      $archivo = $f;

      $trozos = explode(".", $archivo);

      $extension = end($trozos);



$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );

$pdf->AddPage();

$pdf->addSociete( $reg_cli->razon_social,

                  "$reg_cli->documento_sucursal: $reg_cli->num_sucursal\n" .

                  "Direccion:".utf8_decode( "$reg_cli->direccion")."\n".

                  "Telefono:".utf8_decode(" $reg_cli->telefono_suc")."\n" .

                  "email : $reg_cli->email_suc ","../$f","$extension");

$pdf->fact_dev( "$reg_cli->tipo_comprobante Nro:  ", "$reg_cli->serie_comprobante" );

$pdf->temporaire( "" );

$pdf->addDate( $reg_cli->fecha);

$pdf -> TituloPDF();

//$pdf->addClient("CL01");

//$pdf->addPageNumber("1");

$pdf->addClientAdresse("Razon Social: ".utf8_decode($reg_cli->nombre),"Domicilio: ".utf8_decode($reg_cli->direccion_calle)." - ".$reg_cli->direccion_departamento,$reg_cli->tipo_documento.": ".$reg_cli->num_documento,"Email: ".$reg_cli->email,"Telefono: ".$reg_cli->telefono);

//$pdf->addReglement("Soluciones Innovadoras Per� S.A.C.");

//$pdf->addEcheance("RUC","2147715777");

//$pdf->addNumTVA("Chongoyape, Jos� G�lvez 1368");

//$pdf->addReference("Devis ... du ....");

$cols=array("Nro." => 10,

             "DESCRIPCION"  => 51,

             "MARCA"  => 25,

             "CODIGO"  => 15,

             "COLOR"  => 20,

             "CANTIDAD"     => 22,

             "P.COSTO"      => 25,

             "IMPORTE"          => 22 );

$pdf->addCols( $cols);

$cols=array( "Nro."  => "C",

             "DESCRIPCION"  => "L",

             "MARCA"  => "L",

             "CODIGO"  => "L",

             "COLOR"  => "L",

             "CANTIDAD"     => "C",

             "P.COSTO"      => "R",

             "IMPORTE"          => "C" );

$pdf->addLineFormat( $cols);

$pdf->addLineFormat($cols);



$y    = 89;



$query_ped = $obIngreso->GetDetalleArticulo($_GET["id"]);

$i=1;

        while ($reg = $query_ped->fetch_object()) {



          //if ($reg->codigo != "") {

          //  $cod = $reg->codigo;

          //} else {

          //  $cod = "-";

          //}

          //            $line = array( "CODIGO"    => utf8_decode("$cod"),

            //                         "DESCRIPCION"  => utf8_decode("$reg->articulo Serie:$reg->serie"),

              //                       "CANTIDAD"     => "$reg->stock_ingreso",

                //                     "P.COSTO"      => "$reg->precio_compra",

                  //                   "P.VENTA" => "$reg->precio_ventadistribuidor",

                    //                 "SUBTOTAL"          => "$reg->sub_total");

                      //$size = $pdf->addLine( $y, $line );

                      //$y   += $size + 2;

                      $line = array( "Nro."  =>  "$i",

                                     "DESCRIPCION"  => utf8_decode("$reg->articulo"),

                                     "MARCA"  => utf8_decode("$reg->marca"),

                                     "CODIGO"  => "$reg->numero",

                                     "COLOR"  => "$reg->color",

                                     "CANTIDAD"     => "$reg->stock_ingreso",

                                     "P.COSTO"      => "$reg->P_compra",

                                     "IMPORTE"          => "$reg->sub_total");

                      $size = $pdf->addLine( $y, $line );

                      $y   += $size + 2;

                      $i++;

                      }







/*require_once "../ajax/Letras.php";



 $V=new EnLetras();

 $con_letra=strtoupper($V->ValorEnLetras($reg_cli->total,"NUEVOS SOLES"));

*/

//$pdf->addCadreTVAs("---".$con_letra);



require_once "../model/Configuracion.php";



$objConfiguracion = new Configuracion();





$query_global = $objConfiguracion->Listar();



$reg_igv = $query_global->fetch_object();



$pdf->addTVAs( $reg_cli->total_descuento, $reg_cli->total,"$reg_igv->simbolo_moneda ");

$pdf->addCadreEurosFrancs("$reg_igv->nombre_impuesto"." $reg_cli->impuesto%");

$pdf->Output('Reporte de Ingreso','I');
   ob_end_flush();

?>
