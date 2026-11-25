# Laravel WhatsApp Voice AI

An AI-powered WhatsApp and Voice Automation Platform built with Laravel.

The platform combines WhatsApp Cloud API, AI-powered conversations, Sarvam AI voice calling, CRM, lead management and business automation.

## Overview

Laravel WhatsApp Voice AI helps businesses automate customer communication through WhatsApp and AI-powered voice calls.

The system is designed to support multiple businesses, AI agents, customer conversations, automated workflows and centralized communication management.

## Key Features

### WhatsApp Integration
- Meta WhatsApp Cloud API
- Automated incoming message handling
- AI-powered WhatsApp replies
- Interactive buttons and list messages
- Shared WhatsApp inbox
- Human takeover and AI resume

### AI Conversation Management
- Business-specific AI agents
- Conversation history and memory
- Knowledge Base and RAG integration
- Lead qualification
- Context-aware responses

### Voice AI
- Sarvam AI integration
- Incoming AI calls
- Outgoing AI calls
- Voice agent management
- Call transcripts and summaries
- Automated callback workflows

### CRM & Lead Management
- Contact management
- Lead qualification
- Conversation tracking
- Follow-up management
- Call history

### Automation
- Visual workflow builder
- Conditional triggers
- Automated WhatsApp messages
- Calling automation
- Business-specific routing

### Multi-Business Support
- Multiple businesses
- Separate AI agents
- Business-specific knowledge bases
- Isolated conversations and workflows

## Technology Stack

- Laravel
- PHP
- Livewire
- MySQL / MariaDB
- Blade
- Tailwind CSS
- JavaScript
- Meta WhatsApp Cloud API
- Sarvam AI Voice Agents API

## Integrations

- Meta WhatsApp Cloud API
- Sarvam AI
- AI Model Providers
- Telephony Services

## Installation

Clone the repository:

```bash
git clone https://github.com/deepakpun10/laravel-whatsapp-voice-ai.git

cd laravel-whatsapp-voice-ai
```

Install PHP dependencies:

```bash
composer install
```

Create environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure your database and API credentials in `.env`.

Run migrations:

```bash
php artisan migrate
```

Install frontend dependencies:

```bash
npm install
npm run build
```

Start the application:

```bash
php artisan serve
```

## Environment Configuration

Configure the required credentials in your local `.env` file.

Examples:

```env
APP_NAME="Laravel WhatsApp Voice AI"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=

WHATSAPP_ACCESS_TOKEN=
WHATSAPP_PHONE_NUMBER_ID=
WHATSAPP_BUSINESS_ACCOUNT_ID=
WHATSAPP_WEBHOOK_VERIFY_TOKEN=

SARVAM_API_KEY=
SARVAM_WORKSPACE_ID=
SARVAM_AGENT_ID=
```

Never commit actual API keys, access tokens, passwords or private credentials to GitHub.

## Webhook Configuration

Configure the Meta WhatsApp Cloud API webhook to point to:

```text
https://your-domain.com/webhook/whatsapp
```

For local development, use a secure HTTPS tunnel such as Cloudflare Tunnel.

## Security

- Keep API credentials encrypted.
- Never commit `.env`.
- Validate incoming webhooks.
- Verify webhook signatures.
- Enforce business-level data isolation.
- Protect administrative routes.

## Development Status

This project is under active development.

Features and integrations are being implemented and tested incrementally.

## License

License information will be added when the project license is finalized.
