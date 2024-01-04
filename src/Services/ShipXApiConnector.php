<?php

namespace WebLivesInPost\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;
use WebLivesInPost\Models\Payload\AbstractPayload;
use WebLivesInPost\Models\Payload\ShipXResponse;

class ShipXApiConnector
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ConfigService
     */
    private $config;

    /**
     * @var Client
     */
    private $client;

    public function __construct(
        Logger $logger,
        ConfigService $configService
    ) {
        $this->logger = $logger;
        $this->config = $configService;
        $this->client = new Client();
    }

    public function createShipment(AbstractPayload $payload): ?ShipXResponse
    {
        $url = $this->config->getShipXEndpoint() . '/organizations/' . $this->config->getShipXOrgId() . '/shipments';

        try {
            $guzzleRes = $this->client->post(
                $url,
                [
                    'headers' => $this->prepareHeaders(),
                    'json' => $payload->__toArray()
                ]
            );

            $res = new ShipXResponse($guzzleRes);

            $this->logger->info('Successfully created shipment in ShipX.', [
                'id' => $res->getBody()['id'],
                'status' => $res->getBody()['status'],
                'orderNumber' => $payload->getReference(),
                'service' => $payload->getService()
            ]);

            return $res;
        } catch (ReflectionException $e) {
            $this->logger->err('Transforming the ShipX payload went wrong!', [
                'trace' => $e->getTraceAsString()
            ]);

            return null; // return no response
        } catch (RequestException $e) {
            $this->logger->err('Error creating shipment in ShipX!', [
                'orderNumber' => $payload->getReference(),
                'service' => $payload->getService(),
                'response' => $this->getResponseBody($e->getResponse()),
//                'trace' => $e->getTraceAsString()
            ]);

            return new ShipXResponse($e->getResponse());
        }
    }

    public function getShipment(string $id): ShipXResponse
    {
        $url = $this->config->getShipXEndpoint() . '/shipments/' . $id;

        $res = $this->client->get(
            $url,
            [
                'headers' => $this->prepareHeaders()
            ]
        );

        return new ShipXResponse($res);
    }

    public function getShipments(): ShipXResponse
    {
        $url = $this->config->getShipXEndpoint() . '/organizations/' . $this->config->getShipXOrgId() . '/shipments';

        $res = $this->client->get(
            $url,
            [
                'headers' => $this->prepareHeaders()
            ]
        );

        return new ShipXResponse($res);
    }

    private function prepareHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->config->getShipXToken(),
            'Accept' => 'application/json'
        ];
    }

    private function getResponseBody(?ResponseInterface $res)
    {
        if (empty($res)) return 'N/A';

        return json_decode($res->getBody()->getContents());
    }

    private function getRequestBody(?RequestInterface $req)
    {
        if (empty($req)) return 'N/A';

        return json_decode($req->getBody()->getContents());
    }
}
