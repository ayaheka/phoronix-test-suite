<?php

/*
	Phoronix Test Suite
	URLs: http://www.phoronix.com, http://www.phoronix-test-suite.com/
	Copyright (C) 2009 - 2011, Phoronix Media
	Copyright (C) 2009 - 2011, Michael Larabel
	pts_ImageComparisonGraph.php: A graph object for image comparisons

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

class pts_ImageComparisonGraph extends pts_Graph
{
	public function __construct(&$result_object, &$result_file = null)
	{
		parent::__construct($result_object, $result_file);
		$this->graph_value_type = "ABSTRACT";
		$this->graph_hide_identifiers = true;
		$this->graph_data_title = array("PASSED", "FAILED");
	}
	protected function render_graph_pre_init()
	{
		if(!function_exists("imagecreatefromstring"))
		{
			echo "\nCurrently you must have PHP-GD installed to utilize this feature.\n";
			return false;
		}

		// Do some common work to this object
		$draw_count = count($this->graph_identifiers);
		$img_first = imagecreatefromstring(base64_decode($this->graph_data[0][0]));
		$img_width = imagesx($img_first);
		$img_height = imagesy($img_first);

		// Assume if the images are being rendered together they are same width and height
		$this->c['graph']['height'] = 72 + ($draw_count * ($img_height + 22)); // 110 at top plus 20 px between images
		$this->c['graph']['width'] = $this->c['graph']['width'] < ($img_width + 20) ? $img_width + 20 : $this->c['graph']['width'];

		$this->update_graph_dimensions($this->c['graph']['width'], $this->c['graph']['height']);
	}
	public function renderGraph()
	{
		if(!function_exists("imagecreatefromstring"))
		{
			echo "\nCurrently you must have PHP-GD installed to utilize this feature.\n";
			return false;
		}

		$this->render_graph_pre_init();
		$this->render_graph_init();
		$this->render_graph_heading(false);

		$img_first = imagecreatefromstring(base64_decode($this->graph_data[0][0]));
		$img_width = imagesx($img_first);
		$img_height = imagesy($img_first);
		unset($img_first);

		$draw_count = count($this->graph_identifiers);

		for($i_o = 0; $i_o < $draw_count; $i_o++)
		{
			$from_left = ($this->c['graph']['width'] / 2) - ($img_width / 2);
			$from_top = 60 + ($i_o * ($img_height + 22));
// TODO: make sure this code still works... XXX
			$this->svg_dom->add_element('rect', array('x' => ($from_left - 1), 'y' => ($from_right - 1), 'width' => ($img_width + 2), 'height' => ($img_height + 2), 'fill' => $this->c['color']['body_light']));
			$this->svg_dom->add_element('image', array('xlink:href' => base64_decode($this->graph_data[0][$i_o]), 'x' => $from_left, 'y' => $from_top, 'width' => $img_width, 'height' => $img_height));
			$this->svg_dom->add_text_element($this->graph_identifiers[$i_o], array('x' => round($this->c['graph']['width'] / 2), 'y' => ($from_top + $img_height + 3), 'font-size' => $this->graph_font_size_bars, 'fill' => $this->c['color']['main_headers'], 'text-anchor' => 'middle', 'dominant-baseline' => 'text-before-edge'));
		}

		if(!empty($this->graph_watermark_text))
		{
			$this->svg_dom->add_text_element($this->graph_watermark_text, array('x' => round($this->c['graph']['width']), 'y' => ($this->c['graph']['height'] - 15), 'font-size' => 10, 'fill' => $this->c['color']['text'], 'text-anchor' => 'middle', 'dominant-baseline' => 'text-before-edge'));
		}

		return $this->return_graph_image(100);
	}
}

?>
