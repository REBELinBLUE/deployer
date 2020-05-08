<?php

namespace REBELinBLUE\Deployer\Jobs;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use REBELinBLUE\Deployer\CheckUrl;
use REBELinBLUE\Deployer\Exceptions\CheckUrlMatchException;

/**
 * Request the urls.
 */
class RequestProjectCheckUrl extends Job implements ShouldQueue
{
    use SerializesModels;

    /**
     * @var int
     */
    public $timeout = 0;

    /**
     * @var Collection
     */
    private $links;

    /**
     * RequestProjectCheckUrl constructor.
     *
     * @param Collection $links
     */
    public function __construct(Collection $links)
    {
        $this->links = $links;
    }

    /**
     * Execute the command.
     *
     * @param Client $client
     */
    public function handle(Client $client): void
    {
        $this->links->each(function (CheckUrl $link) use ($client) {
            $link->last_log = null;

            try {
                $request = $client->get($link->url);

                if (!empty($link->match)) {
                    $body = (string) $request->getBody();

                    if (!preg_match('~' . htmlentities(trim(preg_quote($link->match, '~'))) . '~i', $body)) {
                        throw new CheckUrlMatchException(
                            'The URL is live but we could not find the "' . $link->match . '" content'
                        );
                    }
                }

                $link->online();
            } catch (RequestException $error) {
                $link->offline();

                $link->last_log = $this->generateLog($error);
            } catch (CheckUrlMatchException $error) {
                $link->offline();

                $link->last_log = $error->getMessage();
            }

            $link->save();
        });
    }

    /**
     * Generates the log.
     *
     * @param RequestException $error
     *
     * @return string
     */
    private function generateLog(RequestException $error): string
    {
        $message = $error->getMessage();

        // Only care about the first line
        $message = preg_replace('/response:$/', 'response', trim(strtok($message, PHP_EOL)));

        $log = $message . PHP_EOL . PHP_EOL . '--- Request ---' . PHP_EOL;
        $log .= Psr7\str($error->getRequest());

        if ($error->hasResponse()) {
            $log = trim($log) . PHP_EOL . PHP_EOL;
            $log .= '--- Response ---' . PHP_EOL . Psr7\str($error->getResponse());
        }

        // Normalise the newlines
        $log = str_replace("\r\n", PHP_EOL, $log);

        return trim($log);
    }
}
