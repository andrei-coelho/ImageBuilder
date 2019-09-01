<?php 

/**
 * @author Andrei Coelho
 * @version 0.1
 */

namespace ImageBuilder;

use ImageBuilder\ImageBuilderException as ImageBuilderException;
use ImageBuilder\Image as Image;

class Build {

	public static function image(Image $image, Image $copy, $alias)
	{

		$isPng  = false;
		$isCopy = count($image -> actions) === 0;

		switch ($copy -> mime) {
			case 'jpg':
				$source = imagecreatefromjpeg($copy -> path . $copy -> name);
				break;
			case 'png':
				$isPng = true;
				$source = imagecreatefrompng($copy -> path . $copy -> name);
				break;
			default:
				return;
		}
		
		$image -> resource = 
			$isCopy ?
			$source :
			@imagecreatetruecolor($image -> sizes[0], $image -> sizes[1]);

		if($isPng) {
			imagealphablending($image -> resource, false);
            imagesavealpha($image -> resource, true);
		}

   		# actions... here we go!
   		foreach ($image -> actions as $action)
		   $image -> resource = self::$action($source, $image -> sizes, $copy -> sizes, $isPng);

   		# filters... it's your turn!
   		foreach ($image -> filters as $filter => $values)
			self::$filter();
		
		# save Image now!
		$suffixed = $image -> modify ? "" : $alias."_"; 
   		$create = $image -> mime."_create";
   		self::$create($image, $suffixed);
   	}

	
	/*                       *
	*------------------------*
	*      CREATE IMAGE      *
	*------------------------*
	*                        */

	private static function jpg_create(Image $image, $suffixed)
	{
		imagejpeg($image -> resource, $image -> path . $suffixed . $image -> name, 100);
		\imagedestroy($image -> resource);
 	}

	private static function png_create(Image $image, $suffixed)
	{
		imagepng($image -> resource, $image -> path . $suffixed . $image -> name, 0);
		\imagedestroy($image -> resource);
	}

   
	/*                       *
	*------------------------*
	*         ACTIONS        *
	*------------------------*
	*                        */

	private static function resize($source, array $newsizes, array $copysizes, $isPng)
	{
		$res = @imagecreatetruecolor($newsizes[0], $newsizes[1]);
		if($isPng) {
			imagealphablending($res, false);
            imagesavealpha($res, true);
		}
		imagecopyresized(
			$res, 
			$source, 0, 0, 0, 0, 
			$newsizes[0], 
			$newsizes[1], 
			$copysizes[0], 
			$copysizes[1]
		);
		return $res;
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
