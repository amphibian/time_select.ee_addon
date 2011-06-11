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
		'version'	=> '1.0.2'
	);
 
 			
	function Time_select_ft()
	{
		parent::EE_Fieldtype();
		$this->EE->lang->loadfile('time_select');	
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
		$standard = array('' => '--');
		$military = array('' => '--');
		
		switch($this->settings['time_increments'])
		{
			case '15min':
				$mins = array('00','15','30','45');
				break;
			case '30min':
				$mins = array('00','30');
				break;
			case '1hour':
				$mins = array('00');
				break;							
		}
		
		$i = 0;
		while($i < 24)
		{
			$k = str_pad($i, 2, '0', STR_PAD_LEFT);
			if($i == 00)
			{
				$h = '12';
				$ap = 'a.m.';
			}
			elseif($i < 12)
			{
				$h = $i;
				$ap = 'a.m.';
			}
			else
			{
				$h = ($i == 12) ? '12' : ($i-12);
				$ap = 'p.m.';
			}
			foreach($mins as $m)
			{
				/*
					The keys (which are the DB values) are stored as seconds
					so we can format them with decode_date() on the front-end.
				*/
				$s = ((intval($m) * 60) + ($i * 3600));
				$standard[$s] = $h.':'.$m.' '.$ap;
				$military[$s] = $k.':'.$m;
			}
			$i++;
		}
		
		/*
			Fix for SafeCracker, where 0 is equated with an empty string,
			causing a double "selected" value. Caught by GDmac.
		*/
		if($data === FALSE) $data = '';
		
		return form_dropdown($name, ($this->settings['display_style'] == '12hr') ? $standard : $military, $data);
	}
	
	
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		/*
			Before and after parameter. 'Starts at '.'12:45'.' hour' 
		    to avoid advanced conditionals like {if field_name != ""} ... {/if}
		*/
		$before = (isset($params['before']) ? $params['before'].' ' : '');
		$after  = (isset($params['after'])  ? ' '.$params['after'] : '');
		
		if(isset($params['format']) && !empty($params['format']))
		{
			$data = $this->EE->localize->decode_date($params['format'], $data, FALSE);
		}
		return $before . $data . $after;
	}

}
