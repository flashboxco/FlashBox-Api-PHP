<?php

namespace FlashBox\Model;

use FlashBox\FlashBoxApiHandler;
use FlashBox\Config\Configs;
use FlashBox\Exception\FlashBoxApiException;
use FlashBox\Validator\FlashBoxValidator;

class Order
{
    // Attributes ------------------------------------------------------------------------------------------------------

    private $transportType;
    private $paymentType;
    private $originAddress;
    private $destinationsAddress;
    private $hasReturn;
    private $cashed;
    private $scheduled_at;
    private $discount_coupon;

    public function __construct($transportType, $paymentType, $originAddress, $destinationsAddress, $scheduled_at = null, $discount_coupon=null)
    {
        $this->setTransportType($transportType);
        $this->setPaymentType($paymentType);
        $this->addOriginAddress($originAddress);
        $this->setHasReturn(false);
        $this->setCashed(false);

        if($scheduled_at)
        {
            $this->setScheduledAt($scheduled_at);
        }

        if($discount_coupon)
        {
            $this->setDiscountCoupon($discount_coupon);
        }

        $this->destinationsAddress = [];
        if (!is_array($destinationsAddress)) {
            $this->addDestinationsAddress($destinationsAddress);
        } else {
            foreach ($destinationsAddress as $destAddress) {
                $this->addDestinationsAddress($destAddress);
            }
        }
    }

    // Setters ---------------------------------------------------------------------------------------------------------

    /**
     * @param $transportType
     * @throws FlashBoxApiException
     */
    public function setTransportType($transportType)
    {
        $transportType = FlashBoxValidator::sanitize($transportType);
        if (!in_array($transportType, array_keys(Configs::TRANSPORT_TYPES))) {
            throw new FlashBoxApiException('Transport Type is not correct');
        }

        $this->transportType = $transportType;
    }

    /**
     * @param $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $paymentType = FlashBoxValidator::sanitize($paymentType);
        $this->paymentType = $paymentType;
    }

    /**
     * Set scheduled_at attribute
     * @param $scheduledAt
     */
    public function setScheduledAt($scheduled_at)
    {
        $this->scheduled_at = $scheduled_at;
    }

    /**
     * Set discount_coupon attribute
     * @param $discountCoupon
     */
    public function setDiscountCoupon($discount_coupon)
    {
        $this->discount_coupon = $discount_coupon;
    }

    /**
     * @param $originAddress
     * @throws FlashBoxApiException
     */
    public function addOriginAddress($originAddress)
    {
        if (!$originAddress instanceof Address) {
            throw new FlashBoxApiException('Origin Address is not valid!');
        }

        if ($originAddress->getType() != 'origin') {
            throw new FlashBoxApiException('Type Of Origin Address is not correct! please change it to `origin`.');
        }

        $this->originAddress = $originAddress;
    }

    /**
     * @param $newDestinationsAddress
     * @throws FlashBoxApiException
     */
    public function addDestinationsAddress($newDestinationsAddress)
    {
        if (!$newDestinationsAddress instanceof Address) {
            throw new FlashBoxApiException('Destination Address is not valid!');
        }

        if ($newDestinationsAddress->getType() != 'destination') {
            throw new FlashBoxApiException('Type Of Destination Address is not correct! please change it to `destination`.');
        }

        array_push($this->destinationsAddress, $newDestinationsAddress);
    }

    /**
     * @param mixed $hasReturn
     */
    public function setHasReturn($hasReturn)
    {
        $this->hasReturn = $hasReturn;
    }

    /**
     * @param mixed $cashed
     */
    public function setCashed($cashed)
    {
        $this->cashed = $cashed;
    }

    // Getters ---------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getTransportType()
    {
        return $this->transportType;
    }

    /**
     * @return mixed
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @return mixed
     */
    public function getOriginAddress()
    {
        return $this->originAddress;
    }

    /**
     * @return mixed
     */
    public function getDestinationsAddress()
    {
        return $this->destinationsAddress;
    }

    /**
     * @return mixed
     */
    public function getDestinationsAddressArray()
    {
        $addresses = [];
        foreach ($this->getDestinationsAddress() as $address) {
            array_push($addresses, $address->toArray('destination'));
        }
        return $addresses;
    }

    /**
     * @return mixed
     */
    public function getHasReturn()
    {
        return $this->hasReturn;
    }

    /**
     * @return mixed
     */
    public function getCashed()
    {
        return $this->cashed;
    }

    // Actions ---------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function create()
    {
        return FlashBoxApiHandler::createOrder($this);
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return FlashBoxApiHandler::getPrice($this);
    }

    public function getScheduledAt()
    {
        return $this->scheduled_at;
    }

    public function getDiscountCoupon()
    {
        return $this->discount_coupon;
    }

    /**
     * @param $orderID
     * @return mixed
     */
    public static function cancel($orderID, $comment)
    {
        return FlashBoxApiHandler::cancelOrder($orderID, $comment);
    }

    /**
     * @param $orderID
     * @return mixed
     */
    public static function finish($orderID, $params)
    {
        return FlashBoxApiHandler::finishOrder($orderID, $params);
    }

    /**
     * @param $orderID
     * @return mixed
     */
    public static function getDetails($orderID)
    {
        return FlashBoxApiHandler::getOrderDetail($orderID);
    }

    // Utilities -------------------------------------------------------------------------------------------------------

    /**
     * @param $endPoint
     * @return array
     */
    public function toArray($endPoint)
    {
        $this->isValid();

        $orderArray = [
            'transport_type' => $this->getTransportType(),
            'payment_type' => $this->getPaymentType(),
            'has_return' => $this->getHasReturn(),
            'cashed' => $this->getCashed(),
            'scheduled_at' => $this->getScheduledAt(),
            'discount_coupon' => $this->getDiscountCoupon()
        ];

        $orderArray['addresses'] = array_merge(
            [$this->getOriginAddress()->toArray($endPoint)],
            $this->getDestinationsAddressArray()
        );

        return $orderArray;
    }

    /**
     * @return bool
     * @throws FlashBoxApiException
     */
    private function isValid()
    {

        // CHECK TRANSPORT_TYPE
        if (!in_array($this->getTransportType(), array_keys(Configs::TRANSPORT_TYPES))) {
            throw new FlashBoxApiException('Transport Type is not correct!');
        }

        // CHECK PAYMENT_TYPE
        if (!$this->getPaymentType()) {
            throw new FlashBoxApiException('Payment Type is not correct!');
        }

        // CHECK ORIGIN
        if (!$this->getOriginAddress()) {
            throw new FlashBoxApiException('Each Order Requires One Origin Address!');
        }
        if ($this->getOriginAddress()->getType() != 'origin') {
            throw new FlashBoxApiException('Type Of Origin Address is not correct! please change it to `origin`.');
        }

        // CHECK DESTINATIONS
        if (count($this->getDestinationsAddress()) < 1) {
            throw new FlashBoxApiException('Each Order Requires At Least One Destination!');
        }
        foreach ($this->getDestinationsAddress() as $destination) {
            if ($destination->getType() != 'destination') {
                throw new FlashBoxApiException('Type of Destination Address is not correct! please change it to `destination`.');
            }
        }

        return true;
    }

}