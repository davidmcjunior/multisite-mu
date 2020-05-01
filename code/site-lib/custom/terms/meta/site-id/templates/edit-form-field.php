<tr class="form-field form-required term-name-wrap">
	<th scope="row"><label for="site_id_radio">Domain Mapping</label></th>
	<td>
		<button type="button" id="show-domain-dialog">Map...</button>
		<?php
			$display_str = 'Unmapped';
			if ( isset( $selected ) && isset( $selected[ 'domain' ] ) && isset( $selected[ 'site_id' ] ) ) {
				$display_str = "{$selected[ 'domain' ]} ({$selected[ 'site_id' ]})";
			}
		?>
		<span style="padding-left:.5em" id="selected-domain"><?php echo $display_str ?></span>
	</td>
</tr>
</tbody>
</table>
<?php include_once 'site-id-select.php' ?>