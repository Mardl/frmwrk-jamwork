<?php

namespace unittest\jamwork\table;

use \jamwork\table\Table;
use \jamwork\table\TableHTML;

class TableDataTest extends \PHPUnit_Framework_TestCase
{

	public function test_Irgendwas()
	{
		$tableOutput = new TableHtml();
		$table = new Table($tableOutput);

		$table->id('table_id_1')->addClass('table1')->addClass('table2');
		$thead = $table->thead()->addClass('head1');

		$row = $thead->row()->addClass('row1'); // rowspan (?)
		$row->addCell('das ist ein Text', 'td1')->addCell('das ist ein Text2', 'td2')->addCell('das ist ein Text3', 'td3');

		$tbody = $table->tbody()->addClass('body1');

		$row = $tbody->row()->addClass('row2'); // rowspan (?)
		$row->addCell('das ist ein Text', 'td1', 2)->addCell('das ist ein Text2', 'td2');

		$row = $tbody->row()->addClass('row2'); // rowspan (?)
		$row->addCell('das ist ein Text')->addCell('das ist ein Text2');

		$tfoot = $table->tfoot()->addClass('foot1');

		$row = $tfoot->row()->addClass('row1'); // rowspan (?)
		$row->addCell('das ist ein Text', 'td1')->addCell('das ist ein Text2', 'td2')->addCell('das ist ein Text3', 'td3');

		$str = $table->create();

		$compStr = '<table id="table_id_1" class="table1 table2">\n\t<thead class="head1">\n\t\t<tr class="row1">\n\t\t\t<th class="td1">das ist ein Text</th>\n\t\t\t<th class="td2">das ist ein Text2</th>\n\t\t\t<th class="td3">das ist ein Text3</th>\n\t\t</tr>\n\t</thead>\n\t<tbody class="body1">\n\t\t<tr class="row2">\n\t\t\t<td class="td1" colspan="2">das ist ein Text</td>\n\t\t\t<td></td>\n\t\t</tr>\n\t\t<tr class="row2">\n\t\t\t<td>das ist ein Text</td>\n\t\t\t<td>das ist ein Text2</td>\n\t\t\t<td></td>\n\t\t</tr>\n\t</tbody>\n\t<tfoot class="foot1">\n\t\t<tr class="row1">\n\t\t\t<td class="td1">das ist ein Text</td>\n\t\t\t<td class="td2">das ist ein Text2</td>\n\t\t\t<td class="td3">das ist ein Text3</td>\n\t\t</tr>\n\t</tfoot>\n</table>';

		$compStr = '<table id="table_id_1" class="table1 table2">
	<thead class="head1">
		<tr class="row1">
			<th class="td1">das ist ein Text</th>
			<th class="td2">das ist ein Text2</th>
			<th class="td3">das ist ein Text3</th>
		</tr>
	</thead>
	<tbody class="body1">
		<tr class="row2">
			<td class="td1" colspan="2">das ist ein Text</td>
			<td></td>
		</tr>
		<tr class="row2">
			<td>das ist ein Text</td>
			<td>das ist ein Text2</td>
			<td></td>
		</tr>
	</tbody>
	<tfoot class="foot1">
		<tr class="row1">
			<td class="td1">das ist ein Text</td>
			<td class="td2">das ist ein Text2</td>
			<td class="td3">das ist ein Text3</td>
		</tr>
	</tfoot>
</table>
';
		$this->assertSame($compStr, $str);

	}

}
