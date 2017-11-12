<?php

class Illdy_Widget_Project extends WP_Widget{
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct( 'illdy_project', __( '[Illdy] - Project', 'illdy-companion' ), [
			'description' => __( 'Add this widget in "Front page - Projects Sidebar".', 'illdy-companion' ),
		] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 *  Enqueue Scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'illdy-widget-upload-image', ILLDY_COMPANION_ASSETS_DIR . 'js/widget-upload-image.js', false, '1.0', true );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$defaults = [
			'title'       => '',
			'url'         => '',
			'image'       => '',
			'description' => '',
		];

		$instance = wp_parse_args( $instance, $defaults );

		$image_id                 = illdy_get_image_id_from_image_url( $instance['image'] );
		$get_attachment_image_src = wp_get_attachment_image_src( $image_id, 'illdy-front-page-projects' );
		$img_src                  = $image_id ? esc_url( $get_attachment_image_src[0] ) : esc_url( $instance['image'] );

		$output = '<a class="project" href="' . esc_url( $instance['url'] ) . '" title="' . esc_html( $instance['title'] ) . '" style="background-image: url(' . $img_src . '); height: 299px;">';
		$output .= '  <div class="project_description_layer">';
		$output .= '    <p class="project_description">' . wp_kses_post( $instance['description'] ) . '</p>';
		$output .= '  </div>';
		$output .= '</a>';

		echo $output;

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$defaults = [
			'title'       => __( '[Illdy] - Project', 'illdy-companion' ),
			'url'         => '',
			'image'       => get_template_directory_uri() . '/layout/images/front-page/front-page-project-1.jpg',
			'description' => '',
		];
		$instance = wp_parse_args( $instance, $defaults );

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'illdy-companion' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_name( 'image' ); ?>"><?php _e( 'Image:', 'illdy-companion' ); ?></label>
            <input type="text" class="widefat custom_media_url_<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" value="<?php echo $instance['image']; ?>" style="margin-top:5px;">
            <input type="button" class="button button-primary custom_media_button" id="custom_media_button_service" data-fieldid="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php _e( 'Upload Image', 'illdy-companion' ); ?>" style="margin-top: 5px;">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'URL:', 'illdy-companion' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo esc_attr( $instance['url'] ); ?>">
        </p>

        <p class="illdy-editor-container">
            <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description:', 'illdy-companion' ); ?></label>
            <textarea name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" class="widefat"><?php echo wp_kses_post( $instance['description'] ); ?></textarea>
        </p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = [];
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['image']       = ! empty( $new_instance['image'] ) ? esc_url_raw( $new_instance['image'] ) : '';
		$instance['url']         = ( ! empty( $new_instance['url'] ) ? esc_url_raw( $new_instance['url'] ) : '' );
		$instance['description'] = ( ! empty( $new_instance['description'] ) ) ? wp_kses_post( $new_instance['description'] ) : '';

		return $instance;
	}
}

function illdy_register_widget_project() {
	register_widget( 'Illdy_Widget_Project' );
}

add_action( 'widgets_init', 'illdy_register_widget_project' );
