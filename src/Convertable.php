<?php

namespace StepStone\PdfReactor;

use Exception;

class Convertable
{
    const JS_MODE_DISABLED              = "DISABLED";
    const JS_MODE_ENABLED               = "ENABLED";
    const JS_MODE_ENABLED_NO_LAYOUT     = "ENABLED_NO_LAYOUT";
    const JS_MODE_ENABLED_REAL_TIME     = "ENABLED_REAL_TIME";
    const JS_MODE_ENABLED_TIME_LAPSE    = "ENABLED_TIME_LAPSE";

    /**
     * The request that is passed to the PDFReactor server.
     *
     * @var array
     */
    protected $config   = [];

    /**
     * Create a new Config.
     *
     * @param string $body
     * @param array $configs
     */
    public function __construct(string $body, array $configs = [])
    {
        $this->config   = array_merge(['document' => $body], $configs);
    }

    /**
     * Add any additional config options.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addConfig(string $name, $value): self
    {
        $this->config[$name]    = $value;

        return $this;
    }

    /**
     * @see https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Configuration-baseURL
     *
     * @param string $url
     * @return self
     */
    public function baseUrl(string $url = '/'): self
    {
        return $this->addConfig('baseURL', $url);
    }

    /**
     * Delete a config option.
     *
     * @param string $name
     * @return void
     */
    public function delConfig(string $name)
    {
        // don't delete the document
        if ($name != 'document') {
            unset($this->config[$name]);
        }
    }

    /**
     * @see https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#JavaScriptMode
     * 
     * @deprecated As of PDFReacter Server v10.
     *
     * @param string $mode
     * @return self
     */
    public function javaScriptMode(string $mode): self
    {
        return $this->addConfig('javaScriptMode', $mode);
    }

    /**
     * @see https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Configuration-keepDocument
     *
     * @param boolean $keep
     * @return self
     */
    public function keepDocument(bool $keep = true): self
    {
        return $this->addConfig('keepDocument', $keep);
    }


    /**
     * @see https://www.pdfreactor.com/product/doc/webservice/web-service-client.html#Configuration-mergeDocuments
     *
     * @param DocResource $resource
     * @return self
     */
    public function mergeDocuments(Document $resource): self
    {
        return $this->addConfig('mergeDocuments', $resource->toArray());
    }

    /**
     * Return the config array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->config;
    }

    /**
     * Helper to create a new convertable instance. $body can be the document body content as a string
     * or a $config array. If $body is a config array it must have a the 'document' key.
     * 
     * @throws Exception if $body is an array and 'document' key is missing or null.
     *
     * @param string|array $body
     * @param array|null $config
     * @return Convertable
     */
    public static function create($body, ?array $config = []): Convertable
    {
        if (is_array($body)) {
            $config = $body;
            $body   = $config['document'] ?? null;
        }

        if (is_null($body)) {
            throw new Exception('A Document body is required.');
        }

        return (new self($body, $config));
    }

    /**
     * Read the contents of a file to pass to the create() method.
     * 
     * @throws Exception if the $file isn't found.
     * 
     * @uses self::create() to create the Convertable instance once the $file data is read.
     *
     * @param string $file
     * @param array $config
     * @return Convertable
     */
    public static function createFromFile(string $file, array $config = []): Convertable
    {
        if (! file_exists($file)) {
            throw new Exception("Can't find file: {$file}");
        }

        return self::create(file_get_contents($file), $config);
    }
}