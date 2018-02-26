<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    This file is part of Time Select add-on for ExpressionEngine.

    Time Select is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Time Select is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    Read the terms of the GNU General Public License
    at <http://www.gnu.org/licenses/>.

    Copyright 2011-2015 Derek Hogue
*/

include(PATH_THIRD.'/time_select/config.php');

class Time_select_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Time Select',
		'version'	=> TIME_SELECT_VERSION
	);
	
	var $display_styles;
	var $time_increments;

	function __construct()
	{
		ee()->lang->loadfile('time_select');
		
		$this->display_styles = array(
			'12hr' => lang('12hr'),
			'24hr' => lang('24hr')
		);
		
		$this->time_increments = array(
			'1min' => lang('1min'),
			'5min' => lang('5min'),
			'15min' => lang('15min'),
			'30min' => lang('30min'),
			'1hour' => lang('1hour')
		);
	}


	function accepts_content_type($name)
	{
		return true;
	}
	

	function display_settings($data)
	{	
		$time_format = ee()->session->userdata('time_format', ee()->config->item('time_format'));
		$default_format = ($time_format == '12') ? '12hr' : '24hr';
		
		$settings = array(
			'time_select' => array(
				'label' => $this->info['name'],
				'group' => 'time_select',
				'settings' => array(
					array(
						'title' => 'display_style',
						'desc' => '',
						'fields' => array(
							'display_style' => array(
								'type' => 'select',
								'choices' => $this->display_styles,
								'value' => (isset($data['display_style'])) ? $data['display_style'] : $default_format
							)
						)
					),
					array(
						'title' => 'time_increments',
						'desc' => '',
						'fields' => array(
							'time_increments' => array(
								'type' => 'select',
								'choices' => $this->time_increments,
								'value' => (isset($data['time_increments'])) ? $data['time_increments'] : '15min'
							)
						)
					)
				)
			)
		);
		return $settings;
	}


	function grid_display_settings($data)
	{
		$settings = $this->display_settings($data);
		$grid_settings = array();
		foreach ($settings as $value) {
			$grid_settings[$value['label']] = $value['settings'];
		}
		return $grid_settings;
	}


	function save_settings($data)
	{
		return array(
			'display_style' => ee('Request')->post('display_style'),
			'field_fmt' => 'none',
			'field_show_fmt' => 'n',
			'time_increments' => ee('Request')->post('time_increments')
		);
	}
	

	function settings_modify_column($data)
	{
		$fields['field_id_'.$data['field_id']] = array(
			'type' => 'INT',
			'constraint' => 10
		);
		return $fields;
	}	


	function grid_save_settings($data)
	{
		return $data;
	}


	function save($data)
	{
		return $this->_create_timestamp($data);
	}


	function display_field($data)
	{
		return $this->display($data, $this->field_name);
	}


	function display($data, $name)
	{
		$selected = array(
			'hour' => '',
			'military_hour' => '',
			'minute' => '',
			'ampm' => ''
		);

		if(is_array($data))
		{
			$data = $this->save($data);
		}

		// Turn the timestamp into something we can use
		if($data != FALSE)
		{
			if($data == 1)
			{
				// 1 = midnight
				$selected['hour'] = '12';
				$selected['military_hour'] = 'midnight';
				$selected['minute'] = '0';
				$selected['ampm'] = 'am';
			}
			else
			{
				$selected['minute'] = ($data % 3600)/60;

				$current_hours = floor($data / 3600);
				if($current_hours < 1)
				{
					$selected['hour'] = '12';
					$selected['military_hour'] = 'midnight';
					$selected['ampm'] = 'am';
				}
				elseif($current_hours < 12)
				{
					$selected['hour'] = $current_hours;
					$selected['military_hour'] = $current_hours;
					$selected['ampm'] = 'am';
				}
				else
				{
					$selected['hour'] = ($current_hours == 12) ? 12 : ($current_hours-12);
					$selected['military_hour'] = $current_hours;
					$selected['ampm'] = 'pm';
				}
			}
		}

		$standard_hours = array(
			'' => '--',
			12 => '12',
			1 => '01',
			2 => '02',
			3 => '03',
			4 => '04',
			5 => '05',
			6 => '06',
			7 => '07',
			8 => '08',
			9 => '09',
			10 => '10',
			11 => '11'
		);

		$military_hours = array(
			'' => '--',
			'midnight' => '00',
			1 => '01',
			2 => '02',
			3 => '03',
			4 => '04',
			5 => '05',
			6 => '06',
			7 => '07',
			8 => '08',
			9 => '09',
			10 => '10',
			11 => '11',
			12 => '12',
			13 => '13',
			14 => '14',
			15 => '15',
			16 => '16',
			17 => '17',
			18 => '18',
			19 => '19',
			20 => '20',
			21 => '21',
			22 => '22',
			23 => '23'
		);

		$am_pm = array(
			'am' => 'AM',
			'pm' => 'PM'
		);

		switch($this->settings['time_increments'])
		{
			case '1min':
				$mins = array('' => '--', 0 => '00', 1 => '01', 2 => '02', 3 => '03', 4 => '04', 5 => '05', 6 => '06', 7 => '07', 8 => '08', 9 => '09', 10 => '10', 11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29', 30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39', 40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49', 50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59', 60 => '60');
				break;
			case '5min':
				$mins = array('' => '--', 0 => '00', 5 => '5', 10 => '10', 15 => '15', 20 => '20', 25 => '25', 30 => '30', 35 => '35', 40 => '40', 45 => '45', 50 => '50', 55 => '55');
				break;
			case '15min':
				$mins = array('' => '--', 0 => '00', 15 => '15', 30 => '30', 45 => '45');
				break;
			case '30min':
				$mins = array('' => '--', 0 => '00', 30 => '30');
				break;
			case '1hour':
				$mins = array('' => '--', 0 => '00');
				break;
		}

		if($this->settings['display_style'] == '12hr')
		{
			$r = form_dropdown($name.'[]',$standard_hours, $selected['hour']).NBS.':'.NBS;
			$r .= form_dropdown($name.'[]', $mins, $selected['minute']).NBS.NBS;
			$r .= form_dropdown($name.'[]', $am_pm, $selected['ampm']);
		}
		else
		{
			$r = form_dropdown($name.'[]', $military_hours, $selected['military_hour']).NBS.':'.NBS;
			$r .= form_dropdown($name.'[]', $mins, $selected['minute']);
		}
		return $r;
	}


	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		$data = $this->_create_timestamp($data);
		if(!empty($data) && $data > 0)
		{
			if(isset($params['format']) && !empty($params['format']))
			{
				$data = ee()->localize->format_date($params['format'], $data, FALSE);
			}
			return $data;
		}
	}


	/*
		Low Variables	
	*/
	function var_display_field($data)
	{
		return $this->display($data, $this->name);
	}
	
	
	/*
		Matrix	
	*/
	function display_cell($data)
	{
		return $this->display($data, $this->cell_name);
	}

	function save_cell($data)
	{
		return $this->save($data);
	}
	
	function display_cell_settings($settings)
	{
		return array(
		    array(lang('display_style'),
		    form_dropdown('display_style', $this->display_styles, (isset($settings['display_style'])) ? $settings['display_style'] : '')),
		    array(lang('time_increments'),
		    form_dropdown('time_increments', $this->time_increments, (isset($settings['time_increments'])) ? $settings['time_increments'] : ''))
		);
	}	


	/*
		Zenbu	
	*/
	function zenbu_display($entry_id, $channel_id, $data, $table_data = array(), $field_id, $settings, $rules = array())
	{
		$format = (isset($settings['setting'][$channel_id]['extra_options']['field_'.$field_id]['format'])) ? $settings['setting'][$channel_id]['extra_options']['field_'.$field_id]['format'] : '%g:%i%a';
		return (!empty($data)) ? ee()->localize->format_date($format, $data, FALSE) : '';

	}


	function zenbu_field_extra_settings($table_col, $channel_id, $extra_options)
	{
		$value = (isset($extra_options['format'])) ? $extra_options['format'] : '';
		$settings = array(
			'format' => form_label(lang('time_select_time_format').NBS.form_input('settings['.$channel_id.']['.$table_col.'][format]', $value))
		);
		return $settings;
	}
	
	function update($current = '')
	{
		if($current == $this->info['version'])
		{
			return FALSE;
		}
		return TRUE;
	}
	
	function _create_timestamp($data)
	{
		// Do we have something here?
		if(!empty($data))
		{
			// Arrays come from an entry form
			if(is_array($data))
			{
				// At least an hour is required
				if(!empty($data[0]))
				{
					$hour = $data[0];
					$min = (empty($data[1])) ? 0 : $data[1];
	
					// Did we post AM/PM? 12-hour time
					if(isset($data[2]))
					{
						if($data[2] == 'am' && $hour == 12)
						{
							$hour = 0;
						}
						if($data[2] == 'pm')
						{
							$hour = ($hour < 12) ? $hour + 12 : 12;
						}
					}
					else
					{
						// Otherwise, 24-hour time
						if($hour == 'midnight')
						{
							$hour = 0;
						}
					}
					$s = ((intval($min) * 60) + ($hour * 3600));
					// Midnight gets set as 1 to prevent false falsiness
					return ($s == 0) ? 1 : $s;
				}
				else
				{
					// There was no hour set, so no go
					return null;
				}
			}
			
			if(is_string($data))
			{
				// Someone is maybe be passing an integer via the API
				if(is_numeric($data) && $data <= 86400)
				{
					return $data;
				}

				// Someone is maybe be passing HH:MM via the API
				if(preg_match('/(\d\d?):(\d\d?)/', $data, $matches))
				{
					return ($matches[1] * 3600) + ($matches[2] * 60);
				}
				 
			}
		}
		return null;
	}

}