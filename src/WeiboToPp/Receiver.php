<?php
namespace Fwolf\Bin\WeiboToPp;

use Fwlib\Util\UtilContainerAwareTrait;

/**
 * Receiver
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 *
 * @copyright   Copyright 2015 Fwolf
 * @license     http://opensource.org/licenses/MIT MIT
 */
class Receiver
{
    use UtilContainerAwareTrait;


    /**
     * Raw posted content
     *
     * @var array
     */
    protected $contents = [];


    /**
     * Get weibo body
     *
     * @return  string
     */
    public function getBody()
    {
        $arrayUtil = $this->getUtilContainer()->getArray();

        return $arrayUtil->getIdx($this->contents, 'subject');
    }


    /**
     * Get attached images
     *
     * @return  string[]
     */
    public function getImages()
    {
        return $this->isMailFromIfttt()
            ? $this->getImagesFromIfttt()
            : $this->getImagesFromGmail();
    }


    /**
     * Get attached images sent from gmail to mailgun
     *
     * @return  string[]
     */
    public function getImagesFromGmail()
    {
        $arrayUtil = $this->getUtilContainer()->getArray();
        $attachCount =
            $arrayUtil->getIdx($this->contents, 'attachment-count', 0);

        $images = [];
        for ($i = 0; $i < $attachCount; $i++) {
            $key = 'attachment-' . ($i + 1);
            $images[] = $_FILES[$key]['tmp_name'];
        }

        return $images;
    }


    /**
     * Get attached images sent from IFTTT mail to mailgun
     *
     * @return  string[]
     */
    public function getImagesFromIfttt()
    {
        return [];
    }


    /**
     * Mail is send from IFTTT
     *
     * @return  bool
     */
    protected function isMailFromIfttt()
    {
        $arrayUtil = $this->getUtilContainer()->getArray();
        $from = $arrayUtil->getIdx($this->contents, 'from', '');

        return '@ifttt.com>' == substr($from, -11);
    }


    /**
     * Receive posted mail from mailgun
     *
     * @return  static
     */
    public function receive()
    {
        $httpUtil = $this->getUtilContainer()->getHttp();

        $this->contents = $httpUtil->getPosts();

        return $this;
    }
}
