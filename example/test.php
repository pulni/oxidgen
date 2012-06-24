<?php

/**
 * PHP Command Line library
 *
 * @author Philip Sturgeon
 * @created 7 Oct 2008
 *
 */

class CLI {

    var $wait_msg = 'Press any key to continue...';


    function CLI (){
        // get CI!
    }

    // Output a line (or lines) to the command line
    function write($output = '') {

        // If there are multiple lines, seperate them by newlines
      if(is_array($output)) {
        $output = implode("\n", $output);
      }

        // Output the lot
      fwrite(STDOUT, $output."\n");
    }

    // Read in a variable from the command line
    function read() {

        // Work out whats what based on what params are given
        $args = func_get_args();

        // Ask question with options
        if(count($args) == 2) {
            list($output, $options)=$args;

        // No question (probably been asked already) so just show options
        } elseif(count($args) == 1 && is_array($args[0])) {
            $output = '';
            $options = $args[0];

        // Question without options
        } elseif(count($args) == 1 && is_string($args[0])) {
            $output = $args[0];
            $options = array();

        // Nothing or too many, forget trying to be clever and just get what they asked for
        } else {
            $output = '';
            $options = array();
        }

        // If a question has been asked with the read
        if(!empty($output)) {

            $options_output = '';
            if(!empty($options)) {
              $options_output = ' [ '.implode(', ', $options).' ]';
            }

            fwrite(STDOUT, $output.$options_output.': ');
        }

        // Read the input from keyboard.
        $input = trim(fgets(STDIN));

        // If options are provided and the choice is not in the array, tell them to try again
        if(!empty($options) && !in_array($input, $options)) {
          $this->write('This is not a valid option. Please try again.');

            $input = $this->read($output, $options);
        }

        // Read the input
        return $input;
    }

    function new_line($lines = 1) {
        // Do it once or more, write with empty string gives us a new line
        for($i = 0; $i < $lines; $i++) $this->write();
    }

    function wait($seconds = 0, $countdown = FALSE) {

        // Diplay the countdown
        if($countdown == TRUE) {
          $i = $seconds;
            while ( $i > 0 ) {
               fwrite(STDOUT, $i.'... ');
               sleep(1);
               $i--;
            }

        // No countdown timer please
        } else {

            // Set number of seconds?
            if($seconds > 0) {
                sleep($seconds);

            // No seconds mentioned, lets wait for user input
            } else {
              $this->write($this->wait_msg);
                $this->read();
            }
        }

        return TRUE;
    }

}

$cli = new CLI();

$cli->write("----------------");
$cli->write("Phil's CLI Demo!");
$cli->write("----------------");

// Output a new empty line
$cli->new_line();

// Output multiple lines using an array
$description = array(
    'This library is designed to make working with the command line just a little bit easier.',
    '',
    'You can read and write to the command line without using confusing functions and constants!'
);

// Output the array
$cli->write($description);

// Multiple empty lines
$cli->new_line(2);

// Questions / Input ---------------------------------------------

// Ask a question that can have any input
$name = $cli->read('What is your name?');

$cli->new_line(2);

$cli->write("You are called '".$name."'.");
// ---------------------------------------

$cli->new_line(2);

// Ask a question with a list of possible inputs
$cheese_fan = $cli->read('Do you like cheese?', array('yes', 'no'));

$cli->new_line();

if($cheese_fan == 'yes') {
    $cli->write($name." is a fan of cheese! You rock!");

} else {
    $cli->write($name." is clearly rubbish, doesnt like cheese at all...");
}


// Ask the system to wait x seconds
$cli->wait(2);

// Ask the system to wait x seconds
$cli->wait();

$cli->new_line();

$cli->write("Exit in...");

// Ask the system to wait x seconds AND display a countdown timer
$cli->wait(5, TRUE);

// Exit correctly
exit(0);
?>