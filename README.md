# Image Resize and Crop

## Example

```php
if($normalize = !$cropMode) {
    $cropMode = Image::RESIZE_MODE_NORMALIZE;
}

$image = new ImageFromFile($imagePath);

if(!$w) {
    $image->resizeByHeight($h, $normalize);
} else if(!$h) {
    $image->resizeByWidth($w, $normalize);
} else {
    $image->resize($w, $h, $cropMode);
}

$image->output();
```