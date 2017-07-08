<?php

// create thumb with fixed width
function createThumbnail($pathToImage, $thumbWidth = 180, $thumbPath = '') {
    $result = 'Failed';
    if (is_file($pathToImage)) {
        $info = pathinfo($pathToImage);

        $extension = strtolower($info['extension']);
        if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {

            switch ($extension) {
                case 'jpg':
                    $img = imagecreatefromjpeg("{$pathToImage}");
                    break;
                case 'jpeg':
                    $img = imagecreatefromjpeg("{$pathToImage}");
                    break;
                case 'png':
                    $img = imagecreatefrompng("{$pathToImage}");
                    break;
                case 'gif':
                    $img = imagecreatefromgif("{$pathToImage}");
                    break;
                default:
                    $img = imagecreatefromjpeg("{$pathToImage}");
            }
            // load image and get image size

            $width = imagesx($img);
            $height = imagesy($img);

            // calculate thumbnail size
            $new_width = $thumbWidth;
            $new_height = floor($height * ( $thumbWidth / $width ));

            // create a new temporary image
            $tmp_img = imagecreatetruecolor($new_width, $new_height);

            // copy and resize old image into new image
            imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            if($thumbPath == "") $pathToImage = $pathToImage . '.thumb.jpg';
			else{
				if(!is_dir($thumbPath)) mkdir($thumbPath, 0755, true);
				$pathToImage = $thumbPath . $info['basename'] . '.thumb.jpg';
			}
            // save thumbnail into a file
            imagejpeg($tmp_img, "{$pathToImage}");
			//imagedestroy($img);
            $result = $pathToImage;
        } else {
            $result = 'Failed|Not an accepted image type (JPG, PNG, GIF).';
        }
    } else {
        $result = 'Failed|Image file does not exist.';
    }
    return $result;
}



// image rotation function with given degree
function imgRotateCustom($pathToImage, $rot_deg = 0, $overwrite = true) {
	
	if (is_file($pathToImage)) {
        $info = pathinfo($pathToImage);

        $extension = strtolower($info['extension']);
        if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {

            switch ($extension) {
                case 'jpg':
                    $type = "jpeg"; $img = imagecreatefromjpeg("{$pathToImage}");
                    break;
                case 'jpeg':
                    $type = "jpeg"; $img = imagecreatefromjpeg("{$pathToImage}");
                    break;
                case 'png':
                    $type = "png"; $img = imagecreatefrompng("{$pathToImage}");
                    break;
                case 'gif':
                    $type = "gif"; $img = imagecreatefromgif("{$pathToImage}");
                    break;
                default:
                    $type = "jpeg"; $img = imagecreatefromjpeg("{$pathToImage}");
            }
	
			$rotate = imagerotate($img, $rot_deg, 0);
			imagedestroy($img);
			
			$output_filename = ($overwrite)? $pathToImage : $pathToImage.'.rotate.'.$extension;
			if($overwrite) unlink($pathToImage);
			
			switch($type){
				case 'jpeg':
					imagejpeg($rotate, $output_filename, 100);
					break;
				case 'png':
					imagepng($rotate, $output_filename, 100);
					break;
				case 'gif':
					imagegif ($rotate, $output_filename);
					break;
			}
			
			imagedestroy($rotate);
			
			return array("status" => true, "errorcode" => "");
		}else{
			return array("status" => false, "errorcode" => "Not supported image format");
		}
	}else{
		return array("status" => false, "errorcode" => "Image file does not exist");
	}
}

// Make thumbnail with fixed width, height ( No loss original image, padding white color)
function generate_image_thumbnail($source_image_path, $thumbnail_image_path, $thumb_width = 200, $thumb_height = 150)
{
	list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
	switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = $thumb_width / $thumb_height;
    if ($source_image_width <= $thumb_width && $source_image_height <= $thumb_height) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
    } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) ($thumb_height * $source_aspect_ratio);
        $thumbnail_image_height = $thumb_height;
    } else {
        $thumbnail_image_width = $thumb_width;
        $thumbnail_image_height = (int) ($thumb_width / $source_aspect_ratio);
    }
    $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

    $img_disp = imagecreatetruecolor($thumb_width,$thumb_height);
    $backcolor = imagecolorallocate($img_disp, 255, 255, 255);
    imagefill($img_disp,0,0,$backcolor);

        imagecopy($img_disp, $thumbnail_gd_image, (imagesx($img_disp)/2)-(imagesx($thumbnail_gd_image)/2), (imagesy($img_disp)/2)-(imagesy($thumbnail_gd_image)/2), 0, 0, imagesx($thumbnail_gd_image), imagesy($thumbnail_gd_image));

    imagejpeg($img_disp, $thumbnail_image_path, 50);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    imagedestroy($img_disp);
    return true;
}


// Make thumbnail with given width and height no white color space padding
function generate_image_thumbnail2($source_image_path, $thumbnail_image_path, $thumb_width = 200, $thumb_height = 150)
{
	list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
	switch ($source_image_type) {
        case IMAGETYPE_GIF:
            $source_gd_image = imagecreatefromgif($source_image_path);
            break;
        case IMAGETYPE_JPEG:
            $source_gd_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_gd_image = imagecreatefrompng($source_image_path);
            break;
    }
    if ($source_gd_image === false) {
        return false;
    }
    $source_aspect_ratio = $source_image_width / $source_image_height;
    $thumbnail_aspect_ratio = $thumb_width / $thumb_height;
    
	if ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $source_copy_width = $source_image_width;
        $source_copy_height = (int) ($source_image_width / $thumbnail_aspect_ratio);
    } else {
        $source_copy_width = (int) ($source_copy_height * $thumbnail_aspect_ratio);
        $source_copy_height = $source_image_height;
    }
	$source_off_x = ($source_image_width - $source_copy_width) / 2;
	$source_off_y = ($source_image_height - $source_copy_height) / 2;
	
    $thumbnail_gd_image = imagecreatetruecolor($thumb_width, $thumb_height);
    imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, $source_off_x, $source_off_y, $thumb_width, $thumb_height, $source_copy_width, $source_copy_height);

    $img_disp = imagecreatetruecolor($thumb_width, $thumb_height);

    imagecopy($img_disp, $thumbnail_gd_image, (imagesx($img_disp)/2)-(imagesx($thumbnail_gd_image)/2), (imagesy($img_disp)/2)-(imagesy($thumbnail_gd_image)/2), 0, 0, imagesx($thumbnail_gd_image), imagesy($thumbnail_gd_image));

    imagejpeg($img_disp, $thumbnail_image_path, 50);
    imagedestroy($source_gd_image);
    imagedestroy($thumbnail_gd_image);
    imagedestroy($img_disp);
    return true;
}