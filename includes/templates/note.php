		<div class='wp-dashboard-note-wrap regular-note' data-note-type='regular' data-color-text='<?php echo $note_meta['color_text']; ?>'>

			<div class='wp-dashboard-note' contenteditable='true'>
				<?php echo $note->post_content; ?>
			</div>

			<div class='wp-dashboard-note-options'>

				<span class='status'></span>
				<div class='wpdn-extra'>
					<span class='wpdn-option-visibility'>
						<?php
						if ( 'private' == $note_meta['visibility'] && $note_meta ) :
							$status['icon'] 		= 'dashicons-lock';
							$status['title'] 		= __( 'Just me', 'wp-dashboard-notes' );
							$status['visibility'] 	= 'private';
						else :
							$status['icon'] 		= 'dashicons-groups';
							$status['title'] 		= __( 'Everyone', 'wp-dashboard-notes' );
							$status['visibility'] 	= 'public';
						endif; ?>

						<span class='wpdn-toggle-visibility' title='<?php _e( 'Visibility:', 'wp-dashboard-notes' ); ?> <?php echo $status['title']; ?>' data-visibility='<?php echo $status['visibility']; ?>'>
							<div class='wpdn-visibility visibility-publish dashicons <?php echo $status['icon']; ?>'></div>
						</span>

						<span class='wpdn-color-note' title='<?php _e( 'Give me a color!', 'wp-dashboard-notes' ); ?>'>
							<span class='wpdn-color-palette'>
								<span class='color color-white'	 data-select-color-text='white' 	data-select-color='#ffffff'></span>
								<span class='color color-red' 	 data-select-color-text='red' 		data-select-color='#f7846a'></span>
								<span class='color color-orange' data-select-color-text='orange'	data-select-color='#ffbd22'></span>
								<span class='color color-yellow' data-select-color-text='yellow' 	data-select-color='#eeee22'></span>
								<span class='color color-green'  data-select-color-text='green' 	data-select-color='#bbe535'></span>
								<span class='color color-blue' 	 data-select-color-text='blue' 		data-select-color='#66ccdd'></span>
								<span class='color color-black'  data-select-color-text='black' 	data-select-color='#777777'></span>
							</span>
							<div class='dashicons dashicons-art wpdn-note-color' data-note-color='<?php echo $note_meta['color']; ?>'></div>
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

					</span>
				</div>
			</div>

		</div>
