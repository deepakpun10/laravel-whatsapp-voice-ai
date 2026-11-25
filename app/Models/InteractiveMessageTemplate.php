<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class InteractiveMessageTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'slug',
        'category',
        'interactive_type',
        'header_text',
        'body_text',
        'footer_text',
        'list_button_label',
        'cta_url',
        'cta_display_text',
        'language',
        'status',
        'ai_availability',
        'trigger_intent',
    ];

    protected $casts = [
        'ai_availability' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (InteractiveMessageTemplate $t) {
            if (empty($t->slug)) {
                $baseSlug = Str::slug($t->name) ?: 'template';
                $slug = $baseSlug;
                $count = 1;
                while (static::where('business_id', $t->business_id)->where('slug', $slug)->exists()) {
                    $count++;
                    $slug = "{$baseSlug}-{$count}";
                }
                $t->slug = $slug;
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function buttons(): HasMany
    {
        return $this->hasMany(InteractiveMessageButton::class, 'template_id')->orderBy('position');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(InteractiveMessageSection::class, 'template_id')->orderBy('position');
    }

    public function automationRules(): HasMany
    {
        return $this->hasMany(InteractiveAutomationRule::class, 'template_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(InteractiveMessageEvent::class, 'template_id');
    }

    public function isButtonType(): bool
    {
        return $this->interactive_type === 'button';
    }

    public function isListType(): bool
    {
        return $this->interactive_type === 'list';
    }

    public function isCtaType(): bool
    {
        return $this->interactive_type === 'cta_url';
    }

    /**
     * Build exact Meta WhatsApp Cloud API payload
     */
    public function toWhatsAppPayload(string $toPhone): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $toPhone,
            'type' => 'interactive',
        ];

        $interactive = [
            'type' => $this->interactive_type,
            'body' => [
                'text' => Str::limit($this->body_text, 1024),
            ],
        ];

        if (! empty($this->header_text)) {
            $interactive['header'] = [
                'type' => 'text',
                'text' => Str::limit($this->header_text, 60),
            ];
        }

        if (! empty($this->footer_text)) {
            $interactive['footer'] = [
                'text' => Str::limit($this->footer_text, 60),
            ];
        }

        if ($this->isButtonType()) {
            $buttons = [];
            foreach ($this->buttons->take(3) as $btn) {
                $buttons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => Str::limit($btn->button_id, 256),
                        'title' => Str::limit($btn->title, 20),
                    ],
                ];
            }

            $interactive['action'] = [
                'buttons' => $buttons,
            ];
        } elseif ($this->isListType()) {
            $sections = [];
            $totalRows = 0;

            foreach ($this->sections as $sec) {
                $rows = [];
                foreach ($sec->rows as $r) {
                    if ($totalRows >= 10) {
                        break;
                    }
                    $rowItem = [
                        'id' => Str::limit($r->row_id, 200),
                        'title' => Str::limit($r->title, 24),
                    ];
                    if (! empty($r->description)) {
                        $rowItem['description'] = Str::limit($r->description, 72);
                    }
                    $rows[] = $rowItem;
                    $totalRows++;
                }

                if (! empty($rows)) {
                    $sections[] = [
                        'title' => Str::limit($sec->title, 24),
                        'rows' => $rows,
                    ];
                }
            }

            $interactive['action'] = [
                'button' => Str::limit($this->list_button_label ?: 'Select Option', 20),
                'sections' => $sections,
            ];
        } elseif ($this->isCtaType()) {
            $interactive['action'] = [
                'name' => 'cta_url',
                'parameters' => [
                    'display_text' => Str::limit($this->cta_display_text ?: 'Open Link', 20),
                    'url' => $this->cta_url ?: 'https://pundeer.ai',
                ],
            ];
        }

        $payload['interactive'] = $interactive;

        return $payload;
    }
}
