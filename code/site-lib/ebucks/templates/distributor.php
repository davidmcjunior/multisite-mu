<?php
$user_id = get_current_user_id();
$groups  = \Site\Ebucks\Distributor::get_sales_portal_groups();
?>

<link href="<?php echo get_stylesheet_directory_uri() ?>/css/selectize/selectize.css" rel="stylesheet">
<script src="<?php echo get_stylesheet_directory_uri() ?>/js/selectize/selectize.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/js/inputmask/inputmask.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/js/inputmask/jquery.inputmask.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/js/inputmask/inputmask.numeric.extensions.min.js"></script>
<style type="text/css">
	.spinner {
		background: black url('/wp-admin/images/wpspin_light-2x.gif') center center no-repeat;
		position: fixed;
		width: 100%;
		height: 100%;
		opacity: .5;
	}
</style>
<form id="ebucks-distribute" method="post">
	<table class="form-table">
		<tbody>
		<tr>
			<th scope="row"><label for="groups">Groups</label></th>
			<td>
				<select id="groups" type="text" name="groups[]" class="regular-text" placeholder="Begin typing group name..." required>
				<?php foreach ( $groups as $group ) : ?>
					<option value="<?php echo $group ?>"><?php echo $group ?></option>
				<?php endforeach ?>
				</select>
				<p class="description">Enter Groups</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="amount">Amount</label></th>
			<td>
				<input id="amount" style="padding: 8px;" type="text" name="amount" class="regular-text" required>
				<p class="description">Enter Amount</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="type">Type</label></th>
			<td>
				<select id="type" type="text" class="regular-text" name="type" style="height: 34px;" required>
					<option value="credit">Credit</option>
					<option value="debit">Debit</option>
				</select>
				<p class="description">Select payment type</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="details">Description</label></th>
			<td>
				<textarea id="details" name="details" class="regular-text"></textarea>
				<p class="description">Enter Description</p>
			</td>
		</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="button" name="submit" id="submit" class="button button-primary" value="Distribute e-Bucks">
		<input type="button" id="reload" onclick="window.location.reload(true)" class="button button-primary" value="Make Another Distribution" style="display: none">
	</p>
</form>
<div id="status" class="status-box">
	<p></p>
</div>
<script type="text/javascript">
	(function ($) {
		'use strict';

		var $reload = $('#reload');

		$('#groups').selectize({
			maxItems : null
		});

		$('#amount').inputmask({
			alias        : 'currency',
			rightAlign   : false,
			numericInput : true
		});

		$('#submit').click(function (event) {
			var $spinner = $('#spinner'),
				$details = $('#details'),
				$status  = $('#status'),
				$groups  = $('#groups'),
				$amount  = $('#amount'),
				$type    = $('#type'),
				$this    = $(this);

			var	amount = $amount.val().replace(/[$,]/g, ''),
				groups = $groups.val();

			$status.html('');
			$spinner.show();

			if (groups === null) {
				$status.html('At least one group must be selected.');
				$groups.focus();
				return;
			}

			if (amount < 0.01) {
				$status.html('Amount must be greater than 0.');
				$amount.focus();
				return;
			}

			$.post(
				ajaxurl,
				{
					type       : $type.val(),
					action     : 'site_distribute_ebucks',
					groups     : groups,
					amount     : amount,
					details    : $details.val()
				}, function(response) {
					$this.remove();
					$reload.show();
					$status.html(response.message);
					$spinner.hide();
				},
				'json'
			)
			.fail(function(data) {
				$('input').clear();
			});
		});
	}) (jQuery);
</script>