<?php
/**
 * Created by PhpStorm.
 * User: namma
 * Date: 01/05/2015
 * Time: 07:41
 */
namespace App\Services;

use Sly\NotificationPusher\Adapter\Apns;
use Sly\NotificationPusher\Adapter\Gcm;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;

class PushService
{
    const SERVICE_APNS  = 'Apns';
    const SERVICE_GCM   = 'Gcm';
    const SERVICE_MS    = 'Microsoft';

    protected $certificatesPath;

    public function __construct()
    {
        $this->certificatesPath = base_path('config/certificates');
    }

    /**
     * Push a message (with arguments) to online service
     *
     * @param string $serviceName
     * @param string $message
     * @param array $tokens
     * @param array $parameters
     * @return \Sly\NotificationPusher\Collection\PushCollection
     */
    public function push($serviceName, $message, $tokens, array $parameters = [])
    {
        $pushManager = new PushManager();
        $adapter = $this->getApnsAdapter();
        $deviceTokens = [
            '69542aa979db1cba6e0266b4e94300772eb6181b6e199313f16e92a7764c0800',
            '7db6d3fefb30cec6703af6aff2728fba51b37be69a94425e9aecd0e566ac5b88',
        ];
        $devices = $this->createDevices($deviceTokens);
        $options = ['sound' => 'default'];

        $pushManager->add(new Push(
            $adapter,
            $devices,
            $this->createMessage($message),
            $options
        ));

        $pushes = $pushManager->push();

        return $pushes;
    }

    public function pushToGcm($message, $tokens, array $parameters = [])
    {
        $pushManager = new PushManager();
        $adapter = $this->getGcmAdapter();
        $devices = $this->createDevices($tokens);
        $options = ['sound' => 'default'];

        $pushManager->add(new Push(
            $adapter,
            $devices,
            $this->createMessage($message),
            $options
        ));

        $pushes = $pushManager->push();

        return $pushes;
    }

    /**
     * Retrieve an APNS adapter
     *
     * @return Apns
     */
    protected function getApnsAdapter()
    {
        $adapter = new Apns([
            'certificate' => base_path('config/certificates/apns/apns.pem'),
        ]);

        return $adapter;
    }

    /**
     * Retrieve an GCM adapter
     *
     * @return Gcm
     */
    protected function getGcmAdapter()
    {
        $config = json_decode(file_get_contents($this->certificatesPath . '/gcm/gcm.json'), true);

        $adapter = new Gcm([
            'apiKey' => $config['api_key'],
        ]);

        return $adapter;
    }

    /**
     * Create a Device object
     *
     * @param string $token
     * @param array $parameters
     * @return Device
     */
    protected function createDevice($token, $parameters = [])
    {
        $device = new Device($token, $parameters);
        return $device;
    }

    /**
     * Return a collection of devices for the given tokens
     *
     * @param array $deviceTokens List of device tokens
     * @param array $parameters
     * @return DeviceCollection
     */
    protected function createDevices($deviceTokens, $parameters = [])
    {
        $deviceCollection = new DeviceCollection();

        foreach ($deviceTokens as $token) {
            $deviceCollection->add($this->createDevice($token, $parameters));
        }

        return $deviceCollection;
    }

    /**
     * Create a Message object
     *
     * @param string $message
     * @param array $options
     * @return Message
     */
    protected function createMessage($message, $options = [])
    {
        $message = new Message($message, $options);
        return $message;
    }
}
