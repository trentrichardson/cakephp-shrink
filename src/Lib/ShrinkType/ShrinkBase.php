<?php
namespace Shrink\Lib\ShrinkType;


/**
* base class with some helper functions
*/
class ShrinkBase{
    protected $_error;

    /**
    * Run a command and return the output
    * @param string $cmd command to run
    * @param string $input input to pass to the command STDIN
    * @param array $env - key value pairs of environment variables to be set
    * @return string - output from the command
    */
    public function cmd($cmd, $input=null, $env=array()){
        $out = '';
        $descriptorSpec = array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w'));

        $process = proc_open($cmd, $descriptorSpec, $pipes, null, $env);

        if (is_resource($process)) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);

            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $this->run_error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            proc_close($process);
        }
        return $out;
    }
}


