<?php

namespace SamuelTerra22\LaravelEvolutionClient\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use SamuelTerra22\LaravelEvolutionClient\Facades\Evolution;
use SamuelTerra22\LaravelEvolutionClient\Models\Button;
use SamuelTerra22\LaravelEvolutionClient\Models\ListRow;
use SamuelTerra22\LaravelEvolutionClient\Models\ListSection;

class EvolutionWhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toEvolutionWhatsApp($notifiable);

        if (!$message) {
            return;
        }

        // Get the WhatsApp number from the notifiable entity
        $phoneNumber = $this->getPhoneNumber($notifiable);

        if (!$phoneNumber) {
            throw new \Exception('WhatsApp phone number not found for notifiable entity');
        }

        // Use specific instance if provided
        $evolution = isset($message['instance'])
            ? Evolution::instance($message['instance'])
            : Evolution::instance(Config::get('evolution.default_instance'));

        // Send based on message type
        switch ($message['type'] ?? 'text') {
            case 'text':
                return $this->sendTextMessage($evolution, $phoneNumber, $message);

            case 'image':
                return $this->sendImageMessage($evolution, $phoneNumber, $message);

            case 'document':
                return $this->sendDocumentMessage($evolution, $phoneNumber, $message);

            case 'location':
                return $this->sendLocationMessage($evolution, $phoneNumber, $message);

            case 'contact':
                return $this->sendContactMessage($evolution, $phoneNumber, $message);

            case 'buttons':
                return $this->sendButtonsMessage($evolution, $phoneNumber, $message);

            case 'list':
                return $this->sendListMessage($evolution, $phoneNumber, $message);

            case 'poll':
                return $this->sendPollMessage($evolution, $phoneNumber, $message);

            case 'template':
                return $this->sendTemplateMessage($evolution, $phoneNumber, $message);

            default:
                throw new \Exception("Unsupported message type: {$message['type']}");
        }
    }

    /**
     * Get the WhatsApp phone number from the notifiable entity.
     *
     * @param  mixed  $notifiable
     * @return string|null
     */
    protected function getPhoneNumber($notifiable)
    {
        // Try different common attributes for phone number
        $phoneAttributes = [
            'whatsapp_number',
            'phone_number',
            'phone',
            'mobile',
            'cellphone'
        ];

        foreach ($phoneAttributes as $attribute) {
            if (isset($notifiable->$attribute)) {
                return $notifiable->$attribute;
            }
        }

        // Check if notifiable has a method to get WhatsApp number
        if (method_exists($notifiable, 'getWhatsAppNumber')) {
            return $notifiable->getWhatsAppNumber();
        }

        return null;
    }

    /**
     * Send a text message.
     */
    protected function sendTextMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendText(
            $phoneNumber,
            $message['text'],
            $message['quoted'] ?? false,
            $message['delay'] ?? 0,
            $message['linkPreview'] ?? true
        );
    }

    /**
     * Send an image message.
     */
    protected function sendImageMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendImage(
            $phoneNumber,
            $message['url'],
            $message['caption'] ?? null
        );
    }

    /**
     * Send a document message.
     */
    protected function sendDocumentMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendDocument(
            $phoneNumber,
            $message['url'],
            $message['filename'] ?? 'document',
            $message['caption'] ?? null
        );
    }

    /**
     * Send a location message.
     */
    protected function sendLocationMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendLocation(
            $phoneNumber,
            $message['latitude'],
            $message['longitude'],
            $message['name'] ?? null,
            $message['address'] ?? null
        );
    }

    /**
     * Send a contact message.
     */
    protected function sendContactMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendContact(
            $phoneNumber,
            $message['contact_name'],
            $message['contact_number']
        );
    }

    /**
     * Send a buttons message.
     */
    protected function sendButtonsMessage($evolution, $phoneNumber, $message)
    {
        $buttons = [];

        foreach ($message['buttons'] as $buttonData) {
            $buttons[] = new Button(
                $buttonData['type'],
                $buttonData['text'],
                $buttonData['data'] ?? []
            );
        }

        return $evolution->message->sendButtons(
            $phoneNumber,
            $message['title'] ?? '',
            $message['body'] ?? '',
            $message['footer'] ?? '',
            $buttons
        );
    }

    /**
     * Send a list message.
     */
    protected function sendListMessage($evolution, $phoneNumber, $message)
    {
        $sections = [];

        foreach ($message['sections'] as $sectionData) {
            $rows = [];

            foreach ($sectionData['rows'] as $rowData) {
                $rows[] = new ListRow(
                    $rowData['title'],
                    $rowData['description'] ?? '',
                    $rowData['id']
                );
            }

            $sections[] = new ListSection($sectionData['title'], $rows);
        }

        return $evolution->message->sendList(
            $phoneNumber,
            $message['title'],
            $message['body'],
            $message['buttonText'],
            $message['footer'] ?? '',
            $sections
        );
    }

    /**
     * Send a poll message.
     */
    protected function sendPollMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendPoll(
            $phoneNumber,
            $message['question'],
            $message['selectableCount'] ?? 1,
            $message['options']
        );
    }

    /**
     * Send a template message.
     */
    protected function sendTemplateMessage($evolution, $phoneNumber, $message)
    {
        return $evolution->message->sendTemplate(
            $phoneNumber,
            $message['templateName'],
            $message['language'] ?? 'en_US',
            $message['components'] ?? []
        );
    }
}