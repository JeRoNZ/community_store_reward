<?php
$form = Core::make('helper/form');
/* @var $form \Concrete\Core\Form\Service\Form */
use \Concrete\Core\Support\Facade\Url;
?>

<div class="ccm-dashboard-header-buttons">
	<a href="<?= Url::to('/dashboard/store/discounts'); ?>" class="btn btn-primary"><i class="fa fa-truck fa-flip-horizontal"></i> <?= t("Discounts"); ?></a>
	<a href="<?= Url::to('/dashboard/store/discounts/add'); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> <?= t("Add Discount"); ?></a>
</div>

<form method="post" action="<?= $view->action('save'); ?>">
	<?= $token->output('community_store'); ?>

	<h3><?= t('Rule Selection') ?></h3>

	<div class="form-group">
		<?= $form->label('rule', 'Rule'); ?>
		<?= $form->select('rule', $rules, Config::get('community_store_rewards.discount_rule_id')) ?>
	</div>

	<div class="well">
	<span class="text-muted"><?= t('Choose the discount rule that will be used to generate a unique code.') ?>
		<?= t('Rules must require a single use code to be entered') ?></span>
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="form-group">
				<?= $form->label('spent', t('Amount spent')); ?>
				<?= $form->number('spent', Config::get('community_store_rewards.spent_amount'), ['min' => 1, 'step' => 1]) ?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<?= $form->label('points', t('Points to award')); ?>
				<?= $form->number('points', Config::get('community_store_rewards.points_awarded'), ['min' => 1, 'step' => 1]) ?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<?= $form->label('issue', t('Issue code when points total is')); ?>
				<?= $form->number('issue', Config::get('community_store_rewards.issue_points_total'), ['min' => 1, 'step' => 1]) ?>
			</div>
		</div>
	</div>


	<h3><?= t('Notifications'); ?></h3>

	<h4><?= t('Emails Sent From'); ?></h4>

	<div class="row">
		<div class="col-xs-6">
			<div class="form-group">
				<?php echo $form->label('emailAlert', t('From Email'));
				$from_email = Config::get('community_store_rewards.emailalerts');
				if (!$from_email)
					$from_email = Config::get('community_store.emailalerts');
				echo $form->text('emailAlert', $from_email, ['placeholder' => t('From Email Address')]); ?>
			</div>
		</div>

		<div class="col-xs-6">
			<div class="form-group">
				<?php
				echo $form->label('emailAlertName', t('From Name'));
				$from_name = Config::get('community_store_rewards.emailalertsname');
				if (!$from_name)
					$from_name = Config::get('app.community_store.rewards.emailalertsname');
				echo $form->text('emailAlertName', $from_name, ['placeholder' => t('From Name')]); ?>
			</div>
		</div>
	</div>

	<h3><?= t('Reward Emails'); ?></h3>

	<div class="form-group">
		<?= $form->label('receiptHeader', t('Reward Email Header Content')); ?>
		<?php $editor = Core::make('editor');
		echo $editor->outputStandardEditor('receiptHeader', Config::get('community_store_rewards.rewardHeader')); ?>
	</div>

	<div class="form-group">
		<?= $form->label('receiptFooter', t('Reward Email Footer Content')); ?>
		<?php $editor = Core::make('editor');
		echo $editor->outputStandardEditor('receiptFooter', Config::get('community_store_rewards.rewardFooter')); ?>
	</div>

	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<button class="pull-right btn btn-success" type="submit"><?= t('Save Settings'); ?></button>
		</div>
	</div>

</form>
