<?php

/**
 * StepStone PDFreactor PHP Wrapper version 2
 * 
 * This library is based on RealObjects PDFreactor PHP Wrapper v4.
 * https://www.pdfreactor.com
 * 
 * Released under the following license:
 * 
 * The MIT License (MIT)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace StepStone\PdfReactor;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use stdClass;
use StepStone\PdfReactor\Exceptions\HttpException;
use StepStone\PdfReactor\Data\Progress;
use StepStone\PdfReactor\Data\Result;
use StepStone\PdfReactor\Data\Version;

class PdfReactor
{
    const CLIENT    = 'DSEbot';
    const VERSION   = 2;

    /** @var string */
    protected $apiRestPrefix    = 'service/rest';

    /** @var Client */
    protected $httpClient;
    
    /**
     * Store the result of the last API call made.
     *
     * @var null|stdClass
     */
    protected $result;

    /**
     * Class Constructor.
     * 
     * Setup HttpClient for future calls to PdfReactor server.
     *
     * @param string $host
     * @param string|null $key
     * @param integer $port
     * @param string|null $apiRestPrefix
     * @param array $options @see https://docs.guzzlephp.org/en/stable/request-options.html
     */
    public function __construct(
        string $host, 
        ?string $key            = null, 
        int $port               = 9423, 
        ?string $apiRestPrefix = null, 
        array $headers          = [],
        bool $allowRedirects    = false,
        bool $httpErrors        = true
    ) {
        $this->apiRestPrefix    = $apiRestPrefix ?: $this->apiRestPrefix;
        
        $options    = [
            'allow_redirects'   => $allowRedirects,
            'base_uri'  => rtrim($host, '/') . ":{$port}",
            'headers'   => array_merge([
                'Content-Type'  => 'application/json',
                'User-Agent'    => PdfReactor::CLIENT . ' PHP Client v' . PdfReactor::VERSION,
            ], $headers),
            'http_errors'   => $httpErrors,
            'query'         => [],
        ];

        if ($key) {
            $options['query']['apiKey'] = $key;
        }

        $this->httpClient   = new Client($options);
    }

    /**
     * Make an async request to PDFReactor to create a new Document.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#post-convert-async
     * 
     * @throws Exception if $convertable is not an instance of Convertable or can't be converted to one.
     * 
     * @throws HttpException If Location header is missing from result.
     * 
     * @throws HttpException If the Document Id sent by the server can't be parsed from the Location header.
     *      The PDFreactor service will send a UUID as a document Id. 
     *
     * @param Convertable $config
     * @return string
     */
    public function convertAsync($convertable): string
    {
        if (is_array($convertable)) {
            $convertable    = Convertable::create($convertable);
        }

        if (! $convertable instanceof Convertable) {
            throw new Exception('$convertable must be an Array or Convertable.');
        }

        $this->result = $this->call('POST', 'convert/async.json', $convertable->toArray());

        if (! isset($this->result->headers['Location'][0])) {
            throw new HttpException("Unable to retrieve Document ID from Response.", 500);
        }

        // we get a url back like /progress/some-uu-id we only want the uuid.
        preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $this->result->headers['Location'][0], $matches);

        if (! count($matches) || ! is_string($matches[0])) {
            throw new HttpException("Unable to retrieve Document ID from Response.", 500);
        }

        return $matches[0];
    }

    /**
     * Delete a document from the server.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#delete-document-id
     * 
     * @throws HttpException if the $documentId isn't found.
     *
     * @param string $documentId
     * @return boolean
     */
    public function deleteDocument(string $documentId): bool
    {
        try {
            $this->result = $this->call('DELETE', "document/{$documentId}.json");

            return ($this->result->status === 204);

        } catch (HttpException $e) {
            // if it's a 404 error rewrite the message to include $documentId.
            throw new HttpException(
                ($e->getStatus() == 404 ? "No document was found with ID {$documentId}." : $e->getMessage()),
                $e->getStatus(),
                $e->getCode()
            );
        }
    }

    /**
     * Returns the document with a given ID.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#get-document-id
     *
     * @param string $documentId
     * @return Result
     */
    public function getDocument(string $documentId): Result
    {
        try {
            $this->result   = $this->call('GET', "document/{$documentId}.json");

            return (new Result($this->result->body));

        } catch (HttpException $e) {
            // if it's a 404 error rewrite the message to include $documentId.
            throw new HttpException(
                ($e->getStatus() == 404 ? "No document was found with ID {$documentId}." : $e->getMessage()),
                $e->getStatus(),
                $e->getCode()
            );
        }
    }

    /**
     * Download a document. This will pull down the binary data of the document. If a $filename is given
     * it will attempt to write the file to disk otherwise it will return the binary data.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#get-document-id
     * 
     * @throws Exception If a $filename is given but can't be written to.
     * @throws HttpException If the API throws an HTTPException we will rewrite any 404 errors to
     *      include the $documentId.
     *
     * @param string $documentId
     * @param string|null $filename
     * @return bool|string true if $filename is given and written to successfully. string if no $filename.
     */
    public function getDocumentAsBinary(string $documentId, ?string $filename = null) 
    {
        try {

            $this->result   = $this->call('GET', "document/{$documentId}.bin");

            // write the pdf to disk if a $filename is given.
            if ($filename) {

                // can we actually write to this file?
                if (! is_writable($filename)) {
                    throw new Exception("Unable to write document to {$filename}.");
                }

                $handle = fopen($filename, 'w');
                fwrite($handle, $this->result->body, strlen($this->result->body));

                return true;
            }

            return $this->result->body;            

        } catch (HttpException $e) {
            // if it's a 404 error rewrite the message to include $documentId.
            throw new HttpException(
                ($e->getStatus() == 404 ? "No document was found with ID {$documentId}." : $e->getMessage()),
                $e->getStatus(),
                $e->getCode()
            );
        }
    }

    /**
     * Returns result from the last API call made.
     *
     * @return stdClass|null
     */
    public function getLastResult(): ?stdClass
    {
        return $this->result;
    }

    /**
     * Returns the PdfReactor Object.
     *
     * @return self
     */
    public function getPdfReactor(): self
    {
        return $this;
    }

    /**
     * Get the progress of an aysnc conversion process.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#get-progress-id
     * 
     * @throws HttpException when the server returns a 404 error.
     * 
     *
     * @param string $documentId
     * @return Progress
     */
    public function getProgress(string $documentId): Progress
    {
        // The native PDFreactor error doesn't attach the $documentId in the
        // error message, so we'll catch and rethrow with it.
        try {
            $this->result = $this->call('GET', "progress/{$documentId}.json");

            return (new Progress($this->result->body));

        } catch (HttpException $e) {

            throw new HttpException(
                ($e->getStatus() == 404 ? "No document was found with ID {$documentId}." : $e->getMessage()),
                $e->getStatus(),
                $e->getCode()
            );
        }
    }

    /**
     * Get the status of the server.
     * 
     * We're only getting the status of the server so we'll supress the HttpException that is raised if the
     * server responds back with something other than a 200 status. It'll be up to the developer to decide
     * how to raise the error to the end user.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#get-status
     *
     * @return stdClass
     */
    public function getStatus(): stdClass
    {
        try {

            $this->result   = $this->call('GET', 'status.json');

            return (object)['status' => 200, 'message' => 'OK'];

        } catch (HttpException $e) {
            $message    = [
                'status'    => $e->getStatus(),
                'error'     => [],
            ];

            if ($message['status'] == 401) {
                $message['error']   = 'Client Authentication failed. Check your API key and try again.';
            } elseif ($message['status'] == 503) {
                $message['error']   = 'The server is currently unavailable to process requests.';
            } else {
                $message['status']  = 500;
                $message['error']   = 'An unknown error occurred, please try again later.';
            }

            return (object)$message;

        } catch (Exception $e) {
            $message    = [
                'status'    => 500,
                'error'     => $e->getMessage(),
            ];

            return (object)$message;
        }
    }

    /**
     * Get the version info for the PDFreactor server.
     * 
     * @link https://www.pdfreactor.com/product/doc/webservice/rest.html#get-version
     *
     * @return Version
     */
    public function getVersion(): Version
    {
        $this->result   = $this->call('GET', 'version.json');
        
        return (new Version($this->result->body));
    }

    /**
     * Makes the HTTP request to the server.
     *
     * @param string $verb
     * @param string $uri
     * @param string|null $body
     * @return ReactorResponse
     */
    protected function call(string $verb, string $uri, $body = null): ReactorResponse
    {
        try {
            $uri        = $this->apiRestPrefix . '/' . ltrim($uri, '/');
            
            switch (strtoupper($verb)) {
                case 'GET':
                    $response   = $this->httpClient->request('GET', $uri);
                break;

                case 'POST':
                    $response   = $this->httpClient->request('POST', $uri, ['json' => $body]);
                break;

                case 'DELETE':
                    $response   = $this->httpClient->request('DELETE', $uri);
                break;

                default:
                    throw new Exception('Request method is not supported');
            }

            return new ReactorResponse($response);

        } catch (RequestException $e) {
            die($e->getMessage());
        
        } catch (Exception $e) {
            // convert to HttpException
            throw new HttpException($e->getMessage(), 500, $e->getCode(), $e);
        } 
    }
}