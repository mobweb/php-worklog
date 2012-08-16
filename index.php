<?php

/*
 *
 * Configuration
 *
 */
$date_format = 'd.m.Y';
$mail_recipient = "info@mobweb.ch";
$time_zone = 'UTC';
date_default_timezone_set( $time_zone );

/*
 *
 * Load the "database"
 *
 */
$db_array = unserialize( file_get_contents( 'db' ) );
$db_array = ( $db_array ) ? $db_array : array();

/*
 *
 * Get today's and yesterday's date
 *
 */
$today = date( $date_format );
$yesterday = date( $date_format, time()-24*60*60 );

$timestamp_today = strtotime( date( 'd-m-Y' ) );
$timestamp_yesterday = strtotime( date( 'd-m-Y', time()-24*60*60 ) );

/*
 *
 * Check if the current request triggers the sending of the E-Mail
 *
 */
if( isset( $_GET[ 'send' ] ) ) {
    // Yes -> Check if there's a log entry for the yesterday
    if( in_array( $timestamp_yesterday, array_keys( $db_array ) ) ) {
        // Yes -> E-Mail yesterday's log entry
        $mail_subject = 'Your acomplishments for ' . $yesterday;
        $mail_body = $db_array[ $timestamp_yesterday ];
    } else {
        // No -> Send a notification, anyway, informing the user that nothing
        // was logged yesterday
        $mail_subject = 'No acomplishments added for ' . $yesterday . ' :(';
        $mail_body = "It seems that you haven't added any completed tasks yesterday. Did you enjoy a nice day off or did you forget to add your acomplishments? Anyway you can still add them: http://spielwiese.mobweb.ch/done/";
    }

    // Actually send the E-Mail (Note: For more sophisticated options,
    // consider implementing PHPMailer or another package with advanced
    // E-Mail sending options)
    mail( $mail_recipient, $mail_subject, $mail_body, "From: MobWeb.ch Mailer <123.456.789.000>" );
    exit();
}

/*
 *
 * Check if the current requests contains any POST data from a log entry
 *
 */
if( isset( $_POST ) && !empty( $_POST ) ) {
    // Check if there is an entry for today
    if( !empty( $_POST[ 'today' ] ) ) {
        // Save it in the database array
        $db_array[ $timestamp_today ] = $_POST[ 'today' ];
    }

    // Check if there is an entry for yesterday
    if( !empty( $_POST[ 'yesterday' ] ) ) {
        // Save it in the database array
        $db_array[ $timestamp_yesterday ] = $_POST[ 'yesterday' ];
    }

    // Save the database array to the database file
    file_put_contents( 'db', serialize( $db_array ) );

    // Reload the page to show the newly addded log entries
    header( 'Location: index.php' );
    exit();
}
?>
<DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href='http://netdna.bootstrapcdn.com/twitter-bootstrap/2.0.4/css/bootstrap-combined.min.css' />
    <style>
        .separator td {
            height: 2em;
        }

        h1 {
            margin: .5em 0;
        }

        .separated {
            margin-top: 1em;
        }
    </style>
</head>
<body>
<div class="container">
<?php
/*
 *
 * Check which (if any) form fields to display
 *
 */
// Check if a form for today's log entry needs to be displayed
$display_form_today = !in_array( $timestamp_today, array_keys( $db_array ) );

// Check if a form for yesterday's log entry needs to be displayed
$display_form_yesterday = !in_array( $timestamp_yesterday, array_keys( $db_array ) );

// Check if any form at all needs to be displayed
$display_form = ( $display_form_today || $display_form_yesterday );

if( $display_form ) { ?>
    <form action="index.php" method="POST" class="well">
    <?php if( $display_form_today ) { ?>
        <h1>What have you done today?</h1>
        <div class="control-group">
            <div class="controls">
                <textarea class="input-xlarge" name="today" id="today"></textarea>
            </div>
        </div>
    <?php }
    if( $display_form_yesterday ) { ?>
        <h1>What have you done yesterday?</h1>
        <div class="control-group">
            <div class="controls">
                <textarea class="input-xlarge" name="yesterday" id="yesterday"></textarea>
            </div>
        </div>
    <?php } ?>
    <div class="control-group">
        <div class="controls">
            <input type="submit" value="Submit" />
        </div>
    </div>
    </form>
<?php } ?>

<h1 class="separated">What have you done previously?</h1>
<table class='table table-striped'>
<?php
/*
 *
 * This part displays the previous log entries
 *
 */

// Since the keys of the array contain the entrie's timestamp as
// unix timestamps, we can simply order the entries by key to
// get the correct order
ksort( $db_array );

// Helper var to be used later
$previous_week_number = 0;

// Starting at the beginning (oldest entries first), loop through all the
// entries
foreach( array_reverse( $db_array, true ) AS $date_timestamp => $content ) {

    // Convert the timestamp into a human readable format
    $date = date( $date_format, $date_timestamp );

    // In order to display a space between each week's entries, we have to
    // figure out each entrie's week number to detect a new week.
    $week_number = date( 'W', $date_timestamp );

    // Compare the current week number to the week number of the previous
    // log entry to detect a new week
    if( $previous_week_number > $week_number ) {
        // New week, insert an empty row
        echo "<tr class='separator'><td></td><td></td></tr>";
    }

    // Update the variable that holds the last log entry's week number
    $previous_week_number = $week_number;

    // Get the weekday of this log entry
    $weekday = date( 'l', $date_timestamp );

    // Finally, display the current log entry
    echo "<tr><td>" . $weekday . ", " . $date . "</td><td>" . $content . "</td></tr>";
}
?>
</table>
</div>
</body>
</html>