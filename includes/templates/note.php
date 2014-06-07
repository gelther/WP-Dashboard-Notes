		<div class='wp-dashboard-note-wrap'>
		
			<div class='wp-dashboard-note'>
				<?php echo $note->post_content; ?>
			</div>

			<div class='wp-dashboard-note-options'>
				<div class="dashicons dashicons-plus" style='color: #ccc; margin-right: 8px;'></div>
				<input type='text' name='list_item' class='add-list-item' data-id='<?php echo $note->ID; ?>' placeholder='<?php _e( 'List item', 'wp-dashboard-notes' ); ?>' value=''>
				<span class='status'></span>
				<div class='wpdn-extra'>
					<span class='wpdn-option-visibility'>
						<?php 
						if ( 'publish' == $note->post_status ) :
							$status['icon'] 		= 'dashicons-groups';
							$status['title'] 		= __( 'Public', 'wp-dashboard-notes' );
							$status['visibility'] 	= 'publish';
						elseif ( 'private' == $note->post_status ) :
							$status['icon'] 		= 'dashicons-lock';
							$status['title'] 		= __( 'Private', 'wp-dashboard-notes' );
							$status['visibility'] 	= 'private';
						endif; ?>
						
						<span class='wpdn-toggle-visibility' title='<?php __( 'Visibility:', 'wp-dashboard-notes' ); ?> <?php echo $status['title']; ?>' data-visibility='<?php echo $status['visibility']; ?>'>
							<span style='line-height: 40px;'>Visibility: </span><div class="wpdn-visibility visibility-publish dashicons <?php echo $status['icon']; ?>"></div>
						</span>
						
						<span class='wpdn-delete-note' title='<?php _e( 'Delete note', 'wp-dashboard-notes' ); ?>'>
							<div class="dashicons dashicons-trash"></div>
						</span>

						<span class='wpdn-add-note' title='<?php _e( 'Add a new note', 'wp-dashboard-notes' ); ?>'>
							<div class="dashicons dashicons-plus"></div>
						</span>
						
						<span class='wpdn-color-note' title='<?php _e( 'Give me a color!', 'wp-dashboard-notes' ); ?>'>
							<span class='wpdn-color-palette'>
								<span class='color color-white' 	data-select-color='#ffffff'></span>
								<span class='color color-red' 		data-select-color='#f7846a'></span>
								<span class='color color-orange' 	data-select-color='#ffbd22'></span>
								<span class='color color-yellow' 	data-select-color='#eeee22'></span>
								<span class='color color-green' 	data-select-color='#bbe535'></span>
								<span class='color color-blue' 		data-select-color='#66ccdd'></span>
								<span class='color color-black' 	data-select-color='#777777'></span>
							</span>
							<div class="dashicons dashicons-art wpdn-note-color" data-note-color='<?php echo $note_meta['color']; ?>'></div>
						</span>
						
						<span class='wpdn-note-type'>
							<div class="dashicons dashicons-list-view"></div>
						</span>
	
					</span>
				</div>
			</div>
			
		</div>
