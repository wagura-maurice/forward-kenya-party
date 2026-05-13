# WhatsApp Integration with WAHA

This document describes the WhatsApp integration for the Forward Kenya Party application using WAHA (WhatsApp HTTP API).

## Overview

The WhatsApp integration allows users to register for the Forward Kenya Party through WhatsApp conversations. The system provides a complete conversational onboarding flow that replicates the web registration process.

## Features

- **Automated Welcome Messages**: Introduces users to Forward Kenya Party
- **Party Registration Check**: Guides users to verify existing party memberships
- **Step-by-Step Registration**: Collects user information through WhatsApp
- **Data Validation**: Validates all inputs before proceeding
- **Member Creation**: Automatically creates users and members in the database
- **Conversation State Management**: Tracks user progress through registration

## Architecture

### Components

1. **WahaService** - Handles all outgoing WhatsApp messages
2. **WahaWebhookController** - Processes incoming messages and manages conversation flow
3. **WhatsappConversation Model** - Stores conversation state and user data
4. **Database Migration** - Creates the necessary tables
5. **Webhook Routes** - Handles WAHA callbacks

### Configuration

The integration uses the following settings (stored in the `settings` table):

- `WAHA_API_URL`: WAHA server endpoint (default: `http://84.247.143.79:3000`)
- `WAHA_API_KEY`: WAHA API authentication key
- `WAHA_SESSION`: WhatsApp session name (default: `default`)

## Setup Instructions

### 1. Database Setup

Run the migration to create the necessary tables:

```bash
php artisan migrate
```

### 2. Seed Settings

Run the settings seeder to populate WAHA configuration:

```bash
php artisan db:seed --class=SettingsTableSeeder
```

### 3. Configure WAHA

1. Access your WAHA dashboard
2. Navigate to webhook settings
3. Set the webhook URL to: `http://your-domain.com/webhook/waha`
4. Ensure the webhook is configured to receive message events

### 4. Test the Integration

Run the test command to verify everything is working:

```bash
php artisan app:test-whatsapp-integration
```

To test message sending:

```bash
php artisan app:test-whatsapp-integration --phone=254712345678
```

## User Flow

### Registration Commands

- `/start` - Begin the registration process
- `/join` - Join Forward Kenya Party
- `/help` - Show help information
- `/cancel` - Cancel current registration

### Registration Steps

1. **Welcome**: User receives party introduction and registration check instructions
2. **Surname**: User provides their surname
3. **Other Names**: User provides their other names (1-3 names)
4. **Phone Number**: User confirms their phone number
5. **ID Number**: User provides their 8-digit National ID number
6. **Date of Birth**: User provides their birth date (YYYY-MM-DD format)
7. **Gender**: User selects their gender (1=Male, 2=Female)
8. **County**: User selects their county from a numbered list
9. **Confirmation**: User reviews all details and confirms registration

### Validation Rules

- **Surname**: Letters only, 2-50 characters
- **Other Names**: 1-3 names, letters only, 2-100 characters total
- **Phone Number**: 10 digits starting with 07
- **ID Number**: Exactly 8 digits
- **Date of Birth**: Valid date, age 18-120 years
- **Gender**: Must be 1 (Male) or 2 (Female)
- **County**: Valid county number or name

## API Endpoints

### Webhook Endpoint

```
POST /webhook/waha
```

This endpoint receives all WhatsApp message events from WAHA.

### Message Templates

The system uses predefined message templates for each step of the registration process. All messages are formatted with proper WhatsApp markdown and include the Forward Kenya Party branding.

## Database Schema

### whatsapp_conversations

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| chat_id | string | WhatsApp chat ID (unique) |
| phone_number | string | Extracted phone number (unique) |
| current_step | string | Current registration step |
| conversation_data | json | Collected user data |
| last_activity_at | timestamp | Last interaction time |
| is_active | boolean | Conversation status |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

## Error Handling

The system includes comprehensive error handling:

- **Validation Errors**: Clear error messages with instructions
- **API Errors**: Logged with detailed information
- **Database Errors**: Graceful fallback with user notification
- **Network Errors**: Retry logic and timeout handling

## Logging

All WhatsApp interactions are logged:

- Incoming messages and processing results
- Outgoing message delivery status
- Validation errors and corrections
- Registration completion events

## Security Considerations

- API key is stored securely in database settings
- Webhook endpoint processes only message events
- User input validation prevents injection attacks
- Rate limiting can be applied to prevent spam

## Monitoring

Monitor the following:

- Laravel logs for WhatsApp-related errors
- Database for conversation states
- WAHA dashboard for message delivery status
- Registration completion rates

## Troubleshooting

### Common Issues

1. **Messages not sending**: Check WAHA server connectivity and API key
2. **Webhook not receiving**: Verify webhook URL configuration in WAHA
3. **Validation failing**: Check input formats and validation rules
4. **Database errors**: Ensure migrations are run and permissions are correct

### Debug Commands

```bash
# Test basic connectivity
php artisan app:test-whatsapp-integration

# Check conversation states
php artisan tinker
>>> WhatsappConversation::all()

# View recent logs
tail -f storage/logs/laravel.log
```

## Future Enhancements

Potential improvements to consider:

1. **Multi-language Support**: Add support for Swahili and other local languages
2. **Media Messages**: Support for document uploads and verification
3. **Interactive Buttons**: Use WhatsApp interactive messages for better UX
4. **Appointment Scheduling**: Allow users to schedule in-person meetings
5. **Payment Integration**: Add membership fee collection via WhatsApp

## Support

For issues or questions:

- Email: forwardkenyaparty@gmail.com
- Website: https://forwardkenyaparty.com
- WAHA Documentation: https://waha.devlike.pro

---

*Forward Kenya Party - OUR LIVES, OUR HERITAGE* 🇰🇪
