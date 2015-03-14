		<div class='wp-dashboard-note-wrap regular-note' data-note-type='regular' data-color-text='<?php echo $note_meta['color_text']; ?>' data-note-color='<?php echo $note_meta['color']; ?>'>

			<div class='wp-dashboard-note' contenteditable='true'>
				<?php echo $content; ?>
			</div>

			<div class='wp-dashboard-note-options'>

				<span class='status'></span>
				<div class='wpdn-extra'>

					<span class='wpdn-visibility-settings' title='<?php _e( 'Visibility settings', 'wp-dashboard-notes' ); ?>'>
						<div class='dashicons dashicons-welcome-view-site'></div>
					</span>

					<span class='wpdn-color-note' title='<?php _e( 'Give me a color!', 'wp-dashboard-notes' ); ?>'>
						<span class='wpdn-color-palette'>

							<?php foreach ( $colors as $name => $color ) : ?>
								<span class='color color-<?php echo $name;?>' data-select-color-text='<?php echo $name; ?>'	data-select-color='<?php echo $color; ?>' style='background-color: <?php echo $color; ?>'></span>
							<?php endforeach; ?>

						</span>
						<div class='dashicons dashicons-art wpdn-note-color'></div>
					</span>

					<span title='<?php _e( 'Convert to list note', 'wp-dashboard-notes'); ?>'>
						<div class='wpdn-note-type dashicons dashicons-list-view'></div>
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
		<div class='visibility-settings'>

			<i class='dashicons dashicons-arrow-left-alt2 close-visibility-settings'></i>

			<form class='user-permissions-form'>

				<div class='user-permissions-roles'><?php

					?><h3 style='padding-left: 0; padding-top: 0;'><?php _e( 'User roles', 'wp-dashboard-notes' ); ?></h3><?php
					$roles = array_keys( get_editable_roles() );
					$roles = array_combine( $roles, $roles );

					foreach ( $roles as $role ) :
						?><div class='user-permission-role user-permission-role-<?php echo $role; ?>'>
						<input type='checkbox' name='user_permission[user_role][]' id='user_permission_<?php echo $note->ID; ?>_<?php echo $role; ?>' <?php checked( in_array( $role, $role_permissions ) ); ?> value='<?php echo $role; ?>'>
							<label for='user_permission_<?php echo $note->ID; ?>_<?php echo $role; ?>'><?php echo ucfirst( $role ); ?></label>
						</div><?php
					endforeach;

				?></div>

				<div class='user-permissions-users'>

				</div>

			</form>

		</div>
