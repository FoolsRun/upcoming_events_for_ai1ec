<?php
/*
Plugin Name: Upcoming Events widget for Time.ly
Description: Display upcoming events from time.ly All-In-One Event Calendar plugin as a widget
Author: FoolsRun
Version: 1
*/
 
 
class UpcomingEventsWidget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'Upcoming_Events_Widget',
            'Upcoming Events for Time.ly',
            array( 'description' => 'Upcoming events from time.ly All-In-One Event Calendar plugin' )
        );
    }
  
	/* Widget Display */
	public function widget( $args, $instance )
	{
	 
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
	 
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		//Global necessary ai1ec classes
		global $ai1ec_calendar_helper, $ai1ec_events_helper;
		
		// get current local time
		$bits = $ai1ec_events_helper->gmgetdate( $ai1ec_events_helper->gmt_to_local( time() ) );
		$start_time       = gmmktime( 0, 0, 0, $bits['mon'], $bits['mday'], $bits['year'] );
		 
		// Five years from today
		$end_time         = gmmktime( 0, 0, 0, 0, 0, $bits['year'] + 5 );
		
		//Limit the number of events shown
		$limit_events     = $instance['eventstodisplay'];
		
		//Filter events to show only one category
		$filter = array('cat_ids' => array($instance['eventcategory']));
		
		//The finalized events query
		$events = $ai1ec_calendar_helper->get_events_between( $start_time, $end_time, $filter, false );
		foreach ($events as $event) {
			$post = get_post($event->post->ID);
			$excerpt = get_the_excerpt(); // or print it directly with the_excerpt()
		}
		if($events) {
		 global $post;
			$save = $post;
			$i = 0;
				foreach ($events as $event) {
					if($i < $limit_events) {
						$post = get_post($event->post->ID); 
				?>
								<?php echo '<h4><a href="'.get_permalink().'">'.get_the_title().'</a></h4>'; ?> 
								<?php the_excerpt(); ?>
								<div class="read_more"><a href="<?php the_permalink(); ?>">Read More</a></div>
				<?php
				$i++;
					}
				}
		}	
		$post = $save;
		echo $after_widget;
	}
		
		
	/* Widget Update Functions */
	public function update( $new_instance, $old_instance )
	{
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['eventcategory'] = strip_tags( $new_instance['eventcategory'] );
		$instance['eventstodisplay'] = strip_tags( $new_instance['eventstodisplay'] );

		return $instance;
	}

	
	/* Widget Configuration Form */
	public function form( $instance )
	{
		$title = isset( $instance[ 'title' ] )  ? $instance[ 'title' ] : 'Upcoming Events';
		$eventcategory = isset( $instance[ 'eventcategory' ] )  ? $instance[ 'eventcategory' ] : '0';
		$eventstodisplay = isset( $instance[ 'eventstodisplay' ] )  ? $instance[ 'eventstodisplay' ] : '5';
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventcategory' ); ?>"><?php _e( 'Display events from event category (required):' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'eventcategory' ); ?>" name="<?php echo $this->get_field_name( 'eventcategory' ); ?>" type="text" value="<?php echo esc_attr( $eventcategory ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'eventstodisplay' ); ?>"><?php _e( 'Number of events to display:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'eventstodisplay' ); ?>" name="<?php echo $this->get_field_name( 'eventstodisplay' ); ?>" type="text" value="<?php echo esc_attr( $eventstodisplay ); ?>" />
		</p>
		<?php
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget( "UpcomingEventsWidget" );' ) );
?>
