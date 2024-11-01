<?php
/*
* Plugin Name: WP Fahrkostenrechner
* Plugin URI: https://webseitenhilfe.ch/tipps-tricks/wordpress-plugin-wp-fahrkostenrechner/
* Description: Einfacher Fahrkostenrechner per Google Distance Matrix API und Shortcode - Anleitung und Informationen <a href="https://webseitenhilfe.ch/tipps-tricks/wordpress-plugin-wp-fahrkostenrechner/">hier</a>
* Version: 1.0.1
* Author: KMU-Internetseiten - Daniel Wehrli
* Author URI: https://www.kmu-internetseiten.ch
* License: GPL2
* Text Domain: wp-fahrkostenrechner
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// FPTP Fahrpreis tag
// FPNP Fahrpreis nacht
// FPTEXT Text unterhalb Fahrpreise
//
function WPFKR_shortcode( $WPFKR_atts ) {
    $WPFKR_aw = shortcode_atts( array (
				'apikey' => '',
        'fpland' => 'CH',
				'fptag' => '1',
        'fpwaehrung' => 'CHF',
        'fpnacht' => '1',
		'fptext' => '<b>Preisangaben ohne Gewähr.</b> Das Taxiunternehmen ist nicht verpflichtet, Kunden aufgrund von diesen Preisbereichnungen zu transportieren. Es gilt jeweils die offizielle Taxuhr.',
    ), $WPFKR_atts );


	if($WPFKR_aw['apikey'] == ""){
		$WPFKR_fpoutput = "Geben Sie bitte Ihre Google Distance Matrix API ein damit WP Fahrkostenrechner korrekt funktioniert, danke! <br /><br />Hilfe benötigt? Lesen Sie die <a href='https://webseitenhilfe.ch/tipps-tricks/wordpress-plugin-wp-fahrkostenrechner/'>Anleitung</a> oder kontaktieren Sie <a href='https://www.kmu-internetseiten.ch'>KMU-Internetseiten.ch</a>";
		return $WPFKR_fpoutput;
	}else{


		$WPFKR_distance = 0;
		if($_POST['WPFKR_sstrasse'] != "" AND $_POST['WPFKR_splz'] != ""){
		$WPFKR_start_adresse = $_POST['WPFKR_sstrasse']." ".$_POST['WPFKR_splz'];
		}else{
		  $WPFKR_start_adresse="";
		}
		if($_POST['WPFKR_zstrasse'] != "" AND $_POST['WPFKR_zplz'] != ""){
		$WPFKR_ziel_adresse = $_POST['WPFKR_zstrasse']." ".$_POST['WPFKR_zplz'];
		}else{
		 $WPFKR_ziel_adresse = "";
		}

		if($WPFKR_start_adresse!='' && $WPFKR_ziel_adresse!=''){
		    $WPFKR_url="https://maps.googleapis.com/maps/api/distancematrix/xml?origins=".$WPFKR_start_adresse."+".$WPFKR_aw['fpland']."&destinations=".$WPFKR_ziel_adresse."+".$WPFKR_aw['fpland']."&mode=driving&language=de-DE&sensor=false&key=".$WPFKR_aw['apikey'];
		    if($WPFKR_xml=simplexml_load_file($WPFKR_url)){
		           if($WPFKR_xml->status=='OK'){
		              $WPFKR_distance = $WPFKR_xml->row->element->distance->text;
					   //$distance = substr($distance, 0, -3);
					   $WPFKR_distance = str_replace(",", ".", $WPFKR_distance);
		              $WPFKR_fahrzeit = $WPFKR_xml->row->element->duration->text;
					   $WPFKR_fptagpreis = $WPFKR_aw['fptag'] * $WPFKR_distance;
		              $WPFKR_fpnachtpreis = $WPFKR_aw['fpnacht'] * $WPFKR_distance;
					   $WPFKR_fptagpreis = round($WPFKR_fptagpreis, 2);
						$WPFKR_fpnachtpreis = round($WPFKR_fpnachtpreis, 2);
									$WPFKR_fpoutput = "";
					   	$WPFKR_fpoutput = '<style>.fp,.fp b{width:100%;display:block;}.fpcopy,.fpcopy a{margin-top:10px;font-size:9px !important;line-height:10px;}</style>';
									$WPFKR_fpoutput .= "<div class='fp fpcontainer'>";
		              $WPFKR_fpoutput .=  '<h2 class="fp subtitle">Fahrkosten berechnen</h2>';
		              $WPFKR_fpoutput .=  '<span class="fp fptime"><b>Fahrzeit:</b> ' . $WPFKR_fahrzeit.'</span>';
									$WPFKR_fpoutput .=  '<span class="fp fpdistance"><b>Distanz:</b> ' . $WPFKR_distance.'</span>';
									$WPFKR_fpoutput .=  '<span class="fp fpnight"><b>Fahrpreis Tag:</b> ' . $WPFKR_aw['fpwaehrung']." ". $WPFKR_fptagpreis.' ('. $WPFKR_aw['fpwaehrung']." ".$WPFKR_aw['fptag'].'/km)</span>';
									$WPFKR_fpoutput .=  '<span class="fp fpnight"><b>Fahrpreis Nacht:</b> ' . $WPFKR_aw['fpwaehrung']." ". $WPFKR_fpnachtpreis.' ('. $WPFKR_aw['fpwaehrung']." ".$WPFKR_aw['fpnacht'].'/km)</span>';
		              $WPFKR_fpoutput .=  '<span class="fp fptext"><b>Info:</b> ' . $WPFKR_aw['fptext'].'</span>';
                  $WPFKR_fpoutput .=  '<span class="fp fpcopy">WP Fahrkostenrechner <br />by <a href="https://www.kmu-internetseiten.ch">KMU Internetseiten</a><br /><a href="https://webseitenhilfe.ch/tipps-tricks/wordpress-plugin-wp-fahrkostenrechner/">WebseitenHilfe</a></span>';
		              $WPFKR_fpoutput .= "</div>";
					   return $WPFKR_fpoutput;
		           }
		    }
		}else{
		    $WPFKR_fpoutput = '';
			$WPFKR_fpoutput = '<style>.fp,.fp b{width:100%;display:block;}.fpcopy,.fpcopy a{margin-top:10px;font-size:9px !important;line-height:10px;}</style>';
				$WPFKR_fpoutput .= "<div class='fp fpcontainer'>";
				$WPFKR_fpoutput .= '
		    <h2 class="fp fptitle">Fahrkosten-Kalkulator</h2>
		    <p class="fp fpbodytext">Berechnen Sie mit unserem Fahrkosten-Tool die Kosten für Ihre nächste Fahrt.</p>
		    <form action="" method="post">
		    <div class="fp adress">
		    <label><b>Startadresse:</b></label>
		    <input class="fp fpstr" type="text" id="WPFKR_sstrasse" name="WPFKR_sstrasse" placeholder="Strasse Nr." value="">
		    <input class="fp fport" type="text" id="WPFKR_splz" name="WPFKR_splz" placeholder="PLZ Ortschaft" value="">
		    </div>
		    <div class="fp adress">
		    <label><b>Zieladresse:</b></label>
		    <input class="fp fpstr" type="text" id="WPFKR_zstrasse" name="WPFKR_zstrasse" placeholder="Strasse Nr." value="">
		    <input class="fp fport" type="text" id="WPFKR_zplz" name="WPFKR_zplz" placeholder="PLZ Ortschaft" value="">
		    </div>
		    <input class="button" type="submit" value="Jetzt berechnen">
		    </form>';
        $WPFKR_fpoutput .=  '<span class="fp fpcopy">WP Fahrkostenrechner <br />by <a href="https://www.kmu-internetseiten.ch">KMU Internetseiten</a><br /><a href="https://webseitenhilfe.ch/tipps-tricks/wordpress-plugin-wp-fahrkostenrechner/">WebseitenHilfe</a></span>';

				$WPFKR_fpoutput .= "</div>";
				return $WPFKR_fpoutput;
		}
			}
	}
// SHORTCODE: [KMUFahrkostenrechner apikey= fpland=CH fptag=2.00 fpnacht=4.00 fptext="Das ist einfach mal ein Test von WP Fahrkostenrechner"]
	add_shortcode( 'KMUFahrkostenrechner', 'WPFKR_shortcode' );
	add_filter( 'widget_text', 'do_shortcode' );
?>
