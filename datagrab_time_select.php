<?php

/**
 * DataGrab MX Google Map fieldtype class
 *
 * @package   DataGrab
 * @author    Andrew Weaver <aweaver@brandnewbox.co.uk>
 * @copyright Copyright (c) Andrew Weaver
 */
class Datagrab_time_select extends Datagrab_fieldtype {

	function prepare_post_data( $DG, $item, $field_id, $field, &$data, $update = FALSE ) {

		$field_data = $DG->datatype->get_item( $item, $DG->settings["cf"][ $field ] );

		if( $field_data == "" ) {
			$data[ "field_id_" . $field_id ] = "";
		} else {
			$data[ "field_id_" . $field_id ] = explode( ":", $field_data );
		}

	}

}

?>