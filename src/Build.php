<?php 

/**
 * @author Andrei Coelho
 * @version 0.1
 */

namespace ImageBuilder;

use ImageBuilder\ImageBuilderException as ImageBuilderException;
use ImageBuilder\Image as Image;

class Build {

	public static function image(Image $image, array $source, $alias)
	{

		$isPng  = false;
		$isCopy = count($image -> actions) === 0;

   		# actions... here we go!
   		foreach ($image -> actions as $action)
		   $image -> resource = self::$action($image, $source);

   		# filters... it's your turn!
   		foreach ($image -> filters as $filter => $values)
			self::$filter();
		
		# save Image now!
		$suffixed = $image -> modify ? "" : "_".$alias; 
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
		\imagejpeg($image -> resource, $image -> path . $image -> name . $suffixed . "." . $image -> mime, 100);
		\imagedestroy($image -> resource);
	}

	private static function png_create(Image $image, $suffixed)
	{
		\imagepng($image -> resource, $image -> path . $image -> name . $suffixed . "." . $image -> mime, 0);
		\imagedestroy($image -> resource);
	}

   
	/*                       *
	*------------------------*
	*         ACTIONS        *
	*------------------------*
	*                        */

	private static function resize(Image $image, array $copysizes)
	{
		$res = imagecreatetruecolor($image -> sizes[0], $image -> sizes[1]);
		if(self::isPng($image)) {
			imagealphablending($res, false);
            imagesavealpha($res, true);
		}
		imagecopyresized(
			$res, 
			$image -> resource, 0, 0, 0, 0, 
			$image -> sizes[0], 
			$image -> sizes[1], 
			$copysizes[0], 
			$copysizes[1]
		);
		return $res;
	}

	private static function isPng(Image $image)
	{
		return $image -> mime === 'png';
	}

}
