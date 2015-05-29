<?php
namespace Shrink\Lib;


/**
* base class with some helper functions
*/
class ShrinkBase{
    protected $_error;

    
    /**
    * Constructor - merges settings with options
    * @param array $options - options to set for compilers and compressors
    * @return void
    */
    function __construct($options=[]){
        $this->settings = array_replace_recursive($this->settings, $options);
    }


    /**
    * Run a command and return the output
    * @param string $cmd command to run
    * @param string $input input to pass to the command STDIN
    * @param array $env - key value pairs of environment variables to be set
    * @return string - output from the command
    */
    public function cmd($cmd, $input=null, $env=[]){
        $out = '';
        $descriptorSpec = [ 0=>['pipe', 'r'], 1=>['pipe', 'w'], 2=>['pipe', 'w'] ];

        $process = proc_open($cmd, $descriptorSpec, $pipes, null, $env);

        if (is_resource($process)) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);

            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $this->_error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            proc_close($process);
        }
        return $out;
    }


    /**
    * Determine the path to an executable file
    * @param string $exc name of the executable to find
    * @return mixed - file path or false if not available
    */
    protected function getPath($exc){
        $ret = false;

        // build the command dependent on windows or linux
        $cmd = 'which '. $exc;
        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            $cmd = 'where '. $exc;
        }

        // determine if the exec is available
        $path = trim(shell_exec(escapeshellcmd($cmd)));
        if((is_link($path) || is_file($path)) && is_executable($path)){
            $ret = $path;
        }
        return $ret;
    }

}


