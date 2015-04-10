<?php
namespace Fwolf\Bin\WeiboToPp;

use Fwlib\Net\Curl;
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
     * @var Curl
     */
    protected $curl = null;


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
     * @return  Curl
     */
    protected function getCurl()
    {
        if (is_null($this->curl)) {
            $this->curl = new Curl();
        }

        return $this->curl;
    }


    /**
     * Call {@see curl_getinfo()} on handle
     *
     * @param   resource    $handle
     * @param   int         $option
     * @return  int
     */
    protected function getCurlInfo($handle, $option)
    {
        return curl_getinfo($handle, $option);
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
        $arrayUtil = $this->getUtilContainer()->getArray();
        $plainBody = $arrayUtil->getIdx($this->contents, 'body-plain', '');

        $curl = $this->getCurl();
        $iftttMailUrl = $curl->match('/^<img src="([^"]+)" /', $plainBody);

        if (!empty($iftttMailUrl)) {
            $iftttJumpUrl = $this->getLocationHeader($iftttMailUrl);
            $weiboImgUrl = $this->getLocationHeader($iftttJumpUrl);

            if (!empty($weiboImgUrl)) {
                return [$weiboImgUrl];
            }
        }

        return [];
    }


    /**
     * Get HTTP Location header
     *
     * @param   string  $url
     * @return  string
     */
    protected function getLocationHeader($url)
    {
        $curl = $this->getCurl();

        $handle = $curl->getHandle();
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, false);

        $result = $curl->get($url);

        $headerSize = $this->getCurlInfo($handle, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $headerSize);

        $location = $curl->match("/\nLocation: (.*?)\n/", $header);

        return trim($location);
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
