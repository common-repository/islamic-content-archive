<?php

//============== Cronjob Start ======================//
// add custom interval
function cron_add_hour( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everyhour'] = array(
	    'interval' => 3600,
	    'display' => __( 'Once Hour' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_hour' );

function cron_add_every_day( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everyday'] = array(
	    'interval' => 3600*24,
	    'display' => __( 'Once Ever day' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_every_day' );

function cron_add_six_hours( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everysixhours'] = array(
	    'interval' => 3600*6,
	    'display' => __( 'Once Six Hours' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_six_hours' );


function cron_add_twelve_hours( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everytwelvehours'] = array(
	    'interval' => 3600*12,
	    'display' => __( 'Once Twelve Hours' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_twelve_hours' );


function cron_add_two_days( $schedules ) {
	// Adds once every minute to the existing schedules.
    $schedules['everytwodays'] = array(
	    'interval' => (3600*24)*2,
	    'display' => __( 'Once Two Days' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'cron_add_two_days' );

// create a scheduled event (if it does not exist already)

function cronstarter_activation() {
	if( !wp_next_scheduled('ica_cronjob' ) ) {  
	   wp_schedule_event( time(), get_option(ICA_Input_SLUG.'cronjobtime'),'ica_cronjob' );  
	}
}
// and make sure it's called whenever WordPress loads
add_action('wp', 'cronstarter_activation');

// unschedule event upon plugin deactivation
function cronstarter_deactivate() {	
	// find out when the last event was scheduled
	$timestamp = wp_next_scheduled ('ica_cronjob');
	// unschedule previous event if any
	wp_unschedule_event ($timestamp,'ica_cronjob');
} 
register_deactivation_hook (__FILE__, 'cronstarter_deactivate');

// here's the function we'd like to call with our cron job
function my_repeat_function() {
	global $ica_cronjob;
	$ica_cronjob->InsetPost();
}

// hook that function onto our scheduled event:
add_action ('ica_cronjob', 'my_repeat_function'); 



//============== Cronjob End ======================//
?>