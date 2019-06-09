<?php 

class ImageBuilderException extends Exception {

    private $msg;

    public function __construct(string $msg, int $code){
        $this -> generateMessage($msg, $code);
        parent::__construct($this->msg, $code);
    }

    private function generateMessage(string $msg, int $code){
        switch ($code) {
            case 0:
                $this->msg = "The GD library is not installed or loaded";
                break;

            case 1:
                $this->msg = "This file: '$msg' is not a image or not exists";
                break;
            
            case 2:
                $this->msg = "Too few arguments to function ImageBuilder::resize(), 
                    $msg passed. Minimum arguments required are 2.";
                break;

            case 3:
                $this->msg = "The passed value '$msg' is not standard for sizes.";
                break;

            case 4:
                $this->msg = "This file: '$msg' is not a customizable image. Use images with PNG or JPG extensions.";
                break;

            default:
                $this->msg = "This file: '$msg' is not a image or not exists";
                break;
        }
    }

}