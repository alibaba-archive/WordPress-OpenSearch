<?php

use OpenSearch\Admin\Controllers\Settings;
use OpenSearch\Core;

$registry = Core\Registry::instance();
$langDomain = $registry->getPluginShortName();
?>
<div id="opSouSettings" class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2><a href="http://opensearch.console.aliyun.com/" target="_blank">AliYun Open Search Settings</a>&nbsp;&nbsp;</h2>
	<form action="" method="post" id="opSou_form">
		<p>
		IF you have not got this template, please first download it for "AliYun OpenSearch". &nbsp;
		<input type="hidden" value="1" name="<?php echo Settings::settingsField; ?>[accessTemplateValid]">
		<input type="submit" value="<?php esc_attr_e('Download Template', $langDomain); ?>" id="opSou_dlTemplate" class="button button-primary">&nbsp;
		Then refresh this page when this template has been downloaded.
		</p>
		<?php if (Core\Utils::readyForTemplate()): ?>
		<div id="opSoutabs">
			<ul>
				<!-- <li><a class="nav-tab nav-tab-active" href="#tab-general"><?php esc_html_e('Account', $langDomain); ?></a></li> -->
			</ul>
			<div id="tab-general">
				<input type="hidden" value="<?php echo Settings::updateAction; ?>" name="opSou_action">
				<?php wp_nonce_field(Settings::updateAction); ?>
				<table style="width: 50%" class="widefat">
					<thead>
						<tr>
							<th class="row-title" colspan="2">
							<strong><?php esc_html_e('Configure your AccessKeys', $langDomain); ?></strong>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row"><label for="opSou_accessKeyId"><?php esc_html_e('Access Key ID', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getAccessKeyId()); ?>" id="opSou_accessKeyId" name="<?php echo Settings::settingsField; ?>[accessKeyId]"></td>
						</tr>
						<tr>
							<th scope="row"><label for="opSou_accessKeySecret"><?php esc_html_e('Access Key Secret', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getAccessKeySecret()); ?>" id="opSou_accessKeySecret" name="<?php echo Settings::settingsField; ?>[accessKeySecret]"></td>
						</tr>
						<tr>
							<th scope="row"><label for="opSou_accessHost"><?php esc_html_e('Access Host', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getAccessHost()); ?>" id="opSou_accessHost" name="<?php echo Settings::settingsField; ?>[accessHost]"></td>
						</tr>
						<tr>
							<td>
								<p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes', $langDomain); ?>" class="button button-primary" id="submit" name="submit"></p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php if (Core\Utils::readyForClient()): ?>
				<table class="widefat" style="margin-top: 30px; width: 50%; ">
					<thead>
						<tr>
							<th class="row-title" colspan="2"><strong><?php esc_html_e('Upload Your Document', $langDomain); ?></strong></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row"><label for="opSou_appName"><?php esc_html_e('App Name', $langDomain); ?></label></th>
							<td><input type="text" class="regular-text" value="<?php echo esc_attr($registry->getAppName()); ?>" id="opSou_appName" name="<?php echo Settings::settingsField; ?>[appName]"></td>
						</tr>
						<?php if ($registry->getAppName()): ?>

							<tr class="index-action-row index-action-button">
								<th scope="row"><label for="opSou_index"><?php esc_html_e('Click to upload document', $langDomain); ?></label></th>
								<td>
									<div class="algolia-action-button" style="width:50%;">
										<button type="button" class="button button-secondary"  id="opSou_index" name="opSou_index"><?php esc_html_e('Upload', $langDomain); ?></button>
										<span class="spinner algolia-index-spinner"></span>
									</div>
								</td>
							</tr>
							<tr class="index-action-row index-messages">
								<th>&nbsp;</th>
								<td>
									<div class="success"><ul id="op-sou-index-result"></ul></div>
									<div class="error error-message" style="display: none;"><p id="mvn-alg-index-error" ></p></div>
								</td>
							</tr>
						<?php else: ?>
							<tr>
								<td colspan="2">
									<p><?php _e('Please set an "Index Name" and then update the settings to start indexing content.', $langDomain) ?></p>
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>
								<p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes', $langDomain); ?>" class="button button-primary" id="submit" name="submit"></p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php endif; ?>
			</div>
		</div>
		<?php endif ?>
	</form>
	<h3><a href="https://ak-console.aliyun.com/#/accesskey" target="_blank">Here, you can get you AccessKeys</a></h3>
</div>
