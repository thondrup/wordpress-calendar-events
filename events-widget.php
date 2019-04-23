<?php
/**
 * @package Events_widget
 * @version 1.0.1
 */

/*
  Plugin Name: Events Widget
  Plugin URI: http://wordpress.org/extend/plugins/#
  Description: Read data from posts display them as events.
  Author: Christoffer N. Aa. Thondrup
  Version: 1.0
  Author URI: http://thondrup.com/wp/events
*/

class Events_Widget extends WP_Widget {
  /**
   * Sets up the widgets name etc
   *
   * @link https://developer.wordpress.org/reference/classes/wp_widget/__construct/
   * @see https://developer.wordpress.org/reference/functions/wp_register_sidebar_widget/
   *
   */
   public function __construct() { 
     $widget_ops = array( 
       'classname' => 'events',
       'description' => 'Shows a calendar with events extracted from posts',
     );
     parent::__construct( 'events', 'Events', $widget_ops );
   }

  /**
   * Outputs the content of the widget on front-end
   *
   * @param array $args Widget arguments
   * @param array $instance
   *
   * @link https://developer.wordpress.org/reference/classes/wp_widget/widget/
   */
  public function widget( $args, $instance ) {
    $args = array(
      'posts_per_page'   => 5,
      'offset'           => 0,
      'cat'              => $instance['cat'],
      'orderby'          => 'meta_value',
      'meta_key'         => 'start_time',
      'order'            => 'ASC',
      'post_type'        => 'post',
      'post_status'      => 'publish',
      'meta_query' => array(
        array(
          'key' => 'start_time',
          'value' => date("Y-m-d H:i"),
          'compare' => '>=',
          'type' => 'DATE'
        )
      )
    );

    $query = new WP_Query( $args );

    ?>
    <?php if( $query->have_posts() ): ?>
      <section style="font-family: 'Archivo Narrow', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: bold; font-size:24px; padding: 1.2em; background: #eeece8;">
        <h2 class="widget-title">Arrangementer</h2>
        <ul style="list-style: none; margin: 0;">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
          <?php $custom_fields = get_post_custom(get_the_id()); ?>
          <li style="margin-bottom: 30px;">
            <div style="width:55px; vertical-align: top; display: inline-block; margin-bottom: 10px;">
              <div style="font-size:30px; font-weight: bold; line-height: 35px;">
                <?php echo date('d',  strtotime($custom_fields['start_time'][0])); ?>
              </div>
              <div style="font-size:18px; font-weight: 500; line-height: 15px;">
                <?php echo date('M',  strtotime($custom_fields['start_time'][0])); ?>
              </div>
            </div>
            <div style="width:190px; display: inline-block;">
              <a style="font-size:20px;" href="<?php the_permalink(); ?>">
                <p style="line-height: 30px; margin-bottom: 10px;">
                  <?php the_title(); ?>
                </p>
              </a>
              <div style="font-size:18px; font-weight: normal;color: #192930; font-family: 'Cooper Hewitt', 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                <div>Tid: 
                  <?php echo date('H:i',  strtotime($custom_fields['start_time'][0])); ?>
                  -
                  <?php echo date('H:i',  strtotime($custom_fields['end_time'][0])); ?>
                </div>

                <div>Sted: <?php echo $custom_fields['place_name'][0]; ?></div>
                <div><?php echo $custom_fields['place_address'][0]; ?></div>
              </div>
            </div>
          </li>
        <?php endwhile; ?>
        </ul>
      </section>
    <?php endif; ?>

    <?php wp_reset_query(); ?>
    <?php
  }

  /**
   * Outputs the options form on admin
   *
   * @param array $instance The widget options
   *
   * @link https://developer.wordpress.org/reference/classes/wp_widget/form/
   */
  public function form( $instance ) {
    $title = !empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Title', 'text_domain' );
    $cat = !empty( $instance['cat'] ) ? $instance['cat'] : false;

    ?>
    <p>
    <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
    <?php esc_attr_e( 'Title:', 'text_domain' ); ?>
    </label> 
    
    <input 
      class="widefat" 
      id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
      name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
      type="text" 
      value="<?php echo esc_attr( $title ); ?>">
    </p>

    <label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>">
    <?php esc_attr_e( 'Category:', 'text_domain' ); ?>
    </label> 

    <?php $args = array(
      'id' => $this->get_field_id( 'cat' ),
      'name' => $this->get_field_name( 'cat' ),
      'selected' => $cat
    ); ?>
    <?php wp_dropdown_categories( $args ); ?> 

    <?php
  }

  /**
   * Processing widget options on save
   *
   * @param array $new_instance The new options
   * @param array $old_instance The previous options
   *
   * @link https://developer.wordpress.org/reference/classes/wp_widget/update/
   */
  public function update( $new_instance, $old_instance ) {
    return $new_instance;
  }
}

// Register Events_Widet
add_action( 'widgets_init', function(){
  register_widget( 'Events_Widget' );
});
