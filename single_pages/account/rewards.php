<?php
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Package\CommunityStoreReward\Src\CommunityStoreReward\Point;

/* @var $paginator \Concrete\Core\Search\Pagination\Pagination */

?>
<?php if (empty($points)) { ?>
	<br/><p class="alert alert-info"><?= t('No reward points found'); ?></p>
<?php } else { ?>


	<table class="table">
		<tr>
			<th>Date</th>
			<th>Points</th>
			<th>Awarded for</th>
			<th>Code</th>
			<th>Redeemed</th>
		</tr>
		<?php
		foreach ($points as $point) {
			/* @var $point Point */
			?>
			<tr>
				<td><?= $point->getDate()->format('d/m/y') ?></td>
				<td style="text-align: right"><?= $point->getPoints() ?></td>
				<td><?php
					if ($point->getOrderObject()) {
						echo $point->getRewardCodeObject()->getID();
					} ?></td>
				<td><?php
					if ($code = $point->getRewardCodeObject()) {
						echo $code->getDiscountCodeObject()->getCode();
					}
					?>
				</td>
				<td><?php
					if ($rc = $point->getRewardCodeObject()) {
						$oID = $rc->getDiscountCodeObject()->getOID();
						if ($oID) { // TODO check that community_store_order_history is installed
							echo t('Yes'); ?> - <a href="<?= \URL::to('/account/orders/order', $oID) ?>"><?= $oID ?></a><?php
						} else {
							echo t('No');
						}
					}
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</table>
<?php
}

if ($paginator->haveToPaginate()) {
	echo $pagination;
}
?>

<div class="form-actions">
	<a href="<?= \URL::to('/account') ?>" class="btn btn-default"/><?= t('Back to Account') ?></a>
</div>
