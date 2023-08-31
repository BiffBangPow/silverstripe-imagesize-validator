<?php

namespace BiffBangPow\Validators;

use BiffBangPow\Exceptions\PixelSizeValidatorFailedException;
use SilverStripe\Assets\Upload_Validator;
use SilverStripe\Core\Config\Configurable;

class PixelSizeValidator extends Upload_Validator
{
    use Configurable;

    private static $max_width = 1920;

    private static $max_height = 1080;

    /**
     * @return bool|null
     * @throws PixelSizeValidatorFailedException
     */
    public function pixelSizesAreValid()
    {
        $maxWidth = $this->config()->get('max_width');
        $maxHeight = $this->config()->get('max_height');

        $imageDetails = $this->checkImage($this->tmpFile['tmp_name']);

        if ($imageDetails['isImage'] === false) {
            throw new PixelSizeValidatorFailedException(
                _t(__CLASS__.'.NotAnImage', 'File is not an image')
            );
        }

        if ($imageDetails['width'] > $maxWidth) {
            throw new PixelSizeValidatorFailedException(
                _t(__CLASS__.'.ImageTooWide', 'Image is {width} pixels wide, maximum allowed width is {maxwidth}', [
                    'width' => $imageDetails['width'],
                    'maxwidth' => $maxWidth
                ])
            );
        }

        if ($imageDetails['height'] > $maxHeight) {
            throw new PixelSizeValidatorFailedException(
                _t(__CLASS__.'.ImageTooTall', 'Image is {height} pixels tall, maximum allowed height is {maxheight}', [
                    'height' => $imageDetails['height'],
                    'maxheight' => $maxHeight
                ])
            );
        }

        return true;
    }

    /**
     * @param string $filename
     * @return array
     */
    public function checkImage(string $filename)
    {
        $imageDetails = getimagesize($filename);

        if ($imageDetails === false) {
            return [
                'isImage' => false,
                'width'   => null,
                'height'  => null,
            ];
        } else {
            return [
                'isImage' => true,
                'width'   => $imageDetails[0],
                'height'  => $imageDetails[1],
            ];
        }
    }

    public function validate()
    {
        if (parent::validate() === false) {
            return false;
        }

        try {
            return $this->pixelSizesAreValid();
        } catch (PixelSizeValidatorFailedException $e) {
            $this->errors[] = sprintf('Pixel Size: %s', $e->getMessage());
            return false;
        }
    }
}
