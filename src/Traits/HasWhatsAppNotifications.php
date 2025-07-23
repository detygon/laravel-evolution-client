<?php

namespace SamuelTerra22\LaravelEvolutionClient\Traits;

trait HasWhatsAppNotifications
{
    /**
     * Get the WhatsApp number for notifications.
     */
    public function getWhatsAppNumber(): ?string
    {
        return $this->whatsapp_number ?? $this->phone_number ?? $this->phone ?? $this->mobile;
    }

    /**
     * Route notifications for the Evolution WhatsApp channel.
     */
    public function routeNotificationForEvolutionWhatsApp($notification): ?string
    {
        return $this->getWhatsAppNumber();
    }

    /**
     * Format phone number for WhatsApp (remove special characters, ensure country code).
     */
    public function formatWhatsAppNumber(?string $number = null): ?string
    {
        $phone = $number ?? $this->getWhatsAppNumber();

        if (!$phone) {
            return null;
        }

        // Remove all non-numeric characters
        $phone = preg_replace('/\D/', '', $phone);

        // If it doesn't start with country code, assume it's local (adjust as needed)
        if (strlen($phone) === 10) {
            $phone = '1' . $phone; // Add US country code, adjust for your country
        }

        return $phone;
    }
}