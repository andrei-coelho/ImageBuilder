<?php 

/**
 * @author Andrei Coelho
 * @version 0.1
 */

require "ImageBuilderException.php";

class ImageBuilder {
    
    private $from;
    private $source;
    private $copy;
    private $name;
    private $fullName;
    private $ext;
    private $salt;

    private $png = false;
    private $to = false;
    private $generate = false;

    private $imageCreate;

    public function __construct(string $from){

        if(!extension_loaded('gd')){
            throw new ImageBuilderException($from, 0);
        }

        if($this->is_url($from)){
            $this -> from = $from;
            $this -> createCopy(true);
            return;
        }
        $this ->from = $from;
        if(!($this ->source = getimagesize($this -> from))){
            throw new ImageBuilderException($from, 1);
        }
        $this -> createCopy();
    }

    public function setPath(string $path){
        $this->to = $path;
    }

    public function setSalt(string $salt){
        $this->salt = $salt;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function getFullName(){
        return $this->fullName;
    }

    public function destroy(){
        return imagedestroy($this->copy);
    }

    private function createCopy(bool $url = false){

        if(!$url){
            
            switch($this ->source['mime']){
                case "image/jpeg": $this -> copy = imagecreatefromjpeg($this->from); $this->ext = ".jpg"; break;
                case "image/png": 
                    $this -> copy = imagecreatefrompng($this->from);
                    $this->ext = ".png";
                    $this->png = true;
                    break;
                default: $this -> copy = false;
            }
            if(!$this->copy){
                throw new ImageBuilderException($this->from, 4);
            }
            return;

        }

        if(($this -> copy = imagecreatefromwebp($this->from)) === false){
            throw new ImageBuilderException($from, 1);
        }
    }

    private function is_url(string $from){
        return preg_match('/(https?:\/\/(?:www\.|(?!www))
        [a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|
        https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|
        www\.[a-zA-Z0-9]+\.[^\s]{2,})/', $from);
    }

    public function resize(){

        if(($count = func_num_args()) == 0)
            throw new ImageBuilderException($count, 2);

        $args = func_get_args();

        if($count > 1){
            foreach ($args as $size) {
                if(is_string($size) && preg_match('/((^\d{2,}x\d{2,}$)|(^\*x\d{2,}$)|(^\d{2,}x\*$))/i', $size)){
                    $vars = $this->generateWidthHeight(explode("x", strtolower($size)));
                    if($this->generateImageResized($vars[1], $vars[2])){
                        $this->save($this->imageCreate, $this->generateName($vars[0]));
                        continue;
                    }
                }
                throw new ImageBuilderException($size, 3);
            }
            return;
        }

        $size = $args[0];
        if(is_string($size) && preg_match('/((^\d{2,}x\d{2,}$)|(^\*x\d{2,}$)|(^\d{2,}x\*$))/i', $size)){
            $vars = $this->generateWidthHeight(explode("x", strtolower($size)));
            if($this->generateImageResized($vars[1], $vars[2])){
                $this->save($this->imageCreate, $this->generateName());
            }
            return;
        }
        throw new ImageBuilderException($size, 3);
    }

    private function generateWidthHeight(array $sizes){
        if ($sizes[0] == "*"){
            $multipl = $sizes[1] / $this->source[1];
            $s = "_x".$sizes[1];
            $h = $sizes[1];
            $w =  (int)($this->source[0] * $multipl);
        } else 
        if ($sizes[1] == "*"){
            $multipl = $sizes[0] / $this->source[0];
            $s = $sizes[0]."x_";
            $w = $sizes[0];
            $h =  (int)($this->source[1] * $multipl);
        } else {
            $h = $sizes[1];
            $w = $sizes[0];
            $s = $sizes[0]."x".$sizes[1];
        }
        return [$s,$w,$h];
    }

    private function generateName(string $name = null){
        if($this->name == null){
            return sha1(date("dmY-His").rand(1,1000).($this->salt === null ? "__SALT__" : $this->salt));
        }
        if($name != null)
            return $this->salt === null ? $this->name . "-" . $name: sha1($this->salt.$this->name). "-" .$name;
        return $this->salt === null ? $this->name : sha1($this->salt.$this->name);
    }

    private function generateImageResized(int $width, int $height){
       
        $this->imageCreate = imagecreatetruecolor($width, $height);
        if($this->png){
            imagealphablending( $this->imageCreate, false);
            imagesavealpha( $this->imageCreate, true);
        }
        return imagecopyresized($this->imageCreate, $this->copy, 0, 0, 0, 0, $width, $height,$this->source[0], $this->source[1]);
    }

    private function save($resource, string $name){

        $path = $this->to !== false ? $this->to : $this->from;
        $file = $this->to !== false ?$path.DIRECTORY_SEPARATOR.$name.$this->ext : $path;
        
        switch($this ->source['mime']){
            case "image/jpeg": imagejpeg($this->imageCreate, $file, 100); break;
            case "image/png": imagepng($this->imageCreate, $file, 9); break;
            default: imagejpeg($this->imageCreate, $file, 100);
        }
    }

}
