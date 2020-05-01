<?php

if ( ! isset( $site_data ) ) {
	$site_data = array();
}

$is_edit = ( 'edit' === $action ) ? true : false;

?>
<style>
	.ui-widget-header {
		color: #FFFFFF !important;
		background-color: #0085ba !important;
	}
	.dlg-button-wide {
		width: 10em;
	}
</style>
<div id="domain-dlg" style="width: 100%" hidden="hidden">
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th width="3em"></th>
				<th>Domain</th>
				<th>Site ID</th>
				<th>Destination Tags Assigned</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="radio" id="site-id-none" name="site_id_radio" value="" <?php if ( ! isset( $selected ) ) echo 'checked="checked"' ?>></td>
				<td><span style="font-style: italic; color: lightgrey">(no mapping)</span></td>
				<td></td>
				<td></td>
			</tr>
			<?php foreach ( $site_data as $id => $data ) : ?>
			<tr style="border: none">
				<td><input type="radio" id="site-<?php echo $id ?>-id" name="site_id_radio" value="<?php echo $id ?>" <?php echo $id === (int) $selected[ 'site_id' ] ? 'checked="checked"' : '' ?>></td>
				<td><span id="site-<?php echo $id ?>-domain"><?php echo $data[ 'domain' ] ?></span></td>
				<td><?php echo $id ?></td>
				<td><?php echo $data[ 'destination_tags' ] ?></td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	(function($) {
		var input = '<input type="hidden" id="site_id" name="site_id" value="<?php echo ( isset( $selected[ 'site_id' ] ) && $is_edit ) ? $selected[ 'site_id' ] : '' ?>">';

		$('form#addtag').append(input);
		$('form#edittag').append(input);

		$(document).ready(function() {
			$('#show-domain-dialog').click(function(e) {
				$('#domain-dlg').dialog('open');
			});

			$('#domain-dlg').dialog({
				modal: true,
				autoOpen: false,
				width: 800,
				title: 'Map Destination Tag to Domain',
				buttons: [
					{
						class: 'dlg-button-wide button-primary',
						text: 'Select',
						click: function() {
							var	site_id = $('input[name=site_id_radio]:checked').val(),
								domain  = '<?php echo $is_edit ? 'Change to: ' : '' ?>';

							$('#site_id').val(site_id);

							if (site_id) {
								domain += '<span style="font-weight: bold">' + $('#site-' + site_id + '-domain').html() + ' (' + site_id + ')</span>';
							} else {
								domain += 'Unmapped';
							}

							$('#selected-domain').html(domain);
							$(this).dialog('close');
						}
					},
					{
						class: 'dlg-button-wide button-primary',
						text: 'Cancel',
						click: function() {
							$(this).dialog('close');
						}
					}
				]
			});
		});
	})(jQuery);
</script>