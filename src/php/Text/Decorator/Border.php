<?php
namespace randomhost\Image\Text\Decorator;

use randomhost\Image;

/**
 * Decorates a generic image overlay text with a colored border
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://github.random-host.com/image/
 */
class Border extends Generic implements Image\Text\Text
{
    /**
     * Image\Color object instance
     *
     * @var Image\Color|null
     */
    protected $borderColor = null;

    /**
     * Sets the border color used for rendering text overlay borders.
     *
     * @param Image\Color $color Color object instance.
     *
     * @return $this
     */
    public function setBorderColor(Image\Color $color)
    {
        $this->borderColor = $color;

        return $this;
    }

    /**
     * Returns the border color used for rendering text overlay borders.
     *
     * @return null|\randomhost\Image\Color
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * Renders the given text onto the image resource, using the given coordinates.
     *
     * @param int    $xPosition The x-ordinate.
     * @param int    $yPosition The y-ordinate position of the fonts baseline.
     * @param string $text      The text string in UTF-8 encoding.
     *
     * @return $this;
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     *                           resource or the font file isn't set.
     */
    public function insertText($xPosition, $yPosition, $text)
    {
        $this->insertTextBorder($xPosition, $yPosition, $text);

        parent::insertText($xPosition, $yPosition, $text);

        return $this;
    }

    /**
     * Renders the given text border onto the image resource, using the given
     * coordinates.
     *
     * @param int    $xPosition The x-ordinate.
     * @param int    $yPosition The y-ordinate position of the fonts baseline.
     * @param string $text      The text string in UTF-8 encoding.
     *
     * @return $this;
     *
     * @throws \RuntimeException Thrown if $this->image is not a valid image
     *                           resource or the font file isn't set.
     */
    protected function insertTextBorder($xPosition, $yPosition, $text)
    {
        if (!$this->getBorderColor() instanceof Image\Color) {
            throw new \RuntimeException(
                'Attempt to render text border without setting a color'
            );
        }

        /*
         * Set the alpha transparency to zero since we are going to render the
         * overlay text multiple times using different offsets to achieve a
         * border effect which is not natively supported by GD.
         */
        $borderAlpha = $this->getBorderColor()->getAlpha();
        $this->getBorderColor()->setAlpha(0);

        /*
         * Overwrite the original text color with the border color so we can use
         * the decorated class for rendering.
         */
        $textColor = $this->getTextColor();
        $this->setTextColor($this->getBorderColor());

        // render border
        parent::insertText($xPosition - 1, $yPosition - 1, $text);
        parent::insertText($xPosition - 1, $yPosition, $text);
        parent::insertText($xPosition - 1, $yPosition + 1, $text);
        parent::insertText($xPosition, $yPosition - 1, $text);
        parent::insertText($xPosition, $yPosition + 1, $text);
        parent::insertText($xPosition + 1, $yPosition - 1, $text);
        parent::insertText($xPosition + 1, $yPosition, $text);
        parent::insertText($xPosition + 1, $yPosition + 1, $text);

        // restore original border alpha transparency
        $this->getBorderColor()->setAlpha($borderAlpha);

        // restore original text color for rendering the text
        $this->setTextColor($textColor);

        return $this;
    }
}
