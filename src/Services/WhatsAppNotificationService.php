<?php

namespace SamuelTerra22\LaravelEvolutionClient\Services;

use Illuminate\Support\Facades\Notification;
use SamuelTerra22\LaravelEvolutionClient\Notifications\Channels\EvolutionWhatsAppChannel;
use SamuelTerra22\LaravelEvolutionClient\Notifications\Messages\EvolutionWhatsAppMessage;

class WhatsAppNotificationService
{
    /**
     * Send a quick text message to a phone number.
     */
    public static function sendText(string $phoneNumber, string $message, ?string $instance = null): void
    {
        $notification = new class($message, $instance) extends \Illuminate\Notifications\Notification {
            protected $message;
            protected $instance;

            public function __construct($message, $instance)
            {
                $this->message = $message;
                $this->instance = $instance;
            }

            public function via($notifiable): array
            {
                return [EvolutionWhatsAppChannel::class];
            }

            public function toEvolutionWhatsApp($notifiable): array
            {
                $msg = EvolutionWhatsAppMessage::text($this->message);

                if ($this->instance) {
                    $msg->instance($this->instance);
                }

                return $msg->toArray();
            }
        };

        // Create a temporary notifiable object
        $notifiable = new class($phoneNumber) {
            public $whatsapp_number;

            public function __construct($number)
            {
                $this->whatsapp_number = $number;
            }
        };

        Notification::send($notifiable, $notification);
    }

    /**
     * Send a bulk message to multiple phone numbers.
     */
    public static function sendBulkText(array $phoneNumbers, string $message, ?string $instance = null): void
    {
        $notifiables = collect($phoneNumbers)->map(function ($number) {
            return new class($number) {
                public $whatsapp_number;

                public function __construct($number)
                {
                    $this->whatsapp_number = $number;
                }
            };
        });

        $notification = new class($message, $instance) extends \Illuminate\Notifications\Notification {
            protected $message;
            protected $instance;

            public function __construct($message, $instance)
            {
                $this->message = $message;
                $this->instance = $instance;
            }

            public function via($notifiable): array
            {
                return [EvolutionWhatsAppChannel::class];
            }

            public function toEvolutionWhatsApp($notifiable): array
            {
                $msg = EvolutionWhatsAppMessage::text($this->message);

                if ($this->instance) {
                    $msg->instance($this->instance);
                }

                return $msg->toArray();
            }
        };

        Notification::send($notifiables, $notification);
    }

    /**
     * Send an image to a phone number.
     */
    public static function sendImage(string $phoneNumber, string $imageUrl, ?string $caption = null, ?string $instance = null): void
    {
        $notification = new class($imageUrl, $caption, $instance) extends \Illuminate\Notifications\Notification {
            protected $imageUrl;
            protected $caption;
            protected $instance;

            public function __construct($imageUrl, $caption, $instance)
            {
                $this->imageUrl = $imageUrl;
                $this->caption = $caption;
                $this->instance = $instance;
            }

            public function via($notifiable): array
            {
                return [EvolutionWhatsAppChannel::class];
            }

            public function toEvolutionWhatsApp($notifiable): array
            {
                $msg = EvolutionWhatsAppMessage::image($this->imageUrl, $this->caption);

                if ($this->instance) {
                    $msg->instance($this->instance);
                }

                return $msg->toArray();
            }
        };

        $notifiable = new class($phoneNumber) {
            public $whatsapp_number;

            public function __construct($number)
            {
                $this->whatsapp_number = $number;
            }
        };

        Notification::send($notifiable, $notification);
    }

    /**
     * Send a location to a phone number.
     */
    public static function sendLocation(
        string $phoneNumber,
        float $latitude,
        float $longitude,
        ?string $name = null,
        ?string $address = null,
        ?string $instance = null
    ): void {
        $notification = new class($latitude, $longitude, $name, $address, $instance) extends \Illuminate\Notifications\Notification {
            protected $latitude;
            protected $longitude;
            protected $name;
            protected $address;
            protected $instance;

            public function __construct($latitude, $longitude, $name, $address, $instance)
            {
                $this->latitude = $latitude;
                $this->longitude = $longitude;
                $this->name = $name;
                $this->address = $address;
                $this->instance = $instance;
            }

            public function via($notifiable): array
            {
                return [EvolutionWhatsAppChannel::class];
            }

            public function toEvolutionWhatsApp($notifiable): array
            {
                $msg = EvolutionWhatsAppMessage::location(
                    $this->latitude,
                    $this->longitude,
                    $this->name,
                    $this->address
                );

                if ($this->instance) {
                    $msg->instance($this->instance);
                }

                return $msg->toArray();
            }
        };

        $notifiable = new class($phoneNumber) {
            public $whatsapp_number;

            public function __construct($number)
            {
                $this->whatsapp_number = $number;
            }
        };

        Notification::send($notifiable, $notification);
    }
}
