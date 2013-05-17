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

    Copyright 2011 Derek Hogue
*/

class Time_select_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> 'Time Select',
		'version'	=> '1.0.8'
	);


	function Time_select_ft()
	{
		EE_Fieldtype::__construct();
		$this->EE->lang->loadfile('time_select');

		// Backwards-compatibility with pre-2.6 Localize class
		$this->format_date_fn = (version_compare(APP_VER, '2.6', '>=')) ? 'format_date' : 'decode_date';
	}


	function display_settings($settings)
	{
		$styles = $this->_get_display_styles();
		$increments = $this->_get_time_increments();
		$this->EE->table->add_row(
			$this->EE->lang->line('display_style'),
			form_dropdown('display_style', $styles, (isset($settings['display_style'])) ? $settings['display_style'] : '', 'id="display_style"')
		);
		$this->EE->table->add_row(
			$this->EE->lang->line('time_increments'),
			form_dropdown('time_increments', $increments, (isset($settings['time_increments'])) ? $settings['time_increments'] : '')
		);
	}


	function display_cell_settings($settings)
	{
		$styles = $this->_get_display_styles();
		$increments = $this->_get_time_increments();
		return array(
		    array($this->EE->lang->line('display_style'),
		    form_dropdown('display_style', $styles, (isset($settings['display_style'])) ? $settings['display_style'] : '')),
		    array($this->EE->lang->line('time_increments'),
		    form_dropdown('time_increments', $increments, (isset($settings['time_increments'])) ? $settings['time_increments'] : ''))
		);

	}


	function _get_display_styles()
	{
		return array(
			'12hr' => $this->EE->lang->line('12hr'),
			'24hr' => $this->EE->lang->line('24hr')
		);
	}


	function _get_time_increments()
	{
		return array(
			'1min' => $this->EE->lang->line('1min'),
			'15min' => $this->EE->lang->line('15min'),
			'30min' => $this->EE->lang->line('30min'),
			'1hour' => $this->EE->lang->line('1hour')
		);
	}


	function save_settings($data)
	{
		return array(
			'display_style' => $this->EE->input->post('display_style'),
			'time_increments' => $this->EE->input->post('time_increments')
		);
	}

	function save($data)
	{
		// Do we have something here?
		if(!empty($data))
		{
			if( is_array($data) && !empty($data[0]) )
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
					// 24-hour
					if($hour == 'midnight')
					{
						$hour = 0;
					}
				}
				$s = ((intval($min) * 60) + ($hour * 3600));
				return ($s == 0) ? 1 : $s;
			}
			else
			{
				// Someone might be passing an integer via the API
				return (is_numeric($data) && $data <= 86400) ? $data : false;
			}
		}
		return false;
	}


	function save_cell($data)
	{
		return $this->save($data);
	}


	function display_field($data)
	{
		return $this->display($data, $this->field_name);
	}


	function display_cell($data)
	{
		return $this->display($data, $this->cell_name);
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
		if(isset($params['format']) && !empty($params['format']))
		{
			$data = $this->EE->localize->{$this->format_date_fn}($params['format'], $data, FALSE);
		}
		return $data;
	}


	function zenbu_display($entry_id, $channel_id, $data, $table_data = array(), $field_id, $settings, $rules = array())
	{
		$format = (isset($settings['setting'][$channel_id]['extra_options']['field_'.$field_id]['format'])) ? $settings['setting'][$channel_id]['extra_options']['field_'.$field_id]['format'] : '%g:%i%a';
		return (!empty($data)) ? $this->EE->localize->{$this->format_date_fn}($format, $data, FALSE) : '';

	}


	function zenbu_field_extra_settings($table_col, $channel_id, $extra_options)
	{
		$value = (isset($extra_options['format'])) ? $extra_options['format'] : '';
		$settings = array(
			'format' => form_label($this->EE->lang->line('time_select_time_format').NBS.form_input('settings['.$channel_id.']['.$table_col.'][format]', $value))
		);
		return $settings;
	}

}