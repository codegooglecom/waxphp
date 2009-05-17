<?
    function imagecreatefrombmp($filename) {
         //Ouverture du fichier en mode binaire 
           if (! $f1 = fopen($filename,"rb")) return FALSE;

         //1 : Chargement des ent?tes FICHIER
           $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
           if ($FILE['file_type'] != 19778) return FALSE;

         //2 : Chargement des ent?tes BMP
           $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
                         '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
                         '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
           $BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
           if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
           $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
           $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
           $BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
           $BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
           $BMP['decal'] = 4-(4*$BMP['decal']);
           if ($BMP['decal'] == 4) $BMP['decal'] = 0;

         //3 : Chargement des couleurs de la palette
           $PALETTE = array();
           if ($BMP['colors'] < 16777216)
           {
            $PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
           }

         //4 : Cr?ation de l'image
           $IMG = fread($f1,$BMP['size_bitmap']);
           $VIDE = chr(0);

           $res = imagecreatetruecolor($BMP['width'],$BMP['height']);
           $P = 0;
           $Y = $BMP['height']-1;
           while ($Y >= 0)
           {
            $X=0;
            while ($X < $BMP['width'])
            {
             if ($BMP['bits_per_pixel'] == 24)
                $COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
             elseif ($BMP['bits_per_pixel'] == 16)
             { 
                $COLOR = unpack("n",substr($IMG,$P,2));
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
             }
             elseif ($BMP['bits_per_pixel'] == 8)
             { 
                $COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
             }
             elseif ($BMP['bits_per_pixel'] == 4)
             {
                $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
             }
             elseif ($BMP['bits_per_pixel'] == 1)
             {
                $COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
                if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
                elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1]+1];
             }
             else
                return FALSE;
             imagesetpixel($res,$X,$Y,$COLOR[1]);
             $X++;
             $P += $BMP['bytes_per_pixel'];
            }
            $Y--;
            $P+=$BMP['decal'];
           }

         //Fermeture du fichier
           fclose($f1);

         return $res;
    }

    /***********************************************************
     * CGL/Thumbnail : Thumbnails an image
     * controls inherit. 
     * ---------------------------------------------------------
     * (c) 2007 Joe Chrzanowski
     ***********************************************************/
     

	define("NO_ACTION"      ,0);
	define("IMAGE_THUMBNAIL",1);
	
	define("RESOURCE"       ,0);	    // Return an image resource 
	define("IMAGE"          ,1);		// Output the raw PNG image - make sure to set the content-type: image/png if you want to use this mode.
	define("HTML"           ,2);		// output an html image tag
	
	// define constraints
	define("WM_CONSTRAINT"          ,420); // if part of an image is smaller than this, then skip watermark
	define("IMAGE_CONSTRAINT"       ,560); // resize images to a max of this size unless specified otherwise
	define("THUMBNAIL_CONSTRAINT"   ,100); // when rendering a small thumbnail
	
	// define sizes
	define("CUSTOM"         ,0);
	
	// define watermark locations
	define("WM_TOP_LEFT"    ,0);
	define("WM_TOP"         ,1);
	define("WM_TOP_RIGHT"   ,2);
	define("WM_RIGHT"       ,3);
	define("WM_BOTTOM_RIGHT",4);
	define("WM_BOTTOM"      ,5);
	define("WM_BOTTOM_LEFT" ,6);
	define("WM_LEFT"        ,7);
	define("WM_CENTER"      ,8);
	
	class Thumbnail extends CGLObject {
        var $name = "Thumbnail";
		var $fs_cache;    // the location to use for caching images
		var $webcache;       // how to access that location from a browser
		var $cachelocation = "";
		var $cachefilename = "";
		var $enablecache = true;
		var $quality = 75;
		
		var $imagepath = "";
		var $resizex = 0;
		var $resizey = 0;
		
		var $action = NO_ACTION;
		var $enablewatermark = false;
		var $watermarksource = '';
		var $watermarkanchor = WM_CENTER;
		var $outputformat = HTML;
		
		function __construct($imagepath, $x = null, $y = null, $outputformat = HTML, $watermark = false, $watermarkanchor = WM_CENTER) {
            global $_CGL;
            $this->fs_cache = $_CGL['installpath'] . "/__cache";
            $this->webcache = $_CGL['cgl_root'] . '/__cache';
            
            // if the file doesn't exist then don't do anything
            if (file_exists($imagepath))
				$this->action = IMAGE_THUMBNAIL;
			else
				$this->action = NO_ACTION;
				
			$this->imagepath = $imagepath;
			if ($x == null && $y == null) {
				list($this->resizex,$this->resizey) = $this->get_sizes($imagepath);
			}
			else {
				$this->resizex = $x;
				$this->resizey = $y;
			}
			
			if ($watermark) {
                $this->enablewatermark = true;
                $this->watermarksource = $watermark;
                $this->watermarkanchor = $watermarkanchor;
            }
			$this->outputformat = $outputformat;
		}
		
		function SetQuality($value) {
            if (is_numeric($value)) $this->quality = ($value > 0 && $value <= 100 ? $value : 75);
		}
		
		function Render($returnbuffer = false) {
			switch ($this->outputformat) {
				case RESOURCE:
					return $this->generate();		// Return the image resource returned by create_thumb
				break;
				
				case IMAGE:
					imagepng($this->generate());	// Output the image generated by create_thumb
				break;
				
				case JPEG:
                    imagejpeg($this->generate(),$this->quality);
				break;
				
				case HTML:
					$this->generate();
					// Output an HTML tag to the cached image
					$buf = "";
					
					if (is_file($this->cachelocation))
						$buf = "<img class='thumbnailimage' " . $this->RenderStyles() . " " . $this->RenderAttributes() . " src='" . $this->webcache . "/" . $this->cachefilename . "' alt='" . $this->imagepath . "' />";
					else 
						$buf = "<!-- image not found: " . $this->imagepath . " -->";
						
                    if ($returnbuffer) return $buf;
                    else echo $buf;
				break;
			}
		}
		
		function get_sizes($imagestr) {
			// Note: by keeping x or y null, the image is automatically resized
			// to proportion
			
			list($imx, $imy) = getimagesize($imagestr);
            $outsize = ($this->resizex > $this->resizey ? $this->resizex : $this->resizey);
			
			// make sure we're not stretching the image
			if ($imx > $outsize || $imy > $outsize) {
				if ($imx > $imy) {
					$x = $outsize;
					$y = null;
				}
				else {
					$x = null;
					$y = $outsize;
				}
			}
			else {
				$x = $imx;
				$y = $imy;
			}
			
			return array($x, $y);
		}
		
		private function generate() {
			if ($this->action == IMAGE_THUMBNAIL)
				return $this->create_thumb($this->imagepath, $this->resizex, $this->resizey);
			else
				return null;
		}
		
		private function get_ext($filename) {
			$parts = explode(".",$filename);
			return $parts[count($parts)-1];
		}
		
		function AddWatermark($baseimage, $watermarksrc = WATERMARK_SOURCE) {
			$watermark = imagecreatefrompng($watermarksrc);
			
			$basex = imagesx($baseimage);
			$basey = imagesy($baseimage);
			
			// make sure the image is large enough to warrant a watermark
			if ($basex < WM_CONSTRAINT || $basey < WM_CONSTRAINT) {
				$this->enablewatermark = false;
				return $baseimage;
			}
			else {
				$wmx = imagesx($watermark);
				$wmy = imagesy($watermark);
				
				// calculate positioning				
				switch ($this->watermarkanchor) {
                    case WM_TOP_LEFT:
                        $x = 0;
                        $y = 0;
                    break;
                    case WM_TOP:
                        $x = ($basex/2) - ($wmx/2);
                        $y = 0;
                    break;
                    case WM_TOP_RIGHT:
                        $x = $basex - $wmx;
                        $y = 0;
                    break;
                    case WM_RIGHT:
                        $x = $basex - $wmx;
                        $y = ($basey/2) - ($wmy/2);
                    break;
                    case WM_BOTTOM_RIGHT:
                        $x = $basex - $wmx;
                        $y = $basey - $wmy;
                    break;
                    case WM_BOTTOM:
                        $x = ($basex/2) - ($wmx/2);
                        $y = $basey - $wmy;
                    break;
                    case WM_BOTTOM_LEFT:
                        $x = 0;
                        $y = $basey - $wmy;
                    break;
                    case WM_LEFT:
                        $x = 0;
                        $y = ($basey/2) - ($wmy/2);
                    break;
                    default:
                        $x = ($basex/2) - ($wmx/2);
                        $y = ($basey/2) - ($wmy/2);
				}
				
				imagecopyresampled($baseimage, $watermark, $x, $y, 0, 0, $wmx, $wmy, $wmx, $wmy);
				return $baseimage;
			}
		}
		
		function get_image_resource($src) {        
			$ext = $this->get_ext($src);
			switch (strtolower($ext)) {
                case "bmp":
                    $image = imagecreatefrombmp($src);
				break;
				
				case "jpg":
					$image = imagecreatefromjpeg($src);
				break;
				
				case "jpeg":
					$image = imagecreatefromjpeg($src);
				break;
				
				case "png":
					$image = imagecreatefrompng($src);
					imagealphablending($image, false);
					imagesavealpha($image, true);
				break;
				
				case "gif":
					$image = imagecreatefromgif($src);
				break;
				
				default:
					$image = imagecreatefrompng($src);
					imagealphablending($image, false);
					imagesavealpha($image, true);
			}
			return $image;
		}
		
		private function proportional($a,$b,$c,$d = null) {
			if ($c == null && $d == null) // if x AND y are null, then we can't do anything
				return array($a, $b);
			else if ($c == null) { // solve for c
				$c = ($a * $d)/$b;
			}
			else { // solving for d
				$d = ($c * $b) / $a;
			}
				
			return array(round($c),round($d));
		}
		
		private function isimage($file) {
			$validexts = array("jpg","jpeg","png","gif");
			if (in_array($this->get_ext(strtolower($file)),$validexts))
				return true;
			else
				return false;
		}
		
		public function get_images($folder) {
			if (!is_dir($folder)) {
				return;
			}
			else {
				$files = scandir($folder);
				$images = array();
				for ($x = 0; $x < count($files); $x++) {
					if ($this->isimage($files[$x])) {
						$images[] = $files[$x];
					}
				}
				return $images;
			}
		}
		
		private function create_thumb($src, $x, $y, $cache=true) {
			list($imx,$imy) = getimagesize($src);
			
			// If only 1 dimension is provided, calculate the last one
			if ($x == null) 
				list($x,$y) = $this->proportional($imx,$imy,null,$y);
			else if ($y == null)
				list($x,$y) = $this->proportional($imx,$imy,$x);
			
			// Create image resource and cache string
			$image_copy = imagecreatetruecolor($x,$y);
			$wmstr = ($this->enablewatermark ? "_wm" : "");
			$cachestr = (md5(file_get_contents($src)) . "${wmstr}_${x}x${y}.png");
			
			// Look for a precached file
			$this->cachelocation = $this->fs_cache . "/" . $cachestr;
			$this->cachefilename = $cachestr;
			if (is_file($this->fs_cache . "/" . $cachestr)) {
				$src = $this->fs_cache . "/" . $cachestr;
				$image_copy = $this->get_image_resource($src);
			}
			else if (is_file($src)) { // Create the actual thumbnail
				$image = $this->get_image_resource($src);
				
				// resize the image
				imagecopyresampled($image_copy,$image,0,0,0,0,$x,$y,$imx,$imy);
				
				if ($this->enablewatermark)
					$image_copy = $this->AddWatermark($image_copy);
				
				if ($this->enablecache) { // If caching is enabled, then save the image
					$this->cachelocation = $this->fs_cache . '/' . $cachestr;
					imagepng($image_copy,$this->fs_cache . '/' . $cachestr);
				}
			}
			else {
				$this->cachelocation="INVALID IMAGE";
				$image_copy = $this->get_image_resource("invalid image");
			}
			
			return $image_copy;
		}
	}
?>