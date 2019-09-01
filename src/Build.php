<?php 

/**
 * @author Andrei Coelho
 * @version 0.1
 */

namespace ImageBuilder;

use ImageBuilder\ImageBuilderException as ImageBuilderException;
use ImageBuilder\Image as Image;

class Build {

   	public static function image(Image $image, string $from, string $mime)
   	{
   		
   		$imageInfo = $image -> getInfo();

   		switch ($mime) {
   			case 'jpg':
   				$source = imagecreatefromjpeg($from);
   				break;
   			case 'png':
   				$source = imagecreatefrompng($from);
   				break;
   			default:
   				return;
   		}

   		# actions... here we go!

   		foreach ($imageInfo['actions'] as $action => $values) {
   			
   		}

   		$create = $imageInfo['mime']."_create";
   		self::$create($image);
   	}


   	/*                       *
	*------------------------*
	*      CREATE IMAGE      *
	*------------------------*
	*                        */

   	private static function jpg_create(Image $image, string $suffixed){
   		echo "gerou a imagem jpg";
   	}

   	private static function png_create(Image $image, string $suffixed){
   		echo "gerou a imagem png";
   	}

   
   	/*                       *
	*------------------------*
	*         ACTIONS        *
	*------------------------*
	*                        */

	private static function resize($source, array $sizes)
	{
		return imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	}

    private function createCopy(bool $url = false){

        if(!$url){
            
            switch($this ->source['mime']){
                case "image/jpeg": $this -> copy =  $this->ext = ".jpg"; break;
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


    public function resizea(){

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
        if(is_string($size) && true){
            $vars = $this->generateWidthHeight(explode("x", strtolower($size)));
            if($this->generateImageResized($vars[1], $vars[2])){
                $this->save($this->imageCreate, $this->generateName());
            }
            return;
        }
        throw new ImageBuilderException($size, 3);
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
