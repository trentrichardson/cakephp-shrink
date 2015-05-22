<?php
namespace Shrink\Lib\ShrinkCompressor;

/**
* Interface for each Shrink Compressor abstraction
*/
interface ShrinkCompressorInterface{

	public function isAvailable();
	
    public function compress($code);

}
