		<div class='wp-dashboard-note-wrap list-note' data-note-type='list' data-color-text='<?php echo esc_attr( $note_meta['color_text'] ); ?>' data-note-color='<?php echo esc_attr( $note_meta['color'] ); ?>'>

			<div class='wp-dashboard-note'>
				<?php echo $content; ?>
			</div>

			<div class='wp-dashboard-note-options'>
				<div class='dashicons dashicons-plus wpdn-add-item'></div>
				<input type='text' name='list_item' class='add-list-item' data-id='<?php echo esc_attr( $note->ID ); ?>' placeholder='<?php _e( 'List item', 'wp-dashboard-notes' ); ?>'>
				<span class='status'></span>
				<div class='wpdn-extra'>

					<span class='wpdn-visibility-settings' title='<?php _e( 'Visibility settings', 'wp-dashboard-notes' ); ?>'>
						<div class='dashicons dashicons-welcome-view-site'></div>
					</span>

					<span class='wpdn-color-note' title='<?php _e( 'Give me a color!', 'wp-dashboard-notes' ); ?>'>
						<span class='wpdn-color-palette'>

							<?php foreach ( $colors as $name => $color ) : ?>
								<span class='color color-<?php echo esc_attr( $name );?>' data-select-color-text='<?php echo $name; ?>'	data-select-color='<?php echo esc_attr( $color ); ?>' style='background-color: <?php echo $color; ?>'></span>
							<?php endforeach; ?>

						</span>
						<div class='dashicons dashicons-art wpdn-note-color'></div>
					</span>

					<span title='<?php _e( 'Convert to regular note', 'wp-dashboard-notes'); ?>'>
						<div class='wpdn-note-type dashicons dashicons-welcome-write-blog'></div>
					</span>

					<span class='wpdn-add-note' title='<?php _e( 'Add a new note', 'wp-dashboard-notes' ); ?>'>
						<div class='dashicons dashicons-plus'></div>
					</span>


					<span style='float: right; margin-right: 10px;' class='wpdn-delete-note' title='<?php _e( 'Delete note', 'wp-dashboard-notes' ); ?>'>
						<div class='dashicons dashicons-trash'></div>
					</span>

				</div>
			</div>

		</div>



		<!-- Visibility settings -->
		<div class='visibility-settings-overlay'></div>
		<div class='visibility-settings closed'>

			<i class='dashicons dashicons-arrow-left-alt2 close-visibility-settings'></i>

			<h2><?php _e( 'Note visibility', 'wp-dashboard-notes' ); ?></h2>
			<p><?php _e( 'Grant the following users persmission to view and edit this note.', 'wp-dashboard-notes' ); ?>
			<form class='user-permissions-form'>

				<div class='user-permissions-roles'>

					<h3><?php _e( 'User roles', 'wp-dashboard-notes' ); ?></h3><?php
					$roles = array_keys( get_editable_roles() );
					$roles = array_combine( $roles, $roles );

					if ( is_array( $roles ) ) :
						foreach ( $roles as $role ) :
							?><div class='user-permission-role user-permission-role-<?php echo $role; ?>'>
							<input type='checkbox' name='user_permission[user_role][]' id='user_permission_<?php echo esc_attr( $note->ID . '_' . $role ); ?>' <?php
								checked( in_array( $role, (array) $role_permissions ) ); ?> value='<?php echo esc_attr( $role ); ?>'>
								<label for='user_permission_<?php echo esc_attr( $note->ID . '_' . $role ); ?>'><?php echo esc_html( ucfirst( $role ) ); ?></label>
							</div><?php
						endforeach;
					endif;

				?></div>

				<div class='user-permissions-users'>

					<h3><?php _e( 'Users', 'wp-dashboard-notes' ); ?></h3>
					<input class='user-permission select2' placeholder='<?php _e( 'Search for a user', 'wp-dashboard-notes' ); ?>'><?php

					if ( is_array( $user_permissions ) ) :
						foreach ( $user_permissions as $user_id ) :
							$user_data = get_userdata( $user_id );
							?><div class='user-permission-user user-permission-user-'>
								<input type='checkbox' name='user_permission[user][]' checked='checked' value='<?php echo esc_attr( $user_data->ID ); ?>'>
								<label for='user_permission_<?php echo esc_attr( $note->ID . '_' . $user_data->ID ); ?>'><?php echo $user_data->display_name; ?></label>
							</div><?php
						endforeach;
					endif;

				?></div>

			</form>

		</div>
