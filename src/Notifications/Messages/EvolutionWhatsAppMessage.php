<?php

namespace SamuelTerra22\LaravelEvolutionClient\Notifications\Messages;

class EvolutionWhatsAppMessage
{
    protected $data = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Set the message instance.
     */
    public function instance(string $instance): self
    {
        $this->data['instance'] = $instance;
        return $this;
    }

    /**
     * Create a text message.
     */
    public static function text(string $text): self
    {
        return new self([
            'type' => 'text',
            'text' => $text,
        ]);
    }

    /**
     * Set text message options.
     */
    public function options(bool $quoted = false, int $delay = 0, bool $linkPreview = true): self
    {
        $this->data['quoted'] = $quoted;
        $this->data['delay'] = $delay;
        $this->data['linkPreview'] = $linkPreview;
        return $this;
    }

    /**
     * Create an image message.
     */
    public static function image(string $url, string $caption = null): self
    {
        return new self([
            'type' => 'image',
            'url' => $url,
            'caption' => $caption,
        ]);
    }

    /**
     * Create a document message.
     */
    public static function document(string $url, string $filename = 'document', string $caption = null): self
    {
        return new self([
            'type' => 'document',
            'url' => $url,
            'filename' => $filename,
            'caption' => $caption,
        ]);
    }

    /**
     * Create a location message.
     */
    public static function location(float $latitude, float $longitude, string $name = null, string $address = null): self
    {
        return new self([
            'type' => 'location',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'name' => $name,
            'address' => $address,
        ]);
    }

    /**
     * Create a contact message.
     */
    public static function contact(string $name, string $number): self
    {
        return new self([
            'type' => 'contact',
            'contact_name' => $name,
            'contact_number' => $number,
        ]);
    }

    /**
     * Create a poll message.
     */
    public static function poll(string $question, array $options, int $selectableCount = 1): self
    {
        return new self([
            'type' => 'poll',
            'question' => $question,
            'options' => $options,
            'selectableCount' => $selectableCount,
        ]);
    }

    /**
     * Create a button message.
     */
    public static function buttons(string $title, string $body, array $buttons, string $footer = ''): self
    {
        return new self([
            'type' => 'buttons',
            'title' => $title,
            'body' => $body,
            'footer' => $footer,
            'buttons' => $buttons,
        ]);
    }

    /**
     * Add a reply button.
     */
    public function addReplyButton(string $text, array $data = []): self
    {
        $this->data['buttons'][] = [
            'type' => 'reply',
            'text' => $text,
            'data' => array_merge(['id' => 'btn-' . strtolower(str_replace(' ', '-', $text))], $data),
        ];
        return $this;
    }

    /**
     * Add a URL button.
     */
    public function addUrlButton(string $text, string $url): self
    {
        $this->data['buttons'][] = [
            'type' => 'url',
            'text' => $text,
            'data' => ['url' => $url],
        ];
        return $this;
    }

    /**
     * Create a list message.
     */
    public static function list(string $title, string $body, string $buttonText, array $sections, string $footer = ''): self
    {
        return new self([
            'type' => 'list',
            'title' => $title,
            'body' => $body,
            'buttonText' => $buttonText,
            'footer' => $footer,
            'sections' => $sections,
        ]);
    }

    /**
     * Add a list section.
     */
    public function addSection(string $title, array $rows): self
    {
        $this->data['sections'][] = [
            'title' => $title,
            'rows' => $rows,
        ];
        return $this;
    }

    /**
     * Create a template message.
     */
    public static function template(string $templateName, string $language = 'en_US', array $components = []): self
    {
        return new self([
            'type' => 'template',
            'templateName' => $templateName,
            'language' => $language,
            'components' => $components,
        ]);
    }

    /**
     * Add a template component.
     */
    public function addComponent(string $type, array $parameters = []): self
    {
        $this->data['components'][] = [
            'type' => $type,
            'parameters' => $parameters,
        ];
        return $this;
    }

    /**
     * Get the message data.
     */
    public function toArray(): array
    {
        return $this->data;
    }
}