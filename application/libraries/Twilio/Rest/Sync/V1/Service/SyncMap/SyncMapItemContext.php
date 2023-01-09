<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Sync\V1\Service\SyncMap;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Serialize;
use Twilio\Values;
use Twilio\Version;

/**
 * PLEASE NOTE that this class contains beta products that are subject to change. Use them with caution.
 */
class SyncMapItemContext extends InstanceContext {
    /**
     * Initialize the SyncMapItemContext
     *
     * @param Version $version Version that contains the resource
     * @param string $serviceSid The SID of the Sync Service with the Sync Map Item
     *                           resource to fetch
     * @param string $mapSid The SID of the Sync Map with the Sync Map Item
     *                       resource to fetch
     * @param string $key The key value of the Sync Map Item resource to fetch
     */
    public function __construct(Version $version, $serviceSid, $mapSid, $key) {
        parent::__construct($version);

        // Path Solution
        $this->solution = ['serviceSid' => $serviceSid, 'mapSid' => $mapSid, 'key' => $key, ];

        $this->uri = '/Services/' . \rawurlencode($serviceSid) . '/Maps/' . \rawurlencode($mapSid) . '/Items/' . \rawurlencode($key) . '';
    }

    /**
     * Fetch the SyncMapItemInstance
     *
     * @return SyncMapItemInstance Fetched SyncMapItemInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch(): SyncMapItemInstance {
        $payload = $this->version->fetch('GET', $this->uri);

        return new SyncMapItemInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['mapSid'],
            $this->solution['key']
        );
    }

    /**
     * Delete the SyncMapItemInstance
     *
     * @return bool True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete(): bool {
        return $this->version->delete('DELETE', $this->uri);
    }

    /**
     * Update the SyncMapItemInstance
     *
     * @param array|Options $options Optional Arguments
     * @return SyncMapItemInstance Updated SyncMapItemInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update(array $options = []): SyncMapItemInstance {
        $options = new Values($options);

        $data = Values::of([
            'Data' => Serialize::jsonObject($options['data']),
            'Ttl' => $options['ttl'],
            'ItemTtl' => $options['itemTtl'],
            'CollectionTtl' => $options['collectionTtl'],
        ]);

        $payload = $this->version->update('POST', $this->uri, [], $data);

        return new SyncMapItemInstance(
            $this->version,
            $payload,
            $this->solution['serviceSid'],
            $this->solution['mapSid'],
            $this->solution['key']
        );
    }

    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string {
        $context = [];
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Sync.V1.SyncMapItemContext ' . \implode(' ', $context) . ']';
    }
}